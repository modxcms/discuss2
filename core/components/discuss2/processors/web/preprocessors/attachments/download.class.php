<?php

class modDownloadProcessor extends modProcessor {
    public function process() {
        if (empty($this->modx->request->parameters['GET']['pid']) || empty($this->modx->request->parameters['GET']['h'])) {
            return $this->failure('One of parameters was not defined');
        }
        if (!$this->modx->user->hasSessionContext($this->modx->context->key)) {
            return $this->failure('You need to login to Discuss2 to download file');
        }

        $c = $this->modx->newQuery('disPostAttachment');
        $c->select(array($this->modx->getSelectColumns('disPostAttachment', '', array('filename', 'hash', 'extension', 'id'))));
        $c->where(array(
            'post' => $this->modx->request->parameters['GET']['pid'],
            'hash' => $this->modx->request->parameters['GET']['h']
        ));
        $obj = $this->modx->getObject('disPostAttachment', $c);

        $path = $this->discuss->forumConfig['attachment_path'];
        $path = str_replace('{base_path}', rtrim(MODX_BASE_PATH, "/"), $path);

        $path .= $obj->post."-".$obj->hash.".".$obj->extension;

        if (!file_exists($path)) {
            return $this->failure('Could not find file');
        }

        $o = file_get_contents($path);

        header('Content-Type: application/force-download');
        header('Content-Disposition: attachment; filename="'.$obj->filename.'.'.$obj->extension);

        return $o;
    }
}