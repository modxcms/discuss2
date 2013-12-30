<?php

abstract class disPageController {
    protected $modx;
    protected $discuss;
    protected $data;
    public function __construct(&$modx, &$discuss,$data) {
        $this->modx = $modx;
        $this->discuss = $discuss;
        $this->data = $data;
    }
    abstract function render();
}