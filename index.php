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
require_once("vendor/bitcoin-php/bitcoin-ecdsa/src/BitcoinPHP/BitcoinECDSA/BitcoinECDSA.php");

// дебаг
if(true){
	error_reporting(E_ALL & ~(E_NOTICE | E_USER_NOTICE | E_DEPRECATED));
	ini_set('display_errors', 1);
}
//block_io

// создаем переменную бота
$token = "1034786397:AAHcw0ianwWmF-879eodCx9Li_sCFljiW-M";
$bot = new \TelegramBot\Api\Client($token,null);
//$bot->sendMessage('368575128', 'Рассылка всем пользователям бота!');
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

$menubutton = getbutton(1);
$privatebutton = getbutton(2);
$change = getbutton(3);

$bot->command("start", function ($message) use ($bot) {
    global $db;
    $cid = $message->getChat()->getId();
    //$username = $message->getFrom()->getUsername();
    if(id_exists($cid) !== false){
	   $username = getusername($cid);
	   $welcomes = taketext($cid,'welcome');
	   $welcometext = '👋'.$welcomes.' ' . $username;
	  $bot->sendSticker($message->getChat()->getId(), 'CAACAgIAAxkBAAI-mV6J16sEtke2YyuUi5w2RFW13iyVAAIKAAN2fWkniokL72wixbcYBA');
	  $bot->sendMessage($message->getChat()->getId(), $welcometext);
	 // $media = new \TelegramBot\Api\Types\InputMedia\ArrayOfInputMedia();
	  //$media->addItem(new TelegramBot\Api\Types\InputMedia\InputMediaVideo('https://mbw.best/bitcoinbot/assets/gif/startpic.gif'));
      //$document = new \CURLFile('startpic.gif');
      	$shopone = shopname(1);
      	$menubutton = getbutton(1);
      	$privatebutton = getbutton(2);
      	$change = getbutton(3);



      //$bot->sendDocument($message->getChat()->getId(), $document);
      
      $keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup(
		    [
			    [
				    ['text' => '♻️ '.$shopone.'♻️'],
				    ['callback_data' => 'languages', 'text' => '🏛'.$privatebutton.'🏛']
			    ]
		    ],false,true,false,true
	    );
    
	    $bot->sendMessage($message->getChat()->getId(), '📜'.$menubutton.'📜', false, null,null,$keyboard);    
     

} 
else if(id_existsglobal($cid) == true)

{
    	$shopone = shopname(1);
      	$menubutton = getbutton(1);
      	$privatebutton = getbutton(2);
      	$change = getbutton(3);

    	   $welcomes = taketext($cid,'start');
    	   $bot->sendMessage($message->getChat()->getId(), $welcomes);

           $keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup(
		    [
			    [
				    ['text' => '♻️ '.$shopone.'♻️'],
				    ['callback_data' => 'languages', 'text' => '🏛'.$privatebutton.'🏛']
			    ]
		    ],false,true,false,true
	    );
    
	    $bot->sendMessage($message->getChat()->getId(), '📜'.$menubutton.'📜', false, null,null,$keyboard);    
     

}

else{
        	$change = getbutton(3);

	$keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup(
		[
			[
				['callback_data' => 'en', 'text' => 'Հայերեն'],
				['callback_data' => 'rus', 'text' => 'Русский'],
				['callback_data' => 'alb', 'text' => 'English']
			]
			
		],false,true
	);
    
	$bot->sendMessage($message->getChat()->getId(), $change, false, null,null,$keyboard);

        
    }
    
    
    
    
});



/*Переводы отдельных фраз*/

$bot->command('help', function ($message) use ($bot) {
    $cid = $message->getChat()->getId();
    $title = 'help';
    $answer = taketext($cid,$title);
    $bot->sendMessage($message->getChat()->getId(), $answer);
   
});


$bot->command('join', function ($message) use ($bot) {
    $mtext = $message->getText();
	$cid = $message->getChat()->getId();
	$privatebutton = getbutton(2);
	$pieces = explode(" ", $mtext);
	if(id_exists($cid) == true){
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
        $username = getusername($cid);
        $goodx = $username . $good;
	    $bot->sendMessage($message->getChat()->getId(),$goodx);
	    	$shopone = shopname(1);

	    $keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup(
		    [
			    [
				   	['text' => '♻️ '.$shopone.'♻️'],
				    ['callback_data' => 'languages', 'text' => '🏛'.$privatebutton.'🏛']
			    ]
		    ],false,true
	    );
	    $menubutton = getbutton(1);

        $balancebut = getbutton(5);
	    $bot->sendMessage($message->getChat()->getId(), $menubutton, false, null,null,$keyboard);
        $balancedollar = $balancebut.newbalance($cid);
        $bot->sendMessage($message->getChat()->getId(), $balancedollar);
	    
	}

	}
	}
});

