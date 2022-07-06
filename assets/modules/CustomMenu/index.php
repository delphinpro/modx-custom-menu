<?php
/*
 * Evo Custom Menu
 * Copyright (c) 2018-2022
 * delphinpro <delphinpro@yandex.ru>
 */

/**
 * @var \DocumentParser $modx
 */

if (IN_MANAGER_MODE != 'true' || empty($modx) || !($modx instanceof DocumentParser)) {
    die('Please use the MODX Content Manager instead of accessing this file directly.');
}

if (!$modx->hasPermission('exec_module')) {
    $modx->sendRedirect('index.php?a=106');
}

define('CUSTOM_MENU_DEBUG', true);

require_once __DIR__.'/lib.php';
require_once __DIR__.'/DatabaseUtils.php';

$moduleId = $_GET['id'];
$managerPath = $modx->getManagerPath();
$method = strtoupper($_SERVER['REQUEST_METHOD']);
$isPost = $method !== 'GET';

$modxConfig = $modx->config;
$dbUtils = initDatabaseUtils($modx);
$dbUtils->createTables();

//initDatabaseStruct();

if ($isPost) {
    foreach ($_POST as &$param) {
        if (strtolower($param) === 'null') $param = null;
    }

    $allowedActions = getAllowedActions(__DIR__.'/actions/');

    if (isset($_GET['action']) && in_array($_GET['action'], $allowedActions, true)) {
        /** @noinspection PhpIncludeInspection */
        include __DIR__.'/actions/'.$_GET['action'].'.php';
        $actionFunctionName = $_GET['action'].'Action';
        if (function_exists($actionFunctionName)) {
            call_user_func_array($actionFunctionName, [$modx, $dbUtils]);
        } else {
            sendError('Invalid action\' function: '.$actionFunctionName.'()');
        }
    } else {
        sendError('Invalid action: '.$_GET['action']);
    }
    sendError('END');
}

$jsData = [
    'moduleUrl' => $managerPath.'?a=112&id='.$moduleId,
];

?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Custom Menu</title>
    <style>
        #app:not(.AppMounted) { opacity: 0; }

        pre {border: 1px solid #ddd;background: #efefef;padding: 1rem;}
    </style>
    <link rel="stylesheet" href="<?=rev('styles.min.css', 'manager/media/style/default/css/')?>">
    <link rel="stylesheet" href="<?=rev('dist/index.bundle.css')?>">
</head>
<body>

<div id="app">
    <transition name="slide-down">
        <div
            id="errors"
            class="alert alert-danger"
            v-if="$state.err.length"
            @click="$store.clearErrors()"
        >
            <ul>
                <li v-for="err in $state.err" v-html="err"></li>
            </ul>
        </div>
    </transition>

    <h1>
        <i class="fa fa-list"></i>
        CustomMenu
    </h1>

    <component
        :is="$state.view"
        :menu="$state.editingMenu"
    ></component>

    <!--    <pre>Store {{$state}}</pre>-->
</div>
<?
//pre($dbUtils);
?>
<script>
    // noinspection JSAnnotator
    const APP = <?= json_encode($jsData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)?>;
</script>
<script src="<?=rev('vue.js')?>"></script>
<script src="<?=rev('dist/index.bundle.js')?>"></script>

</body>
</html>
