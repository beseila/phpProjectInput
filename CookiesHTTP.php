<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<?php
        $time = $_COOKIE["firstphp"];
        if (!isset($time)){
            $time=0;
        }else{
            $time++;
        }
        setcookie("firstphp",$time,time()+60*60);
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>cookie.php</title>
    </head>
    <body>
        <?php
         if ($time==0){
             print("~ Nite to meet you");
         }else if ($time==1){
             print("it is the second time!");
         }else{
             print("~ Thank you for coming a lot");
         }
        ?>
    </body>
</html>
