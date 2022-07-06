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
function menuSaveAction(DocumentParser $modx, DatabaseUtils $db)
{
    $tableName = $db->getTableName('menus');
    $menu = $db->castTypes('menus', $_POST);

    if (!$menu['name']) sendError(__FUNCTION__.': name is empty');
    if (!$menu['title']) sendError(__FUNCTION__.': title is empty');


    if (!$menu['id']) {

        $menu['id'] = $modx->db->insert($menu, $tableName);
        if (!$menu['id']) sendError(__FUNCTION__.': menu is not created. '.$modx->db->getLastError());

    } else {
        if (!$modx->db->update($menu, $tableName, "id = {$menu['id']}")) {
            sendError(__FUNCTION__.': menu is not saved');
        }
    }

    sendResponse([
        'menu' => $db->keysConvertToCamelCase($menu),
    ], 'Menu has been created/saved');
}
