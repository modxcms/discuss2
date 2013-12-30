<?php
if (!class_exists('disPageController')) {
    require_once dirname(dirname(__FILE__)).'/dispagecontroller.class.php';
}

class viewprofileController extends disPageController {
    public function render() {
        return array('profile' => $this->data);
    }
}