<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors','on');
ini_set('error_log', './bot_error.log');
include "vk_api.php"; 

const VK_KEY = "Токеен";  
const ACCESS_KEY = "Код подтверждение "; 
const VERSION = "5.81";
$sql = new sql();
$vk = new vk_api(VK_KEY, VERSION); 
$mc_api = new mega_api();
$data = json_decode(file_get_contents('php://input')); 
if ($data->type == 'confirmation') { 
	exit(ACCESS_KEY); 
}
$vk->sendOK();
// ====== [ ' Переменные ' ] ============

$peer_id = $data->object->peer_id;
$id = $data->object->from_id; 
$message = $data->object->text;
$cmd = explode(" ", $message);
$message_id = $data->object->conversation_message_id;
$msg = $data->object->id;
$date_today = date("m.d.y"); //присвоено даты и год
date_default_timezone_set("Europe/Moscow");
$msk = date('H:i:s');

$userInfo = $vk->request("users.get", ["user_ids" => $id]);
$userName = $userInfo[0]['first_name']; 
$userSurName = $userInfo[0]['last_name']; 

$settings = $db->query("SELECT * FROM `psettings` WHERE `peer_id` = '$peer_id'")->fetch_assoc_array();

$rpsell = $db->query("SELECT * FROM `psettings` WHERE `peer_id` = '$peer_id'")->fetch_assoc()['rpsell'];

$lvl5 = $db->query("SELECT * FROM `psettings` WHERE `peer_id` = '$peer_id'")->fetch_assoc()['5lvl'];
$lvl4 = $db->query("SELECT * FROM `psettings` WHERE `peer_id` = '$peer_id'")->fetch_assoc()['4lvl'];
$lvl3 = $db->query("SELECT * FROM `psettings` WHERE `peer_id` = '$peer_id'")->fetch_assoc()['3lvl'];
$lvl2 = $db->query("SELECT * FROM `psettings` WHERE `peer_id` = '$peer_id'")->fetch_assoc()['2lvl'];
$lvl1 = $db->query("SELECT * FROM `psettings` WHERE `peer_id` = '$peer_id'")->fetch_assoc()['1lvl'];

file_put_contents("settings", var_export($settings,true));

$userSay = "@id".$id."(".$userName." ".$userSurName.")";
$userError = "@id".$id."(⚠)";
//$userError = "&#10060;";
//$userSuc = "@id".$id."(&#9989;)";

$id_bot = '-214582945';
$id_general = 384904677;

$userSuc = "&#9989;";
$who_i = ['!Кто я', '!кто Я', '!Кто Я','!КТО Я','!кто я','!О Себе','!о Себе', '!О себе', '!о себе',];
$rulesa = ['!установить правила', '!Установить правила', '!Установить Правила','!установить Правила','!Установить Правил','!Уст Правила', '!Новые Правила', '!новые правила',];
$server = ['!сервер', '!Сервер', 'сервер', 'Сервер',];
$help_me = ['!помощь', '!Помощь', '!меню', '!Меню'];
$mate = ['!помощь', '!Помощь', '!меню', '!Меню'];

$prefix_system = '[&#128172;] SYSTEM: ';


