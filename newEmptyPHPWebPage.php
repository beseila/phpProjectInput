<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<?php

/* This sample program reads emails from a POP3 mailbox, including multipart emails with
attachments, and summarises the mailbox in a repsonse web page. Any .jpg images that are
included as attachments to emails are saved onto the server and displayed in the page -
very much a proof of concept for people who want to handle email via PHP / web front end. */

# Following are number to names mappings

$codes = array("7bit","8bit","binary","base64","quoted-printable","other");
$stt = array("Text","Multipart","Message","Application","Audio","Image","Video","Other");

$pictures = 0;
$html = "";

# Connect to the mail server and grab headers from the mailbox

$mail = imap_open('{pop.gmail.com}', 'moul.seila@gmail.com', '20082158');
$headers = imap_headers($mail);

# loop through each email

for ($n=1; $n<=count($headers); $n++) {
        $html .=  "<h3>".$headers[$n-1]."</h3><br />";

# Read the email structure and decide if it's multipart or not

        $st = imap_fetchstructure($mail, $n);
        $multi = $st->parts;
        $nparts = count($multi);
        if ($nparts == 0) {
                $html .=  "* SINGLE part email<br>";
        } else{
                $html .=  "* MULTI part email<br>";
        }

# look at the main part of the email, and subparts if they're present

        for ($p=0; $p<=$nparts; $p++) {
                $text =imap_fetchbody($mail,$n,$p);
                if ($p ==  0) {
                        $it = $stt[$st->type];
                        $is = ucfirst(strtolower($st->subtype));
                        $ie = $codes[$st->encoding];
                } else {
                        $it = $stt[$multi[$p-1]->type];
                        $is = ucfirst(strtolower($multi[$p-1]->subtype));
                        $ie = $codes[$multi[$p-1]->encoding];
                }

# Report on the mimetype

                $mimetype = "$it/$is";
                $html .=  "<br /><b>Part $p ... ";
                $html .=  "Encoding: $ie for $mimetype</b><br />";

# decode content if it's encoded (more types to add later!)

                if ($ie == "base64") {
                        $realdata = imap_base64($text);
                        }
                if ($ie == "quoted-printable") {
                        $realdata = imap_qprint($text);
                        }

# If it's a .jpg image, save it (more types to add later)

                if ($mimetype == "Image/Jpeg") {
                        $picture++;
                        $fho = fopen("imx/mp$picture.jpg","w");
                        fputs($fho,$realdata);
                        fclose($fho);
                        # And put the image in the report, limited in size
                        $html .= "<img src=/demo/imx/mp$picture.jpg width=150><br />";
                }

# Add the start of the text to the message

                $shorttext = substr($text,0,800);
                if (strlen($text) > 800) $horttext .= " ...\n";
                $html .=  nl2br(htmlspecialchars($shorttext))."<br>";
        }
}

# report results ...

?>
<html>
<head>
<title>Reading a Mailbox including multipart emails from within PHP</title>
</head>
<body>
<h1>Mailbox Summary ....</h1>
<?= $html ?>
</body>
</html>
