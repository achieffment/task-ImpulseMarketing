<?

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php';

$POST_RIGHT = $APPLICATION->GetGroupRight("chieff.currencies");
if ($POST_RIGHT == "D")
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

\Bitrix\Main\Loader::includeModule("chieff.currencies");

$sTableID = "chieff_currencies_currencies_table";
$oSort = new CAdminSorting($sTableID, "ID", "desc");
$lAdmin = new CAdminList($sTableID, $oSort);

function CheckFilter()
{
    global $FilterArr, $lAdmin;
    $str = "";
    if ($_REQUEST["find_timestamp_x_1"] <> '')
        if (!CheckDateTime($_REQUEST["find_timestamp_x_1"], CSite::GetDateFormat("FULL")))
            $str .= GetMessage("MAIN_EVENTLOG_WRONG_TIMESTAMP_X_FROM") . "<br>";
    if ($_REQUEST["find_timestamp_x_2"] <> '')
        if (!CheckDateTime($_REQUEST["find_timestamp_x_2"], CSite::GetDateFormat("FULL")))
            $str .= GetMessage("MAIN_EVENTLOG_WRONG_TIMESTAMP_X_TO") . "<br>";
    if ($str <> '') {
        $lAdmin->AddFilterError($str);
        return false;
    }
    foreach ($FilterArr as $f) global $$f;
    return count($lAdmin->arFilterErrors) == 0;
}

$FilterArr = Array(
    "find_id",
    "find_code",
    "find_timestamp_x_1",
    "find_timestamp_x_2",
    "find_course",
);

$lAdmin->InitFilter($FilterArr);
if (CheckFilter()) {
    $arFilter = [];
    if ($find_id)
        $arFilter["ID"] = $find_id;
    if ($find_code)
        $arFilter["CODE"] = $find_code;
    if ($find_timestamp_x_1 && !$find_timestamp_x_2)
        $arFilter[">=DATE"] = $find_timestamp_x_1;
    else if (!$find_timestamp_x_1 && $find_timestamp_x_2)
        $arFilter["<DATE"] = $find_timestamp_x_2;
    else if ($find_timestamp_x_1 && $find_timestamp_x_2) {
        $arFilter[">=DATE"] = $find_timestamp_x_1;
        $arFilter["<DATE"]  = $find_timestamp_x_2;
    }
    if ($find_course)
        $arFilter["COURSE"] = $find_course;
}

$arOrder = [];
if ($by && $order)
    $arOrder[mb_strtoupper($by)] = mb_strtoupper($order);

if ($lAdmin->EditAction() && $POST_RIGHT == "W") {
    foreach ($FIELDS as $ID => $arFields) {
        if (!$lAdmin->IsUpdated($ID)) continue;
        $DB->StartTransaction();
        $ID = IntVal($ID);
        $elem = new \chieff\currencies\CurrenciesTable;
        if (($rsData = $elem->GetByID($ID)) && ($arData = $rsData->fetch())) {
            foreach ($arFields as $key => $value) {
                $val = $value;
                if ($key == "DATE")
                    $val = new \Bitrix\Main\Type\DateTime($val);
                if ($key == "COURSE")
                    $val = doubleval($val);
                $arData[$key] = $val;
            }
            $res = $elem->Update($ID, $arData);
            if(!$res->isSuccess()) {
                $lAdmin->AddGroupError("Ошибка обновления:" . " " . print_r($res->getErrorMessages(), true), $ID);
                $DB->Rollback();
            }
        } else {
            $lAdmin->AddGroupError("Ошибка обновления:  не удалось получить информацию элемента по его айди", $ID);
            $DB->Rollback();
        }
        $DB->Commit();
    }
}

if (($arID = $lAdmin->GroupAction()) && $POST_RIGHT == "W") {
    if ($_REQUEST['action_target'] == 'selected') {
        $cData = new \chieff\currencies\CurrenciesTable;
        $rsData = $cData->getList(array(
            "order"  => $arOrder,
            "filter" => $arFilter
        ));
        while ($arRes = $rsData->Fetch())
            $arID[] = $arRes['ID'];
    }
    foreach ($arID as $ID)
    {
        if (strlen($ID) <= 0) continue;
        $ID = IntVal($ID);
        switch ($_REQUEST['action']) {
            case "delete":
                @set_time_limit(0);
                $DB->StartTransaction();
                if(!\chieff\currencies\CurrenciesTable::Delete($ID)->isSuccess()) {
                    $DB->Rollback();
                    $lAdmin->AddGroupError("Ошибка удаления", $ID);
                }
                $DB->Commit();
                break;
        }
    }
}