$bot->command('logout', function ($message) use ($bot) {
	$cid = $message->getChat()->getId();
	$title = taketext($cid,'logout');
    if(logout($cid) == true){
    
     //   $logmess = taketext($cid,$title);
        $bot->sendMessage($message->getChat()->getId(),$title);
    }
    
});

$bot->command('reg', function ($message) use ($bot) {
    $mtext = $message->getText();
	$cid = $message->getChat()->getId();
	$pieces = explode(" ", $mtext);
    if(((strlen($pieces[1]))<5)||((strlen($pieces[2]))<5)){
       // $title = getbutton(7);
       $never = taketext($cid,'nosymbols');
	    $bot->sendMessage($message->getChat()->getId(),$never);
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
     $balancebut = getbutton(5);
     $balancedollar = $balancebut.newbalance($cid);
     $bot->sendMessage($message->getChat()->getId(), $balancedollar);
}
}

});
/*$bot->command('deposit', function ($message) use ($bot) {
	$cid = $message->getChat()->getId();
	$balance = get_deposit($cid);
	$balancedollar = newbalance($cid);
    $username = getusername($cid);

    $deposit = $username.getbutton(8)
. $balancedollar . getbutton(9)
. $balance[0];
	$bot->sendMessage($message->getChat()->getId(),$deposit);
});
*/


/*$bot->command("shop", function ($message) use ($bot) {
    global $db;
    $result = mysql_query("select name from `shop`",$db);
    $arr = mysql_fetch_array($result);
    
	$keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup(
		[
			[
			    ['callback_data' => 'menu', 'text' => '📜Меню'],
				['callback_data' => 'shop1', 'text' => '🛒Магазин1'],
				['callback_data' => 'shop2', 'text' => '🛒Магазин2']
			]
		],false,true
	);
    
	$bot->sendMessage($message->getChat()->getId(), $shops, false, null,null,$keyboard);
	$balancedollar = $balancebut.newbalance($cid);
    $bot->sendMessage($message->getChat()->getId(), $balancedollar);
});*/

