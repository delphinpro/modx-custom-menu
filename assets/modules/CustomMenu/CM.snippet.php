<?php
/*
 * Evo Custom Menu
 * Copyright (c) 2018-2022
 * delphinpro <delphinpro@yandex.ru>
 */


/** @noinspection PhpIncludeInspection */
include_once MODX_BASE_PATH.'assets/snippets/DocLister/lib/DLTemplate.class.php';
include_once __DIR__.'/DatabaseUtils.php';
include_once __DIR__.'/functions.php';
include_once __DIR__.'/lib.php';

$menuId = isset($menuId) ? abs((int)$menuId) : null;
$menuName = isset($menuName) ? trim($menuName) : null;
$json = isset($json) ? (int)($json) : 0;
$tplWrap = isset($tplWrap) ? trim($tplWrap) : '@CODE: <ul>[+wrap+]</ul>';
$tplItem = isset($tplItem) ? trim($tplItem) : '@CODE: <li><a href="[+url+]">[+title+]</a>[+wrap+]</li>';
$tplWrapInner = isset($tplWrapInner) ? trim($tplWrapInner) : $tplWrap;
$tplItemInner = isset($tplItemInner) ? trim($tplItemInner) : $tplItem;

$allTpl = [
    'Wrap'      => $tplWrap,
    'Item'      => $tplItem,
    'WrapInner' => $tplWrapInner,
    'ItemInner' => $tplItemInner,
];

//pre($allTpl);

if (!$menuId && empty($menuName)) {
    return cmError('необходимо указать отображаемое меню параметрами &menuId= или &menuName=');
}

$dbUtils = init_cm_database($modx);

$htmlWrapper = '';
$htmlItems = '';

$whereClause = $menuId
    ? "`id` = '$menuId'"
    : "`name` = '$menuName'";

$modx = evolutionCMS();

$result = $modx->db->select(
    '*',
    $dbUtils->getTableName('menus'),
    $whereClause
);
if (!$result) return cmError($modx->db->getLastError());

$menu = $modx->db->getRow($result);
if (empty($menu)) return cmError("Меню '{$menuName}' не создано");

$menu = $dbUtils->castTypes('menus', $menu, true);

//pre($menu);

$menuId = $menu['id'];

$result = $modx->db->select(
    '*',
    $dbUtils->getTableName('menu_items'),
    "`menu_id` = {$menuId} AND `is_hide` = 0",
    '`order_index`, `id` ASC'
);
if (!$result) return cmError($modx->db->getLastError());

$itemList = [];

while ($row = $modx->db->getRow($result)) {
    $itemList[] = $dbUtils->castTypes('menu_items', $row, true);
}

$tree = buildTree($itemList, 0, 'id', 'parentId');

if ($json) {
    return json_encode($tree);
}
//pre($tree);

$htmlItems .= renderItems($tree, $allTpl);

return DLTemplate::getInstance($modx)->parseChunk($tplWrap, ['wrap' => $htmlItems]);
/* */
