<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if (!empty($arResult["ITEMS"])): ?>
<table>
    <thead>
        <tr>
            <th id="ID" onclick="changeSort('sort_id');">ID <?=(strip_tags(htmlspecialchars($_REQUEST["sort_id"])) == "ASC") ? "↓" : (strip_tags(htmlspecialchars($_REQUEST["sort_id"])) == "DESC") ? "↑" : ""?></th>
            <th id="CODE" onclick="changeSort('sort_code');">Код <?=(strip_tags(htmlspecialchars($_REQUEST["sort_code"])) == "ASC") ? "↓" : (strip_tags(htmlspecialchars($_REQUEST["sort_code"])) == "DESC") ? "↑" : ""?></th>
            <th id="DATE" onclick="changeSort('sort_date');">Дата создания <?=(strip_tags(htmlspecialchars($_REQUEST["sort_date"])) == "ASC") ? "↓" : (strip_tags(htmlspecialchars($_REQUEST["sort_date"])) == "DESC") ? "↑" : ""?></th>
            <th id="COURSE" onclick="changeSort('sort_course');">Курс <?=(strip_tags(htmlspecialchars($_REQUEST["sort_course"])) == "ASC") ? "↓" : (strip_tags(htmlspecialchars($_REQUEST["sort_course"])) == "DESC") ? "↑" : ""?></th>
        </tr>
    </thead>
    <tbody>
        <? foreach ($arResult["ITEMS"] as $item): ?>
            <tr>
                <td><?=$item["ID"]?></td>
                <td><?=$item["CODE"]?></td>
                <td><?=$item["DATE"]?></td>
                <td><?=$item["COURSE"]?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php
    $APPLICATION->IncludeComponent(
        "bitrix:main.pagenavigation",
        ".default",
        array(
            'NAV_TITLE'   => 'Элементы',
            "NAV_OBJECT"  => $arResult["NAV"],
            "SEF_MODE" => "N",
        ),
        null,
        false,
        true
    );
?>
<?php endif; ?>