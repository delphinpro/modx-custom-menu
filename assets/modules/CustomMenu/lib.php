<?php
/*
 * Evo Custom Menu
 * Copyright (c) 2018-2022
 * delphinpro <delphinpro@yandex.ru>
 */

if (empty($modx) || !($modx instanceof DocumentParser)) {
    die('Please use the MODX Content Manager instead of accessing this file directly.');
}

function init_cm_database($modx)
{
    $dbUtils = new DatabaseUtils($modx);
    $dbUtils->addTableDefinition('menus', [
        'fields'  => [
            'id'    => [
                'type'   => 'INT',
                'length' => 11,
                'isNull' => false,
                'attrs'  => 'AUTO_INCREMENT',
            ],
            'name'  => [
                'type'    => 'VARCHAR',
                'length'  => 45,
                'isNull'  => true,
                'default' => null,
            ],
            'title' => [
                'type'    => 'VARCHAR',
                'length'  => 45,
                'isNull'  => false,
                'default' => '',
            ],
        ],
        'primary' => 'id',
        'indexes' => [
            'name' => ['type' => 'UNIQUE INDEX', 'field' => 'name'],
        ],
        'params'  => [
            'COLLATE'        => "'utf8_general_ci'",
            'ENGINE'         => 'MyISAM',
            'AUTO_INCREMENT' => 1,
        ],
    ]);
    $dbUtils->addTableDefinition('menu_items', [
        'fields'  => [
            'id'          => [
                'type'   => 'INT',
                'length' => 11,
                'isNull' => false,
                'attrs'  => 'AUTO_INCREMENT',
            ],
            'parent_id'   => [
                'type'    => 'INT',
                'length'  => 11,
                'isNull'  => true,
                'default' => null,
            ],
            'menu_id'     => [
                'type'   => 'INT',
                'length' => 11,
                'isNull' => false,
            ],
            'order_index' => [
                'type'    => 'INT',
                'length'  => 5,
                'isNull'  => false,
                'default' => 0,
            ],
            'is_hide'     => [
                'type'    => 'INT',
                'length'  => 1,
                'isNull'  => false,
                'default' => 0,
            ],
            'replaced'    => [
                'type'    => 'INT',
                'length'  => 1,
                'isNull'  => false,
                'default' => 0,
            ],
            'doc_id'      => [
                'type'    => 'INT',
                'length'  => 11,
                'isNull'  => true,
                'default' => null,
            ],
            'doc_title'   => [
                'type'    => 'VARCHAR',
                'length'  => 255,
                'isNull'  => true,
                'default' => null,
            ],
            'title'       => [
                'type'    => 'VARCHAR',
                'length'  => 255,
                'isNull'  => true,
                'default' => null,
            ],
            'url'         => [
                'type'    => 'VARCHAR',
                'length'  => 255,
                'isNull'  => true,
                'default' => '',
            ],
            'alias'       => [
                'type'    => 'VARCHAR',
                'length'  => 255,
                'isNull'  => true,
                'default' => null,
            ],
            'props'       => [
                'type'    => 'TEXT',
                'isNull'  => true,
                'default' => null,
            ],
        ],
        'primary' => 'id',
        'indexes' => [],
        'params'  => [
            'COLLATE'        => "'utf8_general_ci'",
            'ENGINE'         => 'MyISAM',
            'AUTO_INCREMENT' => 1,
        ],
    ]);
    return $dbUtils;
}

/*==
 *== Snippet
 *== ======================================= ==*/

function cmError($msg)
{
    $snippetName = 'Snippet CustomMenu';
    return '<i style="color:red">'.$snippetName.': '.$msg.'</i>';
}

function renderItems($rootNode, $allTpl, $depth = 0)
{
    global $modx;
    $html = '';

    $tplItem = $depth < 1 ? $allTpl['Item'] : $allTpl['ItemInner'];
    $tplWrap = $depth < 0 ? $allTpl['Wrap'] : $allTpl['WrapInner'];

    foreach ($rootNode as $item) {

        //$item['wrap'] = '';
        // Замещаемый пункт. Нужно дернуть дочерние ресурсы.
        // В этой версии работаем только в первым уровнем вложенности.
        if ($item['replaced']) {
            $item['title'] .= ' =='.$item['docId'];

            $fields = 'id,type,pagetitle,menutitle';
            $where = '';
            $sort = 'menuindex';
            $limit = null;
            $childrenDocs = $modx->getDocumentChildren($item['docId'], 1, 0, $fields, $where, $sort, 'ASC', $limit);
            //pre($childrenDocs);
            foreach ($childrenDocs as $doc) {
                $doc['title'] = $doc['menutitle'] ? $doc['menutitle'] : $doc['pagetitle'];
                $doc['url'] = $modx->makeUrl((int)$doc['id']);
                $html .= DLTemplate::getInstance($modx)->parseChunk($tplItem, $doc);
            }

        } // Обычный пункт. Рендерим.
        else {
            // Имеются вложенные пункты
            if (count($item['children'])) {
                $children = renderItems($item['children'], $allTpl, $depth + 1);
                $item['wrap'] = DLTemplate::getInstance($modx)->parseChunk($tplWrap, ['wrap' => $children]);
                $item['classes'] = 'hasChildren';
            }

            $html .= DLTemplate::getInstance($modx)->parseChunk($tplItem, $item);
        }
    }

    return $html;
}
