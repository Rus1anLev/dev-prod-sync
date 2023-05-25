<?
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Loader;

Loc::loadMessages(__FILE__);

$module_id = 'medialine.base';

if($_REQUEST['autosave_id']){
    $insertManually = $_REQUEST['USE_MANUALLY'] ? $_REQUEST['USE_MANUALLY'] : 'N';

}else{
    $insertManually = \Bitrix\Main\Config\Option::get($module_id, "USE_MANUALLY", '');
}

$showRightsTab = true;

$arTabs = array(
    array(
        'DIV' => 'edit1',
        'TAB' => Loc::GetMessage('MSG_TAB_NAME'),
        'ICON' => '',
        'TITLE' => Loc::GetMessage('MSG_TAB_TITLE')
    )
);

$arGroups = array(
    'MAIN' => array('TITLE' => Loc::GetMessage('MSG_GROUP_NAME_MAIN'), 'TAB' => 0),
    'META_STRING' => array('TITLE' => Loc::GetMessage('MSG_META_STRING_TITLE'), 'TAB' => 0),
    'BODY_TOP' => array('TITLE' => Loc::GetMessage('MSG_BODY_TOP_TITLE'), 'TAB' => 0),
    'BODY_FOTER' => array('TITLE' => Loc::GetMessage('MSG_BODY_FOTER_TITLE'), 'TAB' => 0),
);

//example
$arOptions = array(
    'CORE_DIR' => array(
        'GROUP' => 'MAIN',
        'TITLE' => Loc::GetMessage('ML_CORE_DIR'),
        'TYPE' => 'STRING',
        'REFRESH' => 'N',
        'SORT' => '5',
        'DEFAULT' => "/home/bitrix/www/bitrix/"
    ),
    'UPLOAD_DIR' => array(
        'GROUP' => 'MAIN',
        'TITLE' => Loc::GetMessage('ML_UPLOAD_DIR'),
        'TYPE' => 'STRING',
        'REFRESH' => 'N',
        'SORT' => '5',
        'DEFAULT' => "/home/bitrix/www/upload/"
    ),
);

$dbSites = \Bitrix\Main\SiteTable::getList(array(
    'filter' => array('ACTIVE' => 'Y')
));
$aSitesTabs = $arOptionsSite = array();
while ($site = $dbSites->fetch()) {
    $arOptionsSite[$site['LID']] = $arOptions;
    $aSitesTabs[] = array('DIV' => 'opt_site_'.$site['LID'], "TAB" => '('.$site['LID'].') '.$site['NAME'], 'TITLE' => '('.$site['LID'].') '.$site['NAME'], 'LID' => $site['LID']);
}

$arOptions = $arOptionsSite;
if(Loader::IncludeModule($module_id)){
    $opt = new \Medialine\Base\Options($module_id, $aSitesTabs, $arTabs, $arGroups,  $arOptions, $showRightsTab);
    $opt->ShowHTML();
}
?>