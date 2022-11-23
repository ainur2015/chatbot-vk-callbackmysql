<?php
require  './vendor/autoload.php';
use Krugozor\Database\Mysql\Mysql as Mysql;
$db = Mysql::create("host", "username", "password")->setDatabaseName("db")->setCharset("utf8");
$shop_id = 'anypay'; // id проекта
$secret_key = 'anyypay'; // секретный ключ который мы генерировали
class user{
	public function regUserPeer($user_id, $peer_id, $c){
		if($c->query("SELECT * FROM `users_settings` WHERE `user_id` = $user_id")->getNumRows()) {
        }else{
            return "no";
        }
	}
}
class sql
{
    public function isRetCMD($connect, $peer_id){
        $onoroff = $connect->query("SELECT * FROM psettings WHERE peer_id = $peer_id")->fetch_assoc()['reiting'];
        if($onoroff == '1'){
            return true;
        }else{
            return false;
        }
    }
	

	public function isBanCMD($connect, $id){
        $onoroff = $connect->query("SELECT * FROM users_settings WHERE user_id = $id")->fetch_assoc()['ban'];
        if($onoroff == '1'){
            return true;
        }else{
            return false;
        }
    }
	
	public function isRepBanCMD($connect, $id){
        $onoroff = $connect->query("SELECT * FROM users_settings WHERE user_id = $id")->fetch_assoc()['rep'];
        if($onoroff == '1'){
            return true;
        }else{
            return false;
        }
    }
	
