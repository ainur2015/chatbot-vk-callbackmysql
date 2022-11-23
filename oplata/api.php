<?php
// Узнаем свой баланс на ANYPAY для этого нужно создать ключ API в настройках

$method = 'balance';
$api_id = 'BLABLABLA';
$api_key = 'БОЛЬШОЙ КЛЮЧ СГЕНЕРИРОВАНЫЙ';

/*Из документации AnyPay
Подпись запроса. Формируется путем склеивания параметров и создания хэша
hash('sha256', 'balance[API_ID][API_KEY]')*/

$sign = hash('sha256', $method.$api_id.$api_key);
$anypay = json_decode(file_get_contents('https://anypay.io/api/'.$method.'/'.$api_id.'?sign='.$sign), true);

?>


<?php print $anypay['result']['balance'];  ?></b> RUB<br>