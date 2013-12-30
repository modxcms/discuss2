<?php
/**
 * @package Discuss
 * @subpackage mysql
 */
class disClosure extends xPDOObject {
    public function moveClosure($obj) {

    }

    /**
     * Generates closure tree from id and parent and stores it to database
     * @param $id
     * @param $parent
     * @return bool
     */

    public function createClosure($id, $parent) {
        $saved = false;
        $closureTable = $this->xpdo->getTableName('disClosure');
        $q = "INSERT INTO {$closureTable} ({$this->xpdo->getSelectColumns('disClosure', '', '')})
            SELECT {$this->xpdo->escape('c')}.{$this->xpdo->escape('ancestor')}, {$id}, ({$this->xpdo->escape('c')}.{$this->xpdo->escape('depth')} + 1)
            FROM {$closureTable} {$this->xpdo->escape('c')}
            WHERE {$this->xpdo->escape('c')}.{$this->xpdo->escape('descendant')} = :parent
            UNION ALL
            SELECT {$id}, {$id}, 0
        ";
        $criteria = new xPDOCriteria($this->xpdo, $q, array('parent' => $parent));
        $criteria->prepare();
        if (!$result= $criteria->stmt->execute()) {
            $this->xpdo->log(xPDO::LOG_LEVEL_ERROR, "Error " . $criteria->stmt->errorCode() . " executing statement:\n{$q}\n");
        } else {
            $saved = true;
        }

        return $saved;
    }
}