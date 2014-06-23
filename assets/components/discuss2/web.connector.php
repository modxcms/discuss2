<?php

if (!empty($_REQUEST['action']) && $allowAction) {
    @session_cache_limiter('public');
    define('MODX_REQP',false);
}

require_once dirname(dirname(dirname(dirname(__FILE__)))).'/config.core.php';
require_once MODX_CORE_PATH.'config/'.MODX_CONFIG_KEY.'.inc.php';
require_once MODX_CONNECTORS_PATH.'index.php';

$corePath = $modx->getOption('discuss2.core_path',null,$modx->getOption('core_path').'components/discuss2/');

if (!is_object($modx->discuss2)) {
    $modx->log(xPDO::LOG_LEVEL_ERROR, 'Discuss2 not loaded on web controller request');
}
if ($allowAction && ( $modx->user->hasSessionContext('mgr') || $modx->user->hasSessionContext($modx->context->key))) {
    if ($modx->user->hasSessionContext('mgr')) {
        $_SESSION["modx.{$modx->context->key}.user.token"] = $_SESSION["modx.mgr.user.token"];
    }
    $_SERVER['HTTP_MODAUTH'] = $_SESSION["modx.{$modx->context->key}.user.token"];
    $_REQUEST['HTTP_MODAUTH'] = $_SERVER['HTTP_MODAUTH'];
}

/* force to use web/ processors and handle request */
$path = $modx->getOption('processorsPath',$modx->discuss2->config,$corePath.'processors/') .'web/';
$modx->request->handleRequest(array(
    'processors_path' => $path,
    'location' => '',
));