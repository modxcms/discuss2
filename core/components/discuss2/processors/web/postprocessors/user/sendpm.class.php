<?php

class sendPmProcessor extends modObjectCreateProcessor {
    public $classKey = 'disUserThreadPost';
    public $permission = 'discuss2.can_post';
    public $newThread = true;
    public $participants = array();

    public function beforeSave() {
        if (!empty($this->properties['thread'])) {
            $this->newThread = false;
        }
        if (empty($this->properties['participants'])) {
            $this->modx->log(xPDO::LOG_LEVEL_ERROR, "No participants set for PM");
            return false;
        }
        $participants = explode(",",$this->properties['participants']);
        foreach ($participants as $part) {
            $temp = $this->modx->getObject('modUser', array('username' => trim($part)));
            if ($temp instanceof modUser) {
                $this->participants[$temp->id] = $temp;
            }
        }
        if (empty($this->participants)) {
            $this->modx->log(xPDO::LOG_LEVEL_ERROR, "None of the usernames could be located");
            return false;
        }
        $this->participants[$this->modx->user->id] = $this->modx->user;
        $this->object->author = $this->modx->user->id;

    }

    public function afterSave() {
        //disUserThreadParticipant
        if ($this->newThread === true) {
            $thread = $this->modx->newObject('disUserThread');
            $thread->fromArray(array(
                'title' => $this->object->title,
                'messages' => 1
            ));
            $thread->save();
            foreach ($this->participants as $user) {
                $part = $this->modx->newObject('disUserThreadParticipant');
                $part->fromArray(array(
                    'thread' => $thread->id,
                    'userid' => $user->id,
                    'lastread' => ($user->id == $this->modx->user->id) ? date("Y-m-d H:i:s") : 0
                ));
            }
        } else {
            $q = "UPDATE {$this->modx->getTableName('disUserThread')} SET {$this->modx->escape('messages')} = ({$this->modx->escape('messages')} + 1),
                {$this->modx->escape('lastpost')} = {date('Y-m-d H:i:s')}
                WHERE {$this->modx->escape('thread')} = {$this->object->thread}";
            $criteria = new xPDOCriteria($this->xpdo, $q);
            $criteria->prepare();
            if (!$result= $criteria->stmt->execute()) {
                $this->xpdo->log(xPDO::LOG_LEVEL_ERROR, "Error " . $criteria->stmt->errorCode() . " executing statement:\n{$q}\n");
                return false;
            }
            $participants = $this->modx->getCollection('disUserThreadParticipant', array('thread' => $this->object->thread));
            // Add new participants
            foreach ($this->participants as $user) {
                if (!in_array($user->id, array_keys($participants))) {
                    $part = $this->modx->newObject('disUserThreadParticipant');
                    $part->fromArray(array(
                        'thread' => $this->object->thread,
                        'userid' => $user->id,
                        'lastread' => 0
                    ));
                    $part->save();
                }
            }
        }
        return true;
    }
}