// ====== [' defines '] ========================
define('ERRORS', array(
    ''.$userError.' отсутсвует доступ к команде!'
), true);
define('ARGS', array(
	''.$userError.' отсутствует аргумент {message_fwd}',
	''.$userError.' отсутствует аргумент {id}'
), true);
define('TIPS', array(
	'&#128295;Подсказка: Вы знали, что можно указать причину предупреждения?, для этого напишите сообщение с переносом строки, на след. строке пишите причину ?',
	'&#128295;Подсказка: Для остановки  цикла используйте: !уциклстоп {PID}'
),true);
// ====== [' Когда кто-то написал '] ============
if ($data->type == 'message_new' && $message != '') {
	//$vk->sendMessage($peer_id, var_export($data,true));
	if($peer_id < 2000000000 && $id != $id_general && $message != '!стат'){
		$vk->sendMessage($peer_id, "");
		return false;
	}
	if($peer_id == $id_general && $message == '!стат'){
		
		$date = date('Y.m.d');
		$peers = $db->query("SELECT * FROM `psettings`")->getNumRows();
		$warns = $db->query("SELECT * FROM `warns`")->getNumRows();
		$notes = $db->query("SELECT * FROM `notes`")->getNumRows();
		$clans = $db->query("SELECT * FROM `clan`")->getNumRows();
		$users = $db->query("SELECT * FROM `users_settings`")->getNumRows();
		$users_in_peer = $db->query("SELECT * FROM `users`")->getNumRows();
		$is_admins = $db->query("SELECT SUM(messages) FROM `users`")->fetch_assoc()['SUM(messages)'];
		$rait_all = $db->query("SELECT SUM(reiting_all) FROM `users_settings`")->fetch_assoc()['SUM(reiting_all)'];
		$rait_in_peer = $db->query("SELECT SUM(reiting) FROM `users`")->fetch_assoc()['SUM(reiting)'];
		return $vk->sendMessage($peer_id, "Собираю статистику за $date\n\nВсего зарегистрировано бесед: $peers\nВсего варнов: $warns\nСоздано заметок: $notes\n
		Всего кланов: $clans\nЗарегистрировано: $users(В беседах: $users_in_peer)\nВсего сообщений ~($is_admins)\nВсего рейтинга $rait_all($rait_in_peer)");
	}
	 if($message != '!регалл' && $settings[0]['active'] == 0 && $message != '!dev'){
		$vk->sendMessage($peer_id, "");	
		return $vk->sendMessage($peer_id, "$userError беседа не активирована, введите !регалл");
	}
	
	

	$sql->countMessage($db, $id, $peer_id);
	if ($message == '!бот') {
		if($sql->isBotCmd($db, $peer_id) == false){
				return $vk->sendMessage($peer_id, "");
			}
			
			if($sql->isBanCmd($db, $id) == false){
				return $vk->sendMessage($peer_id, "$userError Вы заблокированы в Robert\n Чтобы узнать подробнее напиши в !репорт");
			}
		
		
		$rpbalance = $db->query("SELECT * FROM `psettings` WHERE `peer_id` = '$peer_id'")->fetch_assoc()['balance'];
		$start = microtime(true);
		$time = microtime(true) - $start;
		$vk->sendMessage($peer_id, " &#9203; Мой ответ: задержки $time сек. \n &#9888; Беседа $peer_id \n &#128184; RP Баланс: $rpbalance \n &#128204; Ваш id: $id");     
	}
		
	if ($message == '!co') {
			
			
		if($sql->isBotCmd($db, $peer_id) == false){
				return $vk->sendMessage($peer_id, "");
			}
			
			if($sql->isBanCmd($db, $id) == false){
				return $vk->sendMessage($peer_id, "$userError Вы заблокированы в Robert\n Чтобы узнать подробнее напиши в !репорт");
			}
			
		
		               	
			
			//if($sql->isAdm1CMD($db, $peer_id) == true){
			//	return $vk->sendMessage($peer_id, "$userError Создателем все в порядке");
		//	}
			
		
			
			if($settings[0]['vlad'] == 1){
			return $vk->sendMessage($peer_id, "$userError Создатель уже выдан!\n Это не так?\n !репорт в помощь.");
		}
		$db->query("UPDATE `psettings` SET `vlad` = 1 WHERE peer_id = $peer_id");
	 $test = $vk->request('messages.getConversationMembers', ['peer_id' => $peer_id]);
		$tester = $test["items"][0]["member_id"];
		               	
	
	
		$db->query("INSERT INTO `base`.`admin` (`id`, `vk_id`, `peer_id`, `adminN`, `adminSN`, `type`) VALUES (NULL, '$tester', '$peer_id', 'Admin', 'Adminov', '5')");
		$vk->sendMessage($peer_id, " Создатель создан @id$tester");  
               

			   }
		
  
	
	
	else if ($message == '!адмлист' || $message == '!staff' || $message == '!админы'){
		if($sql->isBotCmd($db, $peer_id) == false){
				return $vk->sendMessage($peer_id, "");
			}
			
			if($sql->isBanCmd($db, $id) == false){
				return $vk->sendMessage($peer_id, "$userError Вы заблокированы в Robert\n Чтобы узнать подробнее напиши в !репорт");
			}
		
		$symbol = "";
		$is_admins = $db->query("SELECT * FROM admin WHERE peer_id = $peer_id ORDER BY type DESC"); // Получаем данные из колонки vk_id
		while ($row = $is_admins->fetch_assoc()) { // Запускаем цикл
			switch($row['type']){
				case 1:
					$symbol = "&#11088; {$lvl1}";
					break;
				case 2:
					$symbol = "&#11088;&#11088; {$lvl2}";
					break;
				case 3:
					$symbol = "&#11088;&#11088;&#11088;; {$lvl3}";
					break;
				case 4:
					$symbol = "&#11088;&#11088;&#11088;&#11088; {$lvl4}";
					break;
				case 5:
					$symbol = "&#11088;&#11088;&#11088;&#11088;&#11088; {$lvl5}";
					break;
			}
			$admN = $row['adminN'];
			$admSN = $row['adminSN'];
			$types = $row['type'];
			$admID = $row['vk_id'];
			$is_adminss .= "$symbol \n @id$admID($admN $admSN) \n";
			++$n;
		}
		$vk->sendMessage($peer_id, "В беседе [$n] Администраторов:\n$is_adminss", true);
	}
	
	else if(mb_substr($message,0,9) == '!расчетмк' || mb_substr($message,0,9) == '!расчётмк'){
		if($sql->isBotCmd($db, $peer_id) == false){
				return $vk->sendMessage($peer_id, "");
			}
			
			if($sql->isBanCmd($db, $id) == false){
				return $vk->sendMessage($peer_id, "$userError Вы заблокированы в Robert\n Чтобы узнать подробнее напиши в !репорт");
			}
		$onto = mb_substr($message,10);
		$onto = (int)$onto;
		if($onto == '' || $onto < 0 || $onto > 100000){$onto = 1;}
		$duels = $onto*10;
		$other = $onto*50;
		$sd = $onto*15;
		$vk->sendMessage($peer_id, "Ваш множитель \"$onto\"\nВы получите $duels MegaCoins за победу на \"Дуэлях,MurderMystery\"\n
		Вы получите $sd MegaCoins за победу на \"TheBridge\"\n
		Вы получите $other MegaCoins за победу на \"BedWars, SkyWars, EggWars, Annihilation\"");
	}
	
	else if($cmd[0] == '!ген'){
		if($sql->isBotCmd($db, $peer_id) == false){
				return $vk->sendMessage($peer_id, "");
			}
			
			if($sql->isBanCmd($db, $id) == false){
				return $vk->sendMessage($peer_id, "$userError Вы заблокированы в Robert\n Чтобы узнать подробнее напиши в !репорт");
			}
		
		
		if(isset($cmd[1]) == FALSE){return $vk->sendMessage($peer_id, "$userError отсутствует аргумент {param}\nВозможные аргументы: \"пароль\"\nТак-же можно указать длину(необязательно): \"!ген пароль {длина}\"");}
		switch($cmd[1]){
			case 'пароль':
					if(isset($cmd[2]) == FALSE){$number = 7;}else{if((int)$cmd[2] > 300 || (int)$cmd[2] < 7){return $vk->sendMessage($peer_id, "$userError это куда такой пароль-то?");} $number = (int)$cmd[2];}
					$arr = array('a','b','c','d','e','f',
	                 'g','h','i','j','k','l',
	                 'm','n','o','p','r','s',
	                 't','u','v','x','y','z',
	                 'A','B','C','D','E','F',
	                 'G','H','I','J','K','L',
	                 'M','N','O','P','R','S',
	                 'T','U','V','X','Y','Z',
	                 '1','2','3','4','5','6',
	                 '7','8','9','0');
				    // Генерируем пароль
				    $pass = "";
				    for($i = 0; $i < $number; $i++)
				    {
				      // Вычисляем случайный индекс массива
				      $index = rand(0, count($arr) - 1);
				      $pass .= $arr[$index];
				    }
				    $vk->sendMessage($peer_id, "$userSuc Пароль сгенерирован и был отправлен вам в Личные Сообщения");
				    $vk->sendMessage($id, "ПаролЬ: $pass");
				break;
			default:
				$vk->sendMessage($peer_id, "$userError аргумент {param} не найден!\nВозможные аргументы: \"пароль\"\nТак-же можно указать длину (необязательно): \"!ген пароль {длина}\"");
				break;
		}
	}
	else if($message == '!мегаонл'){
		if($sql->isBotCmd($db, $peer_id) == false){
				return $vk->sendMessage($peer_id, "");
			}
			
			if($sql->isBanCmd($db, $id) == false){
				return $vk->sendMessage($peer_id, "$userError Вы заблокированы в Robert\n Чтобы узнать подробнее напиши в !репорт");
			}
		
		
		$data = file_get_contents("https://aurhost.ru");
		$exit = explode(PHP_EOL, $data);
		$vk->sendMessage($peer_id, strip_tags($exit[44]));
	}
	else if ($message == '!настройки'){
		
		if($sql->isBanCmd($db, $id) == false){
				return $vk->sendMessage($peer_id, "$userError Вы заблокированы в Robert\n Чтобы узнать подробнее напиши в !репорт");
			}
		
		$ak = $settings[0]['autokick'];
		$af = $settings[0]['reiting'];
		$stat = $settings[0]['status'];
		$checked = $settings[0]['checked'];
		$robertpay = $settings[0]['robertpay'];
		switch ($stat) {
		    case "default":
		        $stat = "Стандарт";
		        break;
		    case "vip":
		        $stat = "VIP";
		        break;
		    case "Robert":
		        $stat = "Robert class";
		        break;
		    case "off":
		        $stat = "Официальная";
		        break;
		    default:
		    	$stat = "Не подвержденный";
		    	break;
		}

		//$funs_cmd = ($fun == 0) ? "&#128279;Развлекательные команды: Включены" : "&#128279;Развлекательные команды: Отключены"; 
		$auto_kick = ($ak == 1) ? "&#128682;Авто-Кик: Включен" : "&#128682;Авто-Кик: Отключен";
		$reiting = ($af == 1) ? "&#9851;Система Рейтинга: Включена" : "&#9851;Система рейтинга: Отключена";
		$tips = ($sql->isTipOn($db,$peer_id) == true) ? "&#128295;Подсказки: Отключены" : "&#128295;Подсказки: Включены";
		$robertpay1 = ($robertpay == 1) ? "&#128688;Robert PAY: Доступна" : "&#128689;Robert PAY: Недоступна";
		$invite = ($settings[0]['invite_bot']) ? "&#129302;Приглашение других ботов: Разрешено" : "&#129302;Приглашение других ботов: Запрещено";
		$bots1 = ($settings[0]['bot']) ? "&#129302;Работоспособность бота Включена" : "&#129302;Работоспособность бота Выключена";
		return $vk->sendMessage($peer_id, "&#9881; Настройки беседы: \n\n&#127941;Статус беседы: $stat\n $auto_kick \n $reiting \n $tips \n $invite\n  $robertpay1 \n $bots1");
	}
	else if(in_array(mb_substr($message, 0, 5), $help_me) or in_array(mb_substr($message, 0, 8), $help_me)){
		if($sql->isBotCmd($db, $peer_id) == false){
				return $vk->sendMessage($peer_id, "");
			}
			
			if($sql->isBanCmd($db, $id) == false){
				return $vk->sendMessage($peer_id, "$userError Вы заблокированы в Robert\n Чтобы узнать подробнее напиши в !репорт");
			}
		
		
		$vk->sendMessage($peer_id, '&#128174; Список команд бота доступен по ссылке: vk.com/topic-214582945_48954737');
	}
	
	else if(in_array(mb_substr($message, 0, 5), $server) or in_array(mb_substr($message, 0, 8), $server)){
		if($sql->isBotCmd($db, $peer_id) == false){
				return $vk->sendMessage($peer_id, "");
			}
		
		if($sql->isBanCmd($db, $id) == false){
				return $vk->sendMessage($peer_id, "$userError Вы заблокированы в Robert\n Чтобы узнать подробнее напиши в !репорт");
			}
		
		$vk->sendMessage($peer_id, 'Robert | Чат менеджер\nДата-Центр с уровнем надежности Tier 3+\nНаша личная оборудование SuperMicro \n SSD диски Enterprise класса RAID10 \n 1000 GB\s Интернет\n ');
	}
	
	else if(mb_substr($message,0,16) == '!создать заметку'){
		if($sql->isBotCmd($db, $peer_id) == false){
				return $vk->sendMessage($peer_id, "");
			}
			
			if($sql->isBanCmd($db, $id) == false){
				return $vk->sendMessage($peer_id, "$userError Вы заблокированы в Robert\n Чтобы узнать подробнее напиши в !репорт");
			}
		
		
		$param = mb_substr($message,17);
		if($param == '' || strlen($param) < 1 || strlen($param) > 250){$vk->sendMessage($peer_id, "$userError ошибка не был указан описание заметки, описание указано: 0");}
		
		
		$db->query("INSERT INTO `notes`(`user_id`, `text`) VALUES ('$id', '$param')");
		$note_id = $db->query("SELECT * FROM `notes` WHERE `user_id` = '$id' AND `text` = '$param'")->fetch_assoc()['id'];
		return $vk->sendMessage($peer_id, "$userSuc успешно создана заметка ID « $note_id »\n\n&#128466;Чтобы просмотреть заметки, введите « !заметки », чтобы просмотреть данную заметку, введите « !заметка $note_id »");
	}
	else if(mb_substr($message,0,16) == '!удалить заметку'){
		if($sql->isBotCmd($db, $peer_id) == false){
				return $vk->sendMessage($peer_id, "");
			}
			
			if($sql->isBanCmd($db, $id) == false){
				return $vk->sendMessage($peer_id, "$userError Вы заблокированы в Robert\n Чтобы узнать подробнее напиши в !репорт");
			}
		
		
		$param = mb_substr($message,17);
		if($param == ''){$vk->sendMessage($peer_id, "$userError Вы не указали ID, Доступные заметки !заметки");}
		
		if($db->query("SELECT * FROM `notes` WHERE `user_id` = '$id' AND `id` = '$param'")->getNumRows()){
			$db->query("DELETE FROM `notes` WHERE `user_id` = '$id' AND `id` = '$param'");
			return $vk->sendMessage($peer_id, "&#128466;Заметка [ $param ]\n$userSuc была удалена");
		}else{
			return $vk->sendMessage($peer_id, "$userError ошибка, заметка по ID: $param не существует");
		}
	}
	else if(mb_substr($message,0,8) == '!заметка'){
		if($sql->isBotCmd($db, $peer_id) == false){
				return $vk->sendMessage($peer_id, "");
			}
			
			if($sql->isBanCmd($db, $id) == false){
				return $vk->sendMessage($peer_id, "$userError Вы заблокированы в Robert\n Чтобы узнать подробнее напиши в !репорт");
			}
		
		
		$param = mb_substr($message,9);
		if($param == ''){$vk->sendMessage($peer_id, "$userError Вы не указали ID, Доступные заметки !заметки");}
		
		if($db->query("SELECT * FROM `notes` WHERE `user_id` = '$id' AND `id` = '$param'")->getNumRows()){
			$text = $db->query("SELECT * FROM `notes` WHERE `user_id` = '$id' AND `id` = '$param'")->fetch_assoc()['text'];
			return $vk->sendMessage($peer_id, "&#128466;Заметка [ $param ]\n« $text »");
		}else{
			return $vk->sendMessage($peer_id, "$userError ошибка, заметка ID $param отсутствует");
		}
	}
	else if($message == '!заметки'){
		if($sql->isBotCmd($db, $peer_id) == false){
				return $vk->sendMessage($peer_id, "");
			}
			
			if($sql->isBanCmd($db, $id) == false){
				return $vk->sendMessage($peer_id, "$userError Вы заблокированы в Robert\n Чтобы узнать подробнее напиши в !репорт");
			}
		
		
		$quest = $db->query("SELECT * FROM `notes` WHERE `user_id` = '$id'");
		while($row = $quest->fetch_assoc()){
			$text = mb_substr($row['text'], 0,20);
			$text = "$text...";
			$ids = $row['id'];
			$notes .= "&#9197;Заметка: « $ids » $text \n";
			++$i;
		}
		return $vk->sendMessage($peer_id, "&#128466;Ваши заметки [$i]\n\n$notes");
	}
	else if(mb_substr($message,0,4) == '!баг'){
	
	if($sql->isBanCmd($db, $id) == false){
				return $vk->sendMessage($peer_id, "$userError Вы заблокированы в Robert\n Чтобы узнать подробнее напиши в !репорт");
			}
		
		
		$param1 = mb_substr($message,5);
		if($param1==''){
			return $vk->sendMessage($peer_id,"$userError я не могу отослать пустое сообщение");
		}
		$profiles = ['384904677' ];
		foreach($profiles as $p){
			$send = "@id$id($userName $userSurName)";
			$vk->sendMessage($p, "Внимание, заявка на баг!\nПрислали из беседы: $peer_id\nПрислал: $send\n\nТекст: $param1");
		}
		return $vk->sendMessage($peer_id, "$userSuc успешно отправлено на обработку!");
	}
	
	else if(mb_substr($message,0,5) == '!5lvl'){
	if($sql->isUserAdminLevel($db, $id, $peer_id, 5) != true)
		{
			return $vk->sendMessage($peer_id, ERRORS[0]);
		}
		
		$param1 = mb_substr($message,5);
		if($param1==''){
			return $vk->sendMessage($peer_id,"$userError Текст для 5LVL отсутствует");
		}
		$db->query("UPDATE `psettings` SET `5lvl` = '$param1' WHERE `peer_id` = '$peer_id'");
		
		return $vk->sendMessage($peer_id, "$userSuc Теперь 5 LVL называется: $param1");
	}
	
	else if(mb_substr($message,0,5) == '!4lvl'){
	if($sql->isUserAdminLevel($db, $id, $peer_id, 5) != true)
		{
			return $vk->sendMessage($peer_id, ERRORS[0]);
		}
		
		$param1 = mb_substr($message,5);
		if($param1==''){
			return $vk->sendMessage($peer_id,"$userError Текст для 4LVL отсутствует");
		}
		$db->query("UPDATE `psettings` SET `4lvl` = '$param1' WHERE `peer_id` = '$peer_id'");
		
		return $vk->sendMessage($peer_id, "$userSuc Теперь 4 LVL называется: $param1");
	}
	
	else if(mb_substr($message,0,5) == '!3lvl'){
	if($sql->isUserAdminLevel($db, $id, $peer_id, 5) != true)
		{
			return $vk->sendMessage($peer_id, ERRORS[0]);
		}
		
		$param1 = mb_substr($message,5);
		if($param1==''){
			return $vk->sendMessage($peer_id,"$userError Текст для 3LVL отсутствует");
		}
		$db->query("UPDATE `psettings` SET `3lvl` = '$param1' WHERE `peer_id` = '$peer_id'");
		
		return $vk->sendMessage($peer_id, "$userSuc Теперь 3 LVL называется: $param1");
	}
	
	else if(mb_substr($message,0,5) == '!2lvl'){
	if($sql->isUserAdminLevel($db, $id, $peer_id, 5) != true)
		{
			return $vk->sendMessage($peer_id, ERRORS[0]);
		}
		
		$param1 = mb_substr($message,5);
		if($param1==''){
			return $vk->sendMessage($peer_id,"$userError Текст для 2LVL отсутствует");
		}
		$db->query("UPDATE `psettings` SET `2lvl` = '$param1' WHERE `peer_id` = '$peer_id'");
		
		return $vk->sendMessage($peer_id, "$userSuc Теперь 2 LVL называется: $param1");
	}
	
	
	else if(mb_substr($message,0,5) == '!1lvl'){
	if($sql->isUserAdminLevel($db, $id, $peer_id, 5) != true)
		{
			return $vk->sendMessage($peer_id, ERRORS[0]);
		}
		
		$param1 = mb_substr($message,5);
		if($param1==''){
			return $vk->sendMessage($peer_id,"$userError Текст для 1LVL отсутствует");
		}
		$db->query("UPDATE `psettings` SET `1lvl` = '$param1' WHERE `peer_id` = '$peer_id'");
		
		return $vk->sendMessage($peer_id, "$userSuc Теперь 1 LVL называется: $param1");
	}
	
	else if(mb_substr($message,0,3) == '!rp'){
		if($sql->isRobPay($db, $peer_id) == false){
				return $vk->sendMessage($peer_id, "$userError система  Robert Pay Отключена!");
			}
		$param1 = mb_substr($message,4);
		if($param1==''){
			return $vk->sendMessage($peer_id,"$userError Не указана сумма");
		}
    $sign = md5('RUB:'.$param1.':'.$secret_key.':'.$shop_id.':'.$peer_id.'');
	$sign1 = ('RUB:'.$sum.':'.$secret_key.':'.$shop_id.':'.$peer_id.':'.$id.'');
    $url = "https://anypay.io/merchant?merchant_id=$shop_id&amount=$param1&pay_id=$peer_id&sign=$sign&desc=$id";
    $vk->sendMessage($peer_id, "$rpsell\n $url\n");
	$db->query("INSERT INTO RP (peer_id, user_id, summa, data, oplata, sign, sign1) values('$peer_id', '$id', '$param1', '$date_today+$msk', '0', '$sign', '$sign1')"); // Получаем данные из колонки vk_id
		
		
	}
	
	else if(mb_substr($message,0,7) == '!arp'){
	if($sql->isUserAdminLevel($db, $id, $peer_id, 5) != true)
		{
			return $vk->sendMessage($peer_id, ERRORS[0]);
		}
		
		$param1 = mb_substr($message,7);
		if($param1==''){
			return $vk->sendMessage($peer_id,"$userError Текст для Rober Pay отсутствует");
		}
		$db->query("UPDATE `psettings` SET `rpsell` = '$param1' WHERE `peer_id` = '$peer_id'");
		
		return $vk->sendMessage($peer_id, "$userSuc Теперь Текст для пожертвований называется: $param1");
	}
	
	else if (mb_substr($message,0,6) == '!админ'){
		if($sql->isBotCmd($db, $peer_id) == false){
				return $vk->sendMessage($peer_id, "");
			}
		
		
		if($sql->isUserAdmin($db, $id, $peer_id))
		{
			if($sql->isUserAdminLevel($db, $id, $peer_id, '4') == true)
			{
				$param = mb_substr($message,7);
				$param = (int)$param;
				if($param == '' && $param != 0 || $param > 4 ){
					return $vk->sendMessage($peer_id, "$userError возникла ошибка синтаксиса [param1] $param");
				}else{
					if(strpos($message,"\n") !== FALSE){
						$pass = explode("\n", $message);
						$getID = explode("|", mb_substr($pass[1], 3))[0];
						if($getID == $id || $getID == $id_bot){return false;}
						switch ($param) {
						    case 0:
						    	if($sql->isUserAdminLevel($db, $getID, $peer_id, '4') == true && $sql->isUserAdminLevel($db, $id, $peer_id, '5') != true){
						    		return $vk->sendMessage($peer_id, "$userError вы равны или он старше");
						    	}
						        $result = $db->query("DELETE FROM `admin` WHERE `peer_id` = '$peer_id' AND `vk_id` = '$getID'");
						        if($result)
						        {
						        	return $vk->sendMessage($peer_id, "$userSuc пользователь снят с прав администратора!");
						        }else{
						        	return $vk->sendMessage($peer_id, "$userError пользователь не администратор!");
						        }
						        break;
							default:
								if($sql->isUserAdminLevel($db, $getID, $peer_id, '4') == true && $sql->isUserAdminLevel($db, $id, $peer_id, '5') != true){
						    		return $vk->sendMessage($peer_id, "$userError вы равны или он старше");
						    	}
						        if($sql->isUserAdminLevel($db, $getID, $peer_id, '1') == true){
						        	$db->query("UPDATE `admin` SET `type` = '$param' WHERE `peer_id` = '$peer_id' AND `vk_id` = '$getID'");
						        	return $vk->sendMessage($peer_id, "$userSuc пользователь повышен\понижен в правах Администратора!");
						        }else{
						        	$kicked = $vk->request("users.get", ["user_ids" => $getID]);
									$kickedName = $kicked[0]['first_name']; 
									$kickedSurName = $kicked[0]['last_name']; 
						        	$db->query("INSERT INTO `admin`(`vk_id`, `peer_id`, `adminN`, `adminSN`, `type`) VALUES ('$getID', '$peer_id', '$kickedName','$kickedSurName','$param')");
						        	return $vk->sendMessage($peer_id, "$userSuc пользователь стал Администратором!!");
						        }
								break;
						}
					}else{
						return $vk->sendMessage($peer_id, ARGS[1]);
					}
				}
			}
			else
			{
				$vk->sendMessage($peer_id, ERRORS['0']);
			}
		}
		else
		{
			$vk->sendMessage($peer_id, ERRORS['0']);
		}
	}
	else if (mb_substr($message,0,5) == '!инфо'){
		if($sql->isBotCmd($db, $peer_id) == false){
				return $vk->sendMessage($peer_id, "");
			}
		
		if($sql->isBanCmd($db, $id) == false){
				return $vk->sendMessage($peer_id, "$userError Вы заблокированы в Robert\n Чтобы узнать подробнее напиши в !репорт");
			}
		
		$check = $data->object->fwd_messages[0]->from_id;
		if($check) 
		{
			if($check == $id){
				return $vk->sendMessage($peer_id, "$userError используйте команду '!кто я' ");
			}
			if($check == '-214582945'){
				return false;
			}
			$result = $sql->userInfo($db, $vk, $check, $peer_id, 'you');
			return $vk->sendMessage($peer_id, "$result");
		}else {
			$getId = mb_substr($message ,6);
			$getId = explode("|", mb_substr($getId, 3))[0];
			if($getId == "")
			{
				return $vk->sendMessage($peer_id, "$userError вы забыли указать аргумент {ID \ Пересланное сообщение}");
			} 
			else 
			{
				if($getId == $id){
					return $vk->sendMessage($peer_id, "$userError используйте команду '!Кто я'");
				}
				if($getId == '-214582945'){
					return false;
				}
				$result = $sql->userInfo($db, $vk, $getId, $peer_id, 'you');
				return $vk->sendMessage($peer_id, "$result");
			}
		}
	}
	else if($message == '!post'){
		if( $curl = curl_init() ) {
	    	curl_setopt($curl, CURLOPT_URL, 'http://goodbuy852.tmweb.ru/get.php');
		    curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
		    curl_setopt($curl, CURLOPT_POST, true);
		    curl_setopt($curl, CURLOPT_POSTFIELDS, "post=".var_export($data,true));
		    $out = curl_exec($curl);
		    curl_close($curl);
		}
		$vk->sendMessage($peer_id, "Запрос отправлен, ответ сервера \"$out\"");
	}
	else if (mb_strtolower($cmd[0]) == '!кик' || mb_strtolower($cmd[0]) == '!kick' || mb_strtolower($cmd[0]) == '!кикнуть'){
		if($sql->isBotCmd($db, $peer_id) == false){
				return $vk->sendMessage($peer_id, "");
			}
			
			if($sql->isBanCmd($db, $id) == false){
				return $vk->sendMessage($peer_id, "$userError Вы заблокированы в Robert\n Чтобы узнать подробнее напиши в !репорт");
			}
		
		
		if($sql->isUserAdmin($db, $id, $peer_id))
		{
			if(!isset($cmd[1]) || $cmd[1] == ''){return false;}
			$kick_id = $cmd[1];
			$kick_id = explode("|", mb_substr($kick_id, 3))[0];
			
			if($kick_id == $id || $kick_id == '-214582945' || $db->query("SELECT * FROM `admin` WHERE `vk_id` = '$id' AND `peer_id` = '$peer_id'")->fetch_assoc()['type'] <= $db->query("SELECT * FROM `admin` WHERE `vk_id` = '$kick_id' AND `peer_id` = '$peer_id'")->fetch_assoc()['type']){
				return $vk->sendMessage($peer_id, "$userError вы не можете кикнуть себя\бота\админа");
			}
				$chat_id = $peer_id - 2000000000;

				$vk->request('messages.removeChatUser', ['chat_id' => $chat_id, 'member_id' => $kick_id]);
								
				$vk->sendMessage($peer_id, "$prefix_system пользователь https://vk.com/id$kick_id был исключён из этой беседы!");
		}
		else
		{
			return $vk->sendMessage($peer_id, "$userError недостаточно прав");
		}
	}
	
	else if (mb_substr($message,0,4) == '!топ'){
		if($sql->isBotCmd($db, $peer_id) == false){
				return $vk->sendMessage($peer_id, "");
			}
			
			if($sql->isBanCmd($db, $id) == false){
				return $vk->sendMessage($peer_id, "$userError Вы заблокированы в Robert\n Чтобы узнать подробнее напиши в !репорт");
			}
		
		
		$param = mb_substr($message,5);
		if($param ==''){return $vk->sendMessage($peer_id, "$userError Отсутствует аргумент {param}\nДоступные аргументы: (рейтинг, активных, рейтинг весь, активные все)");
		}
		if($param == 'рейтинг'){
			if($sql->isRetCMD($db, $peer_id) == false){
				return $vk->sendMessage($peer_id, "$userError система рейтинга отключена!");
			}
			$is_admins = $db->query("SELECT * FROM users WHERE peer_id = $peer_id AND reiting != 0 ORDER BY reiting DESC LIMIT 5"); // Получаем данные из колонки vk_id
			while ($row = $is_admins->fetch_assoc()) { // Запускаем цикл
				$i++;
				$s = $vk->request("users.get", ["user_ids" => $row['user_id']]);
				$ss = $s[0]['first_name']; 
				$sss = $s[0]['last_name']; 
				$sss = mb_substr($sss,0,1);
				$rei = $row['reiting'];
				$is_adminss .= "&#12935$i; $ss $sss ( &#128171; $rei )\n";
			//$is_adminss .= $row['reiting']. " - запись с бд\n";
			}
			$qq = $is_adminss;
			if($qq == ''){
				$qq = 'Нет таких :(';
			}
			$vk->sendMessage($peer_id, "Топ по рейтингу в Беседе\n $qq");
		}
		
		else if($param == 'активных'){
			
			if($sql->isBanCmd($db, $id) == false){
				return $vk->sendMessage($peer_id, "$userError Вы заблокированы в Robert\n Чтобы узнать подробнее напиши в !репорт");
			}
			
			$is_admins = $db->query("SELECT * FROM users WHERE peer_id = $peer_id AND messages != 0 ORDER BY messages DESC LIMIT 5"); // Получаем данные из колонки vk_id
			while ($row = $is_admins->fetch_assoc()) { // Запускаем цикл
				$i++;
				$s = $vk->request("users.get", ["user_ids" => $row['user_id']]);
				$ss = $s[0]['first_name']; 
				$sss = $s[0]['last_name']; 
				$sss = mb_substr($sss,0,1);
				$rei = $row['messages'];
				$is_adminss .= "&#12935$i; $ss $sss ( &#128233; $rei )\n";
			}
			$qq = $is_adminss;
			if($qq == ''){
				$qq = 'Нет таких :(';
			}
			$vk->sendMessage($peer_id, "Топ по активности (сообщений) в Беседе:\n $qq");
		}
		else if($param == 'рейтинг весь'){
			
			if($sql->isBanCmd($db, $id) == false){
				return $vk->sendMessage($peer_id, "$userError Вы заблокированы в Robert\n Чтобы узнать подробнее напиши в !репорт");
			}
			
			$is_admins = $db->query("SELECT * FROM users_settings WHERE reiting_all != 0 ORDER BY reiting_all DESC LIMIT 5"); // Получаем данные из колонки vk_id
			while ($row = $is_admins->fetch_assoc()) { // Запускаем циклe
				$i++;
				$s = $vk->request("users.get", ["user_ids" => $row['user_id']]);
				$ss = $s[0]['first_name']; 
				$sss = $s[0]['last_name']; 
				$sss = mb_substr($sss,0,1);
				$rei = $row['reiting_all'];
				$is_adminss .= "&#12935$i; $ss $sss. ( &#128165; $rei )\n";
			}
			$qq = $is_adminss;
			if($qq == ''){
				$qq = 'Нет таких :(';
			}
			$vk->sendMessage($peer_id, "Топ по общему рейтингу:\n $qq");
		}
	}
	
	
	
	if ($message == '!rpinfo') {

    if($sql->isBanCmd($db, $id) == false){
				return $vk->sendMessage($peer_id, "$userError Вы заблокированы в Robert\n Чтобы узнать подробнее напиши в !репорт");
			}

		$vk->sendMessage($peer_id, "Информация о Robert Pay : vk.com/@robertchats-robert-pay-bot-pozhertvovanii");     
	}
		else if($param == 'активные все'){
			if($sql->isBotCmd($db, $peer_id) == false){
				return $vk->sendMessage($peer_id, "");
			}
			
			if($sql->isBanCmd($db, $id) == false){
				return $vk->sendMessage($peer_id, "$userError Вы заблокированы в Robert\n Чтобы узнать подробнее напиши в !репорт");
			}
		
			
			$is_admins = $db->query("SELECT * FROM users WHERE messages != 0 ORDER BY messages DESC LIMIT 5"); // Получаем данные из колонки vk_id
			while ($row = $is_admins->fetch_assoc()) { // Запускаем цикл
				$i++;
				$s = $vk->request("users.get", ["user_ids" => $row['user_id']]);
				$ss = $s[0]['first_name']; 
				$sss = $s[0]['last_name']; $sss = mb_substr($sss,0,1);
				$rei = $row['messages'];
				$is_adminss .= "&#12935$i; $ss $sss ( &#128233; $rei )\n";
			}
			$qq = $is_adminss;
			if($qq == ''){
				$qq = 'Нет таких :(';
			}
			$vk->sendMessage($peer_id, "Топ по активности (сообщений) общий:\n $qq");
		}
	
	else if ($message == '+') {
	if($sql->isBotCmd($db, $peer_id) == false){
				return $vk->sendMessage($peer_id, "");
			}
			
			if($sql->isBanCmd($db, $id) == false){
				return $vk->sendMessage($peer_id, "$userError Вы заблокированы в Robert\n Чтобы узнать подробнее напиши в !репорт");
			}
		
	
	
		$check = $data->object->fwd_messages[0]->from_id;
		if($check) 
		{
			if($vk->isUserChatMember($check, $peer_id) == FALSE){
				return $vk->sendMessage($peer_id, "$userError пользователь отсутствует в чате");
			}
			if($settings[0]['reiting'] == 0){
				return $vk->sendMessage($peer_id, "$userError система рейтинга отключена!");
			}
			if($check == $id) {
				$vk->sendMessage($peer_id, "$userError сам себя не похвалишь, никто не похвалит");
			}else
			{
				if($check == '-214582945') 
				{
					$vk->sendMessage($peer_id, "Боту тоже нельзя ставить баллы :-D");
				}
				else
				{
						$addreit = "@id".$check."(+1)";
						$get = $db->query("SELECT * FROM users_settings WHERE user_id = $check")->fetch_assoc()['reiting_all'];
						$co = $get+1;//общий
						
						$db->query("UPDATE `users_settings` SET reiting_all = $co WHERE user_id = $check");
						$get2 = $db->query("SELECT * FROM users WHERE user_id = $check AND peer_id = $peer_id")->fetch_assoc()['reiting'];
						$co2 = $get2+1;//в беседе
						
						$db->query("UPDATE `users` SET reiting = $co2 WHERE user_id = $check AND peer_id = $peer_id");
						$vk->sendMessage($peer_id, "$userSuc рейтинг пользователя успешно повышен ($addreit)");
				}
			}
		}
	}
	else if(in_array(mb_substr($message, 0, 5), $who_i) or in_array(mb_substr($message, 0, 8), $who_i)){
		if($sql->isBotCmd($db, $peer_id) == false){
				return $vk->sendMessage($peer_id, "");
			}
			
			if($sql->isBanCmd($db, $id) == false){
				return $vk->sendMessage($peer_id, "$userError Вы заблокированы в Robert\n Чтобы узнать подробнее напиши в !репорт");
			}
		
		
		$result = $sql->userInfo($db,$vk,$id, $peer_id, 'iam');
		$vk->sendMessage($peer_id, "$result");
	}
	else if ($message == '!dev') {
		if($sql->isBotCmd($db, $peer_id) == false){
				return $vk->sendMessage($peer_id, "");
			}
			
			if($sql->isBanCmd($db, $id) == false){
				return $vk->sendMessage($peer_id, "$userError Вы заблокированы в Robert\n Чтобы узнать подробнее напиши в !репорт");
			}
		
		
		$vk->sendMessage($peer_id, "$peer_id $id $message_id");
	}
	else if (mb_substr($message, 0, 4) == '!ник') {
		if($sql->isBotCmd($db, $peer_id) == false){
				return $vk->sendMessage($peer_id, "");
			}
			
			if($sql->isBanCmd($db, $id) == false){
				return $vk->sendMessage($peer_id, "$userError Вы заблокированы в Robert\n Чтобы узнать подробнее напиши в !репорт");
			}
		
		
		$nick = mb_substr($message, 5); //обрезаем все, после !ник_
		if($nick == '') {
			$otvet = $db->query("UPDATE `users_settings` SET `nickname` = null WHERE user_id = $id");
			return $vk->sendMessage($peer_id, "$userSuc ник-нейм был удалён");
		}
		else
		{
			$otvet = $db->query("UPDATE `users_settings` SET `nickname` = '$nick' WHERE user_id = $id");
			if($otvet) {
				$vk->sendMessage($peer_id, "$userSuc ник-нейм успешно сменён на $nick");
			}
		}
	}

    if(mb_substr($message,0,7) == '!статус'){
		if($sql->isBotCmd($db, $peer_id) == false){
				return $vk->sendMessage($peer_id, "");
			}
			
			if($sql->isBanCmd($db, $id) == false){
				return $vk->sendMessage($peer_id, "$userError Вы заблокированы в Robert\n Чтобы узнать подробнее напиши в !репорт");
			}
		
		
		if($sql->isUserAdminLevel($db, $id, $peer_id, 2)==true)
		{
			$perd = mb_substr($message ,8);
			if($perd == ""){return $vk->sendMessage($peer_id, "$userError отсутсвует аргумент {id}");} 
			$perd = explode("|", mb_substr($perd, 3))[0];
			if($vk->isUserChatMember($perd, $peer_id) == FALSE){
			    return $vk->sendMessage($peer_id, "$userError пользователь отсутствует в чате");
			}
			if(strpos($message,"\n") !== FALSE)
			{
				$end = explode("\n",$message);
				$messag = $end[1];
				if(strlen($messag) >= 25 || strlen($messag) < 2) {return $vk->sendMessage($peer_id, "$userError слишком короткий\длинный статус");}
                $result = $db->query("UPDATE `users` SET `status`= '$messag' WHERE `peer_id` = '$peer_id' AND `user_id` = '$perd'");
                if($result){
                    $re = "@id$perd(пользователя)";
                    return $vk->sendMessage($peer_id, "$userSuc статус обновлён у $re на $messag");
                }
			}else{
                return $vk->sendMessage($peer_id, "$userError отсутствует аргумент {text}");
			}
		}else{
			return $vk->sendMessage($peer_id, ERRORS[0]);
		}
	}
	
	if($message == '!лог'){
		if($sql->isBotCmd($db, $peer_id) == false){
				return $vk->sendMessage($peer_id, "");
			}
		
		if($sql->isBanCmd($db, $id) == false){
				return $vk->sendMessage($peer_id, "$userError Вы заблокированы в Robert\n Чтобы узнать подробнее напиши в !репорт");
			}
		
		if($sql->isUserAdminLevel($db, $id, $peer_id, 3) != true)
		{
			return $vk->sendMessage($peer_id, ERRORS[0]);
		}
		$getted = $db->query("SELECT * FROM logs WHERE peer_id = $peer_id ORDER BY time DESC LIMIT 10");
		while($row = $getted->fetch_assoc()){
			$result .= "[".$row['time']."] : ".$row['text']."\n";
		}
		$vk->sendMessage($peer_id, "История последних 10-ти действий:\n$result");
	}
	if(mb_substr($message,0,11) == '!снять пред'){
		if($sql->isBotCmd($db, $peer_id) == false){
				return $vk->sendMessage($peer_id, "");
			}
		
		
		if($sql->isUserAdminLevel($db, $id, $peer_id, '2')==true)
		{
			$perd = mb_substr($message,12);
			if($perd == ""){return $vk->sendMessage($peer_id, "$userError отсутсвует аргумент {id}");} 
			$perd = explode("|", mb_substr($perd, 3))[0];
			if($vk->isUserChatMember($perd, $peer_id) == FALSE){return $vk->sendMessage($peer_id, "$userError пользователь отсутствует в чате");}
			if($perd == $id){return $vk->sendMessage($peer_id, "$userError у какой хитрый");}
		
			$sdf = $vk->request("users.get", ["user_ids" => $perd]);
			$gName = $sdf[0]['first_name']; 
			$gSurName = $sdf[0]['last_name'];
			
			
			if(strpos($message,"\n") !== FALSE){
				$end = explode("\n",$message);
				$messag = $end[1];
				$result = $sql->unWarn($vk, $id, $db, $perd, $peer_id, $messag);
			}else{
				$result = $sql->unWarn($vk, $id, $db, $perd, $peer_id, 'не указана');
			}
			$vk->sendMessage($peer_id, "$result");
			
		}else{
			$vk->sendMessage($peer_id, ERRORS[0]);
		}
	}
	if(mb_substr($message,0,5) == '!пред'){
	if($sql->isBotCmd($db, $peer_id) == false){
				return $vk->sendMessage($peer_id, "");
			}
			
			if($sql->isBanCmd($db, $id) == false){
				return $vk->sendMessage($peer_id, "$userError Вы заблокированы в Robert\n Чтобы узнать подробнее напиши в !репорт");
			}
		
	
	$end = "";
		if($sql->isUserAdminLevel($db, $id, $peer_id, 2) == true)
		{
			$perd = mb_substr($message ,6);
			if($perd == "лист") {
				if($db->query("SELECT * FROM warns WHERE peer_id=$peer_id")->getNumRows()){
				$getWarn = $db->query("SELECT * FROM warns WHERE peer_id=$peer_id");
				while($row = $getWarn->fetch_assoc()){
					$isd = $row['user_id'];
					$warns = $row['warns'];
					$reas = $row['reason'];
					
					$infinitive = $vk->request("users.get", ["user_ids" => $isd]);
					$warnName = $infinitive[0]['first_name']; 
					$warnSurName = $infinitive[0]['last_name']; 
					
					$end .= "&#128100; $warnName $warnSurName, &#9762; $warns/3 предупреждений, &#9993; Последняя причина: $reas\n";
				}
				return $vk->sendMessage($peer_id, "$userSuc Все предупреждения в этой беседе\n\n$end");}else{
					return $vk->sendMessage($peer_id, "$userError предупреждения в беседе отсутствуют");
				}
			}
			if($perd == ""){return $vk->sendMessage($peer_id, "$userError отсутсвует аргумент {id}");} 
			$perd = explode("|", mb_substr($perd, 3))[0];
			if($vk->isUserChatMember($perd, $peer_id) == FALSE){return $vk->sendMessage($peer_id, "$userError пользователь отсутствует в чате"); }
			$sdf = $vk->request("users.get", ["user_ids" => $perd]);
			$gName = $sdf[0]['first_name']; 
			$gSurName = $sdf[0]['last_name'];
			if($perd == $id){return $vk->sendMessage($peer_id, "$userError у какой хитрый");}
			if(strpos($message,"\n") !== FALSE){
				$end = explode("\n",$message);
				$messag = $end[1];
				$result = $sql->addWarn($vk, $db, $perd, $peer_id, $messag);
			}else{
				$result = $sql->addWarn($vk, $db, $perd, $peer_id, 'не указана');
			}
			$vk->sendMessage($peer_id, "$result");
			
			$ran = rand(0,3);
			if($ran > 2){
				if($sql->isTipOn($db, $peer_id) === FALSE){
					return $vk->sendMessage($peer_id, TIPS[0]);
				}
			}
		}else{
			$vk->sendMessage($peer_id, "$userError ошибка доступа");
		}
	}
	
	else if (mb_substr($message,0,7) == '!призыв') {
		if($sql->isBotCmd($db, $peer_id) == false){
				return $vk->sendMessage($peer_id, "");
			}
		
		if($sql->isBanCmd($db, $id) == false){
				return $vk->sendMessage($peer_id, "$userError Вы заблокированы в Robert\n Чтобы узнать подробнее напиши в !репорт");
			}
		
		if($sql->isUserAdminLevel($db, $id, $peer_id, '2') != true){
			return $vk->sendMessage($peer_id, "$userError у вас нет доступа к данной команде");
		}
		$mes = mb_substr($message, 8);
		if($mes == ''){
			$fraza = "";
		}else{
			$fraza = "\n &#9993;С сообщением: $mes";
		}
		$api = $vk->request("messages.getConversationMembers", array('peer_id' => $peer_id));
		foreach($api['profiles'] as $profile) {
			$ids = $profile['id']; 
			$nn = $profile['first_name'];
			$sbor .= "@id$ids($nn) ";
		}
		$itog = "$sbor";
		$admin = "@id$id(Администратор)";
		return $vk->sendMessage($peer_id, "Внимание: $itog \n\n $admin хочет сделать объявление!\n $fraza");
	}
	else if (mb_substr($message,0,12) == '!приветствие'){
		if($sql->isBotCmd($db, $peer_id) == false){
				return $vk->sendMessage($peer_id, "");
			}
		
		
		if($sql->isUserAdminLevel($db, $id, $peer_id, '3') != true){return $vk->sendMessage($peer_id, "$userError у вас нет доступа к данной команде");}
		$param = mb_substr($message,13);
		if($param == ''){return $vk->sendMessage($peer_id, "$userError отсутствует параметр {text}\nДополнительные параметры: %name% - имя");}
		if(strlen($param) == 1 || strlen($param) > 500){
			return false;
		}
		$db->query("UPDATE psettings SET hello = '$param' WHERE peer_id = $peer_id");
		
		return $vk->sendMessage($peer_id,"$userSuc теперь приветствие:\n$param");
	}
	
	else if (mb_substr($message,0,12) == '!устправила'){
		if($sql->isBotCmd($db, $peer_id) == false){
				return $vk->sendMessage($peer_id, "");
			}
		
		if($sql->isBanCmd($db, $id) == false){
				return $vk->sendMessage($peer_id, "$userError Вы заблокированы в Robert\n Чтобы узнать подробнее напиши в !репорт");
			}
		
		if($sql->isUserAdminLevel($db, $id, $peer_id, '4') != true){return $vk->sendMessage($peer_id, "$userError у вас нет доступа к данной команде");}
		$param = mb_substr($message,13);
		if($param == ''){return $vk->sendMessage($peer_id, "$userError отсутствует параметр {text}\nДополнительные параметры: %name% - имя");}
		if(strlen($param) == 1 || strlen($param) > 500){
			return false;
		}
		$db->query("UPDATE psettings SET rules = '$param' WHERE peer_id = $peer_id");
		
		return $vk->sendMessage($peer_id,"$userSuc теперь приветствие:\n$param");
	}
	
	else if (mb_substr($message,0,12) == '!правила'){
		if($sql->isBotCmd($db, $peer_id) == false){
				return $vk->sendMessage($peer_id, "");
			}
			
			if($sql->isBanCmd($db, $id) == false){
				return $vk->sendMessage($peer_id, "$userError Вы заблокированы в Robert\n Чтобы узнать подробнее напиши в !репорт");
			}
		
			
			$rules = $db->query("SELECT * FROM `psettings` WHERE `peer_id` = '$peer_id'")->fetch_assoc()['rules'];
			return $vk->sendMessage($peer_id, "&#128466;Правила беседы:\n $rules");

		
	}

	
	else if ($message == '!кто онлайн' || $message == '!онлайн'){
		if($sql->isBotCmd($db, $peer_id) == false){
				return $vk->sendMessage($peer_id, "");
			}
			
			if($sql->isBanCmd($db, $id) == false){
				return $vk->sendMessage($peer_id, "$userError Вы заблокированы в Robert\n Чтобы узнать подробнее напиши в !репорт");
			}
		
		
		$result ="";
		$t = 0;
		$pc = 0;
		$api = $vk->request("messages.getConversationMembers", array('peer_id' => $peer_id));
		foreach($api['profiles'] as $profile){
			$fi = $profile['first_name'];
			$sdfsdffi = $profile['id'];
			$se = $profile['last_name'];
			if($profile['online'] == 1 && $profile['online_mobile'] != 1){
				$result .= "&#128421; @id$sdfsdffi($fi $se) \n";
				++$t;
			}
			if($profile['online'] == 1 && $profile['online_mobile'] == 1){
				$result .= "&#128241; @id$sdfsdffi($fi $se) \n";
				++$pc;
			}
		}
		$all = $t+$pc;
		$count = $api['count'];
		$vk->sendMessage($peer_id, "Сейчас онлайн:\n\n$result\nОнлайн: $all (&#128421;$t,&#128241;$pc) из $count ",true);
	}
	else if ($message == '!регалл') {
			
			
			
		if($settings[0]['active'] == 1){
			return $vk->sendMessage($peer_id, "$userError беседа уже активирована!");
		}
		$api = $vk->request("messages.getConversationsById", array('peer_ids' => $peer_id));
		if($api['count'] == 0){return $vk->sendMessage($peer_id, "$userError бот не имеет прав Администратора!");}
		$uInfo2 = $info[0]['first_name']; 
			$usInfo4 = $info[0]['last_name']; 
		
		$db->query("DELETE FROM `admin` WHERE `peer_id` = '$peer_id'");
		
		$owner = $api['items'][0]['chat_settings']['owner_id'];
		
		foreach($api['items'][0]['chat_settings']['admin_ids'] as $s){
			if($s == '-214582945' || $s == $owner){continue;}
			$info = $vk->request("users.get", ["user_ids" => $s]);
			$uInfo = $info[0]['first_name']; 
			$usInfo = $info[0]['last_name']; 
			
		$test = $vk->request('messages.getConversationMembers', ['peer_id' => $peer_id]);
		$tester = $test["items"][0]["member_id"];
		
		$db->query("INSERT INTO `base`.`admin` (`id`, `vk_id`, `peer_id`, `adminN`, `adminSN`, `type`) VALUES (NULL, '$tester', '$peer_id', 'Admin', 'Adminov', '5')");
			
			
		 $db->query("INSERT INTO `admin`(`vk_id`,`peer_id`, `adminN`, `adminSN`,`type`) VALUES ('$s','$peer_id', '$uInfo','$usInfo','4')");
			
		}
		$info = $vk->request("users.get", ["user_ids" => $owner]);
		$uInfo = $info[0]['first_name']; 
		$usInfo = $info[0]['last_name']; 
		
		$result1 = $db->query("UPDATE `admin` SET `type` = '5' WHERE peer_id = $peer_id ");

		
		$i = 0;
		$b = 0;
		$vk->sendMessage($peer_id, "Регистрируем всех...");
		$api = $vk->request("messages.getConversationMembers", array('peer_id' => $peer_id));
		foreach($api['profiles'] as $profile) {
			++$i;
			$ids = $profile['id'];
			$finssss = $db->query("SELECT * FROM users_settings WHERE user_id = $ids")->getNumRows();
			if($finssss) {
				--$i;
			}else{
				$finish = $db->query("INSERT INTO `users_settings` (`user_id`, `nickname`, `megacoins`, `reiting_all`) VALUES ('$ids','nick', '0', '0')");
				if($finish){
				}else{
					$vk->sendMessage($peer_id, "Произошла ошибка");
				}
			}
			++$b;
			$oss = $profile['id'];
			if($db->query("SELECT * FROM users WHERE user_id = $oss AND peer_id = $peer_id")->getNumRows()){
				--$b;
			}else{
				$finka = $db->query("INSERT INTO `users`(`user_id`, `peer_id`, `messages`, `reiting`,`join_date`) VALUES ('$oss','$peer_id','0','0', '$date_today')");
				if($finka){
				}else{
					$vk->sendMessage($peer_id, "Произошла ошибка");
				}
			}
		}

		
		$vk->sendMessage($peer_id, "Зарегистрировано: $i пользователей \\ в беседе: $b \nВ будущем, не придется вводить данную команду!\n Дата $date_today");
		$result1 = $db->query("INSERT INTO `psettings`(`peer_id`,`active`, `status`, `checked`,`autokick`) VALUES ('$peer_id','1', '0','1','1')");
		$result2 = $db->query("UPDATE `psettings` SET `active` = '1' WHERE `peer_id` = '$peer_id'");
	}
	else if($message == '!чатобновить'){
		
		if($sql->isBanCmd($db, $id) == false){
				return $vk->sendMessage($peer_id, "$userError Вы заблокированы в Robert\n Чтобы узнать подробнее напиши в !репорт");
			}
		
		$i = 0;
		$b = 0;
		$vk->sendMessage($peer_id, "Регистрируем всех...");
		$api = $vk->request("messages.getConversationMembers", array('peer_id' => $peer_id));
		foreach($api['profiles'] as $profile) {
			++$i;
			$ids = $profile['id'];
			$finssss = $db->query("SELECT * FROM users_settings WHERE user_id = $ids")->getNumRows();
			if($finssss) {
				--$i;
			}else{
				$finish = $db->query("INSERT INTO `users_settings` (`user_id`, `nickname`, `megacoins`, `reiting_all`) VALUES ('$ids',null, '0', '0')");
				if($finish){
				}else{
					$vk->sendMessage($peer_id, "Произошла ошибка");
				}
			}
			++$b;
			$oss = $profile['id'];
			if($db->query("SELECT * FROM users WHERE user_id = $oss AND peer_id = $peer_id")->getNumRows()){
				--$b;
			}else{
				$finka = $db->query("INSERT INTO `users`(`user_id`, `peer_id`, `messages`, `reiting`) VALUES ('$oss','$peer_id','0','0')");
				if($finka){
				}else{
					$vk->sendMessage($peer_id, "Произошла ошибка");
				}
			}
		}
		$vk->sendMessage($peer_id, "Зарегистрировано: $i пользователей \\ в беседе: $b \nВ будущем, не придется вводить данную команду!");
	}
	else if(mb_strtolower($message) == 'розбойник' || mb_strtolower($message) == 'кыш' || mb_strtolower($message) == 'выйди' || mb_strtolower($message) == 'уйди' || mb_strtolower($message) == 'розбiйник'){
      if($sql->isBotCmd($db, $peer_id) == false){
				return $vk->sendMessage($peer_id, "");
			}
		

		$vk->sendImage($peer_id, "./img/command_pirate.jpg", 'выйди отсюда, розбiйник');
	}
	else if(mb_strtolower($message) == ':((' || mb_strtolower($message) == ':(' || mb_strtolower($message) == 'грусть' || mb_strtolower($message) == 'плохо' ||  mb_strtolower($message) == '('){
			if($sql->isBotCmd($db, $peer_id) == false){
				return $vk->sendMessage($peer_id, "");
			}
		
		$vk->sendImage($peer_id, "./img/command_grustno.jpg", 'Грусть, Печаль, Тоска ');
	}
	else if(mb_strtolower($message) == 'omg' || mb_strtolower($message) == 'боже' || mb_strtolower($message) == 'о боже' || mb_strtolower($message) == 'омг'){
					if($sql->isBotCmd($db, $peer_id) == false){
				return $vk->sendMessage($peer_id, "");
			}
		
		$vk->sendImage($peer_id, "./img/command_god.jpg", '');
		
	}
	else if(mb_strtolower($message) == 'cat'){
		if($sql->isBotCmd($db, $peer_id) == false){
				return $vk->sendMessage($peer_id, "");
			}
		
		
		$vk->sendImage($peer_id, "./img/command_cat_".rand(0,3).".jpg", '');
		
		
	}
	else if(mb_strtolower($message) == 'ору' || mb_strtolower($message) == 'ору с тебя' || mb_strtolower($message) == 'орк' || mb_strtolower($message) == 'орейро'){
		if($sql->isBotCmd($db, $peer_id) == false){
				return $vk->sendMessage($peer_id, "");
			}
		
		$vk->sendImage($peer_id, "./img/command_scream.jpg", '');
		
	}
	else if(mb_strtolower($message) == 'быканул?' || mb_strtolower($message) == 'быканул али как?' || mb_strtolower($message) == 'быканул или мне показалось?'|| mb_strtolower($message) == 'бык' || mb_strtolower($message) == 'быканул'){
					if($sql->isBotCmd($db, $peer_id) == false){
				return $vk->sendMessage($peer_id, "");
			}
		
					
					$link = 'https://sun9-27.userapi.com/c855720/v855720517/29b0b/jNHSWomDE2c.jpg';
		$file = file_get_contents($link);
		file_put_contents("./img/command_cow.jpg", $file);
		$vk->sendImage($peer_id, "./img/command_cow.jpg", '');
		
	}
	else if(mb_strtolower($message) == 'бан' || mb_strtolower($message) == 'бан нахуй' || mb_strtolower($message) == 'опа, бан'){
		if($sql->isBotCmd($db, $peer_id) == false){
				return $vk->sendMessage($peer_id, "");
			}
		
		
		$rand = rand(0,2);
		$vk->sendImage($peer_id, "./img/command_ban$rand.jpg", 'БАН');
		
					/*$link = 'https://sun9-34.userapi.com/c849124/v849124799/134b22/FwW84q7v4E4.jpg';
		$file = file_get_contents($link);
		file_put_contents("./img/command_ban2.jpg", $file);*/
		
	}
	else if(mb_strtolower($message) == 'сябки' || mb_strtolower($message) == 'спасибо' || mb_strtolower($message) == 'спс' || mb_strtolower($message) == 'thx' || mb_strtolower($message) == 'thank' || mb_strtolower($message) == 'sps' || mb_strtolower($message) == 'спасибки' || mb_strtolower($message) == 'сяб' ){
		if($sql->isBotCmd($db, $peer_id) == false){
				return $vk->sendMessage($peer_id, "");
			}
		
		$link = 'https://venuerific.files.wordpress.com/2014/06/thank-you.png';
		$file = file_get_contents($link);
		file_put_contents("./img/command_thank.png", $file);
		$vk->sendImage($peer_id, "./img/command_thank.png", '');
	}
	else if(strpos($message,'&#128545;') === TRUE || mb_strtolower($message) == 'ар' || mb_strtolower($message) == 'агр' || mb_strtolower($message) == 'мда'){
				if($sql->isBotCmd($db, $peer_id) == false){
				return $vk->sendMessage($peer_id, "");
			}
		
		$vk->sendImage($peer_id, "./img/command_angry.jpg", '');
	}
	else if(mb_strtolower($message) == 'кек' || mb_strtolower($message) == 'лол'){
				if($sql->isBotCmd($db, $peer_id) == false){
				return $vk->sendMessage($peer_id, "");
			}
		
		$vk->sendImage($peer_id, "./img/command_kek.jpg", '');
		
	}
	else if(mb_strtolower($message) == 'ъьъ съука' || mb_strtolower($message) == 'ъьъ' || mb_strtolower($message) == 'у съука' || mb_strtolower($message) == 'ъьъ'){
		if($sql->isBotCmd($db, $peer_id) == false){
				return $vk->sendMessage($peer_id, "");
			}
		
		
		$vk->sendImage($peer_id, "./img/command_bbb.jpeg", 'ЪьЪ сЪука');
	}
	else if($message == 'F' || $message == 'f' || mb_strtolower($message) == 'press f'){
		if($sql->isBotCmd($db, $peer_id) == false){
				return $vk->sendMessage($peer_id, "");
			}
		
		$vk->sendImage($peer_id, "./img/command_f.jpg", 'Press F to Pay Respects');
	}
	if(mb_substr($message,0,8) == '!правкац'){
		if($sql->isBotCmd($db, $peer_id) == false){
				return $vk->sendMessage($peer_id, "");
			}
		
		
		if($id != '384904677'){return false;}
		$param = mb_substr($message,9);
		if($param == ''){return $vk->sendMessage($peer_id, "mm");}
		$e = $db->query("UPDATE `psettings` SET `status`='$param' WHERE peer_id = $peer_id");
		if($e){
			return $vk->sendMessage($peer_id, "+");
		}
	} 
	if(mb_substr($message,0,11) == '!robasd123'){
		if($sql->isBotCmd($db, $peer_id) == false){
				return $vk->sendMessage($peer_id, "");
			}
		
		
		$zap = $db->query("SELECT * FROM `psettings` WHERE `peer_id` = '$peer_id' AND `status` = 'Robert'");
		if($zap->fetch_assoc()['status'] != 'Robert'){return $vk->sendMessage($peer_id, "$userError беседа не имеет статуса 'Robert Class'.");}
		$param = mb_substr($message,12);
		if($param == ''){return $vk->sendMessage($peer_id, "$userError отсутствует параметр {nick}");}
		$end = $mc_api->getUserInfo($param);
		$link = 'https://mc-heads.net/head/'.$param.'.png';
		$file = file_get_contents($link);
		file_put_contents("./skins/$param.png", $file);
		$vk->sendImage($peer_id, "./skins/$param.png", $end);
		$vk->sendMessage(384904677, "Отправил: vk.com/".$id."\n".$param."");
		return unlink("./skins/$param.png");
	}
	if($data->object->action->type == 'chat_invite_user_by_link'){
		$get = $db->query("SELECT * FROM `psettings` WHERE `peer_id` = $peer_id");
		$get = $get->fetch_assoc()['checked'];
		if($get != '0'){
			if($vk->isUserInGroup($id,$get) == false){
				$chat_id = $peer_id - 2000000000;
			}
		}
		$finssss = $db->query("SELECT * FROM users_settings WHERE user_id = $id")->getNumRows();
		if($finssss) {
			$in_peer = $db->query("INSERT INTO `users`(`user_id`, `peer_id`, `messages`) VALUES ('$id', '$peer_id', '0')");
			$text = $db->query("SELECT `hello` FROM psettings WHERE peer_id = $peer_id")->fetch_assoc()['hello'];
			$pos = strpos($text, '%name%');
			if($pos){
				$gwas = $vk->request("users.get", ["user_ids" => $id]);
				$ffas = $gwas[0]['first_name']; 
				$text = str_replace("%name%", "@id$id($ffas)", "$text");
			}
			return $vk->sendMessage($peer_id, $text);
		}else{
			$finish = $db->query("INSERT INTO `users_settings` (`user_id`, `nickname`, `megacoins`, `reiting_all`) VALUES ('$id',null, '0', '0')");
			$in_peer = $db->query("INSERT INTO `users`(`user_id`, `peer_id`, `messages`) VALUES ('$id', '$peer_id', '0')");
			if($finish) {
				$text = $db->query("SELECT `hello` FROM psettings WHERE peer_id = $peer_id")->fetch_assoc()['hello'];
				$pos = strpos($text, '%name%');
				if($pos){
					$gwas = $vk->request("users.get", ["user_ids" => $id]);
					$ffas = $gwas[0]['first_name']; 
					$text = str_replace("%name%", "@id$id($ffas)", "$text");
				}
				return $vk->sendMessage($peer_id, $text);
			}
		}
	}
	if(mb_substr($message,0,13) == '!клан создать'){
		if($sql->isBotCmd($db, $peer_id) == false){
				return $vk->sendMessage($peer_id, "");
			}
		
		
		$param = mb_substr($message,14);
		if($param == ''){return $vk->sendMessage($peer_id,"$userError отсуствует аргумент {title}");}
		if($db->query("SELECT `title` FROM `clan` WHERE `title` = '$param'")->getNumRows()){return $vk->sendMessage($peer_id, "$userError клан с таким названием уже существует ");}
		$db->query("INSERT INTO `clan`(`title`, `peer_id`, `gadmin`, `admins`, `members`) VALUES ('$param','$peer_id','$id','$id','$id')");
		return $vk->sendMessage($peer_id, "$userSuc клан [$param] создан.");
	}
	else if($message == '!мои кланы'){
		if($sql->isBotCmd($db, $peer_id) == false){
				return $vk->sendMessage($peer_id, "");
			}
		
		
		if($db->query("SELECT * FROM `clan` WHERE `gadmin` = '$id' AND `peer_id` = '$peer_id'")->getNumRows()){
				$is_admins = $db->query("SELECT * FROM clan WHERE peer_id = $peer_id AND gadmin = $id"); // Получаем данные из колонки vk_id
				while ($row = $is_admins->fetch_assoc()) { // Запускаем цикл
					$admN = $row['title'];
					$isd = $row['id'];
					$is_adminss .= "\n&#9197;$admN\nID клана: $isd\n\n";
				}
				$vk->sendMessage($peer_id, "&#128466;Ваши кланы:\n$is_adminss");
		}else{
			return $vk->sendMessage($peer_id, "$userError у вас нет кланов");
		}
	}
	else if($message == '!кланы беседы'){
		if($sql->isBotCmd($db, $peer_id) == false){
				return $vk->sendMessage($peer_id, "");
			}
		
		
		if($db->query("SELECT * FROM `clan` WHERE `peer_id` = '$peer_id'")->getNumRows()){
			$is_admins = $db->query("SELECT * FROM clan WHERE peer_id = $peer_id"); // Получаем данные из колонки vk_id
			while ($row = $is_admins->fetch_assoc()) { // Запускаем цикл
				$admN = $row['title'];
				$isd = $row['id'];
				$isd4 = $row['admins'];
				$nn = $vk->request("users.get", ["user_ids" => $row['gadmin']]);
				$userNam = $nn[0]['first_name']; 
				$userSNam = $nn[0]['last_name'];
				$userSNam = mb_substr($userSNam,0,1);
				$is_adminss .= "\n&#9197;$admN\nID клана: $isd\nГлава клана: @id$isd4 ($userNam $userSNam).\n\n";
			}
			$vk->sendMessage($peer_id, "&#128466;Kланы беседы:\n$is_adminss");
		}else{
			return $vk->sendMessage($peer_id, "$userError у беседы нет кланов");
		}
	}
	else if(mb_substr($message,0,13) == '!клан расформ'){
		if($sql->isBotCmd($db, $peer_id) == false){
				return $vk->sendMessage($peer_id, "");
			}
		
		
		$param = mb_substr($message,14);
		if($param == ''){return $vk->sendMessage($peer_id,"$userError отсуствует аргумент {ID_CLAN}");}
		if($db->query("SELECT * FROM `clan` WHERE `peer_id` = '$peer_id' AND `id` = '$param'")->getNumRows()){
			if($db->query("SELECT * FROM `clan` WHERE `peer_id` = '$peer_id' AND `gadmin` = '$id' AND `id` = '$param'")->getNumRows()){
				$db->query("DELETE FROM `clan` WHERE `peer_id` = '$peer_id' AND `gadmin` = '$id' AND `id` = '$param'");
				return $vk->sendMessage($peer_id, "$userSuc клан ID[$param] был расформирован! Участники распущены, электричество отключено...");	
			}else{
				return $vk->sendMessage($peer_id, "$userError ошибка, вы не глава клана.");
			}
		}else{
			return $vk->sendMessage($peer_id, "$userError ошибка, клан с ID[$param] не существует.");
		}
	}
	else if(mb_substr($message,0,14) == '!клан покинуть'){
		if($sql->isBotCmd($db, $peer_id) == false){
				return $vk->sendMessage($peer_id, "");
			}
		
		
		$param = mb_substr($message,15);
		if($param == ''){return $vk->sendMessage($peer_id,"$userError отсуствует аргумент {ID_CLAN}");}
		if($db->query("SELECT * FROM `clan` WHERE `peer_id` = '$peer_id' AND `id` = '$param'")->getNumRows()){
			$search = $db->query("SELECT * FROM `clan` WHERE `peer_id` = '$peer_id' AND `id` = '$param'")->fetch_assoc()['members'];
			if(substr_count($search, $id) == 1){
				$next_search = $db->query("SELECT * FROM `clan` WHERE `peer_id` = '$peer_id' AND `id` = '$param'")->fetch_assoc()['admins'];
				if(substr_count($next_search, $id) == 1)
				{
					return $vk->sendMessage($peer_id, "$userError администратор клана не может покинуть его.");
				}else
				{
					$doble_search = $db->query("SELECT * FROM `clan` WHERE `peer_id` = '$peer_id' AND `id` = '$param'")->fetch_assoc()['gadmin'];
					if(substr_count($doble_search, $id) == 1)
					{
						return $vk->sendMessage($peer_id, "$userError глава клана не может покинуть его.");
					}else
					{
						$shablon = ",$id";
						$sad = str_replace("$shablon", "", "$search");
						$db->query("UPDATE `clan` SET `members` = '$sad' WHERE `peer_id` = '$peer_id' AND `id` = '$param'");
						return $vk->sendMessage($peer_id, "$userSuc вы покинули клан ID[$param]!");
					}
				}
			}else{
				return $vk->sendMessage($peer_id, "$userError ошибка, вы не участник клана ID[$param]");
			}
		}else{
			return $vk->sendMessage($peer_id, "$userError ошибка, клан с ID[$param] не существует");
		}
	}
	else if($message == '!посчитай'){
		if($sql->isBotCmd($db, $peer_id) == false){
				return $vk->sendMessage($peer_id, "");
			}
		
		
		$vk->sendMessage($peer_id, "Сообщений в этой беседе (с начала беседы): ".$message_id."");
	}
	else if(mb_substr($message,0,14) == '!клан понизить'){
		if($sql->isBotCmd($db, $peer_id) == false){
				return $vk->sendMessage($peer_id, "");
			}
		
		
		$param = mb_substr($message,15);
		if($param == ''){return $vk->sendMessage($peer_id,"$userError отсуствует аргумент {ID_CLAN}");}
		if($db->query("SELECT * FROM `clan` WHERE `peer_id` = '$peer_id' AND `id` = '$param'")->getNumRows()){
			if($db->query("SELECT * FROM `clan` WHERE `peer_id` = '$peer_id' AND `gadmin` = '$id' AND `id` = '$param'")->getNumRows()){
				if(strpos($message,"\n") !== FALSE){
					$end = explode("\n",$message);
					$messag = $end[1];
					$id_adm = explode("|", mb_substr($messag, 3))[0];
					if($id_adm == $id || $id_adm == '-384904677'){
						return false;
					}
					$search = $db->query("SELECT * FROM `clan` WHERE `peer_id` = '$peer_id' AND `id` = '$param'")->fetch_assoc()['members'];
					if(substr_count($search, $id_adm) == 1){
						$querka = $db->query("SELECT * FROM `clan` WHERE `peer_id` = '$peer_id' AND `id` = '$param'")->fetch_assoc()['admins'];
						if(substr_count($querka, $id_adm) == 1){
							$shablon = ",$id_adm";
							$sad = str_replace("$shablon", "", "$querka");
							$db->query("UPDATE `clan` SET `admins` = '$sad' WHERE `peer_id` = '$peer_id' AND `id` = '$param'");
							return $vk->sendMessage($peer_id, "$userSuc пользователь снять с прав администратора клана!");
						}else{
							return $vk->sendMessage($peer_id, "$userError пользователь не является администратором.");
						}
					}else{
						return $vk->sendMessage($peer_id, "$userError ошибка, участник не вступил в ваш клан.");
					}
					
				}else{
					return $vk->sendMessage($peer_id, "$userError ошибка, отсутствует аргумент {ID}");
				}
			}else{
				return $vk->sendMessage($peer_id, "$userError ошибка, вы не глава клана.");
			}
		}else{
			return $vk->sendMessage($peer_id, "$userError ошибка, клан с ID[$param] не существует");
		}
	}
	else if(mb_substr($message,0,14) == '!клан повысить'){
		if($sql->isBotCmd($db, $peer_id) == false){
				return $vk->sendMessage($peer_id, "");
			}
		
		
		$param = mb_substr($message,15);
		if($param == ''){return $vk->sendMessage($peer_id,"$userError отсуствует аргумент {ID_CLAN}");}
		if($db->query("SELECT * FROM `clan` WHERE `peer_id` = '$peer_id' AND `id` = '$param'")->getNumRows()){
			if($db->query("SELECT * FROM `clan` WHERE `peer_id` = '$peer_id' AND `gadmin` = '$id' AND `id` = '$param'")->getNumRows()){
				if(strpos($message,"\n") !== FALSE){
					$end = explode("\n",$message);
					$messag = $end[1];
					$id_adm = explode("|", mb_substr($messag, 3))[0];
					if($id_adm == $id || $id_adm == '-384904677'){
						return false;
					}
					$search = $db->query("SELECT * FROM `clan` WHERE `peer_id` = '$peer_id' AND `id` = '$param'")->fetch_assoc()['members'];
					if(substr_count($search, $id_adm) == 1){
						$querka = $db->query("SELECT * FROM `clan` WHERE `peer_id` = '$peer_id' AND `id` = '$param'")->fetch_assoc()['admins'];
						if(substr_count($querka, $id_adm) == 1){
							return $vk->sendMessage($peer_id, "$userError ошибка, пользователь является администратором.");
						}else{
							$shablon = "$querka,$id_adm";
							$db->query("UPDATE `clan` SET `admins` = '$shablon' WHERE `peer_id` = '$peer_id' AND `id` = '$param'");
							return $vk->sendMessage($peer_id, "$userSuc теперь пользователь администратор клана!");
						}
					}else{
						return $vk->sendMessage($peer_id, "$userError ошибка, участник не вступил в ваш клан.");
					}
					
				}else{
					return $vk->sendMessage($peer_id, "$userError ошибка, отсутствует аргумент {ID}");
				}
			}else{
				return $vk->sendMessage($peer_id, "$userError ошибка, вы не глава клана.");
			}
		}else{
			return $vk->sendMessage($peer_id, "$userError ошибка, клан с ID[$param] не существует");
		}
	}
	else if(mb_substr($message,0,10) == '!клан инфо'){
		if($sql->isBotCmd($db, $peer_id) == false){
				return $vk->sendMessage($peer_id, "");
			}
		
		
		$param = mb_substr($message,11);
		if($param == ''){return $vk->sendMessage($peer_id,"$userError отсуствует аргумент {ID_CLAN}");}
		if($db->query("SELECT * FROM `clan` WHERE `peer_id` = '$peer_id' AND `id` = '$param'")->getNumRows()){
			$is_admins = $db->query("SELECT * FROM `clan` WHERE `peer_id` = '$peer_id' AND `id` = '$param'"); // Получаем данные из колонки vk_id
			while ($row = $is_admins->fetch_assoc()) { // Запускаем цикл
				$admN = $row['title'];
				$texts = $row['members'];
				
				$nn = $vk->request("users.get", ["user_ids" => $row['gadmin']]);
				$userNam = $nn[0]['first_name']; 
				$userSNam = $nn[0]['last_name'];
				$userSNam = mb_substr($userSNam,0,1);
				
				$membersss = $vk->request("users.get", ["user_ids" => $texts]);
				foreach($membersss as $m){
					$aas = $m['last_name'];
					$aas = mb_substr($aas,0,1);
					$tect .= ''.$m['first_name'].' '.$aas.'. ';
				}
				
				$admin_GET = $vk->request("users.get", ["user_ids" => $row['admins']]);
				foreach($admin_GET as $a){
					$admin_NAME = $a['last_name'];
					$admin_NAME = mb_substr($admin_NAME,0,1);
					$admin_TEXT .= ''.$a['first_name'].' '.$admin_NAME.'. ';
				}	
				
				$is_adminss .= "\n&#9197;Название клана: $admN\nГлава клана: $userNam $userSNam\nАдминистраторы: $admin_TEXT\nУчастники: $tect";
			}
			return $vk->sendMessage($peer_id, "&#128466;Информация о клане:\n$is_adminss");
		}else{
			return $vk->sendMessage($peer_id, "$userError ошибка, клан с ID[$param] не существует");
		}
	}
	else if(mb_substr($message,0,14) == '!клан вступить'){
		if($sql->isBotCmd($db, $peer_id) == false){
				return $vk->sendMessage($peer_id, "");
			}
		
		
		$param = mb_substr($message,15);
		if($param == ''){return $vk->sendMessage($peer_id,"$userError отсуствует аргумент {ID_CLAN}");}
		if($db->query("SELECT * FROM `clan` WHERE `peer_id` = '$peer_id' AND `id` = '$param'")->getNumRows()){
			$search = $db->query("SELECT * FROM `clan` WHERE `peer_id` = '$peer_id' AND `id` = '$param'")->fetch_assoc()['members'];
			if(substr_count($search,$id) == 1){
				return $vk->sendMessage($peer_id, "$userError ошибка, вы уже состоите в клане ID[$param]");
			}else{
				$shablon = "$search,$id";
				$db->query("UPDATE `clan` SET `members` = '$shablon' WHERE `peer_id` = '$peer_id' AND `id` = '$param'");
				$title = $db->query("SELECT * FROM `clan` WHERE `peer_id` = '$peer_id' AND `id` = '$param'")->fetch_assoc()['title'];
				return $vk->sendMessage($peer_id, "$userSuc вы успешно вступили в клан « $title » [$param] ");
			}
		}else{
			return $vk->sendMessage($peer_id, "$userError ошибка, клан с ID[$param] не существует");
		}
	}
	
	else if(mb_substr($message,0,5) == '!mode'){
		if($sql->isUserAdminLevel($db, $id, $peer_id, 3) != true){return false;}
		$param = mb_substr($message,6);
		if($param == ''){return $vk->sendMessage($peer_id, "$userError отсутствует значения для [param1]\nДоступные параметры: invite_bot autokick, tip, rating,news, bot");}
		switch($param){
			case 'autokick':
				if($settings[0][$param] == 0){$flag = 1;}else{$flag = 0;}
				$db->query("UPDATE `psettings` SET `$param` = '$flag' WHERE `peer_id` = '$peer_id'");
				break;
			case 'invite_bot':
				if($settings[0][$param] == 0){$flag = 1;}else{$flag = 0;}
				$db->query("UPDATE `psettings` SET `$param` = '$flag' WHERE `peer_id` = '$peer_id'");
				break;
			case 'tip':
				if($settings[0][$param] == 0){$flag = 1;}else{$flag = 0;}
				$db->query("UPDATE `psettings` SET `$param` = '$flag' WHERE `peer_id` = '$peer_id'");
				break;
			case 'news':
				if($settings[0][$param] == 0){$flag = 1;}else{$flag = 0;}
				$db->query("UPDATE `psettings` SET `$param` = '$flag' WHERE `peer_id` = '$peer_id'");
				break;
			case 'rating':
				$param = 'reiting';
				if($settings[0][$param] == 0){$flag = 1;}else{$flag = 0;}
				$db->query("UPDATE `psettings` SET `$param` = '$flag' WHERE `peer_id` = '$peer_id'");
				$param = 'rating';
				break;
			
			case 'bot':
				if($settings[0][$param] == 0){$flag = 1;}else{$flag = 0;}
				$db->query("UPDATE `psettings` SET `$param` = '$flag' WHERE `peer_id` = '$peer_id'");
				break;
			default:
				return $vk->sendMessage($peer_id, "$userError параметр [param1] не распознан\nДоступные параметры: autokick, tip, rating,news, bot");
			break;
		}
		return $vk->sendMessage($peer_id, ''.$userSuc.' флажок у параметра "'.$param.'" переключен в режим '.$flag.'');
	}
	//include "on_cmd.php";
}

if($data->object->action->type == 'chat_invite_user') {
	$isss = $data->object->action->member_id;
	if($isss == '214582945')
	{
		$db->query("DELETE FROM `admin` WHERE peer_id = $peer_id");
		$db->query("DELETE FROM `psettings` WHERE peer_id = $peer_id");     
		//$db->query("DELETE FROM `users` WHERE `peer_id` = '$peer_id'");
		$db->query("DELETE FROM `warns` WHERE `peer_id` = '$peer_id'");
		$db->query("INSERT INTO `psettings`(`peer_id`) VALUES (".$peer_id.")");
		$uInfo2 = $info[0]['first_name']; 
			$usInfo4 = $info[0]['last_name']; 
		
		
		return $vk->sendMessage($peer_id, "Привет, я {Helper Bot}. Перед тем, как мы начнём с тобой работу, назначь меня Администратором :-D \n После того, как мне выдадут возможности Администратора, введите команду !регалл");
	}
	if($settings[0]['invite_bot'] == 0 && strpos($isss, "-") !== FALSE){
		$chat_id = $peer_id - 2000000000;
		$vk->request('messages.removeChatUser', ['chat_id' => $chat_id, 'member_id' => $isss]);
		return $vk->sendMessage($peer_id,"$prefix_system Кто пригласил бота? Кик бота!\nОтключить данную опцию можно командой !mode invite_bot");
	}
	if($db->query("SELECT * FROM `bans` WHERE `peer_id` = '$peer_id' AND `user_id` = '$isss")->getNumRows() && strpos($isss, '-') === FALSE){
		
	}
	$get = $db->query("SELECT * FROM `psettings` WHERE `peer_id` = $peer_id");
	$get = $get->fetch_assoc()['checked'];
	if($get != '0'){
		if($vk->isUserInGroup($isss,$get) == false){
			$chat_id = $peer_id - 2000000000;
			$vk->request('messages.removeChatUser', ['chat_id' => $chat_id, 'member_id' => $isss]);
			sleep(1);
			$user = "@id$isss(Пользователь)";
			return $vk->sendMessage($peer_id, "$userSuc $user не подписан на сообщество https://vk.com/public$get\nНаказание: kick");
		}
	}
	

	$finssss = $db->query("SELECT * FROM users_settings WHERE user_id = $isss")->getNumRows();
	if($finssss) {
		$in_peer = $db->query("INSERT INTO `users`(`user_id`, `peer_id`, `messages`) VALUES ('$isss', '$peer_id', '0')");
		$text = $db->query("SELECT `hello` FROM psettings WHERE peer_id = $peer_id")->fetch_assoc()['hello'];
		$pos = strpos($text, '%name%');
		if($pos){
			$gwas = $vk->request("users.get", ["user_ids" => $isss]);
			$ffas = $gwas[0]['first_name']; 
			$text = str_replace("%name%", "@id$isss($ffas)", "$text");
		}
		return $vk->sendMessage($peer_id, $text);
	}else{
		$finish = $db->query("INSERT INTO `users_settings` (`user_id`, `nickname`, `megacoins`, `reiting_all`) VALUES ('$isss',null, '0', '0')");
		$in_peer = $db->query("INSERT INTO `users`(`user_id`, `peer_id`, `messages`) VALUES ('$isss', '$peer_id', '0')");
		if($finish) {
			$text = $db->query("SELECT `hello` FROM psettings WHERE peer_id = $peer_id")->fetch_assoc()['hello'];
			$pos = strpos($text, '%name%');
			if($pos){
				$gwas = $vk->request("users.get", ["user_ids" => $isss]);
				$ffas = $gwas[0]['first_name']; 
				$text = str_replace("%name%", "@id$isss($ffas)", "$text");
			}
			return $vk->sendMessage($peer_id, $text);
		}
	}
}
if($data->object->action->type == 'chat_kick_user') {
	$isss = $data->object->action->member_id;
	if($isss == $id){
		if($db->query("SELECT * FROM psettings WHERE peer_id = $peer_id AND autokick = '1'")->getNumRows()){
			$chat_id = $peer_id - 2000000000;
			$vk->request('messages.removeChatUser', ['chat_id' => $chat_id, 'member_id' => $isss]);
			$db->query("DELETE FROM `users` WHERE user_id = $isss AND peer_id = $peer_id");
			if($db->query("SELECT * FROM warns WHERE user_id = $isss AND peer_id = $peer_id")->getNumRows()){
				$db->query("DELETE FROM warns WHERE user_id = $isss AND peer_id = $peer_id");
			}
			if($db->query("SELECT * FROM admin WHERE vk_id = $isss AND peer_id = $peer_id")->getNumRows()){
				$db->query("DELETE FROM admin WHERE vk_id = $isss AND peer_id = $peer_id");
			}
			$userIDDD = "@id$isss(Пользователь)";
			return $vk->sendMessage($peer_id, "$userSuc сработал АвтоКик, переключить режим можно командой:\n!mode autokick");
		}
	}
	$db->query("DELETE FROM `users` WHERE user_id = $isss AND peer_id = $peer_id");
	if($db->query("SELECT * FROM warns WHERE user_id = $isss AND peer_id = $peer_id")->getNumRows()){
		$db->query("DELETE FROM warns WHERE user_id = $isss AND peer_id = $peer_id");
	}
	if($db->query("SELECT * FROM admin WHERE vk_id = $isss AND peer_id = $peer_id")->getNumRows()){
		$db->query("DELETE FROM admin WHERE vk_id = $isss AND peer_id = $peer_id");
	}
}
if($data->type == 'wall_post_new') {
	$owner = $data->object->owner_id;
	$getMediaID = $data->object->id;
	$is_admins = $db->query("SELECT * FROM `psettings` WHERE `news` = '1'");
    while ($row = $is_admins->fetch_assoc()) {
    	$peers = $row['peer_id'];
    	$vk->sendMessage($peers, "&#9881; Чтобы не получать записи с группы введите\n!mode news");
    	$vk->sendWall($peers, $owner, $getMediaID);
    }

	
}
// ====== *************** ============
// ====== *************** ============  
// ====== *************** ============
