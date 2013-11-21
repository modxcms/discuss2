<?php
/**
 * @package Discuss
 * @subpackage mysql
 */

require_once('disthread.class.php');
class disThreadDiscussion extends disThread {
    function __construct(xPDO & $xpdo) {
        parent :: __construct($xpdo);
        $this->set('class_key','disThreadDiscussion');
        $this->set('cacheable', false);
        $this->set('isfolder', true);
        $this->set('show_in_tree', false);
    }

}