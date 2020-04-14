<?php

/** модель работы с пользователями **/
//регистрация пользователя

function make_user($name,$chat_id,$password){
	global $db;
	$name = mysql_real_escape_string($name);
	$chat_id = mysql_real_escape_string($chat_id);
	$password = mysql_real_escape_string($password);
	$apiKey = '8051-6d6e-8751-6e00';
    $pin = '12345678';
    $version = 2; // the API version
    $block_io = new BlockIo($apiKey, $pin, $version);
    $getNewAddressInfo = $block_io->get_new_address();
    $address = $getNewAddressInfo->data->address;
    $label = $getNewAddressInfo->data->label;
    $adres = mysql_real_escape_string($address);
    $newlabel = mysql_real_escape_string($label);
	mysql_query("update `users` SET login ='$name', password ='$password', adress ='$adres', label ='$newlabel' WHERE chat_id = '{$chat_id}'",$db);

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
	$result = mysql_query("select login from `users` where chat_id='$id' LIMIT 1",$db);
	$arrs = mysql_fetch_row($result);
	$ress =  $arrs[0];
    if($ress !== '') return true;
    return false;
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
    $query = "insert into `users`(chat_id,lang) values('{$idsho}','{$res}')";
	mysql_query($query,$db) or die("пользователя создать не удалось");
    return true;
}

function lang($chatid,$lang){
	global $db;
	$lang = mysql_real_escape_string($lang);
	$chat_id = mysql_real_escape_string($chatid);
	$query = "insert into `users`(chat_id,lang) values('{$chat_id}','{$lang}')";
	mysql_query($query,$db) or die("пользователя создать не удалось");
	
}

function taketext($chatid,$title){
    global $db;
    $cid = mysql_real_escape_string($chatid);
    $titlebase = mysql_real_escape_string($title);
    $result = mysql_query("select lang from `users` where chat_id ='$cid'",$db);
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
	mysql_query("update `users` SET lang = '{$lang}' WHERE chat_id = '{$cid}'",$db);
    
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
   $finalpath = 'https://mbw.best/bitcoinbot/asset/pic/sell/'.$time.$path;
   return $finalpath;
    
    
}





