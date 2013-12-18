<?php

abstract class disWebController {
    public $modx;
    public $discuss;

    public $privateActions = array();
    public $publicActions = array();
    public $accessibleActions = array();
    public $currentAction = array();

    public $properties = array();

    public $action = null;

    public $lexicon = 'discuss2:front-end';

    public function __construct(modX &$modx, $properties = array()) {
        $this->modx = $modx;
        $this->discuss = &$modx->discuss2;
        $this->properties = $properties;
        $this->initialize();
    }

    public function loadConfig() {
        if (isset($this->properties['forum'])) {
            $this->discuss->loadConfig($this->properties['forum']);
        } else {
            // Test if parent is any of discuss pages
            $parent = $this->modx->getObject('modResource', $this->modx->resource->parent);
            if (in_array($parent->class_key, array('disPost', 'disThread', 'disThreadDiscussion', 'disThreadQuestion', 'disBoard', 'disCategory', 'disForum'))) {
                $this->discuss->loadConfig($parent->id);
            } else {
                // TODO: implement parent recursion
            }
        }
    }

    public function init() {
        return true;
    }

    public function initialize() {
        if (!$this->init()) {
            $this->modx->sendUnauthorizedPage();
        }

        $action = $this->modx->request->parameters['GET']['action'];
        foreach (array_keys($this->accessibleActions) as $act) {
            if (!$this->modx->hasPermission($act) && $action == $act) {
                $this->modx->sendUnauthorizedPage();
            }
        }
        $this->action = $action;
        $this->modx->lexicon->load($this->lexicon);
    }

    public function render() {
        if ($this->preProcess() === false) {
            $this->modx->sendErrorPage();
        }
        $this->process();
        $this->beforeGetContent();
        $obj = $this->getContent();
        $this->afterGetContent($obj);
        $this->modx->setPlaceholders('discuss2.', $obj);
    }

    public function preProcess() {
        return true;
    }

    public function beforeGetContent() {}

    public function afterGetContent(&$obj) {}

    public function getContent() {
        $chunk = $this->_getContentChunk();

    }

    public function runContentProcessor($action) {

    }

    protected function _getContentChunk() {
        $chunk = null;
        foreach ($this->publicActions as $action => $arr) {
            if ($action == $this->action) {
                $chunk = $arr['chunk'];
                $this->currentAction[$action] = $arr;
            }
        }
        foreach ($this->privateActions as $action => $arr) {
            if ($action == $this->action) {
                $this->currentAction[$action] = $arr;
                $chunk = $arr['chunk'];
            }
        }
        foreach ($this->publicActions as $action => $arr) {
            if ($arr['action'] == $this->action) {
                $this->currentAction[$arr['action']] = $arr;
                $chunk = $arr['chunk'];
            }
        }
        if ($chunk !== null) {
            return $chunk;
        }
        $this->modx->sendErrorPage();
    }

    public abstract function process();
}