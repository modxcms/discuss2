<?php

$groups = array();

$groups[1]= $modx->newObject('modUserGroup');
$groups[1]->fromArray(array(
    'id' => 1,
    'name' => 'Discuss2 Global Moderators',
    'description' => 'Forum wide moderators group',
));
$groups[2]= $modx->newObject('modUserGroup');
$groups[2]->fromArray(array(
    'id' => 2,
    'name' => 'Discuss2 Members',
    'description' => 'Discuss2 Members.',
));

return $groups;