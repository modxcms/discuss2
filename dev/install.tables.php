<?php
require_once dirname(dirname(__FILE__)).'/_build/build.config.php';
include_once MODX_CORE_PATH . 'model/modx/modx.class.php';
$modx= new modX();
$modx->initialize('mgr');
$modx->setLogLevel(modX::LOG_LEVEL_INFO);
$modx->setLogTarget('ECHO');

$root = dirname(dirname(__FILE__)).'/';

$sources = array(
    'model' => $root.'core/components/discuss2/model/',
);

$m = $modx->getManager();

$modx->addPackage('discuss2',$sources['model'], 'discuss2_');

$m->createObjectContainer('disUserProfile');
$m->createObjectContainer('disUserNetwork');
$m->createObjectContainer('disUserMention');
$m->createObjectContainer('disUserIgnoreBoard');
$m->createObjectContainer('disClosure');
$m->createObjectContainer('disThreadParticipant');
$m->createObjectContainer('disThreadRead');
$m->createObjectContainer('disPostAttachment');
$m->createObjectContainer('disForumActivity');
$m->createObjectContainer('disUserActivity');
$m->createObjectContainer('disThreadStatistics');
$m->createObjectContainer('disSubscription');
