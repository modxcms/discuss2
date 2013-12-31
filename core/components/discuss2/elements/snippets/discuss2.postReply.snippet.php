<?php
$response = $modx->discuss2->runProcessor('post/reply', $hook->getValues());

if ($response->isError()) {
    return $response->getMessage();
}
$obj = $response->getObject();
$modx->sendRedirect($modx->discuss2->makeUrl($modx->resource->id).'#post-'.$obj['id']);
return true;