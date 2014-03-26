<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title> output.php </title>
    </head>
    <body>
        <?php
        $name= $_POST['name'];
        print("<br/>that i received the following data:<br/>");
        print("Name: $name<br/>");
        ?>
    </body>
</html>
