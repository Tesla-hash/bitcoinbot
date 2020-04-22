<?php

/** модель работы с пользователями **/
//регистрация пользователя

use BitcoinPHP\BitcoinECDSA\BitcoinECDSA;


function refresh($cid){
global $db;
$cid = mysql_real_escape_string($cid);
$address = mysql_fetch_row(mysql_query("select adress from `users` where chat_id='$cid' LIMIT 1",$db))[0];

$total_received_withoutconfirm = intval(json_decode(file_get_contents("https://blockchain.info/q/addressbalance/".$address), true));
$total_received_withoutconfirm_usd = ceil(floatval((str_replace(",",".",json_decode(file_get_contents('https://blockchain.info/frombtc?currency=USD&value='.$total_received), true)))));
$frozen_balance = mysql_fetch_row(mysql_query("select frozenbalance from `users` where chat_id='$cid' LIMIT 1",$db))[0];
$frozen_satoshi = mysql_fetch_row(mysql_query("select frozensatoshi from `users` where chat_id='$cid' LIMIT 1",$db))[0];
$total_received = intval(json_decode(file_get_contents("https://blockchain.info/q/addressbalance/".$address."?confirmations=1"), true));
$total_received_usd = ceil(floatval((str_replace(",",".",json_decode(file_get_contents('https://blockchain.info/frombtc?currency=USD&value='.$total_received_withoutconfirm), true)))));
$user_balance_satoshi = mysql_fetch_row(mysql_query("select total from `users` where chat_id='$cid' LIMIT 1",$db))[0];
$intnull = 0;
$total_sent = intval(json_decode(file_get_contents("https://blockchain.info/q/getsentbyaddress/".$address), true));
$total_sent_database = mysql_fetch_row(mysql_query("select sentamount from `users` where chat_id='$cid' LIMIT 1",$db))[0];
$user_balance = mysql_fetch_row(mysql_query("select balance from `users` where chat_id='$cid' LIMIT 1",$db))[0];


if(($total_sent>0)&&($total_sent>$total_sent_database)){
    mysql_query("update `users` SET sentamount = '{$total_sent}' WHERE chat_id = '{$cid}' LIMIT 1",$db);
    mysql_query("update `users` SET total = '{$intnull}' WHERE chat_id = '{$cid}' LIMIT 1",$db);
    mysql_query("update `users` SET frozenbalance = '{$intnull}' WHERE chat_id = '{$cid}' LIMIT 1",$db);
    mysql_query("update `users` SET frozensatoshi = '{$intnull}' WHERE chat_id = '{$cid}' LIMIT 1",$db);
    if($total_received>$user_balance_satoshi){
        mysql_query("update `users` SET total = '{$total_received}' WHERE chat_id = '{$cid}' LIMIT 1",$db);
        $new_balance_in_satoshi = $total_received - $user_balance_satoshi;
        $new_balance_in_usd = ceil(floatval((str_replace(",",".",json_decode(file_get_contents('https://blockchain.info/frombtc?currency=USD&value='.$new_balance_in_satoshi), true)))));
        $new_balance_usd = $user_balance+$new_balance_in_usd;
        mysql_query("update `users` SET balance = '{$new_balance_usd}' WHERE chat_id = '{$cid}' LIMIT 1",$db);
        mysql_query("update `users` SET frozenbalance = '{$intnull}' WHERE chat_id = '{$cid}' LIMIT 1",$db);
        return 1;
    }
    if($total_received_withoutconfirm>$frozen_satoshi){
            mysql_query("update `users` SET frozensatoshi = '{$total_received_withoutconfirm}' WHERE chat_id = '{$cid}' LIMIT 1",$db);
            $new_frozen_balance_in_satoshi = $total_received_withoutconfirm-$frozen_satoshi;
            $new_frozen_balance_in_usd = ceil(floatval((str_replace(",",".",json_decode(file_get_contents('https://blockchain.info/frombtc?currency=USD&value='.$new_frozen_balance_in_satoshi), true)))));
            $new_frozen_balance = $frozen_balance + $new_frozen_balance_in_usd;
            mysql_query("update `users` SET frozenbalance = '{$new_frozen_balance}' WHERE chat_id = '{$cid}' LIMIT 1",$db);
        return 2;
    }
}
else{
    if($total_received>$user_balance_satoshi){
        mysql_query("update `users` SET total = '{$total_received}' WHERE chat_id = '{$cid}' LIMIT 1",$db);
        $new_balance_in_satoshi = $total_received-$user_balance_satoshi;
        $new_balance_in_usd = ceil(floatval((str_replace(",",".",json_decode(file_get_contents('https://blockchain.info/frombtc?currency=USD&value='.$new_balance_in_satoshi), true)))));
        $new_balance_usd = $user_balance+$new_balance_in_usd;
        mysql_query("update `users` SET balance = '{$new_balance_usd}' WHERE chat_id = '{$cid}' LIMIT 1",$db);
        mysql_query("update `users` SET frozenbalance = '{$intnull}' WHERE chat_id = '{$cid}' LIMIT 1",$db);
        return 1;
    }
    if($total_received_withoutconfirm>$frozen_satoshi){
            mysql_query("update `users` SET frozensatoshi = '{$total_received_withoutconfirm}' WHERE chat_id = '{$cid}' LIMIT 1",$db);
            $new_frozen_balance_in_satoshi = $total_received_withoutconfirm-$frozen_satoshi;
            $new_frozen_balance_in_usd = ceil(floatval((str_replace(",",".",json_decode(file_get_contents('https://blockchain.info/frombtc?currency=USD&value='.$new_frozen_balance_in_satoshi), true)))));
            $new_frozen_balance = $frozen_balance + $new_frozen_balance_in_usd;
            mysql_query("update `users` SET frozenbalance = '{$new_frozen_balance}' WHERE chat_id = '{$cid}' LIMIT 1",$db);
            return 2;
    }
}
}
function getfrozenbalance($cid){
    global $db;
    $cid = mysql_real_escape_string($cid);
    $frozen_balance = mysql_fetch_row(mysql_query("select frozenbalance from `users` where chat_id='$cid' LIMIT 1",$db))[0];
    return $frozen_balance;
}
function make_user($name,$chat_id,$password){
	global $db;
	$name = mysql_real_escape_string($name);
	$chat_id = mysql_real_escape_string($chat_id);
	$password = mysql_real_escape_string($password);
	$bitcoinECDSA = new BitcoinECDSA();
    $bitcoinECDSA->generateRandomPrivateKey(); //generate new random private key
    $key = $bitcoinECDSA->getPrivateKey();
    $address = $bitcoinECDSA->getAddress(); 
    $adres = mysql_real_escape_string($address);
    $newlabel = mysql_real_escape_string($key);
	mysql_query("insert into `users`(chat_id,login,password,adress,label) values('{$chat_id}','{$name}','{$password}','{$adres}','{$newlabel}')",$db);


}

