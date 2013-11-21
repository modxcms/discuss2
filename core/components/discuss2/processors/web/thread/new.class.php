<?php

class newThreadProcessor extends modObjectCreateProcessor {
    public $classKey = 'disThreadDiscussion';
    public $permission = 'discuss2.can_post';

    public function initialize() {
        $this->classKey = $this->getProperty('thread');
        $this->object = $this->modx->newObject($this->getProperty('thread'));
        return true;
    }

    public function beforeSave() {
        $this->object->published = 1;
        $this->object->parent = $this->modx->resource->id;
        $this->object->createdby = $this->modx->user->id;
        $this->object->template = $this->modx->discuss2->getParentTemplate($this->modx->resource->id);
        return true;
    }

    public function afterSave() {
        $post = $this->modx->newObject('disPost');
        $post->fromArray(array(
            'parent' => $this->object->id,
            'content' => $this->object->content,
            'pagetitle' => $this->object->pagetitle,
            'createdby' => $this->modx->user->id,
            'published' => 1,
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
        }

        $this->object->set('post_id', $post->id);
    }
}

return 'newThreadProcessor';