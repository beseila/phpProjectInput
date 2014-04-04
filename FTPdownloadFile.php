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

            ftp_chdir($conn_id, '/test/errors');
            // get contents of the current directory
            $contents = ftp_nlist($conn_id, "");            
            // output $contents
            var_dump($contents);
            // path to remote file
            //$server_file = '*.csv';
            $local_file = '';
            foreach($contents as $file){
                if(preg_match('/(select|item|Seila)\\..*\\.csv/', $file)){
                    $local_file = "log/" . $file;
                    if (ftp_get($conn_id, $local_file, $file, FTP_BINARY)) {
                        echo "ダウンロードに成功しました $local_file<br>";
                    }
                    else {
                        echo "There was a problem\n";
                    }
                }
                
                
            }
            function readCSV($csvFile){
            $file_handle = fopen($csvFile, 'r');
            while (!feof($file_handle) ) {
      
             $line_of_text[] =fgetcsv($file_handle, 1024);
            }
            fclose($file_handle);
            return $line_of_text;
           }


           // Set path to CSV file
           $csvFile = 'log/item.20140402131919650.csv';
//           file_put_contents( $csvFile, utf8_encode($data));
           $csv = readCSV($csvFile); 
               
//           echo '<pre>';
//           print_r($csv);
//           echo '<pre>';
           
           for ($i=0; $i<count($csv)-1;$i++){
           foreach ($csv[$i] as $key => $value) {
               switch ($key){
               case 0:
                   $csvError[$i]['ItemNo']=mb_convert_encoding( $value, "UTF-8", "SJIS"); 
                   break;
               case 1:
                   $csvError[$i]['Error']=mb_convert_encoding( $value, "UTF-8", "SJIS");
                   break;
               }
                                 
           }
           }
           echo '<pre>';
           print_r($csvError);
           echo '<pre>';
           
           $csvFile = file('log/select.20140402132116626.csv');
           foreach($csvFile as $k)
           $csvs[] = explode(',', mb_convert_encoding( $k, "UTF-8", "SJIS"));//split
        
           echo '<pre>';
           print_r($csvs);
           echo '<pre>';
           
        ?>
    </body>
</html>
