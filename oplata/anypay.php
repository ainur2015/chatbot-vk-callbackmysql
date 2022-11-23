<?php
// ============ KOTOFF.NET ============== //

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors','on');
ini_set('error_log', './bot_any.log');


require_once('simplevk-master/autoload.php');
require  './vendor/autoload.php';
use DigitalStar\vk_api\VK_api as vk_api;
use Krugozor\Database\Mysql\Mysql as Mysql;

$date_today = date("m.d.y"); //присвоено 12.03.15
date_default_timezone_set("Europe/Moscow");
$msk = date('H:i:s');
$signature = md5($shop_id.':'.$_REQUEST['amount'].':'.$_REQUEST['pay_id'].':'.$secret_key);
$db = Mysql::create("localhost", "user", "password")->setDatabaseName("db")->setCharset("utf8"); // база даннных настрйка
$shop_id = 'ANYPAY'; // id проекта
$secret_key = 'ANYPAY1'; // секретный ключ который мы генерировали
const VK_KEY = "Токен";  // Токен сообщества
const VERSION = "5.101"; // Версия API VK LONGPULL
$vk = vk_api::create(VK_KEY, VERSION);
$p_id = 2000000005; // ИД беседы или юзера для доп. оповещения
// ================================================================

function getIP() { // Проверка IP
    if(isset($_SERVER['HTTP_X_REAL_IP'])) return $_SERVER['HTTP_X_REAL_IP'];
    return $_SERVER['REMOTE_ADDR'];
}


/* этот код проверяет с какого IP пришло оповещение, я его не использую, так как есть шанс смены этих IP и тогда все отвалится, при желании раскоментить и следить за списком IP адресов ANYPAY
    $arr_ip = array(
    '185.162.128.38',
    '185.162.128.39',
    '185.162.128.88'
);

if (!in_array($_SERVER['REMOTE_ADDR'], $arr_ip)) {
    die("bad ip!");
}*/





$ammount = $_REQUEST['amount']; // Сумма прихода
$pay_id = $_REQUEST['pay_id']; // ID плательщика, передается в ссылке на оплату
$method = $_REQUEST['method']; // Способ оплаты
$desc = $_REQUEST['desc']; // Способ оплаты
// Оплата прошла успешно, можно проводить операцию и зачислять средства на баланс!

$db->query("UPDATE psettings SET balance = balance + $ammount WHERE peer_id = '$pay_id'"); // Пополняем баланс юзера, переписать под свой запрос!
$db->query("INSERT INTO RP (peer_id, user_id, summa, data, oplata, sign, sign1) values('$pay_id', '$desc', '$ammount', '$date_today+$msk', '1', '$sign', '$sign1')"); // Получаем данные из колонки vk_id


$user_info = $vk->userInfo($pay_id);
$first_name = $user_info['first_name'];
$last_name = $user_info['last_name'];

$vk->sendMessage($p_id, "Пришел платеж Robert PAY\n\nСумма прихода: $ammount\nID плательщика: $desc \n Беседа: $pay_id \nВК: @id$user_id ($first_name $last_name)"); // Сообщаем себе о новом платеже, можно удалить при желании
$vk->sendMessage($user_id, "Ваш баланс пополнен на $ammount рублей. Красавчик :)"); // Сообщаем юзеру что его баланс пополнен :) Он красавчик :))))

die('OK');


// Так формируется ссылка в вашем коде, не раскоменчивать, использовать в своем коде, тут указано как ПРИМЕР!!!!!!!!!

/*$sum = 100; // 100 рублей например
$shop_id = '123'; // id проекта
$secret_key = 'KEY'; // секретный ключ который мы генерировали
$sign = md5('RUB:'.$sum.':'.$secret_key.':'.$shop_id.':ИДПЛАТЕЛЬЩИКА');
$url = "https://anypay.io/merchant?merchant_id=$shop_id&amount=$sum&pay_id=ИДПЛАТЕЛЬЩИКА&sign=$sign";
$vk->sendMessage($peer_id, "Для пополнения баланса на $sum рублей, перейдите по ссылке $url\n");*/


?>

