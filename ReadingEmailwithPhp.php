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
        /* connect to gmail */
        $hostname = '{imap.gmail.com:993/imap/ssl}INBOX';
        $username = 'realmaxseila@gmail.com';
        $password = '20082158';

        $mbox = @imap_open($hostname,$username,$password) or die('Cannot connect to Gmail: ' . imap_last_error());
        $mboxes = imap_mailboxmsginfo($mbox);
	$mail_cnt = $mboxes->Nmsgs;
  
        $emails = imap_search($mbox,'Unseen');
        /* if emails are returned, cycle through each... */
        if($emails) {
                /* put the newest emails on top */
                rsort($emails);
                /* for every email... */
                foreach($emails as $i) {
                        $head = imap_header($mbox, $i); 
			$fromdate= date("Y/m/d H:i:s", $head->udate);
			$fromaddress=$head->fromaddress;
			$toaddress=$head->toaddress;
			$subject=mb_convert_encoding(mb_decode_mimeheader($head->subject), "UTF-8");
			$body=imap_fetchbody($mbox, $i, 2, FT_PEEK);
//                        $body = imap_fetchbody($mbox,$i,2);
                        $structure = imap_fetchstructure($mbox, $i);
                        if(isset($structure->parts) && is_array($structure->parts) && isset($structure->parts[1])) {
                                    $part = $structure->parts[1];
                                   
                                    if($part->encoding == 3) {
                                       $body = imap_base64($body);
                                       $body=mb_convert_encoding($body, "UTF-8", "iso-2022-jp");
                                    } else if($part->encoding == 1) {
                                        $body = imap_8bit($body);
                                    } else {
                                        $body = imap_qprint($body);
                                    }
                                }     
                                echo $fromaddress.'<br>'.$fromdate.'<br>'.$subject.'<br>';
                                echo $body.'<br>'.'<br>';
                                echo '<br>'.'Customer Order'.'<br>';
                                if ($subject=='yahoo sample'||$subject=='yahoo multi item sample'){
                                func_YahooMail($body);
                                }else if ($subject=='rakuten sample'||$subject=='Rakuten sample2'){
                                func_RakutenMail($body);
                                }else if ($subject=='Future Sample'){
                                func_F($body);
                                }else if ($subject=='Other Mail'){
                                func_OtherMail($body);
                                }
                }
        } 
        /* close the connection */
        imap_close($mbox);
        
         function func_YahooMail($MailBody)
	{
            $Temp=str_replace("</div>","<br>",str_replace("<div>","",str_replace("&nbsp;","",$MailBody)));
            $ArrayTemp=preg_split("/<br>/",$Temp,-1,PREG_SPLIT_NO_EMPTY);
//             $ArrayTemp=preg_split('<br>',$MailBody,-1,PREG_SPLIT_NO_EMPTY);
//             print_r($ArrayTemp);
             $YahooOrder=null;
             $ItemNoCnt=0;
             //商�?情報一時�?管用
             $tmpItemName = "";
             $tmpItemQuantity = "";
             $tmpItemUnitPrice= "";
             $tmpItemOption= "";
             $rec = "";
              for ($i = 1; $i <= count($ArrayTemp)-2; $i++) {
                $rec = $ArrayTemp[$i];
               
                switch (true) {
                    case (stristr($rec,"ご注文番号") != false): 
                        if( preg_match('/\s\s.*$/', $rec,$tempItemNo)=== 1) { 
                        }
                        $YahooOrder["OrderNo"] = trim(str_replace("ご注文番号","",$rec));
                        break;
                    case (stristr($rec,"ご注文日") != false): 
                        $rec= trim(str_replace("年","/",str_replace("月","/",str_replace("時",":",str_replace("分",":",str_replace("秒","",str_replace("JST","",str_replace("ご注文日","",$rec))))))));
                        $ArrDayofweek=array("月曜日","火曜日","水曜日","木曜日","金曜日","土曜日","日曜日");
                        foreach ($ArrDayofweek as $value) {
                           $rec=str_replace($value,"",$rec);
                        }
                        $YahooOrder["OrderDate"] = trim(str_replace("年","/",str_replace("月","/",str_replace("日","",str_replace("土曜日 ","",str_replace("時",":",str_replace("分",":",str_replace("秒","",str_replace("JST","",str_replace("ご注文日","",$rec))))))))));
                        break;
                    case (stristr($rec,"小計") != false): 
                        $YahooOrder["SubTotal"] = trim(str_replace("円","",str_replace("小計","",$rec)));
                        break;
                    case (stristr($rec,"手数料") != false): 
                        $YahooOrder["Fee"] = trim(str_replace("円","",str_replace("手数料","",$rec)));
                        break;
                    case (stristr($rec,"送料") != false): 
                        $YahooOrder["Shiping"] = trim(str_replace("円","",str_replace("送料","",$rec)));
                        break;
                    case (stristr($rec," 合計 ") != false): 
                        $YahooOrder["Total"] = trim(str_replace("円","",str_replace("合計","",$rec)));
                        break;
                    case (stristr($rec,"獲得ポイント合計") != false): 
                        $YahooOrder["Points"] = trim(str_replace("ポイント","",str_replace("獲得ポイント合計","",$rec)));
                        break;
                    default :
                        if (strcmp(trim($rec),"") == 0)
                        {
                            break;
                        }
                    
                        if( preg_match('/([a-z]?\\d(\\d|[a-z]){6,7}-?(\\d|[a-z]){4,6})|[0-9]{4}-[A-Za-z]{4}/', $rec,$tempItemNo)=== 1) {
                            $ItemNoCnt = $ItemNoCnt + 1;
                            $YahooOrder["ItemNo"][$ItemNoCnt] = $tempItemNo[0];
                            $tmpItemName=$rec;
                            preg_match('/([a-z]?\\d(\\d|[a-z]){6,7}-?(\\d|[a-z]){4,6})|[0-9]{4}-[A-Za-z]{4}.*/', $rec,$tempReplace);
                            $tmpItemName= str_replace($tempReplace[0], "", $tmpItemName);
                            $YahooOrder["ItemName"][$ItemNoCnt] = trim($tmpItemName);
                        }
                        
                        //商�?�??
                        //行頭スペース以外�?�文字複数�?�らスペース�?��?�
                        if ($ItemNoCnt>0){
                        if( !(preg_match('/([a-z]?\\d(\\d|[a-z]){6,7}-?(\\d|[a-z]){4,6})|[0-9]{4}-[A-Za-z]{4}/', $rec,$tempItemNo)=== 1)) {
                        if( preg_match('/^\\S.*$/', trim($rec) ,$tmpItemName) === 1) {
                            //商�?�??を組�?��?��?�?�る
                            $YahooOrder["ItemName"][$ItemNoCnt] = $YahooOrder["ItemName"][$ItemNoCnt]." ".$tmpItemName[0];
                        }
                        }
                        //数�?
                        if( preg_match('/\\s\\d+\\s/', $rec, $tmpItemQuantity) === 1) {
                            $YahooOrder["ItemQuantity"][$ItemNoCnt] = $tmpItemQuantity[0];
                        }
                        
                        //�?�価
                        if( preg_match('/\\s\\d+円\\s/', $rec, $tmpItemUnitPrice) === 1) {
                            $YahooOrder["ItemUnitPrice"][$ItemNoCnt] = trim(str_replace("円","",$tmpItemUnitPrice[0]));
                        }
                        
                        //オプション
                        //スペース２�?��?�ら行末
                        if(preg_match('/円.+$/', $rec, $tmpItemOption) === 1) {
                            //79�?イト以�?を切り出�?�
                            $YahooOrder["ItemOption"][$ItemNoCnt] = 
                                    str_replace(" ","",
                                    str_replace("円 ","",
                                    trim($tmpItemOption[0])));
                        }
                        }
                        break;
                }
              }
//           print_r ($YahooOrder);
//            print_r (array_keys($YahooOrder));
//            $ColumnName=array_keys($YahooOrder);
//            print_r ($ColumnName);
        echo "OrderNo:".$YahooOrder["OrderNo"]."<br>".
             "OrderDate:".$YahooOrder["OrderDate"]."<br>";
             for ($i=1;$i<=count($YahooOrder["ItemNo"]);$i++){
                 echo '------------------------------------------------<br>';
                 echo 'ItemNo'.$i.':'.$YahooOrder["ItemNo"][$i]."<br>".
                'ItemName'.$i.':'.$YahooOrder["ItemName"][$i]."<br>".
                'ItemQuantity'.$i.':'.$YahooOrder["ItemQuantity"][$i]."<br>".
                'ItemUnitPrice'.$i.':'.$YahooOrder["ItemUnitPrice"][$i]."<br>".
                'ItemOption'.$i.':'.$YahooOrder["ItemOption"][$i]."<br>";
             }
        echo '------------------------------------------------<br>';
        echo    '....................................SubTotal:'.$YahooOrder["SubTotal"]."<br>";
                if (array_key_exists('Fee', $YahooOrder)){
                  echo  '....................................Fee:'.$YahooOrder["Fee"]."<br>";
                }
                
        echo    '....................................Shiping:'.$YahooOrder["Shiping"]."<br>".
                '....................................Total:'.$YahooOrder["Total"]."<br>".
                'Total Points Score:'.$YahooOrder["Points"]."<br><br>Mail Order is success!<br><br><br>";
        }
        
        function func_RakutenMail($MailBody)
        {
                                $SplitWord=']]]@@@';
                                                        $search = array('[受注番号]','[日時]','[注文者]','[支払方法]','[ポイント利用方法]','[配送方法]','[配送日時指定]','[備考]','[ショップ名]','[送付先]','[商品]','合計','送料 ','消費税 ','小計');
                                                        $replace = array($SplitWord.'受注番号'.$SplitWord,$SplitWord.'日時'.$SplitWord,$SplitWord.'注文者'.$SplitWord,$SplitWord.'支払方法'.$SplitWord,$SplitWord.'ポイント利用方法'.$SplitWord,$SplitWord.'配送方法'.$SplitWord,$SplitWord.'配送日時指定'.$SplitWord,$SplitWord.'備考'.$SplitWord,$SplitWord.'ショップ名'.$SplitWord,$SplitWord.'送付先'.$SplitWord,$SplitWord.'商品'.$SplitWord,$SplitWord.'合計'.$SplitWord,$SplitWord.'送料'.$SplitWord,$SplitWord.'消費税'.$SplitWord,$SplitWord.'小計'.$SplitWord);
                                $Temp=str_replace( $search,$replace,$MailBody);
//print $Temp;
                                                        $ArrayTemp=preg_split ('/'.$SplitWord.'/',$Temp,-1,PREG_SPLIT_NO_EMPTY);
                                $RakutenOrder=null;
//print_r ($ArrayTemp);
                                for ($i = 1; $i < count($ArrayTemp); $i++) {
                                        switch (str_replace("\"","",$ArrayTemp[$i])) {
                                                case "受注番号":
                                                        $RakutenOrder["OrderNo"]=trim($ArrayTemp[$i+1]);
                                                        break;
                                                case "日時":
                                                        $RakutenOrder["OrderDate"]=trim($ArrayTemp[$i+1]);
                                                        break;
                                                case "注文者":
                                                        //改行区切り
                                                        $res=$ArrayTemp[$i+1];
                                                        $res=str_replace("</div>","<br>",str_replace("<div>","",str_replace("&nbsp;","",$res)));
                                                        $OrderData=preg_split('/<br>/',$res,-1,PREG_SPLIT_NO_EMPTY);
                                                        //注文者氏名の取得
                                                        $RakutenOrder["OrderName"]=trim(mb_substr($OrderData[0],0,strpos($OrderData[0],"(")));
                                                        if(preg_match('/\(.+?\)/', $OrderData[0], $OrderNameKana)=== 1){
                                                                 $RakutenOrder["OrderNameKana"]=trim(str_replace(")","",str_replace("(","",$OrderNameKana[0])));
                                                        } 
                                                        if(preg_match('/\〒.+?\s/', $OrderData[1], $OrderPost)=== 1){
                                                                $RakutenOrder["OrderPost"]=trim(str_replace("〒","",$OrderPost[0]));
                                                                $RakutenOrder["OrderAddress"]=trim(str_replace($OrderPost,"",$OrderData[1]));
                                                        }else {
                                                                $RakutenOrder["OrderAddress"]=trim($OrderData[1]);								
                                                        }
                                                        $RakutenOrder["OrderPhone"]=trim(str_replace("(TEL)","",$OrderData[2]));
                                                        break;
                                                case "支払方法":
                                                        $RakutenOrder["PaymentMethod"]=trim($ArrayTemp[$i+1]);
                                                        break;
                                                case "ポイント利用方法":
                                                        $RakutenOrder["PointUse"]=trim($ArrayTemp[$i+1]);
                                                        break;
                                                case "配送方法":
                                                        $RakutenOrder["DeliveryMethod"]=trim($ArrayTemp[$i+1]);
                                                        break;
                                                case "配送日時指定":
                                                        $RakutenOrder["DeliveryDate"]=trim($ArrayTemp[$i+1]);
                                                        break;
                                                case "備考":
                                                        $RakutenOrder["Remarks"]=trim($ArrayTemp[$i+1]);
                                                        break;
                                                case "ショップ名":
                                                        $RakutenOrder["ShopName"]=trim(str_replace("==========","",$ArrayTemp[$i+1]));
                                                        break;
                                                case "送付先":
                                                        //改行区切り
                                                        $res=$ArrayTemp[$i+1];
                                                        $res=str_replace("</div>","<br>",str_replace("<div>","",str_replace("&nbsp;","",$res)));
                                                        $ShipData=preg_split('/<br>/',$res,-1,PREG_SPLIT_NO_EMPTY);
                                                
                                                        //注文者氏名の取得
                                                        $RakutenOrder["ShipName"]=trim(mb_substr($ShipData[0],0,strpos($ShipData[0],"(")));
                                                        if(preg_match('/\(.+?\)/', $ShipData[0], $ShipNameKana)=== 1){
                                                                 $RakutenOrder["ShipNameKana"]=trim(str_replace(")","",str_replace("(","",$ShipNameKana[0])));
                                                        } 
                                                        if(preg_match('/\〒.+?\s/', $ShipData[1], $ShipPost)=== 1){
                                                                $RakutenOrder["ShipPost"]=trim(str_replace("〒","",$ShipPost[0]));
                                                                $RakutenOrder["ShipAddress"]=trim(str_replace($ShipPost[0],"",$ShipData[1]));
                                                        }else {
                                                                $RakutenOrder["ShipAddress"]=trim($ShipData[1]);								
                                                        }
                                                        $RakutenOrder["ShipPhone"]=trim(str_replace("(TEL)","",$ShipData[2]));
                                                        break;
                                                case "商品":
                                                        //商品情報を改行ごとに区切り、取得する
                                                                                                        $Itemsearch = array('----------','価格 ','獲得ポイント','**********');
                                                                                                        $Itemreplace = array($SplitWord.'商品名'.$SplitWord,$SplitWord.'価格'.$SplitWord,$SplitWord.'獲得ポイント'.$SplitWord,'');
                                                                                                        $ItemTemp=str_replace( $Itemsearch,$Itemreplace,$SplitWord.'商品名'.$SplitWord.$ArrayTemp[$i+1]);
                                                        $ItemData=preg_split ('/'.$SplitWord.'/',$ItemTemp);
                                                        $ItemNoCnt=0;

                                                        for ($ii = 0; $ii < count($ItemData); $ii++) {

                                                                                                                switch($ItemData[$ii]){
                                                                                                                case "商品名":
                                                                                                                                $ItemNoCnt = $ItemNoCnt + 1;
                                                                                                                                $res=$ItemData[$ii+1];
                                                                                                                                $res=str_replace("</div>","<br>",str_replace("<div>","",str_replace("&nbsp;","",$res)));
                                                                                                                                $ItemName=preg_split ('/<br>/',$res);
                                                                                                                                $RakutenOrder["ItemName"][$ItemNoCnt]=trim(mb_substr($ItemName[1],0,strpos($ItemName[1],"(")));
                                                                                                                                $RakutenOrder["ItemNo"][$ItemNoCnt]=trim(str_replace(")","",str_replace("(","",strrchr($ItemName[1],"("))));
                                                                                                                                 if (array_key_exists(2, $ItemName)){
                                                                                                                                        $RakutenOrder["ItemSpec"][$ItemNoCnt]=trim($ItemName[2]);
                                                                                                                                }
//                                                                                                                                for ($SpecPos = 2; $SpecPos < count($ItemName); $SpecPos++) { 
//                                                                                                                                        $RakutenOrder["ItemSpec"][$ItemNoCnt]=$RakutenOrder["ItemSpec"][$ItemNoCnt].trim($ItemName[$SpecPos]);
//                                                                                                                                }
                                                                                                                                break;
                                                                                                                case "価格":
                                                                                                                                $Price=trim(str_replace("価格","",$ItemData[$ii+1]));
                                                                                                                                $RakutenOrder["ItemPrice"][$ItemNoCnt]=trim(str_replace(",","",mb_substr($Price,0,strpos($Price,"("))));
                                                                                                                                $RakutenOrder["ItemQuantity"][$ItemNoCnt]=trim(str_replace("(","",str_replace("x","",preg_match("/x .\(/",$Price))));
                                                                                                                                break;
                                                                                                                case "獲得ポイント":
                                                                                                                                $RakutenOrder["PointsScored"][$ItemNoCnt]=trim(str_replace('*','',$ItemData[$ii+1]));							

                                                                                                                                break;
                                                                                                                }

                                                        }												
                                                        break;
                                                case "小計":
                                                        $RakutenOrder["Subtotal"]=trim(str_replace(",","",mb_substr($ArrayTemp[$i+1],0,strpos($ArrayTemp[$i+1],"("))));
                                                        break;
                                                case "消費税":
                                                        $RakutenOrder["Tax"]=trim(str_replace(",","",mb_substr($ArrayTemp[$i+1],0,strpos($ArrayTemp[$i+1],"("))));
                                                        break;
                                                case "送料":
                                                        $RakutenOrder["Postage"]=trim(str_replace(",","",mb_substr($ArrayTemp[$i+1],0,strpos($ArrayTemp[$i+1],"("))));
                                                        break;
                                                case "合計":
                                                        $RakutenOrder["Total"]=trim(str_replace(",","",mb_substr($ArrayTemp[$i+1],0,strpos($ArrayTemp[$i+1],"("))));
                                                        break;
                                                default:
                                        }
//                                      print ($ArrayTemp[$i]);
                                }
                                //SQL作成
 print_r ($RakutenOrder);
//                return $RakutenOrder;
                              
        }
        
        function func_F($MailBody)
        {
            $Temp=str_replace("：","<br>",str_replace("</div>","<br>",str_replace("<div>","",$MailBody)));
            $ArrayTemp=preg_split('/<br>/',$Temp,-1,PREG_SPLIT_NO_EMPTY);
            $FutureOrder=null;
            $ItemNoCnt=0;
            
            for ($i = 0; $i < count($ArrayTemp); $i++) {
                switch (str_replace("<div dir=\"ltr\">","",trim($ArrayTemp[$i]))) {               
                    case "注文コード": 
                        $FutureOrder["OrderNo"]=trim($ArrayTemp[$i+1]);
                        break;
                    case "注文日時": 
                        $FutureOrder["OrderDate"]=trim(str_replace("分",":",str_replace("時",":",str_replace("秒","",str_replace("日","",str_replace("年","/",str_replace("月","/",$ArrayTemp[$i+1])))))));
                        break;
                    case "氏名": 
                        $FutureOrder["OrderName"]=trim($ArrayTemp[$i+1]);
                        break;
                    case "氏名（フリガナ）": 
                        $FutureOrder["OrderNameKana"]=trim($ArrayTemp[$i+1]);
                        break;
                    case "郵便番号": 
                        $FutureOrder["OrderPost"]=trim($ArrayTemp[$i+1]);
                        break;
                    case "住所": 
                        $FutureOrder["OrderAddress"]=trim($ArrayTemp[$i+1]);
                        break;
                    case "電話番号": 
                        $FutureOrder["OrderPhone"]=trim($ArrayTemp[$i+1]);
                        break;
                    case "Ｅメールアドレス": 
                        $FutureOrder["OrderMail"]=trim($ArrayTemp[$i+1]);
                        break;
                    case "会社名": 
                        break;
                    case "会社名（フリガナ）": 
                        break;
                    case "部署名": 
                        break;
                    case "部署名（フリガナ）": 
                        break;
                    case "会社電話番号": 
                        break;
                    case "会社ファックス番号": 
                        break;
                    case "支払方法": 
                        $FutureOrder["PaymentMethod"]=trim($ArrayTemp[$i+1]);
                        break;
                    case "商品番号": 
                        $ItemNoCnt=$ItemNoCnt+1;
                        $FutureOrder["ItemNo"][$ItemNoCnt]=trim($ArrayTemp[$i+1]);
                        break;
                    case "注文商品名": 
                        $Rec=preg_match("/【[0-9][0-9]】/",$ArrayTemp[$i+1],$DataTmp);
                        $FutureOrder["ItemName"][$ItemNoCnt]=trim(mb_substr($ArrayTemp[$i+1],0,strpos($ArrayTemp[$i+1],$DataTmp[0])));
                        $FutureOrder["ItemSpec"][$ItemNoCnt]=trim($DataTmp[0].str_replace("】","",strrchr($ArrayTemp[$i+1],"【")));
                        break;
                    case "単価": 
                        $FutureOrder["ItemPrice"][$ItemNoCnt]=trim(str_replace(",","",str_replace("￥","",$ArrayTemp[$i+1])));
                        break;
                    case "数量": 
                        $FutureOrder["ItemQuantity"][$ItemNoCnt]=trim($ArrayTemp[$i+1]);
                        break;
                    case "小計": 
                        $FutureOrder["Subtotal"][$ItemNoCnt]=trim(str_replace(",","",str_replace("￥","",$ArrayTemp[$i+1])));
                        break;
                    case "商品合計": 
                        break;
                    case "送料": 
                        $FutureOrder["Postage"]=trim(str_replace(",","",str_replace("￥","",$ArrayTemp[$i+1])));
                        break;
                    case "クール便送料": 
                        break;
                    case "決済手数料": 
                        break;
                    case "包装手数料": 
                        break;
                    case "ポイント利用額":  //mb_substr($ItemData[$ii],0,strpos($ItemData[$ii],"("))
                        $DataTmp=trim(str_replace("▲￥","",$ArrayTemp[$i+1]));
                        $FutureOrder["PointUse"]=trim(mb_substr($DataTmp,0,strpos($DataTmp,"(")));
                        break;
                    case "合計金額(税込)": 
                        $FutureOrder["Total"]=trim(str_replace(",","",str_replace("￥","",$ArrayTemp[$i+1])));
                        break;
                    case "　送付先1氏名": 
                        $FutureOrder["ShipName"]=trim($ArrayTemp[$i+1]);
                        break;
                    case "　送付先1氏名（フリガナ）": 
                        $FutureOrder["ShipNameKana"]=trim($ArrayTemp[$i+1]);
                        break;
                    case "　送付先1郵便番号": 
                        $FutureOrder["ShipPost"]=trim($ArrayTemp[$i+1]);
                        break;
                    case "　送付先1住所": 
                        $FutureOrder["ShipAddress"]=trim($ArrayTemp[$i+1]);
                        break;
                    case "　送付先1電話番号": 
                        $FutureOrder["ShipPhone"]=trim($ArrayTemp[$i+1]);
                        break;
                    case "　送付先1のし・ギフト包装": 
                        break;
                    case "　送付先1お届け方法": 
                        break;
                    case "　送付先1お届け希望日": 
                        break;
                    case "　送付先1お届け希望時間": 
                        break;
                    case "　送付先1詳細指定事項欄": 
                        break;
                    case "アクセスユーザIPアドレス/機種名": 
                        break;
                    case "会員ID": 
                        break;
                }
            }
            
            print_r(  $FutureOrder);
        }
        
        function func_OtherMail($body)
        {


                    $Temp=str_replace("：","\n",str_replace("</div>","\n",str_replace("<div>","",$body)));
                    $ArrayTemp=preg_split('/\n/',$Temp,-1,PREG_SPLIT_NO_EMPTY);

                $FutureOrder=null;
                $ItemNoCnt=0;
                 // print_r ($ArrayTemp);
                  for ($i = 0; $i <count($ArrayTemp); $i++) {
                //   switch (str_replace("\"","",trim($ArrayTemp[$i]))) {
                 if ( preg_match('/\[[A-Z]{5}\_[A-Z]{2}\]/', $ArrayTemp[$i],$OrderNo)===1)
                 {
                    $OrderNoNumber= preg_replace('/\[[A-Z]{5}\_[A-Z]{2}\]/',"",$ArrayTemp[$i]);  
                   $OtherMailOrder["OrderNo"]=$OrderNoNumber;
                 }

                 if ( preg_match('/\([a-z0-9]{4,20}-[a-z0-9]{4,20}\)/', $ArrayTemp[$i],$Item)===1)
                 {
                     $ItemNoCnt+=1;
                    $OrderItemName= preg_replace('/\[[A-Z]{4}\]/',"", preg_replace('/\([a-z0-9]{4,20}-[a-z0-9]{4,20}\)/',"",$ArrayTemp[$i]));
                    $OrderItemNumber= preg_replace('/\[[A-Z]{4}\]/',"", preg_replace('/[A-Za-z]{4,50}_[A-Za-z]{4,50}/',"",$ArrayTemp[$i]));
                   $OtherMailOrder["OrderName"][$ItemNoCnt]=$OrderItemName;
                   $OtherMailOrder["OrderNumber"][$ItemNoCnt]=$OrderItemNumber;
                 }

                 if (preg_match('/\[[0-9]{2}\].\s/',$ArrayTemp[$i],$ItemSpec)===1)
                 {
                     $OtherMailOrder["OrderSpec"][$ItemNoCnt]=$ItemSpec[0];

                }

                if (preg_match('/[A-Z]{5}:/', $ArrayTemp[$i],$Price)===1)
                {
                    $tempPrice=  preg_replace('/[A-Z]{5}:/',"",  preg_replace('/\sx\s.+?\(.+?\)/',"",  preg_replace('/=\s.+?\(.+?\)/',"",  preg_replace('/\s\(.+?\)/',"",$ArrayTemp[$i]))));
                     $OtherMailOrder["ItemPrice"][$ItemNoCnt]=  str_replace(',','', $tempPrice);

                    if (preg_match('/\sx\s.+?\(.+?\)/',$ArrayTemp[$i],$itemQuality)===1)
                    {

                        $OtherMailOrder["ItemQuality"][$ItemNoCnt]=  str_replace('x','',  str_replace('(Pcs)','',$itemQuality[0]));
                    }

                     if (preg_match('/=\s.+?\(.+?\)/',$ArrayTemp[$i],$itemSubtotal)===1)
                    {

                        $OtherMailOrder["ItemSubtotal"][$ItemNoCnt]=  str_replace(',','',  str_replace('(yen)','', str_replace('=','',$itemSubtotal[0])));
                    }


                }

             }
             print_r($OtherMailOrder);
    //         foreach ($OtherMailOrder as $value) {
    //             echo $value ;
    //             
    //         }
        }
        ?>
    </body>
</html>
