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
$token = "1034786397:AAHcw0ianwWmF-879eodCx9Li_sCFljiW-M";
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

$bot->command("start", function ($message) use ($bot) {
    global $db;
	$keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup(
		[
			[
				['callback_data' => 'en', 'text' => 'English'],
				['callback_data' => 'rus', 'text' => 'Русский'],
				['callback_data' => 'alb', 'text' => 'Shqiptar']
			]
			
		],true
	);
    
	$bot->sendMessage($message->getChat()->getId(), "Choose your language", false, null,null,$keyboard);
});



// тест
$bot->command('help', function ($message) use ($bot) {
    $cid = $message->getChat()->getId();
    $title = 'help';
    $answer = taketext($cid,$title);
    $bot->sendMessage($message->getChat()->getId(), $answer);
});


$bot->command('join', function ($message) use ($bot) {
    $mtext = $message->getText();
	$cid = $message->getChat()->getId();
	$pieces = explode(" ", $mtext);
	if(id_exists($cid) !== false){
	    $title = 'alsoregister';
        $badid = taketext($cid,$title);
        $bot->sendMessage($message->getChat()->getId(),$badid);
	}else{
	if(is_user_set($pieces[1],$pieces[2]) == false){
	    $title = 'badpassword';
     //   $bad = taketext($cid,$title);
	    $bot->sendMessage($message->getChat()->getId(),$title);
	}else{
    if(id_change($cid,$pieces[1]) !== false){
        $title = 'goodresult';
        $good = taketext($cid,$title);
	    $bot->sendMessage($message->getChat()->getId(),$good);
	}

	}
	}
});

$bot->command('logout', function ($message) use ($bot) {
	$cid = $message->getChat()->getId();
    if(logout($cid) == true){
        $title = 'Succesfull logout';
     //   $logmess = taketext($cid,$title);
        $bot->sendMessage($message->getChat()->getId(),$title);
    }
    
});

$bot->command('reg', function ($message) use ($bot) {
    $mtext = $message->getText();
	$cid = $message->getChat()->getId();
	$pieces = explode(" ", $mtext);
    if(((strlen($pieces[1]))<5)||((strlen($pieces[2]))<5)){
        $title = '<-------------------------------->
        Username and password must
        be at least 5 characters!
    <-------------------------------->';
       // $never = taketext($cid,$title);
	    $bot->sendMessage($message->getChat()->getId(),$title);
    }else{
if(user_exists($pieces[1]) !== false){
     $title = 'exists';
     $exists = taketext($cid,$title);
     $bot->sendMessage($message->getChat()->getId(),$exists);
}else{
    make_user($pieces[1],$cid,$pieces[2]);
     $title = 'goodregister';
     $good = taketext($cid,$title);
     $bot->sendMessage($message->getChat()->getId(),$good);
    }
}

});
$bot->command('deposit', function ($message) use ($bot) {
	$cid = $message->getChat()->getId();
	$balance = get_deposit($cid);
	$balancedollar = round(($balance[1] * 8841.24),2);
	
    $deposit ='+----------------------------------------------------------------+
|||||||||||||||Balance:|||||||||||||||
+----------------------------------------------------------------+
'
. $balancedollar . '$
+----------------------------------------------------------------+
+----------------------------------------------------------------+
|||||||||||||||Address:|||||||||||||||
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
    
	$keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup(
		[
			[
				['callback_data' => 'shop1', 'text' => 'Магазин1'],
				['callback_data' => 'shop2', 'text' => 'Магазин2']
			]
		]
	);
    
	$bot->sendMessage($message->getChat()->getId(), "Магазины", false, null,null,$keyboard);
});

