<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if ($this->checkModule()) {

    if ($arParams["SET_TITLE"] == "Y")
        $APPLICATION->SetTitle("Модуль валют");

    $elementsCount = ($arParams["ELEMENTS_COUNT"]) ? $arParams["ELEMENTS_COUNT"] : 10;

    $nav = new \Bitrix\Main\UI\PageNavigation("page");
    $nav->allowAllRecords(true)
        ->setPageSize($elementsCount)
        ->initFromUri();

    $arOrder = $this->setArOrder();

    $bUSER_HAVE_ACCESS = $arParams["USE_PERMISSIONS"] ?? "";

    $cachePath = "/" . SITE_ID . $this->GetRelativePath();
    if ($this->StartResultCache(false, array(($arParams["CACHE_GROUPS"]==="N"? false: $USER->GetGroups()), $bUSER_HAVE_ACCESS, $arOrder, $nav), $cachePath)) {

        global $CACHE_MANAGER; // или $taggedCache = \Bitrix\Main\Application::getInstance()->getTaggedCache();
        $CACHE_MANAGER->StartTagCache($cachePath);
        $CACHE_MANAGER->RegisterTag("currencies_tag");

        $result = \chieff\currencies\CurrenciesTable::getList(array(
            "order" => $arOrder,
            "limit" => $nav->getLimit(),
            "offset" => $nav->getOffset(),
        ));

        $nav->setRecordCount(\chieff\currencies\CurrenciesTable::getList()->getSelectedRowsCount());

        $arResult["ITEMS"] = $result->fetchAll();
        $arResult["NAV"] = $nav;

        $CACHE_MANAGER->EndTagCache();
        $this->IncludeComponentTemplate();
    }

}

?>