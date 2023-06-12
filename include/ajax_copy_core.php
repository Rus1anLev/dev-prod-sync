<?
use Bitrix\Main\Config\Configuration;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Medialine\Deploy\TelegramBot;

$MODULE_ID = 'medialine.deploy';
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");


Loader::includeModule($MODULE_ID);
$tg = new TelegramBot();


$config_db = Configuration::getValue('connections')['default'];
$config_db['core_dir'] = Option::get($MODULE_ID, "CORE_DIR", "/home/bitrix/www/bitrix/");

$arDefaultOptions = Option::getDefaults($MODULE_ID);

$context = Application::getInstance()->getContext();
$request = $context->getRequest();

$exclude = "{'cache','backup'}";

$tg->sendMessage('Запущен процесс синхронизации ядра с prod на dev');
if($request->getPost('remote') == 'Y'){
    shell_exec('rsync --rsh=\'ssh -p '.$arDefaultOptions['DEV_REMOTE_PORT'].'\' -a --exclude='.$exclude.' '.$config_db['core_dir'].' '.$arDefaultOptions['DEV_REMOTE_HOST'].':'.$arDefaultOptions['DEV_REMOTE_CORE']);
}else{
    shell_exec('rsync -a --exclude='.$exclude.' '.$config_db['core_dir'].' '.$arDefaultOptions['DEV_REMOTE_CORE']);
}
$tgRes = $tg->sendMessage('Файлы ядра залиты на dev сайт');
$result = array('success' => 'Y', 'message' => 'Файлы ядра залиты на dev сайт',  'sub_message' => '', 'tg' => $tgRes);


print json_encode($result);
