<?php
/**
 * bitcoibot
 *
 * @author - Tesla
 */
header('Content-Type: text/html; charset=utf-8');
// подрубаем API
require_once("vendor/autoload.php");
require_once("models/db_connect.php");
require_once("models/users.php");
require_once("vendor/block_io/block_io.php");
// дебаг
if(true){
	error_reporting(E_ALL & ~(E_NOTICE | E_USER_NOTICE | E_DEPRECATED));
	ini_set('display_errors', 1);
}
//block_io

// создаем переменную бота
$token = "1133560770:AAEtg1dj10wcieV8OuMkhAlubQlUKGSkrx8";
$bot = new \TelegramBot\Api\Client($token,null);
// если бот еще не зарегистрирован - регистируем
if(!file_exists("registered.trigger")){ 
	/**
	 * файл registered.trigger будет создаваться после регистрации бота. 
	 * если этого файла нет значит бот не зарегистрирован 
	 */
	 
	// URl текущей страницы
	$page_url = "https://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	$result = $bot->setWebhook($page_url);
	if($result){
		file_put_contents("registered.trigger",time()); // создаем файл дабы прекратить повторные регистрации
	} else die("ошибка регистрации");
}

$bot->command('start', function ($message) use ($bot) {
    $answer = 'Sign up or enter yourusername if you have already registered.

  Enter the command to sign up via Telegram:

  /join <username> <password>
  
  to sign up enter
  
  /reg <username> <password>

  Where
  <username> is your login of account,
  <password> is password of your account,.
  
  Example:
  /join vpntest qwerty123 ru
  /reg vpntest qwerty123 ru';
    $bot->sendMessage($message->getChat()->getId(), $answer);
});

// помощ
$bot->command('help', function ($message) use ($bot) {
    $answer = 'Команды:
+----------------------------+
/help - Помощь
/join - Авторизация
/reg - Регистрация
/deposit - Личный кабинет
/shop - Магазин
/logout - Выйти из аккаунта
+----------------------------+';
    $bot->sendMessage($message->getChat()->getId(), $answer);
});



$bot->command('join', function ($message) use ($bot) {
	$mtext = $message->getText();
	$cid = $message->getChat()->getId();
	$pieces = explode(" ", $mtext);
	if(id_exists($cid) !== false){
	     $badid ='<-------------------------------->
 Вы уже зарегистрированы
 на этом устройстве под
 другим аккаунтом!
<-------------------------------->';
$bot->sendMessage($message->getChat()->getId(),$badid);
	}else{
	if(is_user_set($pieces[1],$pieces[2]) == false){
	    $bad ='<-------------------------------->
 Неправильный логин или пароль!
<-------------------------------->';
	    $bot->sendMessage($message->getChat()->getId(),$bad);
	}else{
    if(id_change($cid,$pieces[1]) !== false){
	    $good = '<-------------------------------->
 Вы успешно авторизированны!
<-------------------------------->';
	    $bot->sendMessage($message->getChat()->getId(),$good);
	}

	}
	}
});

$bot->command('logout', function ($message) use ($bot) {
	$cid = $message->getChat()->getId();
    if(logout($cid) == true){
         $logmes ='<-------------------------------->
   Вы успешно вышли
   из своего аккаунта!
<-------------------------------->'
;
$bot->sendMessage($message->getChat()->getId(),$logmes);
    }
    
});

$bot->command('reg', function ($message) use ($bot) {
    $mtext = $message->getText();
	$cid = $message->getChat()->getId();
	$pieces = explode(" ", $mtext);
    if(((strlen($pieces[1]))<5)||((strlen($pieces[2]))<5)){
        $never ='<-------------------------------->
   Логин и пароль должны
 быть не меньше 5 символов!
<-------------------------------->'
;
	$bot->sendMessage($message->getChat()->getId(),$never);
    }else{
if(user_exists($pieces[1]) !== false){
     $exists ='<-------------------------------->
Пользователь с таким именем уже зарегистрирован!
<-------------------------------->';
 $bot->sendMessage($message->getChat()->getId(),$exists);
}else{
    make_user($pieces[1],$cid,$pieces[2]);
    $good ='<-------------------------------->
 Регистрация успешна!
<-------------------------------->';
    $bot->sendMessage($message->getChat()->getId(),$good);
    }
}

});


$bot->command('deposit', function ($message) use ($bot) {
	$cid = $message->getChat()->getId();
	$balance = get_deposit($cid);
	$balancedollar = round(($balance[1] * 8841.24),2);
	
    $deposit ='+----------------------------------------------------------------+
|||||||||||||||Ваш баланс:|||||||||||||||
+----------------------------------------------------------------+
'
. $balancedollar . '$
+----------------------------------------------------------------+
+----------------------------------------------------------------+
|||||||||||||||Ваш адрес:|||||||||||||||
+----------------------------------------------------------------+
'
. $balance[0] . '
+----------------------------------------------------------------+
';
	$bot->sendMessage($message->getChat()->getId(),$deposit);
});

$bot->command("shop", function ($message) use ($bot) {
    global $db;
    $result = mysql_query("select name from `shop`",$db);
    $arr = mysql_fetch_array($result);
    
	$keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
		[
			[
				['callback_data' => 'data_test1', 'text' => 'Товар1 цена:0.000001'],
				['callback_data' => 'data_test2', 'text' => 'Товар2 цена:0.000001']
			],
		[
				['callback_data' => 'data_test3', 'text' => 'Товар3 цена:0.000001'],
				['callback_data' => 'data_test4', 'text' => 'Товар4 цена:0.000001']
			]
				
		]
	);

	$bot->sendMessage($message->getChat()->getId(), "Товары", false, null,null,$keyboard);
});

$bot->on(function($update) use ($bot, $callback_loc, $find_command){
	$callback = $update->getCallbackQuery();
	$message = $callback->getMessage();
	$chatId = $message->getChat()->getId();
	$data = $callback->getData();
	
	if($data == "data_test1"){
		if(check_buy($chatId,1) == false){
		    $no ='<-------------------------------->
У вас не хватает средств!
<-------------------------------->';
        $bot->sendMessage($chatId, $no);
	}
	}
	if($data == "data_test2"){
		if(check_buy($chatId,1) == false){
		    $no ='<-------------------------------->
У вас не хватает средств!
<-------------------------------->';
        $bot->sendMessage($chatId, $no);
	}
	}
	if($data == "data_test3"){
		if(check_buy($chatId,2) == false){
		    $no ='<-------------------------------->
У вас не хватает средств!
<-------------------------------->';
        $bot->sendMessage($chatId, $no);
	}	}
	if($data == "data_test4"){
		if(check_buy($chatId,4) == false){
		    $no ='<-------------------------------->
У вас не хватает средств!
<-------------------------------->';
        $bot->sendMessage($chatId, $no);
	}	}

}, function($update){
	$callback = $update->getCallbackQuery();
	if (is_null($callback) || !strlen($callback->getData()))
		return false;
	return true;
});



$bot->on(function($Update) use ($bot){
	$message = $Update->getMessage();
	$mtext = $message->getText();
	$cid = $message->getChat()->getId();
	
	if(mb_stripos($mtext,"чупокабра") == false){
	     $answer = 'Команды:
+----------------------------+
/help - Помощь
/join - Авторизация
/reg - Регистрация
/deposit - Личный кабинет
/shop - Магазин
/logout - Выйти из аккаунта
+----------------------------+';
		$bot->sendMessage($message->getChat()->getId(), $answer);
	}
}, function($message) use ($name){
	return true; // когда тут true - команда проходит
});


// запускаем обработку
$bot->run();