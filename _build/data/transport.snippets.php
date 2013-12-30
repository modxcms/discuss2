<?php
function getSnippetContent($filename) {
    $o = file_get_contents($filename);
    $o = trim(str_replace(array('<?php','?>'),'',$o));
    return $o;
}
$snippetsPath = $sources['elements']."snippets/";
$snippets = array();

$i = 1;
foreach(glob($snippetsPath."*.php") as $snip) {
    $snippets[$i] = $modx->newObject('modSnippet');
    $snippets[$i]->fromArray(array(
        'id' => $i,
        'name' => implode('.', array_slice(explode(".", basename($snip)), 0, 2)),
        'snippet' => getSnippetContent($snip)
    ));
    $i++;
}

return $snippets;