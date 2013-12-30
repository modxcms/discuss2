<?php
$tstart = explode(' ', microtime());
$tstart = $tstart[1] + $tstart[0];
set_time_limit(0);

define('PKG_NAME','Discuss2');
define('PKG_NAME_LOWER','discuss2');
define('PKG_VERSION','0.6');
define('PKG_RELEASE','alpha');

$root = dirname(dirname(__FILE__))."/";

$sources = array(
    'root' => $root,
    'build' => $root . '_build/',
    'data' => $root . '_build/data/',
    'resolvers' => $root . '_build/resolvers/',
    'chunks' => $root.'core/components/'.PKG_NAME_LOWER.'/chunks/',
    'lexicon' => $root . 'core/components/'.PKG_NAME_LOWER.'/lexicon/',
    'docs' => $root.'core/components/'.PKG_NAME_LOWER.'/docs/',
    'elements' => $root.'core/components/'.PKG_NAME_LOWER.'/elements/',
    'source_assets' => $root.'assets/components/'.PKG_NAME_LOWER,
    'source_core' => $root.'core/components/'.PKG_NAME_LOWER,
);
unset($root);

require_once $sources['build'] . 'build.config.php';
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';

$modx= new modX();
$modx->initialize('mgr');
echo '<pre>';
$modx->setLogLevel(modX::LOG_LEVEL_INFO);
$modx->setLogTarget('ECHO');

$modx->loadClass('transport.modPackageBuilder','',false, true);
$builder = new modPackageBuilder($modx);
$builder->createPackage(PKG_NAME_LOWER,PKG_VERSION,PKG_RELEASE);
$builder->registerNamespace(PKG_NAME_LOWER,false,true,'{core_path}components/'.PKG_NAME_LOWER.'/');

/* add plugins */
$plugins = include $sources['data'].'transport.plugins.php';
if (!is_array($plugins)) { $modx->log(modX::LOG_LEVEL_FATAL,'Adding plugins failed.'); }
$attributes= array(
    xPDOTransport::UNIQUE_KEY => 'name',
    xPDOTransport::PRESERVE_KEYS => false,
    xPDOTransport::UPDATE_OBJECT => true,
    xPDOTransport::RELATED_OBJECTS => true,
    xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array (
        'PluginEvents' => array(
            xPDOTransport::PRESERVE_KEYS => true,
            xPDOTransport::UPDATE_OBJECT => false,
            xPDOTransport::UNIQUE_KEY => array('pluginid','event'),
        ),
    ),
);
foreach ($plugins as $plugin) {
    $vehicle = $builder->createVehicle($plugin, $attributes);
    $builder->putVehicle($vehicle);
}
$modx->log(modX::LOG_LEVEL_INFO,'Packaged in '.count($plugins).' plugins.'); flush();
unset($plugins,$plugin,$attributes);

// Add rest
$category= $modx->newObject('modCategory');
$category->set('id',1);
$category->set('category',PKG_NAME);

/* add snippets */
$modx->log(modX::LOG_LEVEL_INFO,'Packaging in snippets...');
$snippets = include $sources['data'].'transport.snippets.php';
if (empty($snippets)) $modx->log(modX::LOG_LEVEL_ERROR,'Could not package in snippets.');
$category->addMany($snippets);
unset($snippets);

/* add chunks */
$modx->log(modX::LOG_LEVEL_INFO,'Packaging in chunks...');
$chunks = include $sources['data'].'transport.chunks.php';
if (empty($chunks)) $modx->log(modX::LOG_LEVEL_ERROR,'Could not package in chunks.');
$category->addMany($chunks);
unset($chunks);

/* create category vehicle */
$attr = array(
    xPDOTransport::UNIQUE_KEY => 'category',
    xPDOTransport::PRESERVE_KEYS => false,
    xPDOTransport::UPDATE_OBJECT => true,
    xPDOTransport::RELATED_OBJECTS => true,
    xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array (
        'Snippets' => array(
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => true,
            xPDOTransport::UNIQUE_KEY => 'name',
        ),
        'Chunks' => array(
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => true,
            xPDOTransport::UNIQUE_KEY => 'name',
        ),
    ),
);
$vehicle = $builder->createVehicle($category,$attr);


$modx->log(modX::LOG_LEVEL_INFO,'Adding file resolvers to category...');
$vehicle->resolve('file',array(
    'source' => $sources['source_assets'],
    'target' => "return MODX_ASSETS_PATH . 'components/';",
));
$vehicle->resolve('file',array(
    'source' => $sources['source_core'],
    'target' => "return MODX_CORE_PATH . 'components/';",
));
$vehicle->resolve('php',array(
    'source' => $sources['resolvers'] . 'resolve.tables.php',
));
$vehicle->resolve('php',array(
    'source' => $sources['resolvers'] . 'resolve.extpackage.php',
));

