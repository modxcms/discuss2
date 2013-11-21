<?php
/**
 * @package Discuss
 * @subpackage mysql
 */
require_once('disthread.class.php');
class disThreadQuestion extends disThread {
    function __construct(xPDO & $xpdo) {
        parent :: __construct($xpdo);
        $this->set('class_key','disThreadQuestion');
        $this->set('cacheable', false);
        $this->set('isfolder', true);
        $this->set('show_in_tree', false);

        $this->config = $this->xpdo->discuss2->forumConfig;
    }

}