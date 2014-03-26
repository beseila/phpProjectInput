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
        <title>regist_check.php</title>
    </head>
    <body>
        <?php
        print("<br/>");
        print($_SESSION['syouhin']."<br/>");
        ?>
        <a href=Session.php>additional registration</a>
    </body>
</html>
