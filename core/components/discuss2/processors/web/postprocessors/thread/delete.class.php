<?php

class deleteThreadProcessor extends modObjectUpdaterocessor {
    public $classKey = 'disThread';
    public $permission = 'discuss2.remove_thread';

    public $checkRemovePermission = false;
    public $primaryKeyField = 'id';

    private $thread = null;
    private $id;

    public function initialize() {
        $primaryKey = $this->getProperty($this->primaryKeyField,false);
        if (empty($primaryKey)) return $this->modx->lexicon($this->objectType.'_err_ns');
        $this->object = $this->modx->getObject($this->classKey,$primaryKey);
        if (empty($this->object)) return $this->modx->lexicon($this->objectType.'_err_nfs',array($this->primaryKeyField => $primaryKey));

        if ($this->object instanceof modAccessibleObject && !$this->object->checkPolicy('discuss2.remove_thread')) {
            return $this->modx->lexicon('access_denied');
        }
        $this->id = $this->object->id;
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
        $sql = "UPDATE {$this->modx->getTableName('modResource')} SET {$this->modx->escape('deleted')} = 0, {$this->modx->escape('published')} = 0 WHERE {$this->modx->escape('parent')} = {$this->object->id}";
        $affected = $this->modx->exec($sql);
        if ($affected == 0) {
            $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'deleteThread processor could not set children to deleted');
        }
        return true;
    }
}

return 'deleteThreadProcessor';