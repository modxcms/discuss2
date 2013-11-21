<?php

class disStats {
    private $modx;
    private $discuss;

    public function __construct(xPDO &$xpdo) {
        $this->modx = $xpdo;
        $this->discuss = &$xpdo->discuss2;
    }

    public function forumTotals() {
        $siteContent = $this->modx->getTableName('modResource');

        $sql = "SELECT COUNT(*) as xpdo_count, class_key FROM {$siteContent} WHERE class_key IN ('disPost', 'disThread', 'disThreadQuestion', 'disThreadDiscussion') GROUP BY class_key";

        $criteria = new xPDOCriteria($this->modx, $sql);
        $criteria->prepare();
        $criteria->stmt->execute();
        $out = array('posts' => 0, 'thread' => 0);
        foreach ($criteria->stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            if (stripos($row['class_key'], 'disThread') !== false) {
                $out['thread'] += $row['xpdo_count'];
            } else {
                $out['posts'] += $row['xpdo_count'];
            }
        }
        return $out;
    }

    public function users() {
        // Faster for myisam as row count stored into storage engine
        $userTable = $this->modx->getTableName('disUser');
        $sql = "SELECT count(*) as xpdo_count FROM {$userTable}";
        $criteria = new xPDOCriteria($this->modx, $sql);
        $criteria->prepare();
        $criteria->stmt->execute();
        $out['users'] = $criteria->stmt->fetchColumn(0);
        return $out;
    }

    public function process() {
        $values = $this->forumTotals();
        $values = array_merge($values, $this->users());
        $this->modx->setPlaceholders($values, 'discuss2.stats.');
    }

    public function getRepliesAndThreads($id) {
        $siteContent = $this->modx->getTableName('modResource');
        $closure = $this->modx->getTableName('disClosure');
        $sql = "SELECT count(distinct c.id) as xpdo_count, c.class_key FROM {$siteContent} c
            INNER JOIN {$closure} closure ON closure.ancestor = :id AND c.id = closure.descendant
            WHERE c.class_key IN ('disPost', 'disThread', 'disThreadQuestion', 'disThreadDiscussion')
            GROUP BY c.class_key";
        $criteria = new xPDOCriteria($this->modx, $sql, array('id' => $id));
        $criteria->prepare();
        $criteria->stmt->execute();
        $out = array('posts' => 0, 'threads' => 0);
        foreach ($criteria->stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            if (stripos($row['class_key'], 'disThread') !== false) {
                $out['total_threads'] += $row['xpdo_count'];
            } else {
                $out['total_posts'] += $row['xpdo_count'];
            }
        }
        return $out;
    }
}