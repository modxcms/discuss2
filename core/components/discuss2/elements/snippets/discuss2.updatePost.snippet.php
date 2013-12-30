<?php
$response = $modx->discuss2->runProcessor('post/edit', $hook->getValues());

if ($response->isError()) {
    return $response->getMessage();
}
$obj = $response->getObject();
$modx->sendRedirect($modx->discuss2->getLastPostLink($obj['parent'], $obj['id']));