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
function itemSaveAction(DocumentParser $modx, DatabaseUtils $db)
{
    $tableName = $db->getTableName('menu_items');
    $itemData = $db->castTypes('menu_items', $_POST);

    if (!$itemData['menu_id']) sendError(__FUNCTION__.': menuId undefined');
    if (!$itemData['doc_id']) sendError(__FUNCTION__.': docId undefined');
    if (!$itemData['doc_title']) sendError(__FUNCTION__.': docTitle undefined');

    $itemData['title'] = !empty($itemData['title']) ? $itemData['title'] : $itemData['doc_title'];

    if (!$itemData['id']) {

        $parentId = (int)$itemData['parent_id'];
        $result = $modx->db->select(
            'MAX(`order_index`)',
            $tableName,
            "`menu_id` = {$itemData['menu_id']} AND `parent_id` = $parentId"
        );
        if (!$result) sendError(__FUNCTION__.': '.$modx->db->getLastError());
        $nextOrder = ((int)$modx->db->getValue($result)) + 1;
        $itemData['order_index'] = $nextOrder;

        $itemData['id'] = $modx->db->insert($itemData, $tableName);
        if (!$itemData['id']) sendError(__FUNCTION__.': пункт меню не создан. '.$modx->db->getLastError());


    } else {

        if (!$modx->db->update($itemData, $tableName, "id = {$itemData['id']}")) {
            sendError(__FUNCTION__.': menu item is not saved');
        }

    }

    sendResponse([
        'item' => $db->keysConvertToCamelCase($itemData),
    ], 'Пункт меню создан/обновлен');
}
