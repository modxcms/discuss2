<?php

$settings = array();

$settings['discuss2.moderator_page']= $modx->newObject('modSystemSetting');
$settings['discuss2.moderator_page']->fromArray(array(
    'key' => 'discuss2.moderator_page',
    'value' => '0',
    'xtype' => 'numberfield',
    'namespace' => 'discuss2',
    'area' => 'General',
),'',true,true);

$settings['discuss2.user_page']= $modx->newObject('modSystemSetting');
$settings['discuss2.user_page']->fromArray(array(
    'key' => 'discuss2.user_page',
    'value' => '0',
    'xtype' => 'numberfield',
    'namespace' => 'discuss2',
    'area' => 'General',
),'',true,true);

return $settings;