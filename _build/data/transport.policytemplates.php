<?php

$templates = array();

/* administrator template/policy */
$templates['1']= $modx->newObject('modAccessPolicyTemplate');
$templates['1']->fromArray(array(
    'id' => 1,
    'name' => 'Discuss2Template',
    'description' => 'Access Policy Template for the Discuss2 forums.',
    'lexicon' => 'discuss2:permissions',
    'template_group' => 1,
));
$permissions = include dirname(__FILE__).'/permissions/discuss2.permissions.php';
if (is_array($permissions)) {
    $templates['1']->addMany($permissions);
} else { $modx->log(modX::LOG_LEVEL_ERROR,'Could not load Discuss2 Policy Template permissions.'); }

return $templates;