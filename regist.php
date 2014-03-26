<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<?php
       session_start();
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>regist.php</title>
    </head>
    <body>
        <?php
        $syouhin = $_POST['syouhin'];
        $_SESSION['syouhin'] = $_POST['syouhin'];
        print("You have to register the following items <br/>");
        print("Item:$syouhin<br/>");
        ?>
        <a href=regist_check.php>products</a>
    </body>
</html>
