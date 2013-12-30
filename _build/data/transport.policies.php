<?php

$policies = array();
$policies[1]= $modx->newObject('modAccessPolicy');
$policies[1]->fromArray(array (
    'id' => 1,
    'name' => 'Discuss2 Global Moderators',
    'description' => 'A policy with all Discuss2 Policies.',
    'parent' => 0,
    'class' => '',
    'lexicon' => 'discuss2:permissions',
    'data' => '{"discuss2.ban":true,"discuss2.can_post":true,"discuss2.lock_thread":true,"discuss2.merge_thread":true,"discuss2.modify_post":true,"discuss2.modify_thread":true,"discuss2.remove_post":true,"discuss2.remove_thread":true,"discuss2.split_thread":true,"discuss2.stick_thread":true}',
), '', true, true);

$policies[2]= $modx->newObject('modAccessPolicy');
$policies[2]->fromArray(array (
    'id' => 2,
    'name' => 'Discuss2 Moderators',
    'description' => 'A policy with Discuss2 moderator permissions.',
    'parent' => 0,
    'class' => '',
    'lexicon' => 'discuss2:permissions',
    'data' => '{"discuss2.ban":false,"discuss2.can_post":true,"discuss2.lock_thread":true,"discuss2.merge_thread":true,"discuss2.modify_post":true,"discuss2.modify_thread":true,"discuss2.remove_post":true,"discuss2.remove_thread":true,"discuss2.split_thread":true,"discuss2.stick_thread":true}',
), '', true, true);

$policies[3]= $modx->newObject('modAccessPolicy');
$policies[3]->fromArray(array (
    'id' => 3,
    'name' => 'Discuss2 Members',
    'description' => 'A policy with basic Discuss2 posting, viewing and editing permissions for forum members.',
    'parent' => 0,
    'class' => '',
    'lexicon' => 'discuss2:permissions',
    'data' => '{"discuss2.ban":false,"discuss2.can_post":true,"discuss2.lock_thread":false,"discuss2.merge_thread":false,"discuss2.modify_post":false,"discuss2.modify_thread":false,"discuss2.remove_post":false,"discuss2.remove_thread":false,"discuss2.split_thread":false,"discuss2.stick_thread":false}',
), '', true, true);

return $policies;