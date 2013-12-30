<?php
if (!class_exists('disPreProcessor')) {
    require_once dirname(dirname(__FILE__)).'/dispreprocessor.class.php';
}

class modViewSubscriptionsProcessor extends disPreProcessor {
    protected $visibility = 'private';
    public function process() {
        $c = $this->modx->newQuery('disThread');
        $c->setClassAlias('t');
        $c->select(array(
            $this->modx->getSelectColumns('disThread', '', '')
        ));
        $c->innerJoin('disSubscription', 's', "{$this->modx->escape('s')}.{$this->modx->escape('thread')} = {$this->modx->escape('t')} .{$this->modx->escape('id')}");
        $c->where(array(
            's.user' => $this->discuss->user->id,
            't.class_key:IN' => array('disThread', 'disThreadDiscussion', 'disThreadQuestion')
        ));
        $c->sortby("{$this->modx->escape('t')}.{$this->modx->escape('id')}", 'DESC');
        $c->prepare();
        $c->stmt->execute();
        return $this->success(array('OK', $c->stmt->fetchAll(PDO::FETCH_ASSOC)));
    }
}