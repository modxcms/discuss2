<?php

class editThreadProcessor extends modObjectUpdateProcessor {
    public $classKey = 'disThreadDiscussion';
    public $permission = 'discuss2.modify_thread';
    public $primaryKeyField = 'id';

    public function initialize() {
        $this->classKey = $this->getProperty('thread');
        $primaryKey = $this->getProperty($this->primaryKeyField,false);
        if (empty($primaryKey)) return $this->modx->lexicon($this->objectType.'_err_ns');
        $this->object = $this->modx->getObject($this->classKey,$primaryKey);
        if (empty($this->object)) return $this->modx->lexicon($this->objectType.'_err_nfs',array($this->primaryKeyField => $primaryKey));
        if (!$this->modx->hasPermission('discuss2.modify_thread')) {
            return $this->modx->lexicon('access_denied');
        }
        return true;
    }

    public function afterSave() {
        $c = $this->modx->newQuery('disPost');
        $c->where(array('parent' => $this->object->id));
        $c->sortby('id', 'ASC');
        $c->limit(1);

        $post = $this->modx->getObject('disPost', $c);

        $post->fromArray(array(
            'content' => $this->object->content,
            'pagetitle' => $this->object->pagetitle,
        ));
        $post->save();

        if (!empty($_FILES['attachments'])) {
            $post->saveAttachments($_FILES);
        }
        $notify = $this->getProperty('notify');
        if ($notify == 1) {
            $cnt = $this->modx->getCount('disSubscription', array('thread' => $this->object->id, 'user' => $this->modx->user->id));
            if ($cnt == 0) {
                $obj = $this->modx->newObject('disSubscription');
                $obj->fromArray(array(
                    'thread' => $this->object->id,
                    'user' => $this->modx->user->id
                ));
                $obj->save();
            }
        } else {

        }

        $this->object->set('post_id', $post->id);
        return true;
    }
}

return 'editThreadProcessor';