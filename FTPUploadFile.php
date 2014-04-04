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
            $ftp_server = "realmaxsys.sixcore.jp";
            $ftp_user_name = "realmaxsys.sixcore.jp";
            $ftp_user_pass = "Lf5eyXLewD9m";
            $conn_id = ftp_connect($ftp_server) or die ("Couldn't connect to $ftp_server"); 
            // login with username and password
            $login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);
            $file = 'log/Seila.20140402132116626.csv';
            $serverfile='/test/errors/Seila.20140402132116626.csv';
            
//            $file = $_FILES["log"]["Seila.20140402132116626.csv"];
//            $serverfile = "/test/errors/".$file;
            if (ftp_put($conn_id, $serverfile, $file, FTP_ASCII))
            {
                echo "Successfully uploaded $file.";
            }
            else
            {
                echo "Error uploading $file.";
            }

            ////Delete file FTP
//            if (ftp_delete($conn_id, $serverfile)) {
//                echo "$file deleted successful\n";
//            } else {
//                echo "could not delete $file\n";
//            }

          // close connection
          ftp_close($conn_id);
        ?>
    </body>
</html>
