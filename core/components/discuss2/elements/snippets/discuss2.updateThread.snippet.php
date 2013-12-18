<?php
$response = $modx->discuss2->runProcessor('thread/edit', $hook->getValues());

if ($response->isError()) {
    return $response->getMessage();
}
$obj = $response->getObject();
$modx->sendRedirect($modx->discuss2->getLastPostLink($obj['id'], $obj['post_id']));