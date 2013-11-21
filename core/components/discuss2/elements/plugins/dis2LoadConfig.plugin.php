<?php

if (in_array($modx->resource->class_key, array('disForum', 'disCategory', 'disBoard', 'disThread', 'disThreadDiscussion', 'disThreadQuestion', 'disPost'))) {
    $modx->discuss2->init();
}