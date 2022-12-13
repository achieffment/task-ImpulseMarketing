<?php

use \Bitrix\Main\Loader;
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Type;

class Currencies extends CBitrixComponent {

    protected function checkModule() {
        if (!Loader::includeModule("chieff.currencies")) {
            ShowError(Loc::getMessage("CHIEFF_CURRENCIES_MODULE_NOT_INSTALLED"));
            return false;
        }
        return true;
    }

    protected function setArOrder() {
        $arOrder = [];
        if ($_REQUEST["sort_id"])
            $arOrder["ID"] = ((strip_tags(htmlspecialchars($_REQUEST["sort_id"])) == "ASC") ? "ASC" : "DESC");
        if ($_REQUEST["sort_code"])
            $arOrder["CODE"] = ((strip_tags(htmlspecialchars($_REQUEST["sort_code"])) == "ASC") ? "ASC" : "DESC");
        if ($_REQUEST["sort_date"])
            $arOrder["DATE"] = ((strip_tags(htmlspecialchars($_REQUEST["sort_date"])) == "ASC") ? "ASC" : "DESC");
        if ($_REQUEST["sort_course"])
            $arOrder["COURSE"] = ((strip_tags(htmlspecialchars($_REQUEST["sort_course"])) == "ASC") ? "ASC" : "DESC");
        if (!$arOrder)
            $arOrder["ID"] = "DESC";
        return $arOrder;
    }

}