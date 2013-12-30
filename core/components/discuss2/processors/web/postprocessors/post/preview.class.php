<?php

class previewPostProcessor extends modProcessor {
    public function checkPermissions() {
        return $this->modx->hasPermission('discuss2.can_post');
    }

    public function process() {
        $content = $this->getProperty('content');
        $this->modx->lexicon->load('discuss2:front-end');
        $parser = $this->modx->discuss2->loadParser();

        $this->object = $this->modx->newObject('disPost');
        $this->object->fromArray(array(
            'content' => $parser->parse($content),
            'pagetitle' => $this->getProperty('pagetitle')
        ));

        return $this->object;
    }
}