$bot->on(function($update) use ($bot, $callback_loc, $find_command){
	$callback = $update->getCallbackQuery();
	$message = $callback->getMessage();
	$chatId = $message->getChat()->getId();    
	$data = $callback->getData();

	if($data == "data_test1"){
		if(check_buy($chatId,1) == false){
	     $title = 'nobalance';
         $no = taketext($chatId,$title);
         $bot->sendMessage($chatId, $no);
	}else{
	    $link = 'https://yandex.ru';
	    $bot->sendMessage($chatId, $link);
	}
	}
	if($data == "data_test2"){
		if(check_buy($chatId,1) == false){
         $title = 'nobalance';
         $no = taketext($chatId,$title);
        $bot->sendMessage($chatId, $no);
	}
	else{
	    $link = 'https://yandex.ru';
	    $bot->sendMessage($chatId, $link);
	}
	}
	if($data == "data_test3"){
		if(check_buy($chatId,2) == false){
		 $title = 'nobalance';
         $no = taketext($chatId,$title);
        $bot->sendMessage($chatId, $no);
	}else{
	    $link = 'https://yandex.ru';
	    $bot->sendMessage($chatId, $link);
	}
	}
	if($data == "data_test4"){
		if(check_buy($chatId,4) == false){
         $title = 'nobalance';
         $no = taketext($chatId,$title);
        $bot->sendMessage($chatId, $no);
	}
	else{
	    $link = 'https://yandex.ru';
	    $bot->sendMessage($chatId, $link);
	}
	}
	
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
	   
    if(mb_stripos($mtext,"Магазин2") !== false){
	    		$keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
		[
		    [
				['callback_data' => 'data_test3', 'text' => 'Товар3 цена:0.000001(8)'],
				['callback_data' => 'data_test4', 'text' => 'Товар4 цена:0.000001(18)']
			]
				
		],true
	);

	$bot->sendMessage($message->getChat()->getId(), "Товары Магазина 2", false, null,null,$keyboard);

	}
	if(mb_stripos($mtext, "Магазин1") !== false){
			$keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
		[
			[
				['callback_data' => 'data_test1', 'text' => 'Товар1 цена:0.000001(100)'],
				['callback_data' => 'data_test2', 'text' => 'Товар2 цена:0.000001(6)']
			]
		],true
	);
	$bot->sendMessage($message->getChat()->getId(), "Товары Магазина 1", false, null,null,$keyboard);
	}
	
	if(mb_stripos($mtext, "English") !== false){
	    $message = $Update->getMessage();
        $chatId = $message->getChat()->getId();    
        $lang = 'en';
        lang($chatId,$lang);
        
        $answer = 'Sign up or enter yourusername if you have already registered.

  Enter the command to sign up via Telegram:

  /join <username> <password>
  
  to sign up enter
  
  /reg <username> <password>

  Where
  <username> is your login of account,
  <password> is password of your account,.
  
  Example:
  /join vpntest qwerty123
  /reg vpntest qwerty123';
    $bot->sendMessage($message->getChat()->getId(), $answer);
	}
	if(mb_stripos($mtext, "Русский") !== false){
	    $message = $Update->getMessage();
        $chatId = $message->getChat()->getId();    
        $lang = 'ru';
        lang($chatId,$lang);
        
        $answer = 'Зарегистрируйтесь или введите ваше имя пользователя, если вы уже зарегистрированы.

Введите команду для регистрации через Telegram:

 / join <имя пользователя> <пароль>

 зарегистрироваться войти

 / reg <имя пользователя> <пароль>
 где
 <имя пользователя> - ваш логин учетной записи,
 <пароль> - это пароль вашей учетной записи.

 Пример:
 / join к vpntest qwerty123
 / reg vpntest qwerty123';
    $bot->sendMessage($message->getChat()->getId(), $answer);
	}
	if(mb_stripos($mtext, "Shqiptar") !== false){
	    $message = $Update->getMessage();
        $chatId = $message->getChat()->getId();    
        $lang = 'alb';
        lang($chatId,$lang);
        
        $answer = 'Regjistrohuni ose shkruani emrin tuaj nëse keni regjistruar tashmë.

 Vendosni komandën për tu regjistruar përmes Telegram:

/join <username> <password>

të regjistroheni hyni

/reg <username> <password>
ku
<username> është llogaria jote e llogarisë,
<password> është fjalëkalim i llogarisë suaj,.

shembull:
/join vpntest qwerty123
/reg vpntest qwerty123';
    $bot->sendMessage($message->getChat()->getId(), $answer);
	}
	
	
	if(mb_stripos($mtext,"чупокабра") !== false){
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