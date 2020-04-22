<?php

header('Content-Type: text/html; charset=utf-8');

require_once("../models/db_connect.php");

require_once '../vendor/bitcoin-php/bitcoin-ecdsa/src/BitcoinPHP/BitcoinECDSA/BitcoinECDSA.php';

$cid = '980985070';
global $db;
$cid = mysql_real_escape_string($cid);
$address = mysql_fetch_row(mysql_query("select adress from `users` where chat_id='$cid' LIMIT 1",$db))[0];

echo 'User address(database): '.$address.'</br>';
$total_sent = json_decode(file_get_contents("https://blockchain.info/q/getsentbyaddress/".$address), true);
echo 'Total sent by address(blockchain): '.$total_sent.'</br>';
$total_sent_database = mysql_fetch_row(mysql_query("select sentamount from `users` where chat_id='$cid' LIMIT 1",$db))[0];
echo 'Total sent by address(database): '.$total_sent_database.'</br>';
$frozen_balance = mysql_fetch_row(mysql_query("select frozenbalance from `users` where chat_id='$cid' LIMIT 1",$db))[0];
echo 'Frozen user balance(database): '.$frozen_balance.'</br>';
$frozen_satoshi = mysql_fetch_row(mysql_query("select frozensatoshi from `users` where chat_id='$cid' LIMIT 1",$db))[0];
echo 'Frozen user satoshi(database): '.$frozen_satoshi.'</br>';
$total_received = json_decode(file_get_contents("https://blockchain.info/q/addressbalance/".$address."?confirmations=1"), true);
echo 'User balance with confirmations:(blockchain) '.$total_received.'</br>';
$total_received_withoutconfirm = ceil(floatval(json_decode(file_get_contents("https://blockchain.info/q/addressbalance/".$address)), true));
echo 'User balance without confirmations:(blockchain) '.$total_received_withoutconfirm.'</br>';
$total_received_usd = ceil(floatval((str_replace(",",".",json_decode(file_get_contents('https://blockchain.info/frombtc?currency=USD&value='.$total_received_withoutconfirm), true)))));
echo 'Frozen USD balance:(blockchain) '. $total_received_usd.'</br>';
$total_received_withoutconfirm_usd = ceil(floatval((str_replace(",",".",json_decode(file_get_contents('https://blockchain.info/frombtc?currency=USD&value='.$total_received), true)))));
echo 'USD balance:(blockchain) '. $total_received_withoutconfirm_usd.'</br>';
$user_balance = mysql_fetch_row(mysql_query("select balance from `users` where chat_id='$cid' LIMIT 1",$db))[0];
echo 'User balance USD(database): '.$user_balance.'</br>';
$user_balance_satoshi = mysql_fetch_row(mysql_query("select total from `users` where chat_id='$cid' LIMIT 1",$db))[0];
echo 'User total satoshi(database): '.$user_balance_satoshi.'</br>';



/*if ($total_received_withoutconfirm=0){
    
    mysql_query("update `users` SET frozensatoshi = '{$total_received_withoutconfirm}' WHERE chat_id = '{$cid}' LIMIT 1",$db);
    $new_frozen_balance_usd = $frozen_balance + $total_received_usd;
    mysql_query("update `users` SET frozenbalance = '{$new_frozen_balance_usd}' WHERE chat_id = '{$cid}' LIMIT 1",$db);
}
if ($total_received=0){
    mysql_query("update `users` SET total = '{$total_received}' WHERE chat_id = '{$cid}' LIMIT 1",$db);
    $new_balance_usd = $user_balance+$total_received_usd;
    mysql_query("update `users` SET balance = '{$new_balance_usd}' WHERE chat_id = '{$cid}' LIMIT 1",$db);
}

if ($total_sent>0){
    mysql_query("update `users` SET sentamount = '{$total_sent}' WHERE chat_id = '{$cid}' LIMIT 1",$db);

}
*/








