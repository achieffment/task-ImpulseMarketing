<?

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php';

$POST_RIGHT = $APPLICATION->GetGroupRight("chieff.currencies");
if ($POST_RIGHT == "D")
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$aTabs = array(
    array("DIV" => "edit1", "TAB" => "Значения", "ICON" => "main_user_edit", "TITLE" => "Значения"),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);

\Bitrix\Main\Loader::includeModule("chieff.currencies");

$ID            = intval($ID);
$message       = null;
$bVarsFromForm = false;

use Bitrix\Main\Type;

if (
    $REQUEST_METHOD == "POST"
    &&
    ($save != "" || $apply != "")
    &&
    $POST_RIGHT == "W"
    &&
    check_bitrix_sessid()
) {

    $currencies = new \chieff\currencies\CurrenciesTable;

    $arFields = Array(
        "CODE" => $CODE,
        "DATE" => new Type\DateTime($DATE),
        "COURSE" => doubleval($COURSE),
    );

    if ($ID > 0) {
        $res = $currencies->Update($ID, $arFields);
    } else {
        $res = $currencies->Add($arFields);
        if ($res->isSuccess())
            $ID = $res->getId();
    }

    if ($res->isSuccess()) {
        if ($apply != "")
            LocalRedirect(
                "/bitrix/admin/chieff_currencies_edit.php?ID=" .
                $ID .
                "&mess=ok" .
                "&lang=" . LANG .
                "&" . $tabControl->ActiveTabParam()
            );
        else
            LocalRedirect("/bitrix/admin/chieff_currencies_list.php?lang=" . LANG);
    } else {
        if ($e = $APPLICATION->GetException())
            $message = new CAdminMessage("Ошибка сохранения", $e);
        else {
            $mess = print_r($res->getErrorMessages(), true);
            $message = new CAdminMessage("Ошибка сохранения: " . $mess);
        }
        $bVarsFromForm = true;
    }
}

$str_CODE   = "";
$str_DATE   = ConvertTimeStamp(false, "FULL");
$str_COURSE = "";

if ($ID > 0) {
    $result = \chieff\currencies\CurrenciesTable::GetByID($ID);
    if ($result->getSelectedRowsCount()) {
        $currencies = $result->fetch();
        $str_CODE   = $currencies["CODE"];
        $str_DATE   = $currencies["DATE"];
        $str_COURSE = $currencies["COURSE"];
    } else
        $ID = 0;
}

if ($bVarsFromForm) {
    $DB->InitTableVarsForEdit("chieff_currencies_currencies_table", "", "str_");
}

$APPLICATION->SetTitle(($ID > 0 ? "Редактирование " . $ID : "Создание"));

$aMenu = array(
    array(
        "TEXT"  => "К списку",
        "TITLE" => "К списку",
        "LINK"  => "chieff_currencies_list.php?lang=" . LANG,
        "ICON"  => "btn_list",
    )
);

if ($ID > 0) {
    $aMenu[] = array("SEPARATOR"=>"Y");
    $aMenu[] = array(
        "TEXT"  => "Добавить",
        "TITLE" => "Добавить",
        "LINK"  => "chieff_currencies_edit.php?lang=" . LANG,
        "ICON"  => "btn_new",
    );
    $aMenu[] = array(
        "TEXT"  => "Удалить",
        "TITLE" => "Удалить",
        "LINK"  => "javascript:if(confirm('" . "Подтвердить удаление?" . "')) " . "window.location='chieff_currencies_list.php?ID=" . $ID . "&action=delete&lang=" . LANG . "&" . bitrix_sessid_get() . "';",
        "ICON"  => "btn_delete",
    );
    $aMenu[] = array("SEPARATOR"=>"Y");
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_after.php';

$context = new CAdminContextMenu($aMenu);
$context->Show();

if ($_REQUEST["mess"] == "ok" && $ID > 0)
    CAdminMessage::ShowMessage(array("MESSAGE" => "Сохранено успешно", "TYPE" => "OK"));
if ($message)
    echo $message->Show();
 elseif ($currencies->LAST_ERROR != "")
     CAdminMessage::ShowMessage($currencies->LAST_ERROR);

?>

<form method="POST" Action="<?=$APPLICATION->GetCurPage()?>" ENCTYPE="multipart/form-data" name="post_form">
    <?echo bitrix_sessid_post();?>
    <input type="hidden" name="lang" value="<?=LANG?>">
    <?if ($ID > 0 && !$bCopy):?>
        <input type="hidden" name="ID" value="<?=$ID?>">
    <?
    endif;
    $tabControl->Begin();
    $tabControl->BeginNextTab();
    ?>
    <tr>
        <td width="40%"><span class="required">*</span><?="Код"?></td>
        <td width="60%"><input type="text" name="CODE" value="<?=$str_CODE?>"/></td>
    </tr>
    <tr>
        <td width="40%"><span class="required">*</span>Дата создания<?=" (".FORMAT_DATETIME."):"?></td>
        <td width="60%"><?=CalendarDate("DATE", $str_DATE, "post_form", "20")?></td>
    </tr>
    <tr>
        <td width="40%"><span class="required">*</span><?="Курс"?></td>
        <td width="60%"><input type="text" name="COURSE" value="<?=$str_COURSE?>"/></td>
    </tr>

<?
$tabControl->Buttons(
    array(
        "disabled" => ($POST_RIGHT < "W"),
        "back_url" => "chieff_currencies_list.php?lang=".LANG,
    )
);
$tabControl->End();
$tabControl->ShowWarnings("post_form", $message);
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php';
?>
