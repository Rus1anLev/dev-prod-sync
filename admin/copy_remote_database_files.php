<?
$MODULE_ID = 'medialine.deploy';
define("ADMIN_MODULE_NAME", $MODULE_ID);

use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;


require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");
CJSCore::Init(array("jquery2"));

$arDefaultOptions = Option::getDefaults($MODULE_ID);

$APPLICATION->SetTitle(GetMessage("SLAM_INCLUDE_AREA_MODULE"));

//======================================================================================================================
//FORM:
$aTabs = array(
    array("DIV" => "edit1", "TAB" => GetMessage("INCLUDE_AREA_TAB_DB"), "ICON" => "smile", "TITLE" => GetMessage("INCLUDE_AREA_TAB_TITLE_DB")),
    array("DIV" => "edit2", "TAB" => GetMessage("INCLUDE_AREA_TAB_CORE"), "ICON" => "smile", "TITLE" => GetMessage("INCLUDE_AREA_TAB_TITLE_CORE")),
    array("DIV" => "edit3", "TAB" => GetMessage("INCLUDE_AREA_TAB_UPLOAD"), "ICON" => "smile", "TITLE" => GetMessage("INCLUDE_AREA_TAB_TITLE_UPLOAD")),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);
$tabControl->Begin();
$tabControl->BeginNextTab();


$testDB = -1;
if(Loader::IncludeModule($MODULE_ID)) {
    try{
        $connect = new \Medialine\Deploy\ConnectSSH();
        $testConnect = $connect->testConnect();

        $testDB = $connect->testDB();
    }catch (Exception $e){
        echo $e->getMessage();
    }

}

//exec("ssh -p ".$arDefaultOptions['DEV_REMOTE_PORT']."' " . $arDefaultOptions['DEV_REMOTE_HOST'], $testConnection);
?>

    <tr>
        <td>
            <form class = 'js-form' method="POST" action="<?= $APPLICATION->GetCurPageParam() ?>" name="copy_database"
                  enctype="multipart/form-data">

                <input type="hidden" name="Update" value="Y"/>
                <input type="hidden" name="lang" value="<?= LANG ?>"/>
                <input type="hidden" name="ID" value="<?= $ID ?>"/>

                <?if($testDB === 0):?>
                <button class="adm-btn js-copy" data-sended="false">Копировать бд с prod на dev</button>
                <?else:?>
                    <p>Проверьте правильность настроек в файле defaul_option.php</p>
                <?endif;?>
            </form>
        </td>
        <td style="width:70%; padding-left: 40px;">
            <div class = 'js-result'></div>
        </td>
    </tr>

<?
$tabControl->BeginNextTab();
?>
    <tr>
        <td>
            <form class = 'js-form' method="POST" action="<?= $APPLICATION->GetCurPageParam() ?>" name="copy_core"
                  enctype="multipart/form-data">

                <input type="hidden" name="Update" value="Y"/>
                <input type="hidden" name="lang" value="<?= LANG ?>"/>
                <input type="hidden" name="ID" value="<?= $ID ?>"/>

                <?if($testDB === 0):?>
                <button class="adm-btn js-copy-core" data-sended="false">Копировать файлы ядра с prod на dev</button>
                <?else:?>
                <p>Проверьте правильность настроек в файле defaul_option.php</p>
                <?endif;?>
            </form>
        </td>
        <td style="width:70%; padding-left: 40px;">
            <div class = 'js-result-core'></div>
        </td>
    </tr>
<?
$tabControl->BeginNextTab();
?>
    <tr>
        <td>
            <form class = 'js-form' method="POST" action="<?= $APPLICATION->GetCurPageParam() ?>" name="copy_upload"
                  enctype="multipart/form-data">

                <input type="hidden" name="Update" value="Y"/>
                <input type="hidden" name="lang" value="<?= LANG ?>"/>
                <input type="hidden" name="ID" value="<?= $ID ?>"/>

                <?if($testDB === 0):?>
                <button class="adm-btn js-copy-upload" data-sended="false">Копировать файлы upload с prod на dev</button>
                <?else:?>
                    <p>Проверьте правильность настроек в файле defaul_option.php</p>
                <?endif;?>
            </form>
        </td>
        <td style="width:70%; padding-left: 40px;">
            <div class = 'js-result-upload'></div>
        </td>
    </tr>
