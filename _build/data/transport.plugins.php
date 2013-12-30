<?php
function getPluginContent($filename) {
    $o = file_get_contents($filename);
    $o = trim(str_replace(array('<?php','?>'),'',$o));
    return $o;
}
$elementsPath = $sources['elements']."plugins/";
$plugins = array();

$plugins[0] = $modx->newObject('modPlugin');
$plugins[0]->fromArray(array(
    'id' => 1,
    'name' => 'dis2Router',
    'description' => 'Handles onPageNotFound events for user and moderator pages',
    'plugincode' => getPluginContent($elementsPath.'dis2Router.plugin.php')
));
$events = array();

$events['OnPageNotFound'] = $modx->newObject('modPluginEvent');
$events['OnPageNotFound']->fromArray(array(
    'event' => 'OnPageNotFound',
    'priority' => 0,
    'propertyset' => 0
), '', true, true);

$plugins[0]->addMany($events);

unset($events);
$plugins[1] = $modx->newObject('modPlugin');
$plugins[1]->fromArray(array(
    'id' => 2,
    'name' => 'dis2LoadConfig',
    'description' => 'Loads forum config for custom D2 resources',
    'plugincode' => getPluginContent($elementsPath.'dis2LoadConfig.plugin.php')
));

$events = array();
$events['OnLoadWebDocument'] = $modx->newObject('modPluginEvent');
$events['OnLoadWebDocument']->fromArray(array(
    'event' => 'OnLoadWebDocument',
    'priority' => 0,
    'propertyset' => 0
), '', true, true);
$plugins[1]->addMany($events);

return $plugins;