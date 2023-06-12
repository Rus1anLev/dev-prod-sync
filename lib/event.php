<?
namespace Medialine\Deploy;
IncludeModuleLangFile(__FILE__); 

/** 
 * Class with basic functions
 */ 
class Event
{ 
    const MODULE_ID = 'medialine.deploy';
    var $MODULE_ID = 'medialine.deploy';

    private function ShowPanel()
    {
        if ($GLOBALS["USER"]->IsAdmin())
        {
            $arMenu = array();

            $arMenu[] = array(
                "TEXT"  =>  "Эталонная страница",
                "ACTION" => "window.open('/local/templates/html/etalon.html', '_blank');",
            );

            $arMenu[] = array('SEPARATOR' => "Y");
            $arMenu[] = array(
                "TEXT"  =>  "Валидатор HTML",
                "ACTION" => "window.open('http://validator.w3.org/check?uri='+window.location.href+'&charset=%28detect+automatically%29&doctype=Inline&ss=1&group=0&user-agent=W3C_Validator%2F1.3+http%3A%2F%2Fvalidator.w3.org%2Fservices', '_blank');",
            );

            $arMenu[] = array(
                "TEXT"  =>  "Валидатор микроразметки",
                "ACTION" => "window.open('https://search.google.com/structured-data/testing-tool#url='+window.location.href, '_blank');",
            );

            $arMenu[] = array(
                "TEXT"  =>  "Валидатор орфографии",
                "ACTION" => "window.open('http://spell-checker.ru/simple?url='+window.location.href, '_blank');",
            );

            $arMenu[] = array('SEPARATOR' => "Y");
            $arMenu[] = array(
                "TEXT"  =>  "PageSpeed Insights",
                "ACTION" => "window.open('https://developers.google.com/speed/pagespeed/insights/?url='+window.location.href, '_blank');",
            );

            $arMenu[] = array(
                "TEXT"  =>  "GTmetrix",
                "ACTION" => "window.open('https://gtmetrix.com', '_blank');",
            );

            $arMenu[] = array(
                "TEXT"  =>  "Проверка ответа сервера",
                "ACTION" => "window.open('https://webmaster.yandex.ru/tools/server-response/?url='+window.location.href, '_blank');",
            );

//            $arMenu[] = array('SEPARATOR' => "Y");
//            $arMenu[] = array(
//                "TEXT"  =>  "Инструкция по работе с сайтом",
//                "ACTION" => "window.open('/local/templates/html/etalon.html', '_blank');",
//            );


//			$GLOBALS["APPLICATION"]->AddPanelButton(array(
//					"ID" => "slaminstruments",
//					"TEXT" => "Эталонная #BR# страница",
//					"TYPE" => "BIG",
//					"MAIN_SORT" => '1100',
//					"SORT" => 10,
//					"HREF" => '/local/templates/html/etalon.html',
//					"SRC" => "/bitrix/images/".self::MODULE_ID."/icon_setting.png",
//                    "HINT" => array('TITLE' => 'Инструменты веб-разработчика', 'TEXT' => 'Основные вспомогательные инструменты для разработчика. Нажмите стрелочку, чтобы увидеть список инструментов для проверки текущей страницы.'),
//					"MENU" => $arMenu
//			));

            $GLOBALS["APPLICATION"]->AddPanelButton(array(
                "ID" => "slaminstruments",
                "TEXT" => "Установка #BR# счетчиков",
                "TYPE" => "BIG",
                "MAIN_SORT" => '1150',
                "SORT" => 10,
                "HREF" => '/bitrix/admin/settings.php?lang=ru&mid='.self::MODULE_ID,
                "SRC" => "/bitrix/images/".self::MODULE_ID."/ya_icon.png",
                "HINT" => array('TITLE' => 'Установка счетчиков', 'TEXT' => 'Установка счетчиков, meta-тегов, пикселей, а также другого кода в хедер, начало или конец &lt;body&gt; страницы'),
                "MENU" => $arMenu
            ));
        }
    }

    private function fastAuthRedirect(){
        global $USER;

        if (
            defined('ADMIN_SECTION') && ADMIN_SECTION === true
            &&
            $USER->isAuthorized()
            &&
            isset($_REQUEST["fastauth_backurl"]) && strlen($_REQUEST["fastauth_backurl"]) > 0
        )
        {
            LocalRedirect($_REQUEST["fastauth_backurl"]);
        }
    }

    private function fastAuthSetJS()
    {
        global $APPLICATION, $USER;

        if ( is_object($APPLICATION) )
        {
            if(!( defined( ADMIN_SECTION ) && ADMIN_SECTION === true ) && !$USER->isAuthorized())
            {
                $APPLICATION->AddHeadString('<script type="text/javascript" src="/bitrix/js/'.self::MODULE_ID.'/script.js"></script>',true);
            }
        }
    }

    private function handler404(){
		$bDesignMode = $GLOBALS["APPLICATION"]->GetShowIncludeAreas() && is_object($GLOBALS["USER"]) && $GLOBALS["USER"]->IsAdmin();
		if(!$bDesignMode){
			if(defined('ERROR_404') && ERROR_404 == 'Y' && !defined('ADMIN_SECTION')){
				$template = 'html';

				global $APPLICATION;
				$APPLICATION->RestartBuffer();
				$APPLICATION->SetAdditionalCSS(CUtil::GetAdditionalFileURL('/bitrix/templates/'.$template.'/template_styles.css'));
				include $_SERVER['DOCUMENT_ROOT'].'/bitrix/templates/'.$template.'/header.php';
				if(file_exists($_SERVER['DOCUMENT_ROOT'].'/404.php'))
					include $_SERVER['DOCUMENT_ROOT'].'/404.php';
				include $_SERVER['DOCUMENT_ROOT'].'/bitrix/templates/'.$template.'/subscribe.php';
				die();
			}
		}
	}

    private function setCountersInHeader(){
        $insertManually = \Bitrix\Main\Config\Option::get(self::MODULE_ID, "USE_MANUALLY", '', SITE_ID);

        if($insertManually != 'Y'){
            $str = \Bitrix\Main\Config\Option::get(self::MODULE_ID, "META_STRING", '', SITE_ID);
            if(strlen($str) > 0){
                $GLOBALS["APPLICATION"]->AddHeadString($str, true);
            }
        }
    }

    private function setCountersInBody($content){
        $insertManually = \Bitrix\Main\Config\Option::get(self::MODULE_ID, "USE_MANUALLY", '', SITE_ID);

        if($insertManually != 'Y'){
            $strBodyTop = \Bitrix\Main\Config\Option::get(self::MODULE_ID, "BODY_TOP_STRING", '', SITE_ID);
            if($strBodyTop){
                $content = preg_replace("%<body[^>]*>%isU", "$0\n".$strBodyTop, $content);
            }

            $strBodyFooter = \Bitrix\Main\Config\Option::get(self::MODULE_ID, "BODY_FOTER_STRING", '', SITE_ID);
            if($strBodyFooter){
                $content = str_replace('</body>', $strBodyFooter.'</body>', $content);
            }
        }

        return $content;
    }

    public function OnBeforePrologHandler()
    {
        self::ShowPanel();
        self::fastAuthRedirect();
    }

    public function OnBeforeEndBufferContentHandler()
    {
        self::fastAuthSetJS();
        self::setCountersInHeader();
    }

    public static function OnEpilogHandler(){
        //self::handler404();
    }

    public static function OnEndBufferContentHandler(&$content){
        $content = self::setCountersInBody($content);
    }

} 
?>