<?
use Bitrix\Main\Config\Configuration;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Application;

$MODULE_ID = 'medialine.base';
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");

$config_db = Configuration::getValue('connections')['default'];
$config_db['site_dir'] = "/home/bitrix"; //Пока хардкодом


$arDefaultOptions = Option::getDefaults($MODULE_ID);

$context = Application::getInstance()->getContext();
$request = $context->getRequest();


$result = ['success' =>'N'];
$step = intval($request->getPost('step'));
if(!empty($step)){
    switch($step) {
        case 1:
            // Дампм бд со стейджа
            shell_exec('cd '.$config_db['site_dir'].' && mysqldump -u'.$config_db['login'].' -p\''.$config_db['password'].'\' '.$config_db['database'].' > '.$config_db['database'].'.sql');
            $result = array('success' => 'Y', 'message' => 'Дамп stage бд создан',  'sub_message' => '');
            break;
        case 2:
            // Заливаем на dev
            if($request->getPost('remote') == 'Y'){
                shell_exec('rsync --rsh=\'ssh -p '.$arDefaultOptions['DEV_REMOTE_PORT'].'\' '.$config_db['site_dir'].'/'.$config_db['database'].'.sql '.$arDefaultOptions['DEV_REMOTE_HOST'].':'.$arDefaultOptions['DEV_SITE_DIR']);
                shell_exec('ssh -p '.$arDefaultOptions['DEV_REMOTE_PORT'].' '.$arDefaultOptions['DEV_REMOTE_HOST'].' "mysql -u '.$arDefaultOptions['DEV_DATABASE_LOGIN'].' -p'.$arDefaultOptions['DEV_DATABASE_PASSWORD'].' '.$arDefaultOptions['DEV_DATABASE_NAME'].' < '.$arDefaultOptions['DEV_SITE_DIR'].'/'.$config_db['database'].'.sql"');
            }else{
                shell_exec('mysql -u'.$arDefaultOptions['DEV_DATABASE_LOGIN'].' -p\''.$arDefaultOptions['DEV_DATABASE_PASSWORD'].'\' '.$arDefaultOptions['DEV_DATABASE_NAME'].' < '.$config_db['site_dir'].'/'.$config_db['database'].'.sql');
            }
            shell_exec('rm -f '.$config_db['site_dir'].'/'.$config_db['database'].'.sql');

            $result = array('success' => 'Y', 'message' => 'Stage бд залита на dev сайт',  'sub_message' => '');
            break;
        default:
            $result = array('success' => 'N', 'message' => '', 'sub_message' => '');
    }
}

if($_REQUEST['step'] > 0){
    print json_encode($result);
}