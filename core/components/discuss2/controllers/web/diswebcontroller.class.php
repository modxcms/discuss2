<?php

abstract class disWebController {
    public $modx;
    public $discuss;

    public $privateActions = array();
    public $publicActions = array();
    public $accessibleActions = array();
    public $currentAction = array();

    public $properties = array();
    public $controllerPath = null;

    public $action = null;
    public $chunk = null;
    public $pageController = null;

    public $lexicon = 'discuss2:front-end';

    public abstract function process();

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
        $this->_getContentChunk();
        $view = $this->runPageController($obj);
        // TODO : remove sample. prefix

        $view['pagetitle'] = $this->modx->lexicon($this->currentAction[$this->action]['key']);
        $this->modx->toPlaceholder('discuss2.content',$this->discuss->getChunk($this->chunk, $view));
    }

    public function preProcess() {
        return true;
    }

    public function beforeGetContent() {}

    public function afterGetContent(&$obj) {}

    public function getContent() {
        $response = $this->discuss->runProcessor($this->action, array(), array(
            'context' => 'web',
            'target' => 'preprocessors'
        ));
        if ($response->isError()) {
            $this->modx->log(xPDO::LOG_LEVEL_ERROR, "Error running preprocessor {$this->action}\n{$response->getMessage()}");
            return '';
        }
        return $response->getObject();

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
            $this->chunk = $this->modx->getOption($chunk, $this->discuss->forumConfig, 'sample.'.str_replace("_", '', $chunk));
            return $chunk;
        }
        $this->modx->sendErrorPage();
    }

    public function runPageController($obj) {
        $path = dirname(__FILE__).'/'.$this->controllerPath;
        $controllername = str_replace('/','',$this->action);
        $controller = $controllername .".class.php";
        $controllername .= "Controller";
        if (!file_exists($path.$controller)) {
            $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'Could not find page controller ' . $controller . " from " . $path);
        }
        require_once $path.$controller;
        $this->pageController = new $controllername($this->modx, $this->discuss, $obj);
        return $this->pageController->render();
    }
}