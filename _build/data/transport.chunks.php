<?php
function getChunkContents($filename) {
    $o = file_get_contents($filename);
    $o = trim($o);
    return $o;
}
$elementsPath = $sources['elements']."chunks/";
$chunks = array();

$i = 1;
foreach(glob($elementsPath) as $chunk) {
    $chunks[$i] = $modx->newObject('modChunk');
    $chunks[$i]->fromArray(array(
        'id' => $i,
        'name' => implode('.', array_slice(explode(".", basename($chunk)), 0, 2)),
        'snippet' => getChunkContents($chunk)
    ));
    $i++;
}

return $chunks;