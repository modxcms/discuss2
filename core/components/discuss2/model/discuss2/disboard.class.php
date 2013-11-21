<?php
/**
 * @package Discuss
 * @subpackage mysql
 */
class disBoard extends modResource {
    public $showInContextMenu = false;

    private $validActions = array(
        'new/thread',
        'remove/thread',
        'modify/thread',
        'lock/thread',
        'pin/thread'
    );

    function __construct(xPDO & $xpdo) {
        parent :: __construct($xpdo);
        $this->set('class_key','disBoard');
        $this->set('show_in_tree', true);
        $this->set('cacheable', false);
    }

    public function getGlobalActions() {
        $links = array();
        $x = &$this->xpdo;

        if ($this->xpdo->hasPermission('discuss2.can_post')) {
            $links['actions.new_thread'] = $x->makeUrl($this->id, '', array('action' => 'new/thread'));
        }
        $x->toPlaceholders($links, 'discuss2');
    }

    public function getThreadActions($thread) {
        $links = array();

        $x = &$this->xpdo;
        if ($x->hasPermission('discuss2.remove_thread')) {
            $links['actions.remove_thread'] = $x->makeUrl($this->id, '', array('action' => 'remove/thread', 'tid' => $thread));
        }
        if ($x->hasPermission('discuss2.lock_thread')) {
            $links['actions.lock_thread'] = $x->makeUrl($this->id, '', array('action' => 'lock/thread', 'tid' => $thread));
        }
        if ($x->hasPermission('discuss2.modify_thread')) {
            $links['actions.modify_thread'] = $x->makeUrl($this->id, '', array('action' => 'modify/thread', 'tid' => $thread));
        }
        if ($x->hasPermission('discuss2.stick_thread')) {
            $links['actions.stick_thread'] = $x->makeUrl($this->id, '', array('action' => 'pin/thread', 'tid' => $thread));
        }
        return $links;
    }

    public static function getControllerPath(xPDO &$modx) {
        return $modx->getOption('discuss2.core_path',null,$modx->getOption('core_path').'components/discuss2/').'controllers/board/';
    }

    public function getResourceTypeName() {
        $this->xpdo->lexicon->load('discuss2:default');
        return $this->xpdo->lexicon('discuss2.disBoard');
    }

    public function prepareTreeNode(array $node = array()) {
        $this->xpdo->lexicon->load('discuss2:default');
        $menu[] = array(
            'text' => '<b>'.$this->get('pagetitle').'</b>',
            'handler' => 'Ext.emptyFn',
        );
        $menu[] = '-';
        $menu[] = array(
            'text' => $this->xpdo->lexicon('discuss2.edit_disBoard'),
            'handler' => 'this.editResource',
        );
        $menu[] = array(
            'text' => $this->xpdo->lexicon('discuss2.create_disBoard'),
            'handler' => "function(itm,e) {
				var at = this.cm.activeNode.attributes;
		        var p = itm.usePk ? itm.usePk : at.pk;

	            Ext.getCmp('modx-resource-tree').loadAction(
	                'a='+MODx.action['resource/create']
	                + '&class_key='+'disBoard'
	                + '&parent='+p
	                + (at.ctx ? '&context_key='+at.ctx : '')
                );
        	}",
        );
        $menu[] = '-';
        if ($this->get('published')) {
            $menu[] = array(
                'text' => $this->xpdo->lexicon('discuss2.board_unpublish'),
                'handler' => 'this.unpublishDocument',
            );
        } else {
            $menu[] = array(
                'text' => $this->xpdo->lexicon('discuss2.board_publish'),
                'handler' => 'this.publishDocument',
            );
        }
        if ($this->get('deleted')) {
            $menu[] = array(
                'text' => $this->xpdo->lexicon('discuss2.board_undelete'),
                'handler' => 'this.undeleteDocument',
            );
        } else {
            $menu[] = array(
                'text' => $this->xpdo->lexicon('discuss2.board_delete'),
                'handler' => 'this.deleteDocument',
            );
        }

