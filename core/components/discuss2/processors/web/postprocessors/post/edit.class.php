<?php

class editPostProcessor extends modObjectUpdateProcessor {
    public $classKey = 'disPost';
    public $permission = 'discuss2.modify_post';
    public $primaryKeyField = 'id';

    public function afterSave() {
        $c = $this->modx->newQuery('disPost');
        $c->where(array('parent' => $this->object->parent));
        $c->sortby('id', 'ASC');
        $c->limit(1);

        $post = $this->modx->getObject('disPost', $c);
        if ($post->id == $this->object->id) {
            $thread = $this->modx->getObject('disThread', $this->object->parent);
            $thread->fromArray(array(
                'content' => $this->object->content,
                'pagetitle' => $this->object->pagetitle,
            ));
            $thread->save();
        }
        if (!empty($_FILES['attachments'])) {
            $this->object->saveAttachments($_FILES);
        }
        $notify = $this->getProperty('notify');
        if ($notify == 1) {
            $cnt = $this->modx->getCount('disSubscription', array('thread' => $this->object->parent, 'user' => $this->modx->user->id));
            if ($cnt == 0) {
                $obj = $this->modx->newObject('disSubscription');
                $obj->fromArray(array(
                    'thread' => $this->object->parent,
                    'user' => $this->modx->user->id
                ));
                $obj->save();
            }
        } else {

        }
        return true;
    }
}

return 'editPostProcessor';