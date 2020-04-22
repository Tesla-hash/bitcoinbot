<?php
header('Content-Type: text/html; charset=utf-8');
require_once("../models/db_connect.php");

require_once '../vendor/bitcoin-php/bitcoin-ecdsa/src/BitcoinPHP/BitcoinECDSA/BitcoinECDSA.php';

$cid = '980985070';
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
    }
    if($total_received_withoutconfirm>$frozen_satoshi){
            mysql_query("update `users` SET frozensatoshi = '{$total_received_withoutconfirm}' WHERE chat_id = '{$cid}' LIMIT 1",$db);
            $new_frozen_balance_in_satoshi = $total_received_withoutconfirm-$frozen_satoshi;
            $new_frozen_balance_in_usd = ceil(floatval((str_replace(",",".",json_decode(file_get_contents('https://blockchain.info/frombtc?currency=USD&value='.$new_frozen_balance_in_satoshi), true)))));
            $new_frozen_balance = $frozen_balance + $new_frozen_balance_in_usd;
            mysql_query("update `users` SET frozenbalance = '{$new_frozen_balance}' WHERE chat_id = '{$cid}' LIMIT 1",$db);
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
    }
    if($total_received_withoutconfirm>$frozen_satoshi){
            mysql_query("update `users` SET frozensatoshi = '{$total_received_withoutconfirm}' WHERE chat_id = '{$cid}' LIMIT 1",$db);
            $new_frozen_balance_in_satoshi = $total_received_withoutconfirm-$frozen_satoshi;
            $new_frozen_balance_in_usd = ceil(floatval((str_replace(",",".",json_decode(file_get_contents('https://blockchain.info/frombtc?currency=USD&value='.$new_frozen_balance_in_satoshi), true)))));
            $new_frozen_balance = $frozen_balance + $new_frozen_balance_in_usd;
            mysql_query("update `users` SET frozenbalance = '{$new_frozen_balance}' WHERE chat_id = '{$cid}' LIMIT 1",$db);
    }
}

$user_balance = mysql_fetch_row(mysql_query("select balance from `users` where chat_id='$cid' LIMIT 1",$db))[0];
$user_balance_satoshi = mysql_fetch_row(mysql_query("select total from `users` where chat_id='$cid' LIMIT 1",$db))[0];
$frozen_balance = mysql_fetch_row(mysql_query("select frozenbalance from `users` where chat_id='$cid' LIMIT 1",$db))[0];
$frozen_satoshi = mysql_fetch_row(mysql_query("select frozensatoshi from `users` where chat_id='$cid' LIMIT 1",$db))[0];
$total_sent_database = mysql_fetch_row(mysql_query("select sentamount from `users` where chat_id='$cid' LIMIT 1",$db))[0];

echo 'Баланс пользователя: '.$user_balance.'</br> Баланс в сатоши: '.$user_balance_satoshi.'</br> Замороженный баланс: '.$frozen_balance.
'</br> Замороженный баланс в сатоши: '. $frozen_satoshi.'</br> Обналичиный администратором баланс: '.$total_sent_database;
    
    
    
    
    
    
    
    
    
    
    
    