<?php
$response = $modx->discuss2->runProcessor('thread/lock', $hook->getValues());

if ($response->isError()) {
    return $response->getMessage();
}
$obj = $response->getObject();
$modx->sendRedirect($modx->discuss2->makeUrl($this->modx->resource->id));