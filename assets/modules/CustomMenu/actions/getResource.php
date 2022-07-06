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
function getResourceAction(DocumentParser $modx, DatabaseUtils $db)
{
    $docId = getIntParam('docId');

    if (!$docId or $docId < 0) sendError(__FUNCTION__.': не передан ID');

    $docInfo = $modx->getDocument($docId);

    if (!$docInfo) sendError(__FUNCTION__.': Документ не найден');

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
