<?php
/**
 * Custom Menu
 * Module for EvolutionCMS (Modx)
 *
 * @author      delphinpro <delphinpro@gmail.com>
 * @copyright   copyright © 2018 delphinpro
 * @license     licensed under the MIT license
 */

/**
 * @param DocumentParser $modx
 * @param DatabaseUtils  $db
 */
function itemListAction(\DocumentParser $modx, \DatabaseUtils $db)
{
    $tableName = $db->getTableName('menu_items');
    $menuId = getIntParam('menuId');
    if (!$menuId) sendError(__FUNCTION__ . ': menuId undefined');

    $result = $modx->db->select(
        '*',
        $tableName,
        "`menu_id` = $menuId",
        '`order_index`, `id` ASC'
    );

    if (!$result) sendError(__FUNCTION__ . ': ' . $modx->db->getLastError());

    $itemList = [];

    while ($row = $modx->db->getRow($result)) {
        $itemList[] = $db->castTypes('menu_items', $row, true);
    }

    $itemsTree = buildTree($itemList, 0, 'id', 'parentId');

    sendResponse([
        'itemsTree' => $itemsTree,
        'itemList'  => $itemList,
    ]);
}
