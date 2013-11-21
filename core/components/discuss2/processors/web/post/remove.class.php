<?php

class removePostProcessor extends modObjectRemoveProcessor {
    public $classKey = 'disPost';
    public $permission = 'discuss2.remove_post';

    public $checkRemovePermission = false;

    public function initialize() {
        $primaryKey = $this->getProperty($this->primaryKeyField,false);
        if (empty($primaryKey)) return $this->modx->lexicon($this->objectType.'_err_ns');
        $this->object = $this->modx->getObject($this->classKey,$primaryKey);
        if (empty($this->object)) return $this->modx->lexicon($this->objectType.'_err_nfs',array($this->primaryKeyField => $primaryKey));

        if ($this->object instanceof modAccessibleObject && !$this->object->checkPolicy('discuss2.remove_post')) {
            return $this->modx->lexicon('access_denied');
        }
        $this->id = $this->object->id;


        return true;
    }
}

return 'removePostProcessor';