        $node['menu'] = array('items' => $menu);
        $node['hasChildren'] = true;
        return $node;
    }

    public function process() {
        if (isset($_GET['action']) && in_array($_GET['action'], $this->validActions)) {
            $content = false;
            switch ($_GET['action']) {
                case 'new/thread' :
                    $content = $this->xpdo->discuss2->getChunk('thread.newThread');
                    break;
                case 'remove/thread' :
                    $content = '';
                    break;
                case 'edit/thread' :
                    $content = '';
                    break;
            }
            if ($content != false) {
                $this->xpdo->setPlaceholder('discuss2.content', $content);
            }
        } else {
            $this->getGlobalActions();
            $this->_getContent();
        }
        return parent::process();
    }

    private function _getContent() {
        $fieldsToLoad = array('id', 'pagetitle', 'longtitle', 'description', 'parent',
            'alias', 'pub_date', 'parent', 'introtext', 'content', 'createdby', 'createdon', 'class_key');

        $c = $this->xpdo->newQuery('disThread');
        $c->select(array(
            $this->xpdo->getSelectColumns('disThread', 'disThread', 'disThread_', $fieldsToLoad),
            'disThread_author' => $this->xpdo->getSelectColumns('disUser', 'disUser', '', array('id')),
            'disThread_username' => $this->xpdo->getSelectColumns('disUser', 'disUser', '', array('username')),
            $this->xpdo->getSelectColumns('disPost', 'disPost', 'disPost_', $fieldsToLoad),
            'disPost_author' => $this->xpdo->getSelectColumns('disUser', 'disUser2', '', array('id')),
            'disPost_username' => $this->xpdo->getSelectColumns('disUser', 'disUser2', '', array('username'))
        ));
        $c->innerJoin('disPost', 'disPost', "{$this->xpdo->escape('disPost')}.{$this->xpdo->escape('parent')} = {$this->xpdo->escape('disThread')}.{$this->xpdo->escape('id')}");
        $c->innerJoin('disUser', 'disUser', "{$this->xpdo->escape('disUser')}.{$this->xpdo->escape('id')} = {$this->xpdo->escape('disThread')}.{$this->xpdo->escape('createdby')}");
        $c->innerJoin('disUser', 'disUser2', "{$this->xpdo->escape('disUser2')}.{$this->xpdo->escape('id')} = {$this->xpdo->escape('disPost')}.{$this->xpdo->escape('createdby')}");
        $c->where(array(
            'parent' => $this->id,
            'class_key:IN' => array('disThread', 'disThreadQuestion', 'disThreadDiscussion'),
            "disPost.id = (SELECT MAX({$this->xpdo->escape('lastPost')}.{$this->xpdo->escape('id')}) FROM {$this->xpdo->getTableName('disPost')} AS {$this->xpdo->escape('lastPost')}
                WHERE {$this->xpdo->escape('lastPost')}.{$this->xpdo->escape('parent')} = {$this->xpdo->escape('disThread')}.{$this->xpdo->escape('id')})",
            'deleted' => 0,
            'published' => 1
        ));
        $count = $this->xpdo->getCount('disThread', $c);
        $offset = isset($this->xpdo->request->parameters['GET']['page']) ? ($this->xpdo->request->parameters['GET']['page'] -1) * $this->xpdo->discuss2->forumConfig['threads_per_page']: 0;
        $c->limit($this->xpdo->discuss2->forumConfig['threads_per_page'], $offset);
        $c->sortby('disThread.id', 'DESC');
        $c->prepare();
        $c->stmt->execute();
        $rows = $c->stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($rows) == 0) {
            return;
        }
        $hydrated = array();

        foreach ($rows as $row) {
            $this->xpdo->discuss2->hydrateRow($row, $hydrated);
        }

        if ($count > $this->xpdo->discuss2->forumConfig['threads_per_page']) {
            $pages = $this->xpdo->discuss2->loadPagination();
            $pagination = $pages->processMainPagination($count, 'threads_per_page');
            $this->xpdo->setPlaceholder('discuss2.pagination', $pagination);
        }

        $threads = $this->xpdo->discuss2->createTree($hydrated);
        $threads = $this->_treeToView($threads);
        $this->xpdo->setPlaceholder('discuss2.content', $threads);
    }

    private function _getSubBoards() {
        $c = $this->xpdo->newQuery('disBoard');
        $c->where(array(
            'parent' => $this->id,
            'class_key' => 'disBoard'
        ));

        $collection = $this->xpdo->getCollection('disBoard', $c);
        $boards = array();
        foreach ($collection as $board) {
            $boards[] = $this->xpdo->discuss2->getChunk('board.subBoardrow', $board->toArray());
        }
        if (!empty($boards)) {
            $this->xpdo->setPlaceholder('discuss2.subboards', $this->xpdo->discuss2->getChunk('board.subBoardContainer', array(
                'boards' => implode("\n", $boards)
            )));
        }
    }

    private function _treeToView($tree) {
        $threads = array();
        $threadRow = $this->xpdo->getOption('thread_row_chunk', $this->xpdo->discus2s->forumConfig, 'board.threadRow');
        $threadsContainer = $this->xpdo->getOption('thread_row_container', $this->xpdo->discuss2->forumConfig, 'board.threadContainer');
        $pages = $this->xpdo->discuss2->loadPagination();
        $perPage = $this->xpdo->getOption('threads_per_page', $this->xpdo->discuss2->forumConfig, '20');

        foreach ($tree as $thread) {
            if (isset($thread['disPost'])) {
                $lastPost = reset($thread['disPost']);
                unset($thread['disPost']);
                $thread['lastpost.title'] = $lastPost['pagetitle'];
                $thread['lastpost.content'] = $lastPost['content'];
                $thread['lastpost.id'] = $lastPost['id'];
                $thread['lastpost.author_id'] = $lastPost['author'];
                $thread['lastpost.author_uname'] = $lastPost['username'];
                $thread['lastpost.createdon'] = $lastPost['createdon'];
                $thread['link'] = $this->xpdo->makeUrl($thread['id']);
                $thread['lastpost.link'] = $this->xpdo->makeUrl($lastPost['id']);
                $thread = array_merge($thread, $this->xpdo->discuss2->stats->getRepliesAndThreads($thread['id']));
                if ($thread['total_posts'] > $perPage) {
                    $thread['thread_pagination'] = $pages->processThreadPagination($thread['id'], $thread['total_posts'], 'posts_per_page');
                }
            }
            $thread['actions'] = $this->getThreadActions($thread['id']);
            $threads[] = $this->xpdo->discuss2->getChunk($threadRow,$thread);
        }
        return $this->xpdo->discuss2->getChunk($threadsContainer, array(
            'threads' => implode("\n", $threads)
        ));
    }

    public function save($cacheFlag = null) {
        $isNew = $this->isNew();
        $this->cacheable = false;
        $saved = parent::save($cacheFlag);
        if ($isNew && $saved) {
            $closure = $this->xpdo->newObject('disClosure');
            $closSaved = $closure->createClosure(intval($this->id), intval($this->parent));
            $resGroup = $this->_saveModGroup();
            $this->joinGroup($resGroup->id);
        } else if ($saved) {
            if ($this->parentChanged !== null) {

            }
        }
        return $saved;
    }

    private function _saveModGroup() {
        $resGroup = $this->xpdo->newObject('modResourceGroup');
        $resGroup->set('name',"(Moderators) {$this->pagetitle}");
        $resGroup->save();

        $userGroup = $this->xpdo->newObject('modUserGroup');
        $userGroup->set('name',$resGroup->name);
        if (!$userGroup->save()) {
            return false;
        }
        $policy = $this->xpdo->getObject('modAccessPolicy',array('name' => 'Discuss2 Moderators'));

        $acl = $this->xpdo->newObject('modAccessResourceGroup');
        $acl->fromArray(array(
            'context_key' => $this->context_key,
            'target' => $resGroup->get('id'),
            'principal_class' => 'modUserGroup',
            'principal' => $userGroup->get('id'),
            'authority' => 9500,
            'policy' => $policy->get('id'),
        ));
        $acl->save();
        return $resGroup;
    }
}