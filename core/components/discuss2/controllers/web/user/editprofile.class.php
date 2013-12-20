<?php
if (!class_exists('disPageController')) {
    require_once dirname(dirname(__FILE__)).'/dispagecontroller.class.php';
}

class editprofileController extends disPageController {
    public function render() {
        return array('profile' => $this->data,
        'action' => $this->discuss->makeUrl($this->modx->resource->id, 'edit/profile'));
    }
}