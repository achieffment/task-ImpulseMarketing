<?php
if ($APPLICATION->GetGroupRight("chieff.currencies")>"D") {
    $aMenu = array(
        "parent_menu" => "global_menu_content",
        "sort"        => 100,
        "url"         => "chieff_currencies_list.php?lang=".LANGUAGE_ID,
        "more_url"    => "",
        "text"        => "Модуль валют",
        "title"       => "Модуль валют",
        "icon"        => "form_menu_icon",
        "page_icon"   => "form_page_icon",
        "module_id"   => "chieff.currencies",
        "dynamic"     => false,
        "items_id"    => "chieff.currencies",
        "items"       => array(),
    );
    $aMenu["items"][] =  array(
        "title" => "Список",
        "text" => "Список",
        "url"  => "chieff_currencies_list.php?lang=".LANGUAGE_ID,
        "icon" => "form_menu_icon",
        "page_icon" => "form_page_icon",
    );
    $aMenu["items"][] =  array(
        "title" => "Добавить",
        "text" => "Добавить",
        "url"  => "chieff_currencies_edit.php?lang=".LANGUAGE_ID,
        "icon" => "form_menu_icon",
        "page_icon" => "form_page_icon",
    );
    return $aMenu;
}
return false;
?>