$bot->on(function($update) use ($bot, $callback_loc, $find_command){
	$callback = $update->getCallbackQuery();
	$message = $callback->getMessage();
	$chatId = $message->getChat()->getId();    
	$data = $callback->getData();
	$yes = taketext($chatId,'yes');
	$confirm = taketext($chatId,'confirm');
	$balancebut = getbutton(5);
		
	$onegoodone = goodone(1);
    $onegoodtwo = goodone(2);
	$onegoodthree = goodone(3);
	$onegoodfour = goodone(4);
	$onegoodfive = goodone(5);
	$onegoodsix = goodone(6);
	$onegoodseven = goodone(7);
	$onegoodeight = goodone(8);
	$onegoodnine = goodone(9);
	$onegoodten = goodone(10);
	
	if($data == 'refresh'){
	   $reftext = taketext($chatId,'balanceref');
	   refresh($chatId);
       $bot->sendMessage($chatId,$reftext);
       $balance = newbalance($chatId);
       $message = 'Ваш баланс: $'.$balance;
       $bot->sendMessage($chatId,$message);

       

	}
    
    if($data == 'yes1'){
         if(check_amount('data_test1') == false){
            $amount = taketext($chatId,'amount');
	        $bot->sendMessage($chatId,$amount);
        }else{
         $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
		[
			[
			    ['callback_data' => 'data_test1', 'text' => $yes]
			]
				
		],false,true
	);
	  	$confirm = taketext($chatId,'preconfirm').'🐊'.getgoodname('data_test1').$onegoodone[2].' '.$confirm;

	$bot->sendMessage($message->getChat()->getId(), $confirm, false, null,null,$keyboard);  
        } 
    }
    if($data == 'yes2'){
         if(check_amount('data_test2') == false){
            $amount = taketext($chatId,'amount');
	        $bot->sendMessage($chatId,$amount);
        }else{
         $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
		[
			[
			    ['callback_data' => 'data_test2', 'text' => $yes]
			]
				
		],false,true
	);
	  	$confirm = taketext($chatId,'preconfirm').'🍪'.getgoodname('data_test2').$onegoodtwo[2].' '.$confirm;

	$bot->sendMessage($message->getChat()->getId(), $confirm, false, null,null,$keyboard);  
        }
    }
    if($data == 'yes3'){
         if(check_amount('data_test3') == false){
            $amount = taketext($chatId,'amount');
	        $bot->sendMessage($chatId,$amount);
        }else{
         $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
		[
			[
			    ['callback_data' => 'data_test3', 'text' => $yes]
			]
				
		],false,true
	);
	  	$confirm = taketext($chatId,'preconfirm').'🍞'.getgoodname('data_test3').$onegoodone[3].' '.$confirm;

	$bot->sendMessage($message->getChat()->getId(), $confirm, false, null,null,$keyboard);  
        }
    }
    if($data == 'yes4'){
         if(check_amount('data_test4') == false){
            $amount = taketext($chatId,'amount');
	        $bot->sendMessage($chatId,$amount);
        }else{
         $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
		[
			[
			    ['callback_data' => 'data_test4', 'text' => $yes]
			]
				
		],false,true
	);
	  	$confirm = taketext($chatId,'preconfirm').getgoodname('data_test4').$onegoodfour[2].' '.$confirm;

	$bot->sendMessage($message->getChat()->getId(), $confirm, false, null,null,$keyboard);  
        }
    }
    if($data == 'yes5'){
         if(check_amount('data_test5') == false){
            $amount = taketext($chatId,'amount');
	        $bot->sendMessage($chatId,$amount);
        }else{
         $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
		[
			[
			    ['callback_data' => 'data_test5', 'text' => $yes]
			]
				
		],false,true
	);
	  	$confirm = taketext($chatId,'preconfirm').'💎'.getgoodname('data_test5').$onegoodfive[2].' '.$confirm;

	$bot->sendMessage($message->getChat()->getId(), $confirm, false, null,null,$keyboard);  
        }
    }
    if($data == 'yes6'){
         if(check_amount('data_test6') == false){
            $amount = taketext($chatId,'amount');
	        $bot->sendMessage($chatId,$amount);
        }else{
         $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
		[
			[
			    ['callback_data' => 'data_test6', 'text' => $yes]
			]
				
		],false,true
	);
	  	$confirm = taketext($chatId,'preconfirm').'💊'.getgoodname('data_test6').$onegoodsix[2].' '.$confirm;

	$bot->sendMessage($message->getChat()->getId(), $confirm, false, null,null,$keyboard);  
        }
    }
    if($data == 'yes7'){
         if(check_amount('data_test7') == false){
            $amount = taketext($chatId,'amount');
	        $bot->sendMessage($chatId,$amount);
        }else{
         $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
		[
			[
			    ['callback_data' => 'data_test7', 'text' => $yes]
			]
				
		],false,true
	);
	  	$confirm = taketext($chatId,'preconfirm').'🌈 '.getgoodname('data_test7').$onegoodseven[2].' '.$confirm;

	$bot->sendMessage($message->getChat()->getId(), $confirm, false, null,null,$keyboard);  
        }
    }
    if($data == 'yes8'){
         if(check_amount('data_test8') == false){
            $amount = taketext($chatId,'amount');
	        $bot->sendMessage($chatId,$amount);
        }else{
         $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
		[
			[
			    ['callback_data' => 'data_test8', 'text' => $yes]
			]
				
		],false,true
	);
	  	$confirm = taketext($chatId,'preconfirm').getgoodname('data_test8').$onegoodeight[2].' '.$confirm;

	$bot->sendMessage($message->getChat()->getId(), $confirm, false, null,null,$keyboard);  
        }
    }
    
     if($data == 'yes9'){
          if(check_amount('data_test9') == false){
            $amount = taketext($chatId,'amount');
	        $bot->sendMessage($chatId,$amount);
        }else{
         $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
		[
			[
			    ['callback_data' => 'data_test9', 'text' => $yes]
			]
				
		],false,true
	);
	
		$confirm = taketext($chatId,'preconfirm').'💜'.getgoodname('data_test9').$onegoodnine[2].' '.$confirm;

	  
	$bot->sendMessage($message->getChat()->getId(), $confirm, false, null,null,$keyboard);  
        }
    }
    
    if($data == 'yes10'){
        if(check_amount('data_test10') == false){
            $amount = taketext($chatId,'amount');
	        $bot->sendMessage($chatId,$amount);
        }else{
         $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
		[
			[
			    ['callback_data' => 'data_test10', 'text' => $yes]
			]
				
		],false,true
	);
	$confirm = taketext($chatId,'preconfirm').'🍯'.getgoodname('data_test10').$onegoodten[2].' '.$confirm;
	
	  
	$bot->sendMessage($message->getChat()->getId(), $confirm, false, null,null,$keyboard);  
        
    }
    }
    $success = taketext($chatId,'success');
	if($data == 'data_test1'){
	 if(check_amount('data_test1') == false){
	       $amount = taketext($chatId,'amount');
	       $bot->sendMessage($message->getChat()->getId(), $amount);
}else{
	if(check_buy($chatId,$data) == false){
	     $title = 'nobalance';
         $no = taketext($chatId,$title);
         $bot->sendMessage($chatId, $no);
	}else{
	    $link = $success.' 🐊'.getgoodname($data).$onegoodone[2];
	    $path = '1goodone';
	    $pic = getgoodpic($path);
	    $bot->sendMessage($chatId, $link);
	    $bot->sendPhoto($message->getChat()->getId(), $pic);
	   $balancedollar = $balancebut.newbalance($chatId);
         $bot->sendMessage($message->getChat()->getId(), $balancedollar);
       //  	$bot->sendSticker($message->getChat()->getId(), 'CAACAgIAAxkBAAI-mV6J16sEtke2YyuUi5w2RFW13iyVAAIKAAN2fWkniokL72wixbcYBA');

	    }
    
}
	}
	
	if($data == 'data_test2'){
	    	 if(check_amount('data_test2') == false){
	       $amount = taketext($chatId,'amount');
	       $bot->sendMessage($message->getChat()->getId(), $amount);
}else{
	if(check_buy($chatId,$data) == false){
	     $title = 'nobalance';
         $no = taketext($chatId,$title);
         $bot->sendMessage($chatId, $no);
	}else{
        $balancedollar = $balancebut.newbalance($chatId);
        $bot->sendMessage($message->getChat()->getId(), $balancedollar);
	    $link = $success.' 🍪'.getgoodname($data).$onegoodtwo[2];
	     $path = '2goodtwo';
	    
	    $pic = getgoodpic($path);
	    $bot->sendMessage($chatId, $link);
	    $bot->sendPhoto($message->getChat()->getId(), $pic);
	  $balancedollar = $balancebut.newbalance($chatId);
        $bot->sendMessage($message->getChat()->getId(), $balancedollar);
        //	   $bot->sendSticker($message->getChat()->getId(), 'CAACAgIAAxkBAAI-mV6J16sEtke2YyuUi5w2RFW13iyVAAIKAAN2fWkniokL72wixbcYBA');


	}
}
	}
	if($data == 'data_test3'){
	    	 if(check_amount('data_test3') == false){
	       $amount = taketext($chatId,'amount');
	       $bot->sendMessage($message->getChat()->getId(), $amount);
}else{
	if(check_buy($chatId,$data) == false){
	     $title = 'nobalance';
         $no = taketext($chatId,$title);
         $bot->sendMessage($chatId, $no);
	}else{
	    $link = $success.' 🍞'.getgoodname($data).$onegoodthree[2];
	     $path = '3goodthree';
	    
	    $pic = getgoodpic($path);
	    $bot->sendMessage($chatId, $link);
	    $bot->sendPhoto($message->getChat()->getId(), $pic);
	   $balancedollar = $balancebut.newbalance($chatId);
         $bot->sendMessage($message->getChat()->getId(), $balancedollar);
          //        	$bot->sendSticker($message->getChat()->getId(), 'CAACAgIAAxkBAAI-mV6J16sEtke2YyuUi5w2RFW13iyVAAIKAAN2fWkniokL72wixbcYBA');

	}
}
	}
	if($data == 'data_test4'){
	    	 if(check_amount('data_test4') == false){
	       $amount = taketext($chatId,'amount');
	       $bot->sendMessage($message->getChat()->getId(), $amount);
}else{
	if(check_buy($chatId,$data) == false){
	     $title = 'nobalance';
         $no = taketext($chatId,$title);
         $bot->sendMessage($chatId, $no);
	}else{
	    $link = $success.getgoodname($data).$onegoodfour[2];
	     $path = '4goodfour';
	    
	    $pic = getgoodpic($path);
	    $bot->sendMessage($chatId, $link);
	    $bot->sendPhoto($message->getChat()->getId(), $pic);
	    
	   $balancedollar = $balancebut.newbalance($chatId);
         $bot->sendMessage($message->getChat()->getId(), $balancedollar);
                //  	$bot->sendSticker($message->getChat()->getId(), 'CAACAgIAAxkBAAI-mV6J16sEtke2YyuUi5w2RFW13iyVAAIKAAN2fWkniokL72wixbcYBA');

	}
}
	}
	if($data == 'data_test5'){
	    	 if(check_amount('data_test5') == false){
	       $amount = taketext($chatId,'amount');
	       $bot->sendMessage($message->getChat()->getId(), $amount);
}else{
	if(check_buy($chatId,$data) == false){
	     $title = 'nobalance';
         $no = taketext($chatId,$title);
         $bot->sendMessage($chatId, $no);
	}else{
	    $link = $success.' 💎'.getgoodname($data).$onegoodfive[2];
	     $path = '5goodfive';
	    
	    $pic = getgoodpic($path);
	    $bot->sendMessage($chatId, $link);
	    $bot->sendPhoto($message->getChat()->getId(), $pic);
	   $balancedollar = $balancebut.newbalance($chatId);
         $bot->sendMessage($message->getChat()->getId(), $balancedollar);
              //    	$bot->sendSticker($message->getChat()->getId(), 'CAACAgIAAxkBAAI-mV6J16sEtke2YyuUi5w2RFW13iyVAAIKAAN2fWkniokL72wixbcYBA');

	}
}
	}
	if($data == 'data_test6'){
	    	 if(check_amount('data_test6') == false){
	       $amount = taketext($chatId,'amount');
	       $bot->sendMessage($message->getChat()->getId(), $amount);
}else{
	if(check_buy($chatId,$data) == false){
	     $title = 'nobalance';
         $no = taketext($chatId,$title);
         $bot->sendMessage($chatId, $no);
	}else{
	    $link = $success.' 💊'.getgoodname($data).$onegoodsix[2];
	     $path = '6goodsix';
	    
	    $pic = getgoodpic($path);
	    $bot->sendMessage($chatId, $link);
	    $bot->sendPhoto($message->getChat()->getId(), $pic);
	   $balancedollar = $balancebut.newbalance($chatId);
         $bot->sendMessage($message->getChat()->getId(), $balancedollar);
               //   	$bot->sendSticker($message->getChat()->getId(), 'CAACAgIAAxkBAAI-mV6J16sEtke2YyuUi5w2RFW13iyVAAIKAAN2fWkniokL72wixbcYBA');

	}
}
	}
	if($data == 'data_test7'){
	    	 if(check_amount('data_test7') == false){
	       $amount = taketext($chatId,'amount');
	       $bot->sendMessage($message->getChat()->getId(), $amount);
}else{
	if(check_buy($chatId,$data) == false){
	     $title = 'nobalance';
         $no = taketext($chatId,$title);
         $bot->sendMessage($chatId, $no);
	}else{
	    $link = $success.' 🌈'.getgoodname($data).$onegoodseven[2];
	    $path = '7goodseven';
	    
	    $pic = getgoodpic($path);
	    $bot->sendMessage($chatId, $link);
	    $bot->sendPhoto($message->getChat()->getId(), $pic);
	   $balancedollar = $balancebut.newbalance($chatId);
         $bot->sendMessage($message->getChat()->getId(), $balancedollar);
                 // 	$bot->sendSticker($message->getChat()->getId(), 'CAACAgIAAxkBAAI-mV6J16sEtke2YyuUi5w2RFW13iyVAAIKAAN2fWkniokL72wixbcYBA');

	}
}
	}
    if($data == 'data_test8'){
        	 if(check_amount('data_test8') == false){
	       $amount = taketext($chatId,'amount');
	       $bot->sendMessage($message->getChat()->getId(), $amount);
}else{
	if(check_buy($chatId,$data) == false){
	     $title = 'nobalance';
         $no = taketext($chatId,$title);
         $bot->sendMessage($chatId, $no);
	}else{
	    $link = $success.getgoodname($data).$onegoodeight[2];
	     $path = '8goodeight';
	    
	    $pic = getgoodpic($path);
	    $bot->sendMessage($chatId, $link);
	    $bot->sendPhoto($message->getChat()->getId(), $pic);
	   $balancedollar = $balancebut.newbalance($chatId);
         $bot->sendMessage($message->getChat()->getId(), $balancedollar);
            //$bot->sendSticker($message->getChat()->getId(), 'CAACAgIAAxkBAAI-mV6J16sEtke2YyuUi5w2RFW13iyVAAIKAAN2fWkniokL72wixbcYBA');

	}
}
	}
	if($data == 'data_test9'){
	    	 if(check_amount('data_test9') == false){
	       $amount = taketext($chatId,'amount');
	       $bot->sendMessage($message->getChat()->getId(), $amount);
}else{
	if(check_buy($chatId,$data) == false){
	     $title = 'nobalance';
         $no = taketext($chatId,$title);
         $bot->sendMessage($chatId, $no);
	}else{
	    $link = $success.' 💜'.getgoodname($data).$onegoodnine[2];
	     $path = '9goodnine';
	    
	    $pic = getgoodpic($path);
	    $bot->sendMessage($chatId, $link);
	    $bot->sendPhoto($message->getChat()->getId(), $pic);
	   $balancedollar = $balancebut.newbalance($chatId);
         $bot->sendMessage($message->getChat()->getId(), $balancedollar);
              //    	$bot->sendSticker($message->getChat()->getId(), 'CAACAgIAAxkBAAI-mV6J16sEtke2YyuUi5w2RFW13iyVAAIKAAN2fWkniokL72wixbcYBA');

	}
}
	}
	
	if($data == 'data_test10'){
	   if(check_amount('data_test10') == false){
	       $amount = taketext($chatId,'amount');
	       $bot->sendMessage($message->getChat()->getId(), $amount);
}else{
	if(check_buy($chatId,$data) == false){
	     $title = 'nobalance';
         $no = taketext($chatId,$title);
         $bot->sendMessage($chatId, $no);
	}else{
	    $link = $success.' 🍯'.getgoodname($data).$onegoodten[2];
	     $path = '10goodten';
	    
	    $pic = getgoodpic($path);
	    $bot->sendMessage($chatId, $link);
	    $bot->sendPhoto($message->getChat()->getId(), $pic);
	   $balancedollar = $balancebut.newbalance($chatId);
        $bot->sendMessage($message->getChat()->getId(), $balancedollar);
        //$bot->sendSticker($message->getChat()->getId(), 'CAACAgIAAxkBAAI-mV6J16sEtke2YyuUi5w2RFW13iyVAAIKAAN2fWkniokL72wixbcYBA');

	}
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


/*Переменные базы*/

	$shopone = shopname(1);
	$shoptwo = shopname(2);
	$shopthree = shopname(3);
	$shopfour = shopname(4);
	$shopfive = shopname(5);
	$shopsix = shopname(6);
	//$shopsix = goodone(1);
	
	
	$onegoodone = goodone(1);
    $onegoodtwo = goodone(2);
	$onegoodthree = goodone(3);
	$onegoodfour = goodone(4);
	$onegoodfive = goodone(5);
	$onegoodsix = goodone(6);
	$onegoodseven = goodone(7);
	$onegoodeight = goodone(8);
	$onegoodnine = goodone(9);
	$onegoodten = goodone(10);

	$menubutton = getbutton(1);
	$privatebutton = getbutton(2);
	$change = getbutton(3);
	$balancebut = getbutton(5);
	$shops = getbutton(10);
	$operator = getbutton(11);
	$krest = '❌';
	
	if(mb_stripos($mtext,$menubutton) !==false){
	        if(id_exists($cid) == false){
	            $answer = taketext($cid,'youmust');
	                  $bot->sendMessage($message->getChat()->getId(), $answer);

	        }else{
	    
	    $keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup(
		    [
			    [
				    ['text' => '♻️ '.$shopone.'♻️'],
				    ['callback_data' => 'languages', 'text' => '🏛'.$privatebutton.'🏛']
			    ]
		    ],false,true,false,true
	    );
    
	    $bot->sendMessage($message->getChat()->getId(), $menubutton, false, null,null,$keyboard);    
	        $balancedollar = $balancebut.newbalance($cid);
      $bot->sendMessage($message->getChat()->getId(), $balancedollar);
	        }
	}
    if(mb_stripos($mtext,$privatebutton) !==false){
	$cid = $message->getChat()->getId();
	$balance = get_deposit($cid);
	$balancedollar = newbalance($cid);
	  if(id_exists($cid) == false){
	            $answer = taketext($cid,'youmust');
	                  $bot->sendMessage($message->getChat()->getId(), $answer);

	        }else{
    $username = getusername($cid);
    $deposit = 🏛.$username.'\'s ROOM🏛'.getbutton(12).'
💰'.getbutton(8). $balancedollar . getbutton(9);
    
	$bot->sendMessage($message->getChat()->getId(),$deposit);
	$bot->sendMessage($message->getChat()->getId(),$balance[0]);
		    $keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup(
		[
			[
			    ['callback_data' => 'menu', 'text' => '📜'.$menubutton.'📜'],
				['callback_data' => 'language', 'text' => '🏳'.$change]
			]
		],false,true
		);
	 $bot->sendMessage($message->getChat()->getId(), $menubutton, false, null,null,$keyboard);
	      $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
		[
			[
			    ['callback_data' => 'refresh', 'text' => '⚙️'.'Обновить']
			]
				
		],false,true
	);
	$bot->sendMessage($message->getChat()->getId(), taketext($cid,'refresh'), false, null,null,$keyboard);  
}
	}
	
	
	if(mb_stripos($mtext,$shops) !==false){
	    $keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup(
		[
			[
			    ['text' => '📜'.$menubutton.'📜'],
				['text' => '♻️ '.$shopone.'♻️'],
				['text' => $shoptwo],
			],
			[
			    ['text' => $shopthree],
				['text' => $shopfour],
				],
			[
				['text' => $shopfive],
				['text' => $shopsix]
			]
		],false,true
	);
    
	$bot->sendMessage($message->getChat()->getId(), $shops, false, null,null,$keyboard);
	
	    $balancedollar = $balancebut.newbalance($cid);
      $bot->sendMessage($message->getChat()->getId(), $balancedollar);
	

	}
	
	/* Магазин 1*/
	
		if(mb_stripos($mtext, $shopone) !== false){
		    $scobka = ')';
		    $scobka1 = ')';
		    $scobka2 = ')';
		    $scobka3 = ')';
		    $scobka4 = ')';
		    $scobka5 = ')';
		    $scobka6 = ')';
		    $scobka7 = ')';
		    $scobka8 = ')';
		    $scobka9 = ')';
		    $scobka10 = ')';
		    
		      if(id_exists($cid) == false){
	            $answer = taketext($cid,'youmust');
	                  $bot->sendMessage($message->getChat()->getId(), $answer);

	        }else{
		    if($onegoodone[3]=='0'){
		      $scobka1=$scobka1.$krest;
		    }
		     if($onegoodtwo[3]=='0'){
		      $scobka2=$scobka2.$krest;
		    }
		     if($onegoodthree[3]=='0'){
		      $scobka3=$scobka3.$krest;
		    }
		     if($onegoodfour[3]=='0'){
		      $scobka4=$scobka4.$krest;
		    }
		     if($onegoodfive[3]=='0'){
		      $scobka5=$scobka5.$krest;
		    }
		     if($onegoodsix[3]=='0'){
		      $scobka6=$scobka6.$krest;
		    }
		     if($onegoodseven[3]=='0'){
		      $scobka7=$scobka7.$krest;
		    }
		     if($onegoodeight[3]=='0'){
		      $scobka8=$scobka8.$krest;
		    }
		     if($onegoodnine[3]=='0'){
		      $scobka9=$scobka9.$krest;
		    }
		     if($onegoodten[3]=='0'){
		      $scobka10=$scobka10.$krest;
		    }
		    
			$keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
		[
			[
				['callback_data' => 'yes1', 'text' => '🐊'.$onegoodone[1].$onegoodone[2].'  ('.$onegoodone[3].$scobka1]
			],
			[
				['callback_data' => 'yes2', 'text' => '🍪'.$onegoodtwo[1].$onegoodtwo[2].'  ('.$onegoodtwo[3].$scobka2]
			],
			[
				['callback_data' => 'yes3', 'text' =>'🍞'. $onegoodthree[1].$onegoodthree[2].'  ('.$onegoodthree[3].$scobka3]
			],
			[
				['callback_data' => 'yes4', 'text' => $onegoodfour[1].$onegoodfour[2].'  ('.$onegoodfour[3].$scobka4]
			],
			[
				['callback_data' => 'yes5', 'text' => '💎'.$onegoodfive[1].$onegoodfive[2].'  ('.$onegoodfive[3].$scobka5]
			],
			[
				['callback_data' => 'yes6', 'text' => '💊'.$onegoodsix[1].$onegoodsix[2].'  ('.$onegoodsix[3].$scobka6]
			],
			[
				['callback_data' => 'yes7', 'text' => '🌈 '.$onegoodseven[1].$onegoodseven[2].'  ('.$onegoodseven[3].$scobka7]
			],
			[
				['callback_data' => 'yes8', 'text' => $onegoodeight[1].$onegoodeight[2].'  ('.$onegoodeight[3].$scobka8]
			],
			[
			     ['callback_data' => 'yes9','text' => '💜'.$onegoodnine[1].$onegoodnine[2].'    ('.$onegoodnine[3].$scobka9]    
			],
				[
			     ['callback_data' => 'yes10','text' => '🍯'.$onegoodten[1].$onegoodten[2].'    ('.$onegoodten[3].$scobka10]    
			],
			
			
			
		],false,true
	);
	$bot->sendSticker($message->getChat()->getId(), 'CAACAgIAAxkBAAI-jF6J1rPiFqcweUuyaAeTWDM29sx9AAIYAAN2fWknoF5cS5PuwXUYBA');
	$bot->sendMessage($message->getChat()->getId(), '♻️'.$shopone.'♻️', false, null,null,$keyboard);
	$keyboard2 = new \TelegramBot\Api\Types\ReplyKeyboardMarkup(
		    [
			    [
				    ['callback_data' => 'shops', 'text' => '📜'.$menubutton.'📜']
			    ],
			     [
			        ['callback_data' => 'operator', 'text' => '✉️'.$operator]
			     ]
		    ],false,true
	    );
    
	    $bot->sendMessage($message->getChat()->getId(), '📜'.$menubutton.'📜', false, null,null,$keyboard2);
	        $balancedollar = $balancebut.newbalance($cid);
      $bot->sendMessage($message->getChat()->getId(), $balancedollar);
	        }   
	}


	/*Связь с оператором*/
	
	if(mb_stripos($mtext, $operator) !== false){
	     $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
		[
			[
			    ['url' => 'tg://resolve?domain=@trup8888', 'text' => '✉️'.$operator]
			]
				
		],false,true
	);
	$bot->sendSticker($message->getChat()->getId(), 'CAACAgIAAxkBAAI-lV6J10G4ZRRVHyV23WS6TNdUVqRAAAIoAAN2fWknUUejjeM71dYYBA');
	$bot->sendMessage($message->getChat()->getId(), taketext($cid,'operator'), false, null,null,$keyboard);  
	}
	
	
	/* Поменять язык*/
	if(mb_stripos($mtext, $change) !== false){
	    
	    	    $keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup(
		[
			[
			    ['callback_data' => 'alb', 'text' => '🇦🇲' ],
			    ['callback_data' => 'eng', 'text' => '🇷🇺'],
			    ['callback_data' => 'rus', 'text' => '🇺🇸']
			],
			[
			   ['callback_data' => 'meny', 'text' => '🏛'.$privatebutton.'🏛']
			]
		],false,true
	);
    
	$bot->sendMessage($message->getChat()->getId(), $change, false, null,null,$keyboard);
	    
	}
	
	if(mb_stripos($mtext, "🇺🇸") !==false){
	    $lang = 'en';
	    changelang($cid,$lang);
	    
	    $title = taketext($cid,'changelang');
	    $bot->sendMessage($message->getChat()->getId(), $title);
	}
	if(mb_stripos($mtext, "🇷🇺") !==false){
	    $lang = 'ru';
	    changelang($cid,$lang);
	    $title = taketext($cid,'changelang');
	    $bot->sendMessage($message->getChat()->getId(), $title);
	}
	if(mb_stripos($mtext, "🇦🇲") !==false){
	    $lang = 'alb';
	    changelang($cid,$lang);
	    $title = taketext($cid,'changelang');
	    $bot->sendMessage($message->getChat()->getId(), $title);
	}
	

	
	if(mb_stripos($mtext, "English") !== false){
	    $message = $Update->getMessage();
        $chatId = $message->getChat()->getId();    
        $lang = 'en';
        lang($chatId,$lang);
        
        $answer = taketext($chatId,'welcomemes');
    $bot->sendMessage($message->getChat()->getId(), $answer);
         $keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup(
		    [
			    [
				    ['text' => '♻️ '.$shopone.'♻️'],
				    ['callback_data' => 'languages', 'text' => '🏛'.$privatebutton.'🏛']
			    ]
		    ],false,true,false,true
	    );
    
	    $bot->sendMessage($message->getChat()->getId(), '📜'.$menubutton.'📜', false, null,null,$keyboard);    
	}
	if(mb_stripos($mtext, "Русский") !== false){
	    $message = $Update->getMessage();
        $chatId = $message->getChat()->getId();    
        $lang = 'ru';
        lang($chatId,$lang);
        $answer = taketext($chatId,'welcomemes');

    $bot->sendMessage($message->getChat()->getId(), $answer);
 $keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup(
		    [
			    [
				    ['text' => '♻️ '.$shopone.'♻️'],
				    ['callback_data' => 'languages', 'text' => '🏛'.$privatebutton.'🏛']
			    ]
		    ],false,true,false,true
	    );
    
	    $bot->sendMessage($message->getChat()->getId(), '📜'.$menubutton.'📜', false, null,null,$keyboard);    
	}
	if(mb_stripos($mtext, "Հայերեն") !== false){
	    $message = $Update->getMessage();
        $chatId = $message->getChat()->getId();    
        $lang = 'alb';
        lang($chatId,$lang);
        
               $answer = taketext($chatId,'welcomemes');

    $bot->sendMessage($message->getChat()->getId(), $answer);
    $pic = "https://mbw.best/bitcoinbot/assets/gif
    pic.gif";
    $bot->sendPhoto($message->getChat()->getId(), $pic);
        $keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup(
		    [
			    [
				    ['text' => '♻️ '.$shopone.'♻️'],
				    ['callback_data' => 'languages', 'text' => '🏛'.$privatebutton.'🏛']
			    ]
		    ],false,true,false,true
	    );
    
	    $bot->sendMessage($message->getChat()->getId(), '📜'.$menubutton.'📜', false, null,null,$keyboard);    
      

	}
	
	
	if(mb_stripos($mtext,"чупачупс") !== false){
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