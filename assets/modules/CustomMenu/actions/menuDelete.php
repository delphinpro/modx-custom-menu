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
function menuDeleteAction(DocumentParser $modx, DatabaseUtils $db)
{
    $tableName = $db->getTableName('menus');
    $menuId = getIntParam('id', null);

    if (!$menuId or $menuId < 0) {
        sendError(__FUNCTION__.': не передан ID удаляемого меню');
    }

    $modx->db->delete($db->getTableName('menu_items'), "menu_id = $menuId");
    $res = $modx->db->delete($tableName, "id = $menuId");

    if (!$res) {
        sendError(__FUNCTION__.': меню не удалено. '.$modx->db->getLastError());
    }

    sendResponse([], 'Меню удалено');
}
