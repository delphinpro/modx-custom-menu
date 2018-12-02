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
function menuListAction(\DocumentParser $modx, \DatabaseUtils $db)
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
