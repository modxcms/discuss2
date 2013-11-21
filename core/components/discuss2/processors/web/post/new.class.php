<?php

class newPostProcessor extends modObjectCreateProcessor {
    public $classKey = 'disPost';
    public $permission = 'discuss2.can_post';

    public function beforeSave() {
        $this->object->published = 1;
        $this->object->parent = $this->modx->resource->id;
        $this->object->createdby = $this->modx->user->id;
        $this->object->template = $this->modx->discuss2->getParentTemplate($this->modx->resource->id);
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
    }
}

return 'newPostProcessor';