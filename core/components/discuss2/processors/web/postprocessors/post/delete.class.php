<?php

class deletePostProcessor extends modObjectUpdateProcessor {
    public $classKey = 'disPost';
    public $permission = 'discuss2.remove_post';

    public $checkRemovePermission = false;
    private $thread = null;
    private $id;

    public function initialize() {
        $primaryKey = $this->getProperty($this->primaryKeyField,false);
        if (empty($primaryKey)) return $this->modx->lexicon($this->objectType.'_err_ns');
        $this->object = $this->modx->getObject($this->classKey,$primaryKey);
        if (empty($this->object)) return $this->modx->lexicon($this->objectType.'_err_nfs',array($this->primaryKeyField => $primaryKey));

        if ($this->object instanceof modAccessibleObject && !$this->object->checkPolicy('discuss2.remove_post')) {
            return $this->modx->lexicon('access_denied');
        }
        $this->id = $this->object->id;
        $obj = $this->modx->getObject('modResource', $this->object->parent);
        if (in_array($obj->class_key, array('disThread', 'disThreadQuestion', 'disThreadDiscussion'))) {
            $this->thread = $obj;
        }

        return true;
    }

    public function process() {
        $canRemove = $this->beforeRemove();
        if ($canRemove !== true) {
            return $this->failure($canRemove);
        }

        $this->object->deleted = 1;
        $this->object->published = 0;
        $this->object->save();

        $this->afterRemove();
        $this->cleanup();
        return $this->success('',array($this->primaryKeyField => $this->object->get($this->primaryKeyField)));
    }

    public function afterRemove() {
        if ($this->thread !== null) {
            $count = $this->modx->getCount('disThread', array('parent' => $this->thread->id, 'deleted' => 0));
            if ($count == 0) {
                $this->thread->published = 0;
                $this->thread->deleted = 1;
                $this->thread->save();
            }
        }
    }
}

return 'deletePostProcessor';