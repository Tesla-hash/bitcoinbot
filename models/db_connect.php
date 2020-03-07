<?php
/*подключение к базе данных*/

$host = "localhost"; // в 90% случаев это менять не надо
$password = "Volkova2020!";
$username = "t3sla24u_bot";
$databasename = "t3sla24u_bot";

global $db;
$db = mysql_connect($host,$username,$password) or die("error: Failed_connect_database");

mysql_select_db($databasename, $db) or die("error:Database not selected witch mysql_select_db");

mysql_query('SET NAMES utf8',$db);
mysql_query('SET CHARACTER SET utf8',$db );
mysql_query('SET COLLATION_CONNECTION="utf8_general_ci"',$db ); 
setlocale(LC_ALL,"ru_RU.UTF8");