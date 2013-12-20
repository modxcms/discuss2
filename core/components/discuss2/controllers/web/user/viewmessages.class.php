<?php
if (!class_exists('disPageController')) {
    require_once dirname(dirname(__FILE__)).'/dispagecontroller.class.php';
}

class viewmessagesController extends disPageController {
    public function render() {
        $containerChunk = $this->modx->getOption('private_messages_chunk', $this->discuss->forumConfig, 'sample.messagescontainer');
        $rowChunk = $this->modx->getOption('private_messages_chunk', $this->discuss->forumConfig, 'sample.messagesitem');
        if (empty($this->data)) {
            return array('messages' => $this->modx->lexicon('discuss2.no_private_messages'));
        }
        foreach ($this->data as $row) {
            $out[] = $this->discuss->getChunk($rowChunk, $row);
        }
        return array('messages' => $this->discuss->getChunk($containerChunk, implode("\n", $out)));
    }
}