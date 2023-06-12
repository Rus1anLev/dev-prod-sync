1. Установка: в папке local/modules на PROD сервере сделать
git clone git@github.com:Rus1anLev/dev-prod-sync.git
2. Зайти /bitrix/admin/partner_modules.php и установить модуль 	MEDIALINE: DB-dump модуль (medialine.deploy)
При установке необходимо будет выборать вариант установки, где 
	1. Дев версии файлов и бд лежат на этом сервере
	2. Дев версии файлов и бд лежат на удаленном сервере
3. В зависимости от выбранной схемы отредактировать файл default_option.local_sample.php или default_option.remote_sample.php и переименовать его в default_option.php
4. Зайти в Сервисы -> Medialine Deploy

——
TODO
Логировать процесс и результат работы.
Сделать понятным коннекшены.