$builder->putVehicle($vehicle);
/* load roles  */
$roles = include $sources['data'].'transport.roles.php';
if (empty($roles)) $modx->log(modX::LOG_LEVEL_ERROR,'Could not package in Roles.');
$attributes = array (
    xPDOTransport::PRESERVE_KEYS => false,
    xPDOTransport::UPDATE_OBJECT => true,
    xPDOTransport::UNIQUE_KEY => array ('name'),
);

foreach ($roles as $role) {
    $vehicle = $builder->createVehicle($role,$attributes);
    $builder->putVehicle($vehicle);
}
$modx->log(xPDO::LOG_LEVEL_INFO,'Packaged in '.count($roles).' default Roles.'); flush();
/* package in default access policy */
$attributes = array (
    xPDOTransport::PRESERVE_KEYS => false,
    xPDOTransport::UNIQUE_KEY => array('name'),
    xPDOTransport::UPDATE_OBJECT => true,
);
$policies = include $sources['data'].'transport.policies.php';
if (!is_array($policies)) { $modx->log(modX::LOG_LEVEL_FATAL,'Adding policies failed.'); }
foreach ($policies as $policy) {
    $vehicle = $builder->createVehicle($policy,$attributes);
    $builder->putVehicle($vehicle);
}
$modx->log(modX::LOG_LEVEL_INFO,'Packaged in '.count($policies).' Access Policies.'); flush();
unset($policies,$policy,$attributes);

/* package in default access policy template */
$templates = include dirname(__FILE__).'/data/transport.policytemplates.php';
$attributes = array (
    xPDOTransport::PRESERVE_KEYS => false,
    xPDOTransport::UNIQUE_KEY => array('name'),
    xPDOTransport::UPDATE_OBJECT => true,
    xPDOTransport::RELATED_OBJECTS => true,
    xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array (
        'Permissions' => array (
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => true,
            xPDOTransport::UNIQUE_KEY => array ('template','name'),
        ),
    )
);
if (is_array($templates)) {
    foreach ($templates as $template) {
        $vehicle = $builder->createVehicle($template,$attributes);
        $builder->putVehicle($vehicle);
    }
    $modx->log(modX::LOG_LEVEL_INFO,'Packaged in '.count($templates).' Access Policy Templates.'); flush();
} else {
    $modx->log(modX::LOG_LEVEL_ERROR,'Could not package in Access Policy Templates.');
}
unset ($templates,$template,$idx,$ct,$attributes);

/* load system settings */
$modx->log(modX::LOG_LEVEL_INFO,'Packaging in System Settings...');
$settings = include $sources['data'].'transport.settings.php';
if (empty($settings)) $modx->log(modX::LOG_LEVEL_ERROR,'Could not package in settings.');
$attributes= array(
    xPDOTransport::UNIQUE_KEY => 'key',
    xPDOTransport::PRESERVE_KEYS => true,
    xPDOTransport::UPDATE_OBJECT => false,
);
foreach ($settings as $setting) {
    $vehicle = $builder->createVehicle($setting,$attributes);
    $builder->putVehicle($vehicle);
}
unset($settings,$setting,$attributes);

/* load user groups  */
$userGroups = include $sources['data'].'transport.usergroups.php';
if (empty($userGroups)) $modx->log(modX::LOG_LEVEL_ERROR,'Could not package in User Groups.');
$attributes = array (
    xPDOTransport::PRESERVE_KEYS => false,
    xPDOTransport::UPDATE_OBJECT => true,
    xPDOTransport::UNIQUE_KEY => array ('name'),
);

foreach ($userGroups as $userGroup) {
    $vehicle = $builder->createVehicle($userGroup,$attributes);
    $builder->putVehicle($vehicle);
}


$vehicle->resolve('php',array(
    'source' => $sources['resolvers'] . 'resolve.policies.php',
));

$builder->putVehicle($vehicle);


$modx->log(modX::LOG_LEVEL_INFO,'Adding package attributes and setup options...');
$builder->setPackageAttributes(array(
    'license' => file_get_contents($sources['docs'] . 'license.txt'),
    'readme' => file_get_contents($sources['docs'] . 'readme.txt'),
    'changelog' => file_get_contents($sources['docs'] . 'changelog.txt'),
));

/* zip up package */
$modx->log(modX::LOG_LEVEL_INFO,'Packing up transport package zip...');
$builder->pack();

$tend= explode(" ", microtime());
$tend= $tend[1] + $tend[0];
$totalTime= sprintf("%2.4f s",($tend - $tstart));
$modx->log(modX::LOG_LEVEL_INFO,"\n<br />Package Built.<br />\nExecution time: {$totalTime}\n");


session_write_close();
exit ();