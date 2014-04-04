<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
<?php
/*
	$pop3user = "mil_18"; //pop3ユーザー
	$pop3pass = "kpSF7NLS"; //パスワード
	$pop3server = "mail.atomicgolf.jp"; //pop3サー�?ー
 */

$dsn = array(
	'url'  => 'mysql11.sixcore.ne.jp',
	'user' => 'realmaxsys_php',
	'pass' => 'realmax562392',
	'db'   => 'realmaxsys_test'
);
        $link = mysql_connect($dsn['url'],$dsn['user'],$dsn['pass']);
        $sdb = mysql_select_db($dsn['db'],$link);
        mysql_query("set names utf8");
        
	$pop3user = "orders@realmax.co.jp"; //pop3ユーザー
	$pop3pass = "f7TASNSlpe1ENgLk"; //パスワード
	$pop3server = "realmaxmp.sixcore.jp"; //pop3サー�?ー

        //Debug

        //Debug

	// POP3サー�?接続
	$mbox = @imap_open("{".$pop3server.":110/pop3/notls}INBOX",$pop3user, $pop3pass);
	// メール�?�得
	if ($mbox == false) {
		echo 'メール�?�得�?�失敗�?��?��?��?�。(POP3サー�?�?��?�接続�?�失敗) ';
		print_r(imap_errors());
	} else {
		// 新�?�メッセージを�?�得
		$mboxes = imap_mailboxmsginfo($mbox);
		$mail_cnt = $mboxes->Unread;
//		$mail=null;
                $sqlOrder="REPLACE INTO Orders (OrderNo,OrderDate,OrderName,OrderNameKana,OrderPost,OrderAddress,OrderPhone,OrderMail,PaymentMethod,PointUse,DeliveryMethod,Remarks,ShopName,ShipName,ShipNameKana,ShipPost,ShipAddress,ShipPhone,Subtotal,Tax,Postage,Fee,Total) VALUES ";
                $sqlItem="REPLACE INTO OrdersItem (OrderNo,ItemNo,ItemName,ItemSpec,PointsScored,ItemPrice,ItemQuantity) VALUES ";
                
                $LoopCnt=0;
		if ($mail_cnt > 0) {
			for ($i = 1; $i <= $mail_cnt; $i++) {
				// ヘッダ・ボディを格�?
				$head = imap_header($mbox, $i);
//				$fromdate= $head->date;
				$fromdate= date("Y/m/d H:i:s", $head->udate);
				$fromaddress=$head->fromaddress;
				$toaddress=$head->toaddress;
				$subject=mb_convert_encoding(mb_decode_mimeheader($head->subject), "UTF-8");

				$body=imap_fetchbody($mbox, $i, 2, FT_PEEK);
				$charset=imap_getcharset($mbox, $i);
				$Encoding=imap_getEncoding($mbox, $i);

				//メール文字列をUTF-8�?�変�?�
				if($Encoding != "quoted-printable") {
					$EBody=mb_convert_encoding($body, "UTF-8", $charset);
				} else {
					$EBody=quoted_printable_decode(mb_convert_encoding($body, "UTF-8", $charset));
				}
				switch (func_sellsite($mbox, $i)) {
                                    case 0:
                                        break;
                                    case 1:
                                        //本店
                                        $Orders=func_FutureMail($EBody);
                                        if($Orders["OrderNo"] && $Orders["OrderName"]){
                                            $LoopCnt=$LoopCnt+1;
                                            $sqlOrder.=func_ReplaceInsertOrders($Orders,$toaddress).",";
                                            $sqlItem.=func_ReplaceInsertItems($Orders);
                                        }                                        
                                        break;
                                    case 2:
                                        //Yahoo
                                        $Orders=func_YahooMail($EBody);
                                        $Orders["OrderDate"]=$fromdate;
                                        if($Orders["OrderNo"] && $Orders["Total"]){
                                            $LoopCnt=$LoopCnt+1;
                                            $sqlOrder.=func_ReplaceInsertOrders($Orders,$toaddress).",";
                                            $sqlItem.=func_ReplaceInsertItems($Orders);
                                        }
                                        break;
                                    case 3:
                                        //楽天
                                        $Orders=func_RakutenMail($EBody);
                                        if($Orders["OrderNo"] && $Orders["OrderName"]){
                                            $LoopCnt=$LoopCnt+1;
                                            $sqlOrder.=func_ReplaceInsertOrders($Orders,$toaddress).",";
                                            $sqlItem.=func_ReplaceInsertItems($Orders);
                                        }
                                        break;
				}
                                if($LoopCnt>30){
                                    $LoopCnt=0;

                                    $result = mysql_query(rtrim($sqlOrder,","), $link);
                                    mysql_free_result($result);
                                    $result = mysql_query(rtrim($sqlItem,","), $link);
                                    mysql_free_result($result);
                                    $sqlOrder="REPLACE INTO Orders (OrderNo,OrderDate,OrderName,OrderNameKana,OrderPost,OrderAddress,OrderPhone,OrderMail,PaymentMethod,PointUse,DeliveryMethod,Remarks,ShopName,ShipName,ShipNameKana,ShipPost,ShipAddress,ShipPhone,Subtotal,Tax,Postage,Fee,Total) VALUES ";
                                    $sqlItem="REPLACE INTO OrdersItem (OrderNo,ItemNo,ItemName,ItemSpec,PointsScored,ItemPrice,ItemQuantity) VALUES ";

                                }
			}
		} else {
			//デ�?ッグ
			$Messages ="新�?�メール�?��?�";
		}
		// 削除用�?�マーク�?�れ�?��?��?��?��?�メッセージを削除�?�る
		//imap_expunge($box);
		// POP3サー�?切断
		imap_close($mbox);
                mysql_close($link);
	}
        function func_ReplaceInsertItems($Orders){
            $Rec="";
            for ($ItemCnt = 1; $ItemCnt <= count($Orders["ItemNo"]); $ItemCnt++) {
                $Rec.=" (";
                $Rec.="'".M($Orders["OrderNo"])."',";
                $Rec.="'".M($Orders["ItemNo"][$ItemCnt])."',";
                $Rec.="'".M($Orders["ItemName"][$ItemCnt])."',";
                $Rec.="'".M($Orders["ItemSpec"][$ItemCnt])."',";
                $Rec.="'".M($Orders["PointsScored"][$ItemCnt])."',";
                $Rec.="'".M($Orders["ItemPrice"][$ItemCnt])."',";
                $Rec.="'".M($Orders["ItemQuantity"][$ItemCnt])."'),";
            }
            return $Rec;
        }
        
        function func_ReplaceInsertOrders($Orders, $mail){
//            $Sql="REPLACE INTO Orders (ItemNo,ItemName,ItemSpec,PointsScored,ItemPrice,ItemQuantity,
            
            $Rec=" (";

            $Rec.="'".M($Orders["OrderNo"])."',";
            $Rec.="'".M($Orders["OrderDate"])."',";
            $Rec.="'".M($Orders["OrderName"])."',";
            $Rec.="'".M($Orders["OrderNameKana"])."',";
            $Rec.="'".M($Orders["OrderPost"])."',";
            $Rec.="'".M($Orders["OrderAddress"])."',";
            $Rec.="'".M($Orders["OrderPhone"])."',";
            if($Orders["OrderMail"]){
                $Rec.="'".M($Orders["OrderMail"])."',";
            } else {
                $Rec.="'".M($mail)."',";                
            }
            $Rec.="'".M($Orders["PaymentMethod"])."',";
            $Rec.="'".M($Orders["PointUse"])."',";
            $Rec.="'".M($Orders["DeliveryMethod"])."',";
            $Rec.="'".M($Orders["Remarks"])."',";
            $Rec.="'".M($Orders["ShopName"])."',";
            $Rec.="'".M($Orders["ShipName"])."',";
            $Rec.="'".M($Orders["ShipNameKana"])."',";
            $Rec.="'".M($Orders["ShipPost"])."',";
            $Rec.="'".M($Orders["ShipAddress"])."',";
            $Rec.="'".M($Orders["ShipPhone"])."',";
            $Rec.="'".M(str_replace(",","",str_replace("￥","",$Orders["Subtotal"])))."',";
            $Rec.="'".M(str_replace(",","",str_replace("￥","",$Orders["Tax"])))."',";
            $Rec.="'".M(str_replace(",","",str_replace("￥","",$Orders["Postage"])))."',";
            $Rec.="'".M(str_replace(",","",str_replace("￥","",$Orders["Fee"])))."',";
            $Rec.="'".M(str_replace(",","",str_replace("￥","",$Orders["Total"])))."')";
            
            return $Rec;
        }
	//メールヘッダー文字コード�?�得
	function imap_getcharset($context, $number, $defcharset = "iso-2022-jp")
	{
		// Get charset
		$h = imap_fetchheader($context, $number);
		$mc = preg_match("/charsets*=s*(.+?)[;\n]/s", $h, $m);
		return $m[1] ? strtolower(trim($m[1])) : $defcharset;
	}

	//メールヘッダーエンコード�?�得
	function imap_getEncoding($context, $number, $defcharset = "quoted-printable")
	{
		// Get charset
		$h = imap_fetchheader($context, $number);
		$mc = preg_match("/Encodings*:s*(.+?)[;\n]/s", $h, $m);
		return $m[1] ? strtolower(trim($m[1])) : $defcharset;
	}

	//メール種別判定
	//http://okumocchi.jp/php/re.php
	function func_sellsite($context, $number)
	{	$h = imap_fetchheader($context, $number);

		//FutureShop
		if(1 == preg_match("/X-Mailers*:s*(.+?)[;\n]/s", $h, $m)) {
			if(strrpos($m[1],"FutureShop")) {
				return 1;
			}
		}
		//Yahooショッピング
		if(1 == preg_match("/Errors-Tos*:s*(.+?)[;\n]/s", $h, $m)) {
			if(strrpos($m[1],"serr.yahoo.co.jp")) {
				return 2;
			}
		}
		//楽天
		$head = imap_header($context, $number);
		if(strrpos($head->fromaddress,"rakuten.co.jp")) {
			return 3;
		}
		return 0;
	}
	function func_FutureMail($MailBody)
	{
            $Temp=str_replace("：","\n",$MailBody);
            $ArrayTemp=preg_split('/\n/',$Temp,-1,PREG_SPLIT_NO_EMPTY);
            $FutureOrder=null;
            $ItemNoCnt=0;
            
            for ($i = 0; $i <= count($ArrayTemp); $i++) {
                switch (str_replace("\"","",trim($ArrayTemp[$i]))) {
                    case "注文コード": 
                        $FutureOrder["OrderNo"]=trim($ArrayTemp[$i+1]);
                        break;
                    case "注文日時": 
                        $FutureOrder["OrderDate"]=trim(str_replace("分",":",str_replace("時",":",str_replace("秒","",str_replace("日","",str_replace("年","/",str_replace("月","/",$ArrayTemp[$i+1])))))));
                        break;
                    case "�?�??": 
                        $FutureOrder["OrderName"]=trim($ArrayTemp[$i+1]);
                        break;
                    case "�?�??（フリガナ）": 
                        $FutureOrder["OrderNameKana"]=trim($ArrayTemp[$i+1]);
                        break;
                    case "郵便番�?�": 
                        $FutureOrder["OrderPost"]=trim($ArrayTemp[$i+1]);
                        break;
                    case "�?所": 
                        $FutureOrder["OrderAddress"]=trim($ArrayTemp[$i+1]);
                        break;
                    case "電話番�?�": 
                        $FutureOrder["OrderPhone"]=trim($ArrayTemp[$i+1]);
                        break;
                    case "Ｅメールアドレス": 
                        $FutureOrder["OrderMail"]=trim($ArrayTemp[$i+1]);
                        break;
                    case "会社�??": 
                        break;
                    case "会社�??（フリガナ）": 
                        break;
                    case "部署�??": 
                        break;
                    case "部署�??（フリガナ）": 
                        break;
                    case "会社電話番�?�": 
                        break;
                    case "会社ファックス番�?�": 
                        break;
                    case "支払方法": 
                        $FutureOrder["PaymentMethod"]=trim($ArrayTemp[$i+1]);
                        break;
                    case "商�?番�?�": 
                        $ItemNoCnt=$ItemNoCnt+1;
                        $FutureOrder["ItemNo"][$ItemNoCnt]=trim($ArrayTemp[$i+1]);
                        break;
                    case "注文商�?�??": 
                        $Rec=preg_match("/�?[0-9][0-9]】/",$ArrayTemp[$i+1],$DataTmp);
                        $FutureOrder["ItemName"][$ItemNoCnt]=trim(mb_substr($ArrayTemp[$i+1],0,strpos($ArrayTemp[$i+1],$DataTmp[0])));
                        $FutureOrder["ItemSpec"][$ItemNoCnt]=trim($DataTmp[0].str_replace("】","",strrchr($ArrayTemp[$i+1],"�?")));
                        break;
                    case "�?�価": 
                        $FutureOrder["ItemPrice"][$ItemNoCnt]=trim(str_replace(",","",str_replace("￥","",$ArrayTemp[$i+1])));
                        break;
                    case "数�?": 
                        $FutureOrder["ItemQuantity"][$ItemNoCnt]=trim($ArrayTemp[$i+1]);
                        break;
                    case "�?計": 
                        $FutureOrder["Subtotal"][$ItemNoCnt]=trim(str_replace(",","",str_replace("￥","",$ArrayTemp[$i+1])));
                        break;
                    case "商�?�?�計": 
                        break;
                    case "�?料": 
                        $FutureOrder["Postage"]=trim(str_replace(",","",str_replace("￥","",$ArrayTemp[$i+1])));
                        break;
                    case "クール便�?料": 
                        break;
                    case "決済手数料": 
                        break;
                    case "包装手数料": 
                        break;
                    case "�?イント利用�?":  //mb_substr($ItemData[$ii],0,strpos($ItemData[$ii],"("))
                        $DataTmp=trim(str_replace("▲￥","",$ArrayTemp[$i+1]));
                        $FutureOrder["PointUse"]=trim(mb_substr($DataTmp,0,strpos($DataTmp,"(")));
                        break;
                    case "�?�計金�?(税込)": 
                        $FutureOrder["Total"]=trim(str_replace(",","",str_replace("￥","",$ArrayTemp[$i+1])));
                        break;
                    case "　�?付先1�?�??": 
                        $FutureOrder["ShipName"]=trim($ArrayTemp[$i+1]);
                        break;
                    case "　�?付先1�?�??（フリガナ）": 
                        $FutureOrder["ShipNameKana"]=trim($ArrayTemp[$i+1]);
                        break;
                    case "　�?付先1郵便番�?�": 
                        $FutureOrder["ShipPost"]=trim($ArrayTemp[$i+1]);
                        break;
                    case "　�?付先1�?所": 
                        $FutureOrder["ShipAddress"]=trim($ArrayTemp[$i+1]);
                        break;
                    case "　�?付先1電話番�?�": 
                        $FutureOrder["ShipPhone"]=trim($ArrayTemp[$i+1]);
                        break;
                    case "　�?付先1�?��?�・ギフト包装": 
                        break;
                    case "　�?付先1�?�届�?�方法": 
                        break;
                    case "　�?付先1�?�届�?�希望日": 
                        break;
                    case "　�?付先1�?�届�?�希望時間": 
                        break;
                    case "　�?付先1詳細指定事項欄": 
                        break;
                    case "アクセスユーザIPアドレス/機種�??": 
                        break;
                    case "会員ID": 
                        break;
                }
            }
            
            return  $FutureOrder;
        }
	function func_RakutenMail($MailBody)
	{
//                $Temp=str_replace("�?�計  ","]�?�計]",str_replace("�?料  ","]�?料]",str_replace("消費税  ","]消費税]",str_replace("�?計  ","]�?計]",str_replace("�?�得�?イント","]�?�得�?イント]",str_replace("価格  ","]価格]",str_replace("\n","",str_replace("[","]",$MailBody))))))));
                $Temp=str_replace("�?�計  ","]�?�計]",str_replace("�?料  ","]�?料]",str_replace("消費税  ","]消費税]",str_replace("�?計  ","]�?計]",str_replace("[","]",$MailBody)))));
                $ArrayTemp=preg_split ('/\]/',$Temp,-1,PREG_SPLIT_NO_EMPTY);
                $RakutenOrder=null;
        
                for ($i = 1; $i <= count($ArrayTemp); $i++) {
                    switch (str_replace("\"","",$ArrayTemp[$i])) {
                        case "�?�注番�?�":
                            $RakutenOrder["OrderNo"]=trim($ArrayTemp[$i+1]);
                            break;
                        case "日時":
                            $RakutenOrder["OrderDate"]=trim($ArrayTemp[$i+1]);
                            break;
                        case "注文者":
                            //改行区切り
                            $OrderData=preg_split('/\n/',$ArrayTemp[$i+1],-1,PREG_SPLIT_NO_EMPTY);
                            //注文者�?�??�?��?�得
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
                        case "�?イント利用方法":
                            $RakutenOrder["PointUse"]=trim($ArrayTemp[$i+1]);
                            break;
                        case "�?�?方法":
                            $RakutenOrder["DeliveryMethod"]=trim($ArrayTemp[$i+1]);
                            break;
                        case "�?�?日時指定":
                            $RakutenOrder["DeliveryDate"]=trim($ArrayTemp[$i+1]);
                            break;
                        case "備考":
                            $RakutenOrder["Remarks"]=trim($ArrayTemp[$i+1]);
                            break;
                        case "ショップ�??":
                            $RakutenOrder["ShopName"]=trim(str_replace("==========","",$ArrayTemp[$i+1]));
                            break;
                        case "�?付先":
                            //改行区切り
                            $ShipData=preg_split('/\n/',$ArrayTemp[$i+1],-1,PREG_SPLIT_NO_EMPTY);
                            //注文者�?�??�?��?�得
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
                        case "商�?":
                            //商�?情報を改行�?��?��?�区切り�?�?�得�?�る
                            $ItemData=preg_split ('/\n/',str_replace("****","",str_replace("----------","",$ArrayTemp[$i+1])),-1,PREG_SPLIT_NO_EMPTY);
                            $ItemNoCnt=0;
                            $Pos=0;
                            for ($ii = 0; $ii <= count($ItemData); $ii++) {
                                //データ�?�存在�?�る場�?�
                                if(strlen(trim($ItemData[$ii])) > 4) {
                                    $ItemNo=null;
/*                                    $Rec=Right($ItemData[$ii],1);
                                    $Rec=mb_substr($ItemData[$ii],-1,1,"utf-8");
                                    $Rec=preg_match('/\(個\)/', $ItemData[$ii], $tmp);
*/                                      
                                    $Pos=$Pos+1;
                                    //商�?番�?��?��?�得
                                    switch($Pos){
                                    case 1:
                                        $ItemNoCnt = $ItemNoCnt + 1;
                                        $RakutenOrder["ItemNo"][$ItemNoCnt]=trim(str_replace(")","",str_replace("(","",strrchr($ItemData[$ii],"("))));
                                        $RakutenOrder["ItemName"][$ItemNoCnt]=trim(mb_substr($ItemData[$ii],0,strpos($ItemData[$ii],"(")));
                                        break;
                                    case 2:
                                        $RakutenOrder["ItemSpec"][$ItemNoCnt]=trim($ItemData[$ii]);                                        
                                        break;
                                    case 3:
                                        $Price=trim(str_replace("価格","",$ItemData[$ii]));
                                        $RakutenOrder["ItemPrice"][$ItemNoCnt]=trim(mb_substr($Price,0,strpos($Price,"(")));
                                        $RakutenOrder["ItemQuantity"][$ItemNoCnt]=trim(str_replace("(","",str_replace("x","",preg_match("/x .\(/",$Price))));
                                        break;
                                    case 4:
                                        $Tmp=$ItemData[$ii];
                                        $RakutenOrder["PointsScored"][$ItemNoCnt]=trim(str_replace("�?�得�?イント","",$Tmp));                            
                                        $Pos=0;
                                        break;
                                    }
                                }
                            }
                            //$RakutenOrder["Item"]=trim($ArrayTemp[$i+1]);
                            break;
                        case "�?計":
                            $RakutenOrder["Subtotal"]=trim(str_replace(",","",mb_substr($ArrayTemp[$i+1],0,strpos($ArrayTemp[$i+1],"("))));
                            break;
                        case "消費税":
                            $RakutenOrder["Tax"]=trim(str_replace(",","",mb_substr($ArrayTemp[$i+1],0,strpos($ArrayTemp[$i+1],"("))));
                            break;
                        case "�?料":
                            $RakutenOrder["Postage"]=trim(str_replace(",","",mb_substr($ArrayTemp[$i+1],0,strpos($ArrayTemp[$i+1],"("))));
                            break;
                        case "�?�計":
                            $RakutenOrder["Total"]=trim(str_replace(",","",mb_substr($ArrayTemp[$i+1],0,strpos($ArrayTemp[$i+1],"("))));
                            break;
                        default:
                    }
                }
                //SQL作�?
                
		return $RakutenOrder;
	}      
        //yahoo�?�メール解�?
        function func_YahooMail($MailBody)
	{
            $Temp=str_replace("：","\n",$MailBody);
            $ArrayTemp=preg_split('/\n/',$Temp,-1,PREG_SPLIT_NO_EMPTY);
            $YahooOrder=null;
            $ItemNoCnt=0;
            //商�?情報一時�?管用
            $tmpItemName = "";
            $tmpItemQuantity = "";
            $tmpItemUnitPrice= "";
            $tmpItemOption= "";
            $rec = "";
            
            for ($i = 1; $i <= count($ArrayTemp); $i++) {
                $rec = $ArrayTemp[$i];
                switch (true) {
                    case (stristr($rec,"�?�注文番�?�") != false):
                        $YahooOrder["OrderNo"] = trim(str_replace("�?�注文番�?�","",$rec));
                        break;
                    case (stristr($rec,"�?�注文日") != false):
                        $YahooOrder["OrderDate"] = trim(str_replace("�?�注文日","",$rec));
                        break;
                    case (stristr($rec,"�?計") != false):
                        $YahooOrder["Subtotal"] = trim(str_replace("円","",
                            trim(str_replace("�?計","",$rec))));
                        break;
                    case (stristr($rec,"手数料") != false):
                        $YahooOrder["Fee"]= trim(str_replace("円","",
                            trim(str_replace("手数料","",$rec))));
                        break;
                    case (stristr($rec,"�?料") != false):
                        $YahooOrder["Postage"]= trim(str_replace("円","",
                            trim(str_replace("�?料","",$rec))));
                        break;
                    case (stristr($rec,"�?�得�?イント�?�計") != false):
                        $YahooOrder["Point"]= trim(str_replace("�?イント","",
                            trim(str_replace("�?�得�?イント�?�計","",$rec))));
                        break;
                    case (stristr($rec,"�?�計") != false):
                        $YahooOrder["Total"] = trim(str_replace("円","",
                            trim(str_replace("�?�計","",$rec))));
                        break;
                    case (stristr($rec,"商�?�??") != false):
                    case (stristr($rec,"---------------------------------------------------------------------") != false):
                        //空行�?ヘッダ行　�?�場�?��?�何も�?��?��?�。
                        break;
                    
                    default :
                        
                        //空�?�ら�?�処�?�抜�?�
                        if (strcmp(trim($rec),"") == 0)
                        {
                            break;
                        }
                        //�??�?�他
                        //商�?一覧内
                        //商�?番�?��?��?�得
                        //商�?番�?��?�正�?表�?�
                        if( preg_match('/([a-z]?\\d(\\d|[a-z]){6,7}-?(\\d|[a-z]){4,6})|[a-z]?\\d(\\d|[A-Za-z]){8,10}/', $ArrayTemp[$i],$tempItemNo)=== 1) {
                            $ItemNoCnt = $ItemNoCnt + 1;
                            $YahooOrder["ItemNo"][$ItemNoCnt] = $tempItemNo[0];
                            $YahooOrder["ItemName"][$ItemNoCnt] = "";
                        }
                        
                        //商�?�??
                        //行頭スペース以外�?�文字複数�?�らスペース�?��?�
                        if( preg_match('/^\\S*\\s/', $rec ,$tmpItemName) === 1) {
                            //商�?�??を組�?��?��?�?�る
                            $YahooOrder["ItemName"][$ItemNoCnt] = $YahooOrder["ItemName"][$ItemNoCnt].$tmpItemName[0];
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
                        
                        break;
                }
            }
            
            return  $YahooOrder;
        }
function Right($str,$n){
	return mb_substr($str,($n)*(-1),$n,"UTF-8");
}


function Left($str,$n){
	return mb_substr($str,0,$n,"UTF-8");
}
function M($str){
    return mysql_real_escape_string($str);
}

?>
    </body>
</html>
