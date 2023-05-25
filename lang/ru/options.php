<?
$MESS["MSG_TAB_NAME"] = "Установка счетчиков";
$MESS["MSG_TAB_TITLE"] = "Настройки";
$MESS["MSG_GROUP_NAME_MAIN"] = "Основные настройки";
$MESS["MSG_META_STRING_TITLE"] = "Код внутри тега &lt;header&gt;";
$MESS["MSG_BODY_TOP_TITLE"] = "Код в начале страницы, сразу после тега &lt;body&gt;";
$MESS["MSG_BODY_FOTER_TITLE"] = "Код в конце страницы, перед закрывающим тегом &lt;/body&gt;";
$MESS["MSG_USE_MANUALLY_TITLE"] = "Установить счетчики в шаблон вручную <br>(уменьшает нагрузку на сервер)";


$MESS["MSG_META_STRING_NOTES_1"] = "Всегда используйте атрибут <b>data-skip-moving=\"true\"</b>, чтобы Битрикс не перенеc ваш скрипт вниз страницы! Пример: <b>&lt;script data-skip-moving=\"true\"&gt;#код счетчика#&lt;/script&gt;</b>";
$MESS["MSG_META_STRING_NOTES"] = "<strong>Вставьте в шаблон сайта между тегами &lt;header&gt;&lt;/header&gt; строчку:</strong> <pre>&lt;?=\Bitrix\Main\Config\Option::get(\"slam.base\",\"META_STRING\",\"\")?&gt; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</pre>";
$MESS["MSG_BODY_TOP_STRING_NOTES"] = "<strong>Вставьте в шаблон сайта после тега &lt;body&gt; строчку:</strong> <pre>&lt;?=\Bitrix\Main\Config\Option::get(\"slam.base\",\"BODY_TOP_STRING\",\"\")?&gt;&nbsp;&nbsp;&nbsp;</pre>";
$MESS["MSG_BODY_FOTER_STRING_NOTES"] = "<strong>Вставьте в шаблон сайта перед закрывающим тегом &lt;/body&gt; строчку:</strong> <pre>&lt;?\Bitrix\Main\Config\Option::get(\"slam.base\",\"BODY_FOTER_STRING\",\"\")?&gt;&nbsp;</pre>";
$MESS["ML_UPLOAD_DIR"] = "Полный путь к папке upload на текущем сервере";
$MESS["ML_CORE_DIR"] = "Полный путь к папке ядра на текущем сервере";
