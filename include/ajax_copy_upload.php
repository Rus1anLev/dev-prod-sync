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
$config_db['upload_dir'] = Option::get($MODULE_ID, "UPLOAD_DIR", "/home/bitrix/www/upload/");

$arDefaultOptions = Option::getDefaults($MODULE_ID);

$context = Application::getInstance()->getContext();
$request = $context->getRequest();

$tg->sendMessage('Запущен процесс синхронизации папки upload с prod на dev');
if($request->getPost('remote') == 'Y'){
    shell_exec('rsync --rsh=\'ssh -p '.$arDefaultOptions['DEV_REMOTE_PORT'].'\' -a '.$config_db['upload_dir'].' '.$arDefaultOptions['DEV_REMOTE_HOST'].':'.$arDefaultOptions['DEV_REMOTE_UPLOAD']);
}else{
    shell_exec('rsync -a '.$config_db['upload_dir'].' '.$arDefaultOptions['DEV_REMOTE_UPLOAD']);
}

$result = array('success' => 'Y', 'message' => 'Файлы upload залиты на dev сайт',  'sub_message' => '');
$tg->sendMessage('Файлы upload залиты на dev сайт');
print json_encode($result);
