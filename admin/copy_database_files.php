<?
$MODULE_ID = 'medialine.deploy';
define("ADMIN_MODULE_NAME", $MODULE_ID);

use Bitrix\Main\Config\Option;

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");
CJSCore::Init(array("jquery2"));

$arDefaultOptions = Option::getDefaults($MODULE_ID);

$APPLICATION->SetTitle(GetMessage("SLAM_INCLUDE_AREA_MODULE"));

//======================================================================================================================
//FORM:
$aTabs = array(
    array("DIV" => "edit1", "TAB" => GetMessage("INCLUDE_AREA_TAB_DB"), "ICON" => "smile", "TITLE" => GetMessage("INCLUDE_AREA_TAB_TITLE_DB")),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);
$tabControl->Begin();
$tabControl->BeginNextTab();

$arDefaultOptions =  \Bitrix\Main\Config\Option::getDefaults($MODULE_ID);

?>
    <tr>
        <td>
            <form class = 'js-form' method="POST" action="<?= $APPLICATION->GetCurPageParam() ?>" name="copy_database"
                  enctype="multipart/form-data">

                <input type="hidden" name="Update" value="Y"/>
                <input type="hidden" name="lang" value="<?= LANG ?>"/>
                <input type="hidden" name="ID" value="<?= $ID ?>"/>


                <button class="adm-btn js-copy" data-sended="false">Копировать бд с prod на dev</button>
            </form>
        </td>
        <td style="width:70%; padding-left: 40px;">
            <div class = 'js-result'></div>
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
        <span class="required">Важно! Обязательно проверьте правильность настроек!</span>
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
                data: {'step': step},
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
        

        $('form.js-form').on('submit', function(e){
            e.preventDefault();
        })

    </script>

<?
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");