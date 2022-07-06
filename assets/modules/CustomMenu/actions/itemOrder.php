<?php
/*
 * Evo Custom Menu
 * Copyright (c) 2018-2022
 * delphinpro <delphinpro@yandex.ru>
 */

/**
 * @param DocumentParser $modx
 * @param DatabaseUtils $db
 */
function itemOrderAction(DocumentParser $modx, DatabaseUtils $db)
{
    $DIR_UP = 1;
    $DIR_DOWN = 2;

    $tableName = $db->getTableName('menu_items');
    $itemId = getIntParam('id');
    $parentId = getIntParam('parentId', 0);
    $menuId = getIntParam('menuId');
    $dir = getIntParam('dir');

    if (!$itemId) sendError(__FUNCTION__.': itemId undefined');
    if (!$menuId) sendError(__FUNCTION__.': menuId undefined');
    if (!$dir) sendError(__FUNCTION__.': direction order undefined');

    $result = $modx->db->select(
        '*',
        $tableName,
        "`menu_id`={$menuId} AND `parent_id`={$parentId}",
        "`order_index`, `id` ASC"
    );

    // check indexes
    $items = [];
    $orderCounter = 1;
    $moveItem = null;
    while ($row = $modx->db->getRow($result)) {
        $item = $db->castTypes('menu_items', $row, true);
        $item['orderIndex'] = $orderCounter;
        $items[$orderCounter] = $item;
        if ($item['id'] == $itemId) $moveItem = $orderCounter;
        $orderCounter++;
    }

    if ($dir == $DIR_DOWN and $items[$moveItem]['orderIndex'] < count($items)) {
        $items[$moveItem]['orderIndex']++;
        $items[$moveItem + 1]['orderIndex']--;
    }

    if ($dir == $DIR_UP and $items[$moveItem]['orderIndex'] > 1) {
        $items[$moveItem]['orderIndex']--;
        $items[$moveItem - 1]['orderIndex']++;
    }

    foreach ($items as $_item) {
        $modx->db->update(['order_index' => $_item['orderIndex']], $tableName, "`id` = {$_item['id']}");
    }
//    $forUp = array_splice($items, $moveItem['order_index']-1, 1);

//    $nextDecrement = false;
//    foreach ($items as &$_item) {
//        if ($_item['id'] == $itemId && $dir==$DIR_DOWN) {
//            $_item['orderIndex']++;
//            $nextDecrement = true;
//            continue;
//        }
//        if ($nextDecrement && $dir==$DIR_DOWN) {
//            $_item['orderIndex']--;
//            break;
//        }
//    }

//    if (!$itemData['id']) {
//
//        $itemData['id'] = $modx->db->insert($itemData, $tableName);
//        if (!$itemData['id']) sendError(__FUNCTION__ . ': пункт меню не создан');
//
//    } else {
//
//        if (!$modx->db->update($itemData, $tableName, "id = {$itemData['id']}")) {
//            sendError(__FUNCTION__ . ': menu item is not saved');
//        }
//
//    }

    sendResponse([
        'itemId' => $itemId,
        //        'forUp'  => $forUp,
        'items'  => $items,
        //        'item' => $db->keysConvertToCamelCase($itemData),
    ], 'Пункт меню создан/обновлен');
}