$rsData = \chieff\currencies\CurrenciesTable::getList(array(
    "order"  => $arOrder,
    "filter" => $arFilter
));
$rsData = new CAdminResult($rsData, $sTableID);
$rsData->NavStart();
$lAdmin->NavText($rsData->GetNavPrint("Элементов"));

$lAdmin->AddHeaders(array(
    array(
        "id"       => "ID",
        "content"  => "ID",
        "sort"     => "id",
        "default"  => true,
    ),
    array(
        "id"       => "CODE",
        "content"  => "Код",
        "sort"     => "code",
        "default"  => true,
    ),
    array(
        "id"       => "DATE",
        "content"  => "Дата создания",
        "sort"     => "date",
        "default"  => true,
    ),
    array(
        "id"       => "COURSE",
        "content"  => "Курс",
        "sort"     => "course",
        "default"  => true,
    ),
));

while($arRes = $rsData->NavNext(true, "f_")):

    $row =& $lAdmin->AddRow($f_ID, $arRes);

    $row->AddInputField("CODE", array("size"=>20));
    $row->AddViewField(
            "CODE",
            '<a href="chieff_currencies_edit.php?ID=' . $f_ID . '&lang=' . LANG . '">' . $f_CODE . '</a>'
    );

    $row->AddCalendarField("DATE");

    $row->AddInputField("COURSE", array("size"=>20));

    $arActions = Array();
    $arActions[] = array(
        "ICON"    => "edit",
        "DEFAULT" => true,
        "TEXT"    => "Редактировать",
        "ACTION"  => $lAdmin->ActionRedirect("chieff_currencies_edit.php?ID=" . $f_ID)
    );

    if ($POST_RIGHT >= "W")
        $arActions[] = array(
            "ICON"   => "delete",
            "TEXT"   => "Удалить",
            "ACTION" => "if(confirm('"."Удалить"."')) " . $lAdmin->ActionDoGroup($f_ID, "delete")
        );
    $arActions[] = array("SEPARATOR"=>true);

    if(is_set($arActions[count($arActions) - 1], "SEPARATOR"))
        unset($arActions[count($arActions) - 1]);

    $row->AddActions($arActions);

endwhile;

$lAdmin->AddFooter(
    array(
        array(
            "title"=>"Выбрано",
            "value"=>$rsData->SelectedRowsCount()
        ),
        array(
            "counter"=>true,
            "title"=>GetMessage("MAIN_ADMIN_LIST_CHECKED"),
            "value"=>"0"
        ),
    )
);

$lAdmin->AddGroupActionTable(Array(
    "delete" => "Удалить",
));

$aMenu = array(
    array(
        "TEXT" => "К списку",
        "TITLE"=> "К списку",
        "LINK" => "/bitrix/admin/chieff_currencies_list.php?lang=".LANGUAGE_ID,
        "ICON" => "btn_list",
    ),
    array(
        "TEXT" => "Добавить",
        "TITLE"=> "Добавить",
        "LINK" => "/bitrix/admin/chieff_currencies_edit.php?lang=".LANGUAGE_ID,
        "ICON" => "btn_new",
    )
);
$lAdmin->AddAdminContextMenu($aMenu);
$lAdmin->CheckListMode();
$APPLICATION->SetTitle("Модуль валют");

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_after.php';

$oFilter = new CAdminFilter(
    $sTableID."_filter",
    array(
        "ID",
        "CODE",
        "DATE",
        "COURSE",
    )
);
?>
<form name="find_form" method="get" action="<?=$APPLICATION->GetCurPage();?>">
    <input type="hidden" name="lang" value="<?=LANGUAGE_ID?>">
    <?$oFilter->Begin();?>
    <tr>
        <td><?="ID"?>:</td>
        <td>
            <input type="text" name="find_id" size="47" value="<?=htmlspecialcharsbx($find_id)?>">
        </td>
    </tr>
    <tr>
        <td><?="Код"?></td>
        <td><input type="text" name="find_code" size="47" value="<?=htmlspecialcharsbx($find_code)?>"></td>
    </tr>
    <tr>
        <td><?="Дата создания"?>:</td>
        <td><?echo CAdminCalendar::CalendarPeriod("find_timestamp_x_1", "find_timestamp_x_2", $find_timestamp_x_1, $find_timestamp_x_2, false, 15, true)?></td>
    </tr>
    <tr>
        <td><?="Курс"?></td>
        <td><input type="text" name="find_course" size="47" value="<?=htmlspecialcharsbx($find_course)?>"></td>
    </tr>
    <?
    $oFilter->Buttons(
        array(
            "table_id" => $sTableID,
            "url"      => $APPLICATION->GetCurPage(),
            "form"     => "find_form"
        )
    );
    $oFilter->End();
    ?>
</form>

<?php

$lAdmin->DisplayList();

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php';

?>