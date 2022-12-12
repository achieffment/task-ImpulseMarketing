<?
if (is_dir($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/chieff.currencies/"))
    require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/chieff.currencies/admin/chieff_currencies_list.php");
elseif (is_dir($_SERVER["DOCUMENT_ROOT"] . "/local/modules/chieff.currencies/"))
    require_once($_SERVER["DOCUMENT_ROOT"]."/local/modules/chieff.currencies/admin/chieff_currencies_list.php");
?>
