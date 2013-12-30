<?php
if (is_object($modx->resource) && in_array($modx->resource->class_key, array('disForum', 'disCategory', 'disBoard', 'disThread', 'disThreadDiscussion', 'disThreadQuestion', 'disPost'))) {
    $modx->discuss2->init();
}