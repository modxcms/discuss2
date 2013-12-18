<?php
/**
 * @package Discuss
 * @subpackage mysql
 */
class disPost extends modResource {
    public $showInContextMenu = false;
    function __construct(xPDO & $xpdo) {
        parent :: __construct($xpdo);
        $this->set('class_key','disPost');
        $this->set('show_in_tree', false);
    }


    public function save($cacheFlag = null) {
        $isNew = $this->isNew();
        if ($isNew) {
            $this->alias = $this->cleanAlias($this->pagetitle);
            $this->uri = $this->getAliasPath($this->alias);
        }
        $this->set('class_key','disPost');
        $this->cacheable = false;
        $saved = parent::save($cacheFlag);
        if ($isNew && $saved) {
            $statsTable = $this->xpdo->getTableName('disThreadProperty');
            $sql = "UPDATE {$statsTable} SET {$this->xpdo->escape('posts')} = ({$this->xpdo->escape('posts')} + 1) WHERE {$this->xpdo->escape('idx')} = {$this->parent}";
            if (!$this->xpdo->exec($sql)) {
                $this->xpdo->log(xPDO::LOG_LEVEL_ERROR, 'Could not update view count for thread ID ' . $this->id);
            }
            $this->xpdo->cacheManager->refresh();
            $closure = $this->xpdo->newObject('disClosure');
            $closSaved = $closure->createClosure(intval($this->id), intval($this->parent));
        } else if ($saved) {
            if ($this->parentChanged !== null) {

            }
        }
        return $saved;
    }

    public function remove() {
        $sql = "DELETE FROM {$this->modx->getTableName('disClosure')} WHERE descendant = :id";
        $criteria = new xPDOCriteria($this->modx, $sql, array('id' => $this->id));
        $criteria->prepare();
        $criteria->stmt->exec();
        $obj = $this->modx->getObject('modResource', $this->parent);
        if (in_array($obj->class_key, array('disThread', 'disThreadQuestion', 'disThreadDiscussion'))) {
            $this->thread = $obj;
        }
        if ($this->thread !== null) {
            $count = $this->modx->getCount('disThread', array('parent' => $this->thread->id));
            if ($count == 0) {
                $sql = "DELETE FROM {$this->modx->getTableName('disClosure')} WHERE descendant = :id";
                $criteria = new xPDOCriteria($this->modx, $sql, array('id' => $this->thread->id));
                $criteria->prepare();
                $criteria->stmt->exec();
                $this->thread->remove();
            } else {
                $statsTable = $this->xpdo->getTableName('disThreadProperty');
                $sql = "UPDATE {$statsTable} SET {$this->xpdo->escape('posts')} = ({$this->xpdo->escape('posts')} - 1) WHERE {$this->xpdo->escape('idx')} = {$this->thread->id}";
                if (!$this->xpdo->exec($sql)) {
                    $this->xpdo->log(xPDO::LOG_LEVEL_ERROR, 'Could not update view count for thread ID ' . $this->id);
                }
            }
        }
        parent::remove();
    }
    public function saveAttachments($files) {
        if ($this->xpdo->discuss2->forumConfig['allow_attachments'] == false) {
            return ;
        }
        $d = &$this->xpdo->discuss2;
        $path = $d->forumConfig['attachment_path'];
        $path = str_replace('{base_path}', rtrim(MODX_BASE_PATH, "/"), $path);
        $path = rtrim($path, "/");
        $i = 0;
        $fileCount = count($files['attachments']['name']);
        while ($i < $fileCount) {
            if ($files['size'][$i] > $d->forumConfig['attachment_size']) {
                $i++;
                continue;
            }
            $filename = $this->id."-".md5($files['attachments']['name'][$i]).".".pathinfo($files['attachments']['name'][$i], PATHINFO_EXTENSION);

            if (move_uploaded_file($files['attachments']['tmp_name'][$i], $path."/".$filename)) {
                $fileObj = $this->xpdo->newObject('disPostAttachment');
                $fileObj->fromArray(array(
                    'internalKey' => $d->user->id,
                    'post' => $this->id,
                    'filename' => basename($files['attachments']['name'][$i]),
                    'hash' => md5($files['attachments']['name'][$i]),
                    'extension' => pathinfo($files['attachments']['name'][$i], PATHINFO_EXTENSION),
                    'createdon' =>  date('Y-m-d H:i:s'),
                    'downloads' => 0
                ));
                $fileObj->save();
            }
            if ($i >= $d->forumConfig['attachment_num']) {
                break;
            }
            $i++;
        }
    }
}