function is_user_set($login,$password){
	global $db;
	$login = mysql_real_escape_string($login);
	$password = mysql_real_escape_string($password);
	$result = mysql_query("select * from `users` where login='$login' and password = '$password' LIMIT 1",$db);
    if(mysql_fetch_array($result) !== false) return true;
    return false;
}

function get_deposit($cid){
	global $db;
	$cid = mysql_real_escape_string($cid);
	$result = mysql_query("select adress from `users` where chat_id ='$cid'",$db);
	$arr = mysql_fetch_row($result);
	$res = $arr[0];
	return array($res);
	
}

function user_exists($name){
    global $db;
	$login = mysql_real_escape_string($name);
	$result = mysql_query("select * from `users` where login='$login' LIMIT 1",$db);
    if(mysql_fetch_array($result) !== false) return true;
    return false;
}

function getusername($cid){
    global $db;
	$id = mysql_real_escape_string($cid);
	$result = mysql_query("select login from `users` where chat_id='$id' LIMIT 1",$db);
    $arrs = mysql_fetch_row($result);
	$ress = $arrs[0];
	return $ress;
    
}
function id_exists($cid)
{
    global $db;
	$id = mysql_real_escape_string($cid);
	$result = mysql_query("select chat_id from `users` where chat_id='$id' LIMIT 1",$db);
	if(mysql_fetch_array($result) == false){
	    return false;
	}else {
	    return true;
}
}
function id_existsglobal($cid)
{
    global $db;
	$id = mysql_real_escape_string($cid);
	$result = mysql_query("select chat_id from `userslang` where chat_id='$id' LIMIT 1",$db);
    if(mysql_fetch_array($result) == false){
	    return false;
	}else {
	    return true;
	    
	}
}
function id_existslang($cid){
    global $db;
	$id = mysql_real_escape_string($cid);
	$result = mysql_query("select lang from `users` where chat_id='$id' LIMIT 1",$db);
	$arrs = mysql_fetch_row($result);
	$ress =  $arrs[0];
    if($ress !== ''){
        if(id_exists($cid) == false){
            return false;
        }
        
    }

}

