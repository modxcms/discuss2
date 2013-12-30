<?php
if (!class_exists('disPageController')) {
    require_once dirname(dirname(__FILE__)).'/dispagecontroller.class.php';
}

class viewsubscriptionsController extends disPageController {
    public function render() {
        $containerChunk = $this->modx->getOption('subscriptions_row_chunk', $this->discuss->forumConfig, 'sample.subscriptionscontainer');
        $rowChunk = $this->modx->getOption('subscriptions_row_chunk', $this->discuss->forumConfig, 'sample.subscriptionsrowchunk');
        if (empty($this->data)) {
            return array('subscriptions' => $this->modx->lexicon('discuss2.no_subscriptions'));
        }
        foreach ($this->data as $row) {
            $out[] = $this->discuss->getChunk($rowChunk, $row);
        }
        return array('subscriptions' => $this->discuss->getChunk($containerChunk, implode("\n", $out)));
    }
}