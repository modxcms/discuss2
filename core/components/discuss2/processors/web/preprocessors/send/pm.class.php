<?php
if (!class_exists('disPreProcessor')) {
    require_once dirname(dirname(__FILE__)).'/dispreprocessor.class.php';
}
class modSendPMProcessor extends disPreProcessor {
    protected $visibility = 'public';
    public function process() {
        $username = $this->modx->request->parameters['GET']['username'];
        if (!empty($username) && $username != $this->discuss->user) {
            $placeholders['username'] = $username;
        } else {
            $placeholders['username'] = '';
        }

        return $this->success('OK', $placeholders);
    }
}