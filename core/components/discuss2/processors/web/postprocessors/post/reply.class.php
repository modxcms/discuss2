<?php

class replyPostProcessor extends modObjectCreateProcessor {
    public $classKey = 'disPost';
    public $permission = 'discuss2.can_post';

    public function beforeSave() {
        if (!isset($this->properties['parent'])) {
            $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'Parent id was not provided for post reply');
            return false;
        } else {
            $parent = $this->modx->getObject('disPost', $this->properties['parent']);
            if ($parent->class_key!== 'disPost') {
                $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'Trying to reply to thread, not post');
                return false;
            }
        }
        $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'BEFORE SAVE()');
        $this->object->published = 1;
        $this->object->parent = $parent->id;
        $this->object->createdby = $this->modx->user->id;
        $this->object->template = $parent->template;
        return true;
    }

    public function afterSave() {
        if (!empty($_FILES['attachments'])) {
            $this->object->saveAttachments($_FILES);
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
        }
        $cm = $this->modx->getCacheManager();
        $cm->refresh();
    }
}

return 'replyPostProcessor';