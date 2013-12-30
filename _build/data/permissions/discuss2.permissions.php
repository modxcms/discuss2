<?php

$permissions = array();

$permissions[] = $modx->newObject('modAccessPermission', array(
    'name' => 'discuss2.ban',
    'description' => 'discuss2.ban',
    'value' => true
));

$permissions[] = $modx->newObject('modAccessPermission', array(
    'name' => 'discuss2.can_post',
    'description' => 'discuss2.can_post',
    'value' => true
));

$permissions[] = $modx->newObject('modAccessPermission', array(
    'name' => 'discuss2.lock_thread',
    'description' => 'discuss2.lock_thread',
    'value' => true
));

$permissions[] = $modx->newObject('modAccessPermission', array(
    'name' => 'discuss2.merge_thread',
    'description' => 'discuss2.merge_thread',
    'value' => true
));

$permissions[] = $modx->newObject('modAccessPermission', array(
    'name' => 'discuss2.modify_post',
    'description' => 'discuss2.modify_post',
    'value' => true
));

$permissions[] = $modx->newObject('modAccessPermission', array(
    'name' => 'discuss2.modify_thread',
    'description' => 'discuss2.modify_thread',
    'value' => true
));

$permissions[] = $modx->newObject('modAccessPermission', array(
    'name' => 'discuss2.remove_post',
    'description' => 'discuss2.remove_post',
    'value' => true
));

$permissions[] = $modx->newObject('modAccessPermission', array(
    'name' => 'discuss2.remove_thread',
    'description' => 'discuss2.remove_thread',
    'value' => true
));

$permissions[] = $modx->newObject('modAccessPermission', array(
    'name' => 'discuss2.split_thread',
    'description' => 'discuss2.split_thread',
    'value' => true
));

$permissions[] = $modx->newObject('modAccessPermission', array(
    'name' => 'discuss2.stick_thread',
    'description' => 'discuss2.stick_thread',
    'value' => true
));
return $permissions;