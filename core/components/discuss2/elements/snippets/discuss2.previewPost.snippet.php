<?php
$response = $modx->discuss2->runProcessor('post/preview', $hook->getValues());

if ($response->isError()) {
    return $response->getMessage();
}
$obj = $response->getObject();

$hook->setValues($obj);