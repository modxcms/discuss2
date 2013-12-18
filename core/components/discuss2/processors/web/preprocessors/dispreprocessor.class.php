<?php

class disPreProcessor extends modProcessor {
    protected $classKey = null;
    protected $visibility = 'public';
    public $object = null;
    public $discuss = null;

    public function initialize() {
        $this->discuss = &$this->modx->discuss2;
        switch ($this->visibility) {
            case 'private' :
                if ($this->modx->user->id !== $this->discuss->controller->user->id) {
                    return false;
                }
                break;
            case 'moderator' :
                if (!$this->modx->user->isMember(array('Administrator', 'Discuss2 Global Moderators'))) {
                    return false;
                }
                break;
            case 'public' :
            default :
                if ($this->modx->user->isAuthenticated($this->modx->context->key) || !$this->modx->user->sudo) {
                    return false;
                }
        }
    }

    public function process() {}
}