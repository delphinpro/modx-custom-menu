<?php
/*
 * Evo Custom Menu
 * Copyright (c) 2018-2022
 * delphinpro <delphinpro@yandex.ru>
 */

/**
 * @var DocumentParser $modx
 */

/** @noinspection PhpIncludeInspection */
include_once MODX_BASE_PATH.'assets/snippets/DocLister/lib/DLTemplate.class.php';
include_once __DIR__.'/lib.php';
include_once __DIR__.'/DatabaseUtils.php';

function cmError($msg)
{
    $snippetName = 'Snippet CustomMenu';
    return '<i style="color:red">'.$snippetName.': '.$msg.'</i>';
}


$menuId = isset($menuId) ? abs((int)$menuId) : null;
$menuName = isset($menuName) ? trim($menuName) : null;
$json = isset($json) ? (int)($json) : 0;
$tplWrap = isset($tplWrap) ? trim($tplWrap) : '@CODE: <ul>[+wrap+]</ul>';
$tplItem = isset($tplItem) ? trim($tplItem) : '@CODE: <li><a href="[+url+]">[+title+]</a>[+wrap+]</li>';

if (!$menuId && empty($menuName)) {
    return cmError('необходимо указать отображаемое меню параметрами &menuId= или &menuName=');
}

$dbUtils = initDatabaseUtils($modx);

$htmlWrapper = '';
$htmlItems = '';

function renderItems($rootNode, $tplWrap, $tplItem)
{
    global $modx;
    $html = '';
    foreach ($rootNode as $item) {

        $item['wrap'] = '';
        // Замещаемый пункт. Нужно дернуть дочерние ресурсы.
        // В этой версии работаем только в первым уровнем вложенности.
        if ($item['replaced']) {
            $item['title'] .= ' =='.$item['docId'];

            $fields = 'id,type,pagetitle,menutitle';
            $where = '';
            $sort = 'menuindex';
            $limit = null;
            $childrenDocs = $modx->getDocumentChildren($item['docId'], 1, 0, $fields, $where, $sort, 'ASC', $limit);
            pre($childrenDocs);
            foreach ($childrenDocs as $doc) {
                $doc['title'] = $doc['menutitle'] ? $doc['menutitle'] : $doc['pagetitle'];
                $doc['url'] = $modx->makeUrl((int)$doc['id']);
                $html .= DLTemplate::getInstance($modx)->parseChunk($tplItem, $doc);
            }

        } // Обычный пункт. Рендерим.
        else {
            // Имеются вложенные пункты
            if (count($item['children'])) {
                $children = renderItems($item['children'], $tplWrap, $tplItem);
                $item['wrap'] = DLTemplate::getInstance($modx)->parseChunk($tplWrap, ['wrap' => $children]);
            }

            $html .= DLTemplate::getInstance($modx)->parseChunk($tplItem, $item);
        }
    }

    return $html;
}

$whereClause = $menuId
    ? "`id` = '$menuId'"
    : "`name` = '$menuName'";

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

$htmlItems .= renderItems($tree, $tplWrap, $tplItem);

return DLTemplate::getInstance($modx)->parseChunk($tplWrap, ['wrap' => $htmlItems]);
