<?php

if (!class_exists('disPreProcessor')) {
    require_once dirname(dirname(__FILE__)).'/dispreprocessor.class.php';
}

class messagesProcessor extends disPreProcessor {
    public $visibility = 'private';

    public function process() {
        $c = $this->modx->newQuery('disUserThreadParticipant');
        $c->setClassAlias('part');
        $c->select(array(
            $this->modx->getSelectColumn('disUserThreadParticipant', 'part', ''),
            $this->modx->getSelectColumns('disUserThread', 't', '', array('title', 'messages'))
        ));
        $c->innerJoin('disUserThread', 't', "{$this->modx->escape('t')}.{$this->modx->escape('id')} = {$this->modx->escape('part')}.{$this->modx->escape('thread')}");
        $c->where(array(
            'userid' => $this->modx->user->id
        ));

        $c->prepare();
        $c->stmt->execute();

    }
}
return 'messagesProcessor';