function id_change($cid,$login){
    global $db;
	$id = mysql_real_escape_string($cid);
	$loginname = mysql_real_escape_string($login);
	
	$result = mysql_query("select * from `users` where chat_id='$id' and login='$loginname' LIMIT 1",$db);
	if(mysql_fetch_array($result) == false){
	    
	    mysql_query("update `users` SET chat_id = '{$id}' WHERE login = '{$loginname}'",$db);
	    
	    return true;
	}
    
}

function getgoodname($idshop){
    global $db;
    $idsho = mysql_real_escape_string($idshop);
    $results = mysql_query("SELECT name FROM `shopone` WHERE `callback_data`='$idsho'",$db);
	$arrs = mysql_fetch_row($results);
	$ress = $arrs[0];
	return $ress;
    
}
function check_buy($cid,$idshop){
    global $db;
    $idsho = mysql_real_escape_string($idshop);
    $results = mysql_query("SELECT price FROM `shopone` WHERE `callback_data`='$idsho'",$db);
	$arrs = mysql_fetch_row($results);
	$ress = $arrs[0];
    $id = mysql_real_escape_string($cid);
    $result = mysql_query("select balance from `users` where chat_id ='$cid'",$db);
	$arr = mysql_fetch_row($result);
	$balance = $arr[0];
	
    if($balance<$ress){
    return false;
    }
    else{
    $newbalance = $balance - $ress;
    mysql_query("update `users` SET balance ='$newbalance' WHERE chat_id = '{$cid}'",$db);
    $oldamount = mysql_query("SELECT amount FROM `shopone` WHERE `callback_data`='$idsho'",$db);
    $arramount = mysql_fetch_row($oldamount);
    $mainamount = $arramount[0]-1;
    mysql_query("update `shopone` SET amount = '{$mainamount}' WHERE `callback_data` = '{$idsho}'",$db);
    //$newamount = ol
    $resultlogin = mysql_query("select login from `users` where chat_id ='$cid'",$db);
	$arrlogin = mysql_fetch_row($resultlogin);
	$buyer = $arrlogin[0];
	$resultaddress = mysql_query("select adress from `users` where chat_id ='$cid'",$db);
	$arraddress = mysql_fetch_row($resultaddress);
	$buyeraddress = $arraddress[0];
    $resultgood = mysql_query("SELECT name FROM `shopone` WHERE `callback_data`='$idsho'",$db);
	$arrgood = mysql_fetch_row($resultgood);
	$good = $arrgood[0];
	$query = "insert into `orders`(buyer,byueraddress,good,sum) values('{$buyer}','{$buyeraddress}','{$good}','{$ress}')";
    mysql_query($query,$db) or die("пользователя создать не удалось");
    
    return true;
    }
    }
    
function logout($cid){
    global $db;
    $idsho = mysql_real_escape_string($cid);
    $result = mysql_query("select lang from `users` where chat_id ='{$idsho}'",$db);
      $arr = mysql_fetch_row($result);
	$res = $arr[0];
    mysql_query("update `users` SET chat_id = 0 WHERE chat_id = '{$idsho}'",$db);
    return true;
}

function lang($chatid,$lang){
	global $db;
	$lang = mysql_real_escape_string($lang);
	$chat_id = mysql_real_escape_string($chatid);
	$query = "insert into `userslang`(chat_id,lang) values('{$chat_id}','{$lang}')";
	mysql_query($query,$db);
	
}

function taketext($chatid,$title){
    global $db;
    $cid = mysql_real_escape_string($chatid);
    $titlebase = mysql_real_escape_string($title);
    $result = mysql_query("select lang from `userslang` where chat_id ='$cid'",$db);
	$arr = mysql_fetch_row($result);
	$res = $arr[0];
	$titleresult = mysql_query("select `$res` from `languages` where type='$titlebase'",$db);
    $arrtitle = mysql_fetch_row($titleresult);
    $newres = $arrtitle[0];
    return $newres;
}