/*$address = '1BNaquWXUVV1KaNs45j3nxHzJRMPepHydP';

$lastest_transaction = json_decode(file_get_contents("https://blockchain.info/rawaddr/".$address), true);
$test = $lastest_transaction["txs"]['0']['ver'];
echo 'тестовые данные по транзакции '.$test.'</br>';
$x = '</br>'.'Массив по транзакции '.end($lastest_transaction);
echo $x;

$tx_hash_id = 'a72d1e2c7b67ad43bf2296bee5ba6641ecec094c688f6272dcf20381d86ad056';
$raw_lastest_block= json_decode(file_get_contents("https://blockchain.info/latestblock"), true); 
$lastest_block=$raw_lastest_block["height"];
$raw_tx=json_decode(file_get_contents("https://blockchain.info/rawtx/$tx_hash_id"), true); 
$tx_block_height=$raw_tx["block_height"]; 
if($tx_block_height>0){
    $confirmations = $lastest_block - $tx_block_height +1; 
    echo '</br>'.'Потверждения '.$confirmations;
    echo '</br>'.'Последний блок '.$lastest_block;
    echo '</br>'.'Высота блока '.$tx_block_height;
}
else{
    echo 'Mempool';
}
*/
/*require_once '../vendor/bitcoin-php/bitcoin-ecdsa/src/BitcoinPHP/BitcoinECDSA/BitcoinECDSA.php';

use BitcoinPHP\BitcoinECDSA\BitcoinECDSA;

$bitcoinECDSA = new BitcoinECDSA();
$bitcoinECDSA->generateRandomPrivateKey(); //generate new random private key
$key = $bitcoinECDSA->getPrivateKey();
$address = $bitcoinECDSA->getAddress(); //compressed Bitcoin address
echo "Address: " . $address .'</br>'. "Private key: ".$key. PHP_EOL;*/
/*global $db;
$name ='testadmin';
$chat_id ='3423423561';
$password = 'testadmin';

$name = mysql_real_escape_string($name);
$chat_id = mysql_real_escape_string($chat_id);
$password = mysql_real_escape_string($password);
$bitcoinECDSA = new BitcoinECDSA();
$bitcoinECDSA->generateRandomPrivateKey(); //generate new random private key
$key = $bitcoinECDSA->getPrivateKey();
$address = $bitcoinECDSA->getAddress(); 
$adres = mysql_real_escape_string($address);
$newlabel = mysql_real_escape_string($key);
$test = "insert into `users`(chat_id,login,password,adress,label) values('{$chat_id}','{$name}','{$password}','{$adres},'{$newlabel}')";
echo '</br>'.$test;
mysql_query("insert into `users`(chat_id,login,password,adress,label) values('{$chat_id}','{$name}','{$password}','{$adres},'{$newlabel}')",$db);
*/

/*

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://blockchain.info/balance?active='.$address);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

$result = curl_exec($ch);
$arr = json_decode($result);
$total_received = $arr->$address->total_received;
echo 'Result of request(array): '.$result;
echo '</br>'.'Total_received_blockchain: '.$total_received;
curl_close($ch);
$result = mysql_query("select total from `users` where chat_id='$cid' LIMIT 1",$db);
$total_arr = mysql_fetch_row($result);
$total_data = $total_arr[0];
echo '</br>'.'Total_received_database: '.$total_data;

if($total_received == $total_data){
    echo '</br>'.'Balance don\'t changed';
    
}else{
        $balance = $total_received-$total_data;
        echo '</br>'.'Balance in Satoshi: '.$balance;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://blockchain.info/frombtc?currency=USD&value='.$balance);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $resbal = curl_exec($ch);
        echo '</br>'.'Before formating: '.$resbal;
        echo '</br>'.'After formating: '.floatval(str_replace(",","",$resbal));
        echo '</br>'.'Type of resbal: '.gettype($resbal);
        $resbal = floatval(str_replace(",","",$resbal));
        curl_close($ch);
        $userbalance = mysql_query("select balance from `users` where chat_id='$cid' LIMIT 1",$db);
        $ub_arr = mysql_fetch_row($userbalance);
        $ubalance = $ub_arr[0];
        $newbalance = $ubalance + $resbal;
        mysql_query("update `users` SET balance = '{$newbalance}' WHERE chat_id = '{$cid}' LIMIT 1",$db);
        $testbalance = mysql_fetch_row(mysql_query("select balance from `users` where chat_id='$cid' LIMIT 1",$db))[0];
        echo ' </br>'.'Balance changed: '.$testbalance;
        echo '</br>'.'One more Total Received: '.$total_received;
        mysql_query("update `users` SET total = '{$total_received}' WHERE chat_id = '{$cid}' LIMIT 1",$db);
        echo '</br>'.'Total data updates';
}
*/

