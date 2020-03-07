<?php

/** модель работы с пользователями **/
//регистрация пользователя

function make_user($name,$chat_id,$password){
	global $db;
	$name = mysql_real_escape_string($name);
	$chat_id = mysql_real_escape_string($chat_id);
	$password = mysql_real_escape_string($password);
	$apiKey = 'c0b4-382e-1c3c-510b';
    $pin = '12345678';
    $version = 2; // the API version
    $block_io = new BlockIo($apiKey, $pin, $version);
    $getNewAddressInfo = $block_io->get_new_address();
    $address = $getNewAddressInfo->data->address;
    $label = $getNewAddressInfo->data->label;
    $adres = mysql_real_escape_string($address);
    $newlabel = mysql_real_escape_string($label);
	$query = "insert into `users`(login,chat_id,password,adress,label) values('{$name}','{$chat_id}','{$password}','{$adres}','{$newlabel}')";
	mysql_query($query,$db) or die("пользователя создать не удалось");
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
	$apiKey = 'c0b4-382e-1c3c-510b';
    $pin = '12345678';
    $version = 2; // the API version
    $block_io = new BlockIo($apiKey, $pin, $version);
    $getNewAddressInfo = $block_io->get_address_balance(array('addresses' => $res));
    $balance = $getNewAddressInfo->data->balances[0]->available_balance;
	return array($res,$balance);
	
}

function user_exists($name){
    global $db;
	$login = mysql_real_escape_string($name);
	$result = mysql_query("select * from `users` where login='$login' LIMIT 1",$db);
    if(mysql_fetch_array($result) !== false) return true;
    return false;
}

function id_exists($cid)
{
    global $db;
	$id = mysql_real_escape_string($cid);
	$result = mysql_query("select * from `users` where chat_id='$id' LIMIT 1",$db);
    if(mysql_fetch_array($result) !== false) return true;
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

function check_buy($cid,$idshop){
    global $db;
    $idsho = mysql_real_escape_string($idshop);
    $results = mysql_query("select price from `shop` where id ='$idsho'",$db);
	$arrs = mysql_fetch_row($results);
	$ress = $arrs[0];
    $id = mysql_real_escape_string($cid);
    $result = mysql_query("select adress from `users` where chat_id ='$cid'",$db);
	$arr = mysql_fetch_row($result);
	$res = $arr[0];
	$apiKey = 'c0b4-382e-1c3c-510b';
    $pin = '12345678';
    $version = 2; // the API version
    $block_io = new BlockIo($apiKey, $pin, $version);
    $getNewAddressInfo = $block_io->get_address_balance(array('addresses' => $res));
    $balance = $getNewAddressInfo->data->balances[0]->available_balance;
    if($balance<$ress){
    return false;
    }
    }
    
function logout($cid){
    global $db;
    $idsho = mysql_real_escape_string($cid);
    mysql_query("update `users` SET chat_id = 0 WHERE chat_id = '{$idsho}'",$db);
    return true;
}

