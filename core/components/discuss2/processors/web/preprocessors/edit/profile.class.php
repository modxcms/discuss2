<?php
if (!class_exists('disPreProcessor')) {
    require_once dirname(dirname(__FILE__)).'/dispreprocessor.class.php';
}
class modEditProfileProcessor extends disPreProcessor {
    protected $visibility = 'private';
    public function process() {
        $username = $this->modx->request->parameters['GET']['username'];
        $c = $this->modx->newQuery('disUser');
        $c->where(array('username' => $username));
        $usr = $this->modx->getObject('disUser', $c);
        if (!$usr instanceof modUser) {
            $this->failure('Could not find user with username ' . $username);
            return false;
        }
        return $this->success('OK', $usr->toArray());
    }
}