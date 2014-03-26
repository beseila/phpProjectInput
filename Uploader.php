<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title>uploader.php</title>
    </head>
    <body>
        <P>file uploader</p>
        <?php
        $updir="Upload/";
        $filename=$_FILES['upfile']['name'];
        if (move_uploaded_file($_FILES['upfile']['tmp_name'],$updir.$filename)==false){
            print("Upload failed");
            print($_FILES['upfile']['error']);
        }
        else{
            print("<b>$filename</b>uploaded");
        }
                
        ?>
    </body>
</html>
