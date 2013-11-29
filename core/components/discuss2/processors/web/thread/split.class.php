<?php

class splitThreadProcessor extends modObjectUpdateProcessor {
    public $classKey = 'disPost';
    public $permission = 'discuss2.split_thread';
    public $primaryKeyField = 'id';

    public function initialize() {
        $primaryKey = $this->getProperty($this->primaryKeyField,false);
        if (empty($primaryKey)) return $this->modx->lexicon($this->objectType.'_err_ns');
        $this->object = $this->modx->getObject($this->classKey,$primaryKey);
        if (empty($this->object)) return $this->modx->lexicon($this->objectType.'_err_nfs',array($this->primaryKeyField => $primaryKey));
        if (!$this->modx->hasPermission('discuss2.split_thread')) {
            return $this->modx->lexicon('access_denied');
        }
        return true;
    }

    public function process() {
        if (!isset($this->object->properties)) {
            $this->object->properties = array();
        } else if (!is_array($this->object->properties)) {
            $this->object->properties = $this->modx->fromJSON($this->object->properties);
        }

        $response = $this->modx->discuss2->runProcessor('web/thread/new', array(
            'parent' => $this->getProperty('parent'),
            'pagetitle' => $this->getProperty('pagetitle'),
            'content' => $this->getProperty('content'),
            'properties' => array(
                'split_from' => $this->object->id,
                'thread_from' => $this->object->parent
            ),
            'createdby' => $this->object->createdby
        ));
        $obj = $response->getObject();
        $this->object->properties['split_to']  = $obj['post_id'];
        $this->object->save();
        $this->object->set('post_id', $obj['post_id']);
        $this->cleanup();
        return true;
    }
}

return 'splitThreadProcessor';