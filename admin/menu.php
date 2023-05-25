<?
IncludeModuleLangFile(__FILE__);
$MODULE_ID = 'medialine.base';

use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Loader;
Loc::loadMessages(__FILE__);


if ($USER->IsAdmin())
{
    return array(
        array(
            "parent_menu" => "global_menu_services",
            "section" => $MODULE_ID,
            "sort" => 2,
            "text" => Loc::GetMessage("MEDIALINE_COPY_DATABASE_MODULE"),
            "title" => Loc::GetMessage("MEDIALINE_COPY_DATABASE_MODULE"),
            "url" => "copy_remote_database_files.php",
            "icon" => $MODULE_ID."_menu_icon",
            "page_icon" => $MODULE_ID."_page_icon",
            "more_url" => array(),
            "items_id" => "menu_".$MODULE_ID,
            "items" => array()
        )
    );
}
else
{
    return false;
}