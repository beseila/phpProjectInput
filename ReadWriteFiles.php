<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
<head><title>accesslog.php</title></head>
<body>
<?php
  print(date("Y/m/d H:i:s ") . "<br /> \n");
  print("<p> access log : </p> \n");
  $filepointer=fopen("log/access", "a+");
  flock($filepointer, LOCK_EX);
  fputs($filepointer,date("Y/m/d H:i:s ") . $_SERVER["REMOTE_ADDR"] . "\n");
  flock($filepointer, LOCK_UN);
  rewind($filepointer);
  while(!feof($filepointer)){
    $fileline = fgets($filepointer);
    print($fileline . "<br />");
  }
  fclose($filepointer);
?>
</body>
</html>
