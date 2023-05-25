<?
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\EventManager;
use \Bitrix\Main\ModuleManager;
use \Bitrix\Main\Config\Option;
use \Bitrix\Main\Loader;
use \Bitrix\Main\IO\File;
use \Bitrix\Main\IO\Directory;
use \Bitrix\Main\Entity\Base;
use \Bitrix\Main\Application;
use \Bitrix\Main\Config as Conf;

Loc::loadMessages(__FILE__);

if (!class_exists("medialine_base")) {

    class medialine_base extends CModule
    {
        const MODULE_ID = 'medialine.base';
        public $MODULE_ID = "medialine.base";
        public $MODULE_VERSION;
        public $MODULE_VERSION_DATE;
        public $MODULE_NAME;
        public $PARTNER_NAME;
        public $MODULE_DESCRIPTION;

        function __construct()
        {
            $arModuleVersion = array();
            include($this->GetPath() . "/install/version.php");
            if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion)) {
                $this->MODULE_VERSION = $arModuleVersion["VERSION"];
                $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
            }

            $this->MODULE_NAME = Loc::GetMessage("MEDIALINE_BASE_MODULE_NAME");
            $this->MODULE_DESCRIPTION = Loc::GetMessage("MEDIALINE_BASE_MODULE_DESCRIPTION");
            $this->PARTNER_NAME = Loc::GetMessage("MEDIALINE_BASE_PARTNER_NAME");
            $this->PARTNER_URI = Loc::GetMessage("MEDIALINE_BASE_PARTNER_URI");
            $this->MODULE_SORT = 1;
        }

        /**
         * @param bool $notDocumentRoot
         * @return mixed|string
         */
        public function GetPath($notDocumentRoot = false) // метод возвращает путь до корня модуля
        {
            if ($notDocumentRoot)
                return str_ireplace(Application::getDocumentRoot(), '', dirname(__DIR__)); // исключаем документ роот
            else
                return dirname(__DIR__); // текущая папка с полным путем можем узнать с помощью константы __DIR__ а путь то родительского каталога узнаем ф-ей dirname()
        }

        /**
         * @return bool
         * Проверяем что система поддерживает D7
         */
        public function isVersionD7()
        {
            return CheckVersion(ModuleManager::getVersion('main'), '14.00.00');
        }

        function InstallFiles($arParams = array())
        {
            CopyDirFiles(
                $this->GetPath()."/install/js/",
                $_SERVER['DOCUMENT_ROOT']."/bitrix/js/".self::MODULE_ID."/",
                true, true
            );
            CopyDirFiles(
                $this->GetPath()."/install/images/",
                $_SERVER["DOCUMENT_ROOT"]."/bitrix/images/".self::MODULE_ID."/",
                true
            );


            // Копирование файлов модуля для админки (например страницы генерации включаемых областей в админке)
            if($arParams['savedata'] == 'Y'){
                CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/local/modules/".$this->MODULE_ID."/install/admin/copy_database_files.php",
                    $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/copy_database_files.php", true, true);
                CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/local/modules/".$this->MODULE_ID."/install/admin/menu.php",
                    $_SERVER["DOCUMENT_ROOT"]."/local/modules/".$this->MODULE_ID."/admin/menu.php", true, true);
            }else{
                CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/local/modules/".$this->MODULE_ID."/install/admin/copy_remote_database_files.php",
                    $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin/copy_remote_database_files.php", true, true);
                CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/local/modules/".$this->MODULE_ID."/install/admin/menu_remote.php",
                    $_SERVER["DOCUMENT_ROOT"]."/local/modules/".$this->MODULE_ID."/admin/menu.php", true, true);
            }


            return true;
        }

        function UnInstallFiles()
        {
            DeleteDirFilesEx("/bitrix/images/".self::MODULE_ID."/");
            DeleteDirFilesEx("/bitrix/js/".self::MODULE_ID."/");

            // удаление файлов модуля для админки (например страницы генерации включаемых областей в админке)
            DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/local/modules/".$this->MODULE_ID."/install/admin",
                $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin");

            return true;
        }


        function InstallLocal()
        {

            CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . '/bitrix/modules/'.self::MODULE_ID.'/', $_SERVER["DOCUMENT_ROOT"] . '/local/modules/'.self::MODULE_ID.'/', true, true, true); //если есть файлы для копирования

        }

        function DoInstall()
        {

            global $APPLICATION;
            $context = Application::getInstance()->getContext();
            $request = $context->getRequest();
            $step = intval($request["step"]);

            if ($step < 2) {
                $APPLICATION->IncludeAdminFile(Loc::getMessage("MEDIALINE_BASE_INSTALL_TITLE"), $this->GetPath() . "/install/step1.php");
            } elseif ($step == 2) {

                if ($this->isVersionD7()) {

                    ModuleManager::registerModule($this->MODULE_ID);
                    $this->InstallEvents();
                    $this->InstallFiles(['savedata' => $request['savedata']]);

                    $APPLICATION->IncludeAdminFile(Loc::getMessage("MEDIALINE_BASE_INSTALL_TITLE"), $this->GetPath() . "/install/step2.php");

                } else {

                    $APPLICATION->ThrowException(Loc::getMessage("MEDIALINE_BASE_INSTALL_ERROR_VERSION"));
                    $APPLICATION->IncludeAdminFile(Loc::getMessage("MEDIALINE_BASE_INSTALL_TITLE"), $this->GetPath() . "/install/step1.php");

                }

            }
        }

        function DoUninstall()
        {
            global $APPLICATION;
            $this->UnInstallFiles();
            $this->UnInstallEvents();
            ModuleManager::unRegisterModule($this->MODULE_ID);
            $APPLICATION->IncludeAdminFile(Loc::getMessage("ADELSHIN_PERSONE_UNINSTALL_TITLE"), $this->GetPath() . "/install/unstep.php");
        }

    }

}