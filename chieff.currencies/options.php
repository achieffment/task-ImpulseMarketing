<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;

Loc::loadMessages($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/main/options.php");

$module_id = "chieff.currencies";

if ($APPLICATION->GetGroupRight($module_id) < "S")
    $APPLICATION->AuthForm(Loc::getMessage("ACCESS_DENIED"));

Loader::includeModule($module_id);

$request = \Bitrix\Main\HttpApplication::getInstance()->getContext()->getRequest();

$aTabs = [
    array(
        "DIV"   => "edit1",
        "TAB"   => Loc::getMessage("MAIN_TAB_RIGHTS"),
        "TITLE" => Loc::getMessage("MAIN_TAB_TITLE_RIGHTS")
    )
];

$tabControl = new CAdminTabControl('tabControl', $aTabs);

$tabControl->Begin();

?>
<form method="post" name="chieff_currencies_settings" action="<?=$APPLICATION->GetCurPage()?>?mid=<?=htmlspecialcharsbx($request["mid"])?>&lang=<?=$request["lang"]?>">
    <?
    echo bitrix_sessid_post();

    $tabControl->BeginNextTab();
    require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/admin/group_rights.php";

    $tabControl->Buttons();
    ?>
    <input type="submit" name="Update" value="<?=GetMessage("MAIN_SAVE")?>">
    <input type="reset" name="reset" value="<?=GetMessage("MAIN_RESET")?>">
</form>

<?php

$tabControl->End();

?>