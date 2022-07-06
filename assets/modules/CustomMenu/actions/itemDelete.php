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
function itemDeleteAction(DocumentParser $modx, DatabaseUtils $db)
{
    $tableName = $db->getTableName('menu_items');
    $itemId = getIntParam('id', null);

    if (!$itemId or $itemId < 0) {
        sendError(__FUNCTION__.': не передан ID удаляемого пункта меню');
    }

    $children = $modx->db->select('id', $tableName, "`parent_id` = $itemId");
    if (count($modx->db->makeArray($children))) {
        sendError(__FUNCTION__.': Удаляемый пункт содержит подпункты. Удаление невозможно в этой версии модуля. Сначала удалите вложенные подпункты.');
    }


    $res = $modx->db->delete($tableName, "id = $itemId");

    if (!$res) {
        sendError(__FUNCTION__.': пункт меню не удален. '.$modx->db->getLastError());
    }

    sendResponse([], 'Пункт меню удален');
}
