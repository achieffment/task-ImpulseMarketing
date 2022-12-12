<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if ($this->checkModule()) {

    $APPLICATION->SetTitle("Модуль валют");

    $elementsCount = ($arParams["ELEMENTS_COUNT"]) ? $arParams["ELEMENTS_COUNT"] : 10;

    $nav = new \Bitrix\Main\UI\PageNavigation("page");
    $nav->allowAllRecords(true)
        ->setPageSize($elementsCount)
        ->initFromUri();

    $arOrder = $this->setArOrder();

    $bUSER_HAVE_ACCESS = !$arParams["USE_PERMISSIONS"];
    if ($this->StartResultCache(false, array(($arParams["CACHE_GROUPS"]==="N"? false: $USER->GetGroups()), $bUSER_HAVE_ACCESS, $arOrder, $nav))) {

        $result = \chieff\currencies\CurrenciesTable::getList(array(
            "order" => $arOrder,
            "limit" => $nav->getLimit(),
            "offset" => $nav->getOffset(),
        ));

        $nav->setRecordCount(\chieff\currencies\CurrenciesTable::getList()->getSelectedRowsCount());

        $arResult["ITEMS"] = $result->fetchAll();
        $arResult["NAV"] = $nav;

        $this->IncludeComponentTemplate();
    }

}

?>