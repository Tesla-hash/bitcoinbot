<?php
require_once("../vendor/autoload.php");
require_once("../vendor/coinbase/coinbase/src/Client.php");
require_once("../vendor/coinbase/coinbase/src/Configuration.php");

/*$ch = curl_init('https://yandex.ru');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HEADER, false);
$html = curl_exec($ch);
curl_close($ch);
 
echo $html; */
//namespace Coinbase\Wallet;


use Coinbase\Wallet\Client;
use Coinbase\Wallet\Configuration;

$apiKey = 'EGey9aBzBkcZoAv5';
$apiSecret = 'ec6VlM2DYMWhGcDFMDmpVldn30tMKCjJ';

$configuration = Configuration::apiKey($apiKey, $apiSecret);
$client = Client::create($configuration);
$user = $$client->getUser();

echo $user->name;
echo $user->email;
echo $user->time_zone;
echo $user->native_currency;

