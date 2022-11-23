# chatbot-vk-callbackmysql
Чат менеджер, помощник для бесед VK . Callback Mysql.

# Команды самого чат-менеджера:

1) !кто я - узнать информацию о себе

2) !Стат - Статистика беседы
3) !бот - стабильность бота
4) !адмлист | !staff | !админы - кто админ в беседе?
5) !ген (колв символов) - придумать пароль
6) !настройки - настройки беседы
7) !создать заметку (описание) - Создает заметки
8) !удалить заметку (ид) - Удаляет заметки
9) !заметка (ид) - Показывает заметки
10) !админ (1>4 lvl) (Enter) (Ид пользователя) - Выдает админку
11) !инфо (ид,сообщение) - Информация о пользователе
12) !кик | !kick| !кикнуть - Выгнать пользователя из беседы.
13) !топ - Топ по рейтингу в беседе
14) активные все - Топ по активности сообщений общий.
15) активных - топ сообщений в беседе
16) рейтинг весь - Топ общему рейтингу.
17) !ник (ник) - сменить свой ник
18) !статус (ид)
(текст) - Указаь статус в беседе свой.
19) !лог - История последних 10-ти действий
20) !снять пред - Снять предупреждение
21)!пред - Выдать предупреждение
22) !призыв (сообщение не обяз) - Вызывать всех в беседу
23) !приветствие
(текст) - Приветствие при заходе в беседу.
24) !кто онлайн | !онлайн - Кто в сети в беседе
25) !регалл - Активация беседы.
26) !чатобновить - Обновить чат
27) !клан создать - Создать клан
28) !мои кланы - Узнать мои кланы.
29) !кланы беседы - узнать информацию о клане
30) !клан расформ - Расформировать клан
31) !клан покинуть - Покинуть клан
32) !клан понизить - Понизить админа клана
33) !клан повысить - Повысить админа клана
34) !клан инфо - Информация о каком-то клане.
35) !клан вступить - Вступить в какой-то клан
36) !5lvl, !4lvl, 3lvl, 2lvl, 1lvl - Смена названия (в адмлист).
37) !mode (функция) - отключения вкл функций.

- Техническая команда:
!co - Повторная выдача создателя беседы.

- Robert PAY - Донатёрская услуга
Команды:
!rp (Сумма) - Пожертвовать беседу средства на развитие.
!arp (Текст) - Изменить текст пожертвования (5 LVL)

# Объяснения в файлах папках бота.
1) vk_api.php - хранилище функций бота.
2) bot.php - Исходный код всего бота.
3) Остальные файлы не аргументирую.
4) vendor - папка библиотеки бота.
5) skins - Папка для обычных фоток чтобы потом их выгружать.
6) oplata - Бот не является моим исполнением, AnyPay бот для пожертвований средств.

# Настройка бота.

1) Создаем группу в VK.
2) Ключи доступа и создаем ключ доступа и сохраняем его.
3)Callback API // Настройки сервера
- Версия API: 5.81
- Адрес: вводим путь до bot.php (https://doman/bot.php)
4) И сохраняем следующий код: Строка, которую должен вернуть сервер: 00000000
5)Типы событий
- Установливаем повсюду галочки
6) Переходим в файл bot.php
- VK_KEY вставляем наш ключ доступа туда.
- ACCESS_KEY вставляем туда код подтверждение (Строка, которую должен вернуть сервер)
7) Следующее переходим в хостинг-сайта и создаем mysql (база данных)
- Заливаем базу bd.sql
- Запоминаем все данные от mysql
- Переходим обратно в файл vk_api.php и находим строку: 4 database (DB)
- host вводим наш хост ip-адрес у всех по умолчанию 127.0.0.1 либо публичный ipv4 .
- username логин в mysql указываем
- password пароль от базы данных
- db указываем название базы данных

8) Настройка AnyPAY (папка: oplata и vk_api.php
- https://kotoff.net/article/php/57-gotovyj-obrabotchik-platezhej-anypayio-dlja-botov-vk-i-sajtov.html помощь для новичков.

# Подробности о боте.
 Бот был создан для помощи со крупнейшими беседами.
- Программист данного бота: vk.com/kalsc12345 о наличий серьёзных багов и предложений к нему.
- QIWI для пожертвований Nick: MSKTOP
- YOOMoney для пожертвований: 410018686984844
