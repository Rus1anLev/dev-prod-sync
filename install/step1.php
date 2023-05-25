<?
use \Bitrix\Main\Localization\Loc;

if (!check_bitrix_sessid())
    return;


Loc::loadMessages(__FILE__);


if ($ex = $APPLICATION->GetException())

    echo CAdminMessage::ShowMessage(array(
        "TYPE" => "ERROR",
        "MESSAGE" => Loc::getMessage("MOD_INST_ERR"),
        "DETAILS" => $ex->GetString(),
        "HTML" => true,
    ));


?>


<form action="<?echo $APPLICATION->GetCurPage()?>">
    <?=bitrix_sessid_post()?>
    <input type="hidden" name="lang" value="<?echo LANGUAGE_ID?>">
    <input type="hidden" name="id" value="medialine.base">
    <input type="hidden" name="install" value="Y">
    <input type="hidden" name="step" value="2">

    <p><input type="checkbox" name="savedata" id="savedata" value="Y" checked>
        <label for="savedata"><?echo Loc::getMessage("MEDIALINE_INSTAL_SAVE_LOCAL")?></label></p>
    <input type="submit" name="" value="<?echo Loc::getMessage("MEDIALINE_INSTAL_SAVE")?>">
</form>
<p style="color: red;">
    Обязательно вручную отредактируйте настройки удаленного сервера в файле default_option.php после установки модуля!!!
</p>