function changelang($chatid,$lang){
    global $db;
    $cid = mysql_real_escape_string($chatid);
	$lang = mysql_real_escape_string($lang);
	mysql_query("update `userslang` SET lang = '{$lang}' WHERE chat_id = '{$cid}'",$db);
    
}

function shopname($shopid)
{
    global $db;
    $shopid = mysql_real_escape_string($shopid);
    $result = mysql_query("select name from `shops` where id ='$shopid'",$db);
    $arr = mysql_fetch_row($result);
	$res = $arr[0];
    return $res;

}

function goodone($goodid){
     global $db;
     $good = mysql_real_escape_string($goodid);
     $result = mysql_query("select callback_data,name,price,amount from `shopone` where id ='$good'",$db);
     $arr = mysql_fetch_row($result);
     //$res = $arr[3];
     return array($arr[0],$arr[1],$arr[2],$arr[3]);

    
}

function goodtwo($goodid){
     global $db;
     $good = mysql_real_escape_string($goodid);
     $result = mysql_query("select callback_data,name,price,amount from `shoptwo` where id ='$good'",$db);
     $arr = mysql_fetch_row($result);
     //$res = $arr[3];
     return array($arr[0],$arr[1],$arr[2],$arr[3]);

    
}

function goodthree($goodid){
     global $db;
     $good = mysql_real_escape_string($goodid);
     $result = mysql_query("select callback_data,name,price,amount from `shopthree` where id ='$good'",$db);
     $arr = mysql_fetch_row($result);
     //$res = $arr[3];
     return array($arr[0],$arr[1],$arr[2],$arr[3]);

    
}

function goodfour($goodid){
     global $db;
     $good = mysql_real_escape_string($goodid);
     $result = mysql_query("select callback_data,name,price,amount from `shopfour` where id ='$good'",$db);
     $arr = mysql_fetch_row($result);
     //$res = $arr[3];
     return array($arr[0],$arr[1],$arr[2],$arr[3]);

    
}

function goodfive($goodid){
     global $db;
     $good = mysql_real_escape_string($goodid);
     $result = mysql_query("select callback_data,name,price,amount from `shopfive` where id ='$good'",$db);
     $arr = mysql_fetch_row($result);
     //$res = $arr[3];
     return array($arr[0],$arr[1],$arr[2],$arr[3]);

    
}

function goodsix($goodid){
     global $db;
     $good = mysql_real_escape_string($goodid);
     $result = mysql_query("select callback_data,name,price,amount from `shopsix` where id ='$good'",$db);
     $arr = mysql_fetch_row($result);
     //$res = $arr[3];
     return array($arr[0],$arr[1],$arr[2],$arr[3]);

    
}

function newbalance($cid){
    global $db;
    $id = mysql_real_escape_string($cid);
    $result = mysql_query("select balance from `users` where chat_id ='$cid'",$db);
	$arr = mysql_fetch_row($result);
	$balance = $arr[0];
	return $balance;
}

function getbutton($idb){
    global $db;
    $id = mysql_real_escape_string($idb);
    $result = mysql_query("select name from `buttons` where id ='$id'",$db);
	$arr = mysql_fetch_row($result);
	$balance = $arr[0];
	return $balance;

}

function check_amount($amount_id){
    global $db;
    $idsho = mysql_real_escape_string($amount_id);
    $results = mysql_query("SELECT amount FROM `shopone` WHERE `callback_data`='$idsho'",$db);
    $arrs = mysql_fetch_row($results);
	$ress = $arrs[0];
    if($ress == '0'){
        return false;
    }else
    {
        return true;
        
    }
    
}

function getgoodpic($path){
    $time = time();
    $dir = 'asset/pic/'.$path.'/';
    $selldir = 'asset/pic/sell/';
    $result = array(); 
    $cdir = scandir($dir); 
   
    foreach ($cdir as $key => $value) 
       { 
          if (!in_array($value,array(".",".."))) 
          { 
             if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) 
             { 
                $result[$value] = dirToArray($dir . DIRECTORY_SEPARATOR . $value); 
             } 
             else 
             { 
                $result[] = $value; 
             } 
          } 
       } 
   $max = count($result);
   $rand = rand(0,$max-1);
   $path = $result[$rand];
   
   $file = $dir.$path;
   $newfile = $selldir.$time.$path;
   rename($file,$newfile);
   $finalpath = 'https://botforbtc123.ru/bitcoinbot/asset/pic/sell/'.$time.$path;
   return $finalpath;
    
    
}





