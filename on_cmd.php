<? 
	if($message == '+рейтинг'){
		if($sql->isUserAdminLevel($db, $id, $peer_id, '3') != true){
			return $vk->sendMessage($peer_id, ERRORS[0]);
		}
		$onfun = $db->query("UPDATE `psettings` SET `reiting`='0' WHERE peer_id = $peer_id");
		if($onfun){
			$vk->sendMessage($peer_id, "Система рейтинга: включена &#9989;");
		}
	}
	else if($message == '-рейтинг'){
		if($sql->isUserAdminLevel($db, $id, $peer_id, '3') != true){
			return $vk->sendMessage($peer_id, ERRORS[0]);
		}
		$onfun = $db->query("UPDATE `psettings` SET `reiting`='1' WHERE peer_id = $peer_id");
		if($onfun){
			$vk->sendMessage($peer_id, "Система рейтинга: отключена &#9989;");
		}
	}
	else if($message == '+подсказки'){
		if($sql->isUserAdminLevel($db, $id, $peer_id, '3') != true){
			return $vk->sendMessage($peer_id, "$userError у вас нет доступа к данной команде");
		}
		$onfun = $db->query("UPDATE `psettings` SET `tip`='1' WHERE peer_id = $peer_id");
		if($onfun){
			$vk->sendMessage($peer_id, "Подсказки: Включены &#9989;");
		}
	//&#9989;
	}
	else if($message == '-подсказки'){
		if($sql->isUserAdminLevel($db, $id, $peer_id, '3') != true){
			return $vk->sendMessage($peer_id, "$userError у вас нет доступа к данной команде");
		}
		$onfun = $db->query("UPDATE `psettings` SET `tip`='0' WHERE peer_id = $peer_id");
		if($onfun){
			$vk->sendMessage($peer_id, "Подсказки: Отключены &#10060;");
		}
	// &#10060;
	}
	
	else if($message == '+фанкмд'){
		if($sql->isUserAdminLevel($db, $id, $peer_id, '3') != true){
			return $vk->sendMessage($peer_id, "$userError у вас нет доступа к данной команде");
		}
		$onfun = $db->query("UPDATE `psettings` SET `fun_cmd`='0' WHERE peer_id = $peer_id");
		if($onfun){
			$vk->sendMessage($peer_id, "Развлекательные команды разрешены &#9989;");
		}
	}
	else if($message == '-фанкмд'){
		if($sql->isUserAdminLevel($db, $id, $peer_id, '3') != true){
			return $vk->sendMessage($peer_id, "$userError у вас нет доступа к данной команде");
		}
		$onfun = $db->query("UPDATE `psettings` SET `fun_cmd`='1' WHERE peer_id = $peer_id");
		if($onfun){
			$vk->sendMessage($peer_id, "Развлекательные команды отключены &#10060;");
		}
	// &#10060;
	}
	else if($message == '+автокик'){
		if($sql->isUserAdminLevel($db, $id, $peer_id, '3') != true){
			return $vk->sendMessage($peer_id, "$userError у вас нет доступа к данной команде");
		}
		$autokk = $db->query("UPDATE `psettings` SET `autokick`='1' WHERE peer_id = $peer_id");
		if($autokk){
			$vk->sendMessage($peer_id, "Автокик: включен &#9989;");
		}
	//&#9989;
	}
	else if($message == '-автокик'){
		if($sql->isUserAdminLevel($db, $id, $peer_id, '3') != true){
			return $vk->sendMessage($peer_id, "$userError у вас нет доступа к данной команде");
		}
		$autokk = $db->query("UPDATE `psettings` SET `autokick`='0' WHERE peer_id = $peer_id");
		if($autokk){
			$vk->sendMessage($peer_id, "Автокик: отключен &#10060;");
		}
	// &#10060;
	}
	else if($message == '+уведомлять'){
		if($sql->isUserAdminLevel($db, $id, $peer_id, '3') != true){
			return $vk->sendMessage($peer_id, "$userError у вас нет доступа к данной команде");
		}
		$autokk = $db->query("UPDATE `psettings` SET `upom`='1' WHERE peer_id = $peer_id");
		if($autokk){
			$vk->sendMessage($peer_id, "Теперь вы будете получать записи с группы &#9989;");
		}
	//&#9989;
	}
	else if($message == '-уведомлять'){
		if($sql->isUserAdminLevel($db, $id, $peer_id, '3') != true){
			return $vk->sendMessage($peer_id, "$userError у вас нет доступа к данной команде");
		}
		$autokk = $db->query("UPDATE `psettings` SET `upom`='0' WHERE peer_id = $peer_id");
		if($autokk){
			$vk->sendMessage($peer_id, "Теперь вы не будете получать записи с группы &#10060;");
		}
	// &#10060;
	}