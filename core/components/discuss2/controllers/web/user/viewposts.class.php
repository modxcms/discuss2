<?php
if (!class_exists('disPageController')) {
    require_once dirname(dirname(__FILE__)).'/dispagecontroller.class.php';
}

class viewpostsController extends disPageController {
    public function render() {
        $rowContainer = "sample.threadcontainer";
        $rowChunk = "sample.threadrow";
        $parser = $this->discuss->loadParser();
        foreach ($this->data as $row) {
            $row['content'] = $parser->parse($row['content']);
            $row['pagetitle'] = $parser->parse($row['pagetitle']);
            $out[] = $this->discuss->getChunk($rowChunk, $row);
        }
        return array('rows' => $this->discuss->getChunk($rowContainer, array('threads' => implode("\n", $out))));
    }
}