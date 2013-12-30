<?php


$roles = array();

$roles[1] = $modx->newObject('modUserGroupRole');
$roles[1]->fromArray(array(
    'name' => 'Discuss2 Global Moderator',
    'authority' => 9000
));

$roles[2] = $modx->newObject('modUserGroupRole');
$roles[2]->fromArray(array(
    'name' => 'Discuss2 Moderator',
    'authority' => 9500
));

$roles[3] = $modx->newObject('modUserGroupRole');
$roles[3]->fromArray(array(
    'name' => 'Discuss2 Member',
    'authority' => 9999
));
return $roles;