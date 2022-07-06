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
function menuListAction(DocumentParser $modx, DatabaseUtils $db)
{
    $result = $modx->db->select('*', $db->getTableName('menus'));
    $menuList = [];

    while ($row = $modx->db->getRow($result)) {
        $menuList[] = $db->castTypes('menus', $row, true);
    }

    sendResponse([
        'menuList' => $menuList,
    ]);
}
