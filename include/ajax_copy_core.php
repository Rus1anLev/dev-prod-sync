<?
use Bitrix\Main\Config\Configuration;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Application;

$MODULE_ID = 'medialine.base';
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");

$config_db = Configuration::getValue('connections')['default'];
$config_db['core_dir'] = Option::get($MODULE_ID, "CORE_DIR", "/home/bitrix/www/bitrix/");

$arDefaultOptions = Option::getDefaults($MODULE_ID);

$context = Application::getInstance()->getContext();
$request = $context->getRequest();

$exclude = "{'cache','backup'}";

if($request->getPost('remote') == 'Y'){
    shell_exec('rsync --rsh=\'ssh -p '.$arDefaultOptions['DEV_REMOTE_PORT'].'\' -a --exclude='.$exclude.' '.$config_db['core_dir'].' '.$arDefaultOptions['DEV_REMOTE_HOST'].':'.$arDefaultOptions['DEV_REMOTE_CORE']);
}else{
    shell_exec('rsync -a --exclude='.$exclude.' '.$config_db['core_dir'].' '.$arDefaultOptions['DEV_REMOTE_CORE']);
}

$result = array('success' => 'Y', 'message' => 'Файлы ядра залиты на dev сайт',  'sub_message' => '');
print json_encode($result);
