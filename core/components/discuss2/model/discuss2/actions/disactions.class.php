<?php

class disAction {
    public $modx;
    public $discuss;

    protected $isView = false;

    protected $actions = array();
    protected $chunkKeys = array();

    public function __construct(modX &$modx) {
        $this->modx = $modx;
        $this->discuss = &$modx->discuss2;
    }

    public function loadViewChunk() {

    }

    public function process() {

    }
}