<?


$tabControl->EndTab();


$tabControl->End();
//$tabControl->ShowWarnings("smile_import", $message);


//======================================================================================================================
//BUSINESS-LOGIC:
?>
<?echo BeginNote();?>
    <span class="required">Текущие настройки подключения к dev серверу</span> (задаются в файле 'default_option.php' данного модуля вручную)<br>
    <p>
    <pre>
        <?
        print_r($arDefaultOptions);
        ?>
        </pre>

    </p>

    <p>
        Статус подключения к удаленному серверу: <?= $testConnect === 0 ? '<span style="color: green;"><b>Успешно</b></span>' : '<span style="color: red;"><b>Не удалось подключиться!</b></span>'; ?>
    </p>
    <p>
        Статус соединения с удаленной базой данных: <?= $testDB === 0 ? '<span style="color: green;"><b>Успешно</b></span>' : '<span style="color: red;"><b>Не удалось подключиться!</b></span>'; ?>
    </p>
<?echo EndNote();?>
    <script>
        let resultBlock = $('.js-result');
        let resultBlockCore = $(".js-result-core");
        let resultBlockUpload = $(".js-result-upload");

        var sendAdminAjax = function (step) {

            let stepTitles = {1: 'Создание дампа prod бд', 2: 'prod бд заливается на dev сайт', 3: 'Копирование папки upload'};

            resultBlock.append(step + ') ' + stepTitles[step]  + '<br>');

            $.ajax({
                type: 'post',
                url: '/local/modules/medialine.deploy/include/ajax_copy_database.php',//url адрес файла обработчика
                data: {'step': step,remote: "Y"},
                dataType: 'json',
                success: function (result) {
                    try {

                        resultBlock.append(result['message'] + ' ' + result['sub_message']  + '<br><br>');

                        if(step < 2){
                            sendAdminAjax(++step);
                        }else{
                            resultBlock.append('Копирование завершено!');
                        }
                    }
                    catch (e) {
                        console.log(e);
                    }
                }
            });

        };


        $(".js-copy").on("click", function() {

            if($(this).data('sended') === false){
                $(this).data('sended', true);
                resultBlock.html('');
                let step = 1;
                sendAdminAjax(step);
            }

            return false;
        });

        $(".js-copy-core").on("click", function () {
            resultBlockCore.html('Процесс запущен. Дождитесь окончания...');
            BX.showWait();
            $.ajax({
                type: 'post',
                url: '/local/modules/medialine.deploy/include/ajax_copy_core.php',//url адрес файла обработчика
                data: {remote: "Y"},
                dataType: 'json',
                success: function (result) {
                    try {
                        if(result['success'] == 'Y'){
                            resultBlockCore.html(result['message'] + ' ' + result['sub_message']  + '<br><br>');
                        }
                    }
                    catch (e) {
                        console.log(e);
                    }
                    BX.closeWait();
                }
            });

            return false;
        });

        $(".js-copy-upload").on("click", function () {
            resultBlockUpload.html('Процесс запущен. Дождитесь окончания...');
            BX.showWait();
            $.ajax({
                type: 'post',
                url: '/local/modules/medialine.deploy/include/ajax_copy_upload.php',//url адрес файла обработчика
                data: {remote: "Y"},
                dataType: 'json',
                success: function (result) {
                    try {
                        if(result['success'] == 'Y'){
                            resultBlockUpload.html(result['message'] + ' ' + result['sub_message']  + '<br><br>');
                        }
                    }
                    catch (e) {
                        console.log(e);
                    }
                    BX.closeWait();
                }
            });

            return false;
        });

        $('form.js-form').on('submit', function(e){
            e.preventDefault();
        })

    </script>

<?
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");