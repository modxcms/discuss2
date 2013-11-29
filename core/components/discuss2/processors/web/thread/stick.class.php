<?php

class stickThreadProcessor extends modObjectUpdateProcessor {
    public $classKey = 'disThread';
    public $permission = 'discuss2.lock_thread';
    public $primaryKeyField = 'id';

    public function initialize() {
        $primaryKey = $this->getProperty($this->primaryKeyField,false);
        if (empty($primaryKey)) return $this->modx->lexicon($this->objectType.'_err_ns');
        $this->object = $this->modx->getObject($this->classKey,$primaryKey);
        if (empty($this->object)) return $this->modx->lexicon($this->objectType.'_err_nfs',array($this->primaryKeyField => $primaryKey));
        if (!$this->modx->hasPermission('discuss2.lock_thread')) {
            return $this->modx->lexicon('access_denied');
        }
        return true;
    }

    public function process() {
        $prop = $this->modx->getObject('disThreadProperty', array('idx' => $this->id));
        $prop->set('sticky', 1);
        if(!$prop->save()) {
            $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'Could not set thread: ' . $this->object->id . " : " . $this->object->pagetitle . " sticky");
            return false;
        }
        return true;
    }
}

return 'stickThreadProcessor';