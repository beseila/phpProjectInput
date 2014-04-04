<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <?php

// The "chilkat_9_3_2.php" is included in the Chilkat PHP Extension download
// The version number (9_3_2) should match version of the Chilkat extension used.
include("chilkat_9_3_2.php");

//  The mailman object is used for receiving (POP3)
//  and sending (SMTP) email.
$mailman = new CkMailMan();

//  Any string argument automatically begins the 30-day trial.
$success = $mailman->UnlockComponent('30-day trial');
if ($success != true) {
    print 'Component unlock failed' . "\n";
    exit;
}

//  Set the GMail account POP3 properties.
$mailman->put_MailHost('pop.gmail.com');
$mailman->put_PopUsername('moul.seila@gmail.com');
$mailman->put_PopPassword('20082158');
$mailman->put_PopSsl(true);
$mailman->put_MailPort(995);

//  Read mail headers and one line of the body.
//  To get the full emails, call CopyMail instead (no arguments)
// bundle is a Chilkat.EmailBundle2
$bundle = $mailman->GetAllHeaders(1);

if (is_null($bundle)) {
    print $mailman->lastErrorText() . "\n";
    exit;
}

for ($i = 0; $i <= $bundle->get_MessageCount() - 1; $i++) {
    // email is a Chilkat.Email2
    $email = $bundle->GetEmail($i);

    //  Display the From email address and the subject.
    print $email->from() . "\n";
    print $email->subject() . "\n" . "\n";

}


?>
    </body>
</html>
