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
function getResourceAction(\DocumentParser $modx, \DatabaseUtils $db)
{
    $docId = getIntParam('docId');

    if (!$docId or $docId < 0) sendError(__FUNCTION__ . ': не передан ID');

    $docInfo = $modx->getDocument($docId);

    if (!$docInfo) sendError(__FUNCTION__ . ': Документ не найден');

    sendResponse([
        'resource' => [
            'docId'       => (int)$docInfo['id'],
            'parent'      => (int)$docInfo['parent'],
            'hasChildren' => (bool)(int)$docInfo['isfolder'],
            'docTitle'    => $docInfo['pagetitle'],
            'url'         => $modx->makeUrl($docId),
            'alias'       => $docInfo['alias'],
        ],
        'doc'      => defined('CUSTOM_MENU_DEBUG') ? $docInfo : null,
    ]);
}
