<?php
require_once("../models/db_connect.php");

/*require_once '../vendor/bitcoin-php/bitcoin-ecdsa/src/BitcoinPHP/BitcoinECDSA/BitcoinECDSA.php';

use BitcoinPHP\BitcoinECDSA\BitcoinECDSA;

$bitcoinECDSA = new BitcoinECDSA();
$bitcoinECDSA->generateRandomPrivateKey(); //generate new random private key
$key = $bitcoinECDSA->getPrivateKey();
$address = $bitcoinECDSA->getAddress(); //compressed Bitcoin address
echo "Address: " . $address . "Private key: ".$key. PHP_EOL;
*/
$address = '15EW3AMRm2yP6LEF5YKKLYwvphy3DmMqN6';
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://blockchain.info/balance?active='.$address);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

$result = curl_exec($ch);
$arr = json_decode($result);
$total_received = $arr->$address->total_received;
echo $result;
echo '</br>'.'Total_received_blockchain: '.$total_received;
curl_close($ch);
global $db;
$cid = '980985070';
$cid = mysql_real_escape_string($cid);
$result = mysql_query("select total from `users` where chat_id='$id' LIMIT 1",$db);
$total_arr = mysql_fetch_row($result);
$total_data = $total_arr[0];
echo '</br>'.'Total_received_database: '.$total_data;
  /*      $balance = $total_received;
        echo '</br>'.$balance;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://blockchain.info/frombtc?currency=USD&value='.$balance);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $resbal = curl_exec($ch);
        echo '</br>'.$resbal;
        curl_close($ch);*/
        

if($total_receveived = $total_data){
    echo '</br>'.'Balance don\'t changed';
    
}else{
        $balance = $total_received-$total_data;
        echo '</br>'.$balance;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://blockchain.info/frombtc?currency=USD&value='.$balance);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $resbal = curl_exec($ch);
        echo '</br>'.$resbal;
        curl_close($ch);
        $userbalance = mysql_query("select balance from `users` where chat_id='$cid' LIMIT 1",$db);
        $ub_arr = mysql_fetch_row($userbalance);
        $ubalance = $ub_arr[0];
        $newbalance = $ubalance + $resbal;
        mysql_query("update `users` SET balance = '{$newbalance}' WHERE chat_id = '{$cid}'",$db);
        $testbalance = mysql_fetch_row(mysql_query("select balance from `users` where chat_id='$cid' LIMIT 1",$db))[0];
        echo ' </br>'.'Balance changed: '.$testbalance;
        
}