	public function isOMuteCMD($connect, $id){
        $onoroff = $connect->query("SELECT * FROM admin WHERE vk_id = $id AND peer_id = $peer_id")->fetch_assoc()['type'];
        if($onoroff == '6'){
            return true;
        }else{
            return false;
        }
    }
	
	
	public function isAdm1CMD($connect, $peer_id){
       $test = $vk->request('messages.getConversationMembers', ['peer_id' => $peer_id]);
		$tester = $test["items"][0]["member_id"];
		
	   $onoroff = $connect->query("SELECT * FROM admin WHERE peer_id = $peer_id")->fetch_assoc()['$tester'];
         if($onoroff == $tester){
            return true;
        }else{
           
			return false;
        }
    }
	public function isBotCmd($connect, $peer_id){
        $onoroff = $connect->query("SELECT * FROM psettings WHERE peer_id = $peer_id")->fetch_assoc()['bot'];
        if($onoroff == '1'){
            return true;
			return $vk->sendMessage($peer_id, "$userError Бот отключен");
        }else{
            return false;
        }
    }
	public function isRobPay($connect, $peer_id){
        $onoroff = $connect->query("SELECT * FROM psettings WHERE peer_id = $peer_id")->fetch_assoc()['robertpay'];
        if($onoroff == '1'){
            return true;
        }else{
            return false;
        }
    }
    public function days($day) 
    { 
        $a=substr($day,strlen($day)-1,1); 
        if($a==1) $str="день"; 
        if($a >= 2 && $a <= 4) $str="дня"; 
        if($a >=5 && $a <=9 || $a == 0) $str="дней"; 
        return $str; 
    }
    public function isTipOn($connect, $peer_id){
    	$onoroff = $connect->query("SELECT * FROM psettings WHERE peer_id = $peer_id")->fetch_assoc()['tip'];
        if($onoroff == '1'){
            return false;
        }else{
            return true;
        }
    }
    public function isUserAdmin($connect, $user_id, $peer_id)
    {
        $xxmmm = $connect->query("SELECT * FROM admin WHERE vk_id = $user_id AND peer_id = $peer_id")->getNumRows();
        if($xxmmm)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    public function isUserAdminLevel($connect, $user_id, $peer_id, $level) {
        $isADMIN = $connect->query("SELECT * FROM admin WHERE vk_id = $user_id AND peer_id = $peer_id");
        if($isADMIN->getNumRows())
        {
            if($isADMIN->fetch_assoc()['type'] >= $level){
                return true;
            }else{
                return false;
            }
        }
        else
        {
            return false;
        }
    }
    public function regUser($connect, $user_id){
        if($connect->query("SELECT * FROM users_settings WHERE user_id = $user_id")->getNumRows()) {
            return false;
        }else{
            $connect->query("INSERT INTO `users_settings` (`user_id`, `nickname`, `megacoins`, `reiting_all`) VALUES ('$user_id',null, '0', '0')");
            return true;
        }
    }
    public function countMessage($connect, $user_id, $peer_id)
    {
        $old = $connect->query("SELECT * FROM users WHERE user_id = $user_id AND peer_id = $peer_id")->fetch_assoc()['messages'];
        $new = $old+ 1;
        $new = $connect->query("UPDATE `users` SET `messages` = $new WHERE `user_id` = $user_id AND `peer_id` = $peer_id");
    }
    function haveWarn($connect, $user_id, $peer_id) {
    	if($connect->query("SELECT * FROM warns WHERE user_id = $user_id AND peer_id = $peer_id")->getNumRows()){
    		return true;
    	}else{
    		return false;
    	}
    }
    function unWarn($vk_con, $id, $connect, $user_id, $peer_id, $reason){
    	$userwarn = "@id$user_id(пользователя)";
    	$userError = "@id".$id."(⚠)";
		$userSuc = "&#9989;";
		
    	$reason = "&#9993; Причина снятия: $reason";
		if($connect->query("SELECT * FROM warns WHERE peer_id=$peer_id AND user_id=$user_id")->getNumRows()){
			$warns = $connect->query("SELECT warns FROM warns WHERE peer_id=$peer_id AND user_id=$user_id")->fetch_assoc()['warns'];
			if($warns == 1){
				$end = $connect->query("DELETE FROM warns WHERE peer_id=$peer_id AND user_id=$user_id");
				if($end){
					return "$userSuc все предупреждения у $userwarn были сняты\n\n$reason";
				}
			}
			if($warns >= 2){
				$new = $warns - 1;
				$end = $connect->query("UPDATE `warns` SET `warns` = '1' WHERE user_id = $user_id AND peer_id = $peer_id");
				if($end){
					return "$userSuc теперь у $userwarn 1/3 предупреждений\n\n$reason";
				}
			}
		}else{
			return "$userError предупреждения $userwarn отсутствуют!";
		}
    }
    function addWarn($vk_con, $connect, $user_id, $peer_id, $reason){
	    $userwarn = "@id$user_id(пользователя)";
	    $userwarn1 = "@id$user_id(пользователь)";
	    $chat_id = $peer_id - 2000000000;
	    if($connect->query("SELECT * FROM warns WHERE user_id = $user_id AND peer_id = $peer_id")->getNumRows()){
		    $result = $connect->query("SELECT * FROM warns WHERE user_id = $user_id AND peer_id = $peer_id")->fetch_assoc()['warns'];
		    $nr = $result+1;
		    if($nr >= 3){
		    	$connect->query("DELETE FROM warns WHERE user_id = $user_id AND peer_id = $peer_id");
		    	$vk_con->request('messages.removeChatUser', ['chat_id' => $chat_id, 'member_id' => $user_id]);
		    	return "&#9881; $userwarn1 был исключен за 3/3 предупреждений\n\n&#9993; &#65039; Причина выдачи: $reason";
		    }
		    $end = $connect->query("UPDATE `warns` SET `warns` = '2', `reason` = '$reason' WHERE user_id = $user_id AND peer_id = $peer_id");
	        if($end){
		      	return "&#9881; теперь у $userwarn &#9763;2/3 предупреждений\n\n&#9993; &#65039; Причина выдачи: $reason";
	        }
	    }else{
	    	$end = $connect->query("INSERT INTO `warns`(`user_id`, `peer_id`, `warns`, `reason`) VALUES ($user_id, $peer_id, '1', '$reason')");
	        if($end){
		      	return "&#9881; теперь у $userwarn &#9763;1/3 предупреждений\n\n&#9993; &#65039; Причина выдачи: $reason";
	        }
	    }
            	
    }
    function userInfo($connect_id,$vk_connection, $user_id, $peer_id, $type){
	
	    $tempInfo = $vk_connection->request("users.get", ["user_ids" => $user_id]); 
	    $tempName = $tempInfo[0]['first_name']; 
		$tempSurName = $tempInfo[0]['last_name']; 
		
			
		($type == 'iam') ? $header = "@id$user_id($tempName $tempSurName)" : $header = "Это - $tempName $tempSurName";
			
			
		if($connect_id->query("SELECT * FROM users_settings WHERE user_id = $user_id")->fetch_assoc()['nickname'] != null){
		$nicknamess = $connect_id->query("SELECT * FROM users_settings WHERE user_id = $user_id")->fetch_assoc()['nickname'];
		$nicknamess = "&#128100; Ник-Нейм: $nicknamess";}else{$nicknamess="&#128100; Ник-Нейм: отсутсвует";}
		$reiting = $connect_id->query("SELECT * FROM users WHERE user_id = $user_id AND peer_id = $peer_id")->fetch_assoc()['reiting'];
		$reiting_all = $connect_id->query("SELECT * FROM users_settings WHERE user_id = $user_id")->fetch_assoc()['reiting_all'];
		$getStatus = $connect_id->query("SELECT * FROM users WHERE user_id = $user_id AND peer_id = $peer_id")->fetch_assoc()['status'];
		$join_date = $connect_id->query("SELECT * FROM users WHERE user_id = $user_id AND peer_id = $peer_id")->fetch_assoc()['join_date'];
		$join_date = mb_substr($join_date,0,10);
		//$join_date = str_replace('-', ' ', $join_date); // PHP код
		
		$messages = $connect_id->query("SELECT * FROM users WHERE user_id = $user_id AND peer_id = $peer_id")->fetch_assoc()['messages'];
		return "$header\n$nicknamess\n&#127894; Статус: $getStatus\n&#128197; В беседе с $join_date\n&#9993; Сообщений: $messages\n\n&#128171; Рейтинг: $reiting  &#128165; $reiting_all";
	}
    
}
class mega_api
{
	public function getUserInfo($name){
		$userError = "⚠";
		$url = 'http://aurhost.ru/api/users/'.$name;
		$json = file_get_contents($url);
		$data = json_decode($json, TRUE);
		if($data == '' || $data == null || isset($data['error'])) {return "$userError пользователь $name не найден";}
		$b = $data['ban'];
		if($data['staff'] != false){$dop = $data['staff'];}else{$dop = '';}
		if($b == false){$ban_text = "Не Забанен";}
		if($b != false && $data['timeleft'] >= 0){
			$gett = date('Yгг. nм. dд. в H:i:s', $data['ban']['timeleft']);
			$ban_text = 'Забанен, Бан выдан: '.$data['ban']['bannedby'].', Причина: '.$data['ban']['reason'].', Разбан через: '.$gett.'';
		}
		$last_login = date('d.m.Y', $data['lastlogin']);
		$chars = ['&1','&2','&3','&4','&5','&6','&7','&8','&9','&a','&b','&c','&d','&e','&f','&l','&k','&r','&m','&n','&o', '[',']'];
		$chars2 = ['§1','§2','§3','§4','§5','§6','§7','§8','§9','§a','§b','§c','§d','§e','§f','§l','§k','§r','§m','§o', '&n','§n','[',']','&1','&2','&3','&4','&5','&6','&7','&8','&9','&a','&b','&c','&d','&e','&f','&l','&k','&r','&m','&o'];// символы для удаления
		$donate = str_replace($chars, '', $data['groupprefix']); // PHP код
		$prefix = str_replace($chars2, '', $data['prefix']);
		if($prefix == ''){
			$prefix = 'Нет';
		}else if($donate == ''){
			$donate = 'Игрок';
		}
		$mtime = $data['mtime']/60/60;
		$mtime = round($mtime);
		$mtime2 = $data['mtime']/86400;
		$mtime2 = round($mtime2);
		if($data['lastlogin'] != time()){$ingame = 'В игре, сервер ( '.$data['lastserver'].' )';}else{$ingame = 'Был в игре ('.time().');';}
		
		//$get = file_get_contents("https://minecraft-inside.ru/uploads/nick/3d/$name.png");
		//file_put_contents("photos/$name.png", $get);
        
        return "Ник игрока: ".$name."\n".$dop."\n\nУровень: ".$data['level']."\nНаиграл: ".$mtime2."д. // ".$mtime."ч.\n\nПоследний раз заходил: ".$last_login."\nПоследний сервер: ".$data['lastserver']."\n\nМегаКоинов: ".$data['balance']."\n
        Статус: ".$ban_text."\n\nДонат: ".$donate."\nПрефикс: $prefix\nЦветной префикс: ".$data['prefix']."\n\nРеферальная ссылка: megacraft.so/?ref=".$name." ";
		
	}
}
class vk_api{
    /**
     * Токен
     * @var string

    private $token = '';
    private $v = '';
    /**
     * @param string $token Токен
     */
    public function __construct($token, $v){
        $this->token = $token;
        $this->v = $v;
    }
    public function sendDocMessage($sendID, $id_owner, $id_doc){
        if ($sendID != 0 and $sendID != '0') {
            return $this->request('messages.send',array('attachment'=>"doc". $id_owner . "_" . $id_doc,'user_id'=>$sendID));
        } else {
            return true;
        }
    }
    public function sendMessage($sendID,$message,$mention=false){
        if ($sendID != 0 and $sendID != '0') {
        	if($mention == true){
        		return $this->request('messages.send',array('message'=>$message, 'peer_id'=>$sendID, 'disable_mentions' => 1));	
        	}
			return $this->request('messages.send',array('message'=>$message, 'peer_id'=>$sendID));	
        } else {
            return true;
        }
    }
    public function sendOK(){
		//ob_end_clean();
		header("Connection: close\r\n");
		header("Content-Encoding: none\r\n");
		ignore_user_abort(true);
		ob_start();
		echo ('ok');
		$size = ob_get_length();
		header("Content-Length: $size");
		ob_end_flush();
		flush();
    }

	
    public function sendButton($sendID, $message, $gl_massiv = [], $one_time = False) {
        $buttons = [];
        $i = 0;
        foreach ($gl_massiv as $button_str) {
            $j = 0;
              foreach ($button_str as $button) {
                $color = $this->replaceColor($button[2]);
                $buttons[$i][$j]["action"]["type"] = "text";
                if ($button[0] != null)
                    $buttons[$i][$j]["action"]["payload"] = json_encode($button[0], JSON_UNESCAPED_UNICODE);
                $buttons[$i][$j]["action"]["label"] = $button[1];
                $buttons[$i][$j]["color"] = $color;
                $j++;
            }
            $i++;
        }
        $buttons = array(
            "one_time" => $one_time,
            "buttons" => $buttons);
        $buttons = json_encode($buttons, JSON_UNESCAPED_UNICODE);
        //echo $buttons;
        return $this->request('messages.send',array('message'=>$message, 'peer_id'=>$sendID, 'keyboard'=>$buttons));
    }
    public function sendDocuments($sendID, $selector = 'doc'){
        if ($selector == 'doc')
            return $this->request('docs.getMessagesUploadServer',array('type'=>'doc','peer_id'=>$sendID));
        else
            return $this->request('photos.getMessagesUploadServer',array('peer_id'=>$sendID));
    }
    public function saveDocuments($file, $titile){
        return $this->request('docs.save',array('file'=>$file, 'title'=>$titile));
    }
	public function sendWall($sendID, $owner_id, $media_id){
		$construct = 'wall'.$owner_id.'_'.$media_id.'';
        return $this->request('messages.send', array('peer_id' => $sendID, 'attachment' => $construct));
    }
    public function savePhoto($photo, $server, $hash){
        return $this->request('photos.saveMessagesPhoto',array('photo'=>$photo, 'server'=>$server, 'hash' => $hash));
    }
    /**
     * Запрос к VK
     * @param string $method Метод
     * @param array $params Параметры
     * @return mixed|null
     */
    public function request($method,$params=array()){
        $url = 'https://api.vk.com/method/'.$method;
        $params['access_token']=$this->token;
        $params['v']=$this->v;
        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Content-Type:multipart/form-data"
            ));
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            $result = json_decode(curl_exec($ch), True);
            curl_close($ch);
        } else {
            $result = json_decode(file_get_contents($url, true, stream_context_create(array(
                'http' => array(
                    'method'  => 'POST',
                    'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                    'content' => http_build_query($params)
                )
            ))), true);
        }
        if (isset($result['response']))
            return $result['response'];
        else
            return $result;
    }
    private function replaceColor($color) {
        switch ($color) {
            case 'red':
                $color = 'negative';
                break;
            case 'green':
                $color = 'positive';
                break;
            case 'white':
                $color = 'default';
                break;
            case 'blue':
                $color = 'primary';
                break;
            default:
                # code...
                break;
        }
        return $color;
    }
    private function sendFiles($url, $local_file_path, $type = 'file') {
        $post_fields = array(
            $type => new CURLFile(realpath($local_file_path))
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type:multipart/form-data"
        ));
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
        $output = curl_exec($ch);
        return $output;
    }
    public function sendImage($id, $local_file_path, $text)
    {
        $upload_url = $this->sendDocuments($id, 'photo')['upload_url'];
        $answer_vk = json_decode($this->sendFiles($upload_url, $local_file_path, 'photo'), true);
        $upload_file = $this->savePhoto($answer_vk['photo'], $answer_vk['server'], $answer_vk['hash']);
        $this->request('messages.send', array('message'=> $text,'attachment' => "photo" . $upload_file[0]['owner_id'] . "_" . $upload_file[0]['id'], 'peer_id' => $id));
        return 1;
    }
    public function isUserInGroup($member_id,$group_id){
    	$mem = $this->request("groups.isMember", array('group_id' => $group_id, 'user_id' => $member_id));
		if($mem == '0')
			return false;
		else 
			return true;
    }
	public function isUserChatMember($member_id, $peer_id) {
        $api = $this->request("messages.getConversationMembers", array('peer_id' => $peer_id));
        foreach($api['profiles'] as $profile) {
            $ids = $profile['id'];
            if($ids == $member_id){
                return true;
            }
        }
        return false;
	}
}