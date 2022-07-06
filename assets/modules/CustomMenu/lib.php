<?php
/*
 * Evo Custom Menu
 * Copyright (c) 2018-2022
 * delphinpro <delphinpro@yandex.ru>
 */

/**
 * @var \DocumentParser $modx
 */

if (empty($modx) || !($modx instanceof DocumentParser)) {
    die('Please use the MODX Content Manager instead of accessing this file directly.');
}

/**
 *
 */
function pre()
{
    $args = func_get_args();
    foreach ($args as $arg) {
        echo '<pre>';
        print_r($arg);
        echo '</pre>';
    }
}

/**
 *
 */
function vd()
{
    $args = func_get_args();
    foreach ($args as $arg) {
        echo '<pre>';
        var_dump($arg);
        echo '</pre>';
    }
}

/**
 * @param $dir
 * @return array
 */
function getAllowedActions($dir)
{
    $allowedActions = [];
    $actionFiles = scandir($dir);
    foreach ($actionFiles as $actionFile) {
        $info = pathinfo($actionFile);
        if ($info['extension'] === 'php') {
            $allowedActions[] = $info['filename'];
        }
    }
    return $allowedActions;
}

function sendResponse($payload = null, $message = null)
{
    echo json_encode([
        'status'  => true,
        'message' => $message,
        'payload' => $payload,
        '$_POST'  => $_POST,
        '$_GET'   => $_GET,
    ], JSON_UNESCAPED_UNICODE);
    die;
}

function sendError($message = 'Unknown error')
{
    echo json_encode([
        'status'  => false,
        'message' => $message,
        'payload' => null,
        '$_POST'  => $_POST,
        '$_GET'   => $_GET,
    ], JSON_UNESCAPED_UNICODE);
    die;
}

function getIntParam($name, $default = null)
{
    if (!array_key_exists($name, $_POST)) return $default;

    if ($_POST[$name] === null) return null;
    $value = trim($_POST[$name]);

    if ($value === '') return null;
    if (strtolower($value) === 'null') return null;

    return (int)$_POST[$name];
}

function getStringParam($name, $default = null)
{
    if (!array_key_exists($name, $_POST)) return $default;

    if ($_POST[$name] === null) return null;
    $value = trim($_POST[$name]);

    if (strtolower($value) === 'null') return null;

    return $value;
}

function rev($filename, $path = 'assets/modules/CustomMenu/')
{
    global $modx;
    $_filename = $modx->config['base_path'].$path.$filename;
    $md5 = md5_file($_filename);
    $v = substr($md5, 0, 7);
    return $modx->config['base_url'].$path.$filename.'?rev='.$v;
}

/**
 * from https://stackoverflow.com/questions/1993721/how-to-convert-camelcase-to-camel-case
 * @param $input
 * @return string
 */
function camelCase2Shake($input)
{
    preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
    $ret = $matches[0];
    foreach ($ret as &$match) {
        $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
    }
    return implode('_', $ret);
}

function shake2CamelCase($input)
{
    $words = explode('_', $input);
    $first = array_shift($words);
    $words = array_map(function ($word) {
        return ucfirst($word);
    }, $words);

    return $first.implode('', $words);
}

function buildTree(array $items, $parent = 0, $idKey = 'id', $parentIdKey = 'parent_id', $childrenKey = 'children')
{
    $tree = [];
    foreach ($items as $item) {
        if ($item[$parentIdKey] === $parent) {
            $children = buildTree($items, $item[$idKey], $idKey, $parentIdKey, $childrenKey);
            if ($children) {
                $item[$childrenKey] = $children;
            } else {
                $item[$childrenKey] = [];
            }
            $tree[] = $item;
        }
    }

    return $tree;
}
