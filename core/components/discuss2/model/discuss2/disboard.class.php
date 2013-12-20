<?php
/**
 * @package Discuss
 * @subpackage mysql
 */
class disBoard extends modResource {
    public $showInContextMenu = false;

    public $validActions = array(
        'discuss2.can_post' =>'new/thread',
        'discuss2.remove_thread' => 'remove/thread',
        'discuss2.modify_thread' => 'modify/thread',
        'discuss2.lock_thread' =>'lock/thread',
        'discuss2.stick_thread' =>'pin/thread'
    );

    protected $actionLinks = array();

    function __construct(xPDO & $xpdo) {
        parent :: __construct($xpdo);
        $this->set('class_key','disBoard');
        $this->set('show_in_tree', true);
        $this->set('cacheable', false);
    }

    public function getGlobalActions() {
        $links = array();
        $x = &$this->xpdo;
        $usrLogin = (int)$x->getOption('login_page', $x->discuss2->forumConfig, 1);
        if ($this->xpdo->hasPermission('discuss2.can_post')) {
            $links['actions.new_thread'] = $x->discuss2->makeUrl($this->id, '', 'new/thread');
        } else {
            $links['actions.login'] = $x->makeUrl($usrLogin);
        }
        $x->toPlaceholders($links, 'discuss2');
    }

    public function getThreadActions($thread) {
        $x = &$this->xpdo;
        $actionChunk = $x->getOption('thread_actions_item', $x->discuss->forumConfig, 'sample.actionsItem');
        if ($x->hasPermission('discuss2.remove_thread')) {
            $this->actionLinks['actions.remove_thread'] = $x->discuss2->getChunk($actionChunk, array(
                'link' => $x->discuss2->makeUrl($this->id, '', 'remove/thread', array('tid' => $thread)),
                'text' => $x->lexicon('discuss2.remove_thread')));
        }
        if ($x->hasPermission('discuss2.lock_thread')) {
            $this->actionLinks['actions.lock_thread'] = $x->discuss2->getChunk($actionChunk, array(
                'link' => $x->discuss2->makeUrl($this->id, '','lock/thread', array('tid' => $thread)),
                'text' => $x->lexicon('discuss2.lock_thread')));
        }
        if ($x->hasPermission('discuss2.modify_thread')) {
            $this->actionLinks['actions.modify_thread'] = $x->discuss2->getChunk($actionChunk, array(
                'link' => $x->discuss2->makeUrl($this->id, '', 'modify/thread', array('tid' => $thread)),
                'text' => $x->lexicon('discuss2.edit_thread')));
        }
        if ($x->hasPermission('discuss2.stick_thread')) {
            $this->actionLinks['actions.stick_thread'] = $x->discuss2->getChunk($actionChunk, array(
                'link' => $x->discuss2->makeUrl($this->id, '', 'pin/thread', array('tid' => $thread)),
                'text' => $x->lexicon('discuss2.pin_thread')));
        }
        return $this->actionLinks;
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
        $this->xpdo->lexicon->load('discuss2:front-end');
        if (isset($this->xpdo->request->parameters['GET']['action']) && in_array($this->xpdo->request->parameters['GET']['action'], array_values($this->validActions))) {
            if (!$this->xpdo->hasPermission(array_search($this->xpdo->request->parameters['GET']['action'], $this->validActions))) {
                $this->xpdo->sendUnauthorizedPage();
            }
            $contentChunk = false;
            $placeholders = array();
            $parser = $this->xpdo->discuss2->loadParser();
            switch ($this->xpdo->request->parameters['GET']['action']) {
                case 'new/thread' :
                    if (isset($this->xpdo->request->parameters['POST'][$this->xpdo->getOption('form_preview_var', $this->xpdo->discuss2->forumConfig, 'd2-preview')]) &&
                        $this->xpdo->request->parameters['POST'][$this->xpdo->getOption('form_preview_var', $this->xpdo->discuss2->forumConfig, 'd2-preview')] == $this->xpdo->lexicon('discuss2.preview')) {
                        $this->xpdo->toPlaceholders(array(
                            'content' => $this->xpdo->request->parameters['POST']['content'],
                            'pagetitle' => $this->xpdo->request->parameters['POST']['pagetitle'],
                            'thread' => $this->xpdo->request->parameters['POST']['thread'],
                            'preview.pagetitle' => $parser->parse($this->xpdo->request->parameters['POST']['pagetitle']),
                            'preview.content' => $parser->parse($this->xpdo->request->parameters['POST']['content'])
                        ), 'form');
                    }
                    $contentChunk = $this->xpdo->getOption('new_thread_form', $this->xpdo->discuss2->forumConfig, 'sample.newThread');
                    break;
                case 'remove/thread' :
                    if (!isset($this->xpdo->request->parameters['GET']['tid'])) {
                        break;
                    }
                    $obj = $this->xpdo->getObject('disThread', $this->xpdo->request->parameters['GET']['tid']);
                    $obj->content = $parser->parse($obj->content);
                    $contentChunk = $this->xpdo->getOption('remove_thread_form', $this->xpdo->discuss2->forumConfig, 'sample.removeThread');
                    $placeholders = array(
                        'params'  => $this->xpdo->request->parameters,
                        'thread' => $obj->toArray(),
                        'action' => $this->xpdo->makeUrl($this->xpdo->resource->id, '', 'remove/thread', array('tid' => $this->xpdo->request->parameters['GET']['tid']))
                    );
                    break;
                case 'modify/thread' :
                    if (!isset($this->xpdo->request->parameters['GET']['tid'])) {
                        break;
                    }
                    $obj = $this->xpdo->getObject('disThread', $this->xpdo->request->parameters['GET']['tid']);
                    $contentChunk = $this->xpdo->getOption('edit_thread_form', $this->xpdo->discuss2->forumConfig, 'sample.editThread');
                    $placeholders = array(
                        'params'  => $this->xpdo->request->parameters,
                        'thread' => $obj->toArray(),
                        'action' => $this->xpdo->makeUrl($this->xpdo->resource->id, '', 'modify/thread', array('tid' => $this->xpdo->request->parameters['GET']['tid']))
                    );
                    break;
            }
            if ($contentChunk != false) {
                $content = $this->xpdo->discuss2->getChunk($contentChunk, $placeholders);
                $this->xpdo->setPlaceholder('discuss2.content', $content);
            }
        } else {
            $this->getGlobalActions();
            $this->_getSubBoards();
            $this->_getContent();
        }
        return parent::process();
    }

    private function _getContentQuery($criteria = array()) {
        $fieldsToLoad = array('id', 'pagetitle', 'longtitle', 'description', 'parent',
            'alias', 'pub_date', 'parent', 'introtext', 'content', 'createdby', 'createdon', 'class_key');

        $c = $this->xpdo->newQuery('disThread');
        $c->setClassAlias('Thread');
        $c->select(array(
            $this->xpdo->getSelectColumns('disThread', 'Thread', 'Thread_', $fieldsToLoad),
            'Thread_author' => $this->xpdo->getSelectColumns('disUser', 'User', '', array('id')),
            'Thread_username' => $this->xpdo->getSelectColumns('disUser', 'User', '', array('username')),
            'Thread_display_name' => $this->xpdo->getSelectColumns('disUserProfile', 'Profile', '', array('display_name')),
            'Thread_use_display_name' => $this->xpdo->getSelectColumns('disUserProfile', 'Profile', '', array('use_display_name')),
            'Thread_view' => $this->xpdo->getSelectColumns('disThreadProperty', 'Properties', '', array('views')),
            'Thread_post' => $this->xpdo->getSelectColumns('disThreadProperty', 'Properties', '', array('posts')),
            'Thread_locked' => $this->xpdo->getSelectColumns('disThreadProperty', 'Properties', '', array('locked')),
            'Thread_answered' => $this->xpdo->getSelectColumns('disThreadProperty', 'Properties', '', array('answered')),
            $this->xpdo->getSelectColumns('disPost', 'Post', 'Post_', $fieldsToLoad),
            'Post_author' => $this->xpdo->getSelectColumns('disUser', 'User2', '', array('id')),
            'Post_username' => $this->xpdo->getSelectColumns('disUser', 'User2', '', array('username')),
            'Post_display_name' => $this->xpdo->getSelectColumns('disUserProfile', 'Profile2', '', array('display_name')),
            'Post_use_display_name' => $this->xpdo->getSelectColumns('disUserProfile', 'Profile2', '', array('use_display_name')),
        ));
        $c->innerJoin('disThreadProperty', 'Properties', "{$this->xpdo->escape('Properties')}.{$this->xpdo->escape('idx')} = {$this->xpdo->escape('Thread')}.{$this->xpdo->escape('id')}");
        $c->innerJoin('disClosure', 'c', "{$this->xpdo->escape('c')}.{$this->xpdo->escape('ancestor')} = {$this->xpdo->escape('Thread')}.{$this->xpdo->escape('id')}");
        $cSub = $this->xpdo->newQuery('disPost');
        $cSub->setClassAlias('subPost');
        $cSub->select(array("MAX({$this->xpdo->escape('subPost')}.{$this->xpdo->escape('id')})"));
        $cSub->leftJoin('disClosure', 'c2', "{$this->xpdo->escape('subPost')}.{$this->xpdo->escape('id')} = {$this->xpdo->escape('c2')}.{$this->xpdo->escape('descendant')}");
        $cSub->where(array(
            "{$this->xpdo->escape('c2')}.{$this->xpdo->escape('ancestor')} = {$this->xpdo->escape('Thread')}.{$this->xpdo->escape('id')}",
            "{$this->xpdo->escape('subPost')}.{$this->xpdo->escape('class_key')} = 'disPost'"
        ));
        $cSub->prepare();
        $c->leftJoin('disPost', 'Post', "{$this->xpdo->escape('Post')}.{$this->xpdo->escape('parent')} = {$this->xpdo->escape('c')}.{$this->xpdo->escape('descendant')}
            AND {$this->xpdo->escape('Post')}.{$this->xpdo->escape('class_key')} = 'disPost'
            AND {$this->xpdo->escape('Post')}.{$this->xpdo->escape('published')} = 1
            AND {$this->xpdo->escape('Post')}.{$this->xpdo->escape('deleted')} = 0
            AND {$this->xpdo->escape('Post')}.{$this->xpdo->escape('id')} = ({$cSub->toSQL()})");

        $c->innerJoin('disUser', 'User', "{$this->xpdo->escape('User')}.{$this->xpdo->escape('id')} = {$this->xpdo->escape('Thread')}.{$this->xpdo->escape('createdby')}");
        $c->leftJoin('disUserProfile', 'Profile', "{$this->xpdo->escape('Profile')}.{$this->xpdo->escape('internalKey')} = {$this->xpdo->escape('User')}.{$this->xpdo->escape('id')}");

        $c->innerJoin('disUser', 'User2', "{$this->xpdo->escape('User2')}.{$this->xpdo->escape('id')} = {$this->xpdo->escape('Post')}.{$this->xpdo->escape('createdby')}");
        $c->leftJoin('disUserProfile', 'Profile2', "{$this->xpdo->escape('Profile2')}.{$this->xpdo->escape('internalKey')} = {$this->xpdo->escape('User2')}.{$this->xpdo->escape('id')}");
        $c->where($criteria);
        return $c;
    }

    public function getSticky() {
        $c = $this->_getContentQuery(array(
            'Thread.parent' => $this->id,
            'Thread.deleted' => 0,
            'Thread.published' => 1,
            'Properties.sticky' => 1,
            'Thread.class_key:IN' => array('disThread', 'disThreadQuestion', 'disThreadDiscussion'),
        ));
        $c->sortby('Post.createdon', 'DESC');
        $c->prepare();
        $c->stmt->execute();
        $rows = $c->stmt->fetchAll(PDO::FETCH_ASSOC);
        return (!empty($rows)) ? $rows : array();
    }

    private function _getContent() {
        $c = $this->_getContentQuery(array(
            'Thread.parent' => $this->id,
            'Thread.deleted' => 0,
            'Thread.published' => 1,
            'Properties.sticky' => 0,
            'Thread.class_key:IN' => array('disThread', 'disThreadQuestion', 'disThreadDiscussion'),
        ));

        $cCount = $this->xpdo->newQuery('disThread');
        $cCount->innerJoin('disThreadProperty', 'Properties', "{$this->xpdo->escape('Properties')}.{$this->xpdo->escape('idx')} = {$this->xpdo->escape('disThread')}.{$this->xpdo->escape('id')}
            AND {$this->xpdo->escape('Properties')}.{$this->xpdo->escape('sticky')} = 0");

        $cCount->where(array(
            'parent' => $this->id,
            'published' => 1,
            'deleted' => 0,
            'class_key' => array('disThread', 'disThreadQuestion', 'disThreadDiscussion'),
        ));

        $offset = isset($this->xpdo->request->parameters['GET']['page']) ? ($this->xpdo->request->parameters['GET']['page'] -1) * $this->xpdo->discuss2->forumConfig['threads_per_page']: 0;
        $c->limit($this->xpdo->discuss2->forumConfig['threads_per_page'], $offset);
        $c->sortby('Post.createdon', 'DESC');
        $c->prepare();
        $c->stmt->execute();

        $rows = $c->stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($rows) == 0) {
            return;
        }
        $hydrated = array();
        $rows = array_merge($this->getSticky(), $rows);

        foreach ($rows as $row) {
            $this->xpdo->discuss2->hydrateRow($row, $hydrated);
        }

        $count = $this->xpdo->getCount('disThread', $cCount);
        if ($count > $this->xpdo->discuss2->forumConfig['threads_per_page']) {
            $pages = $this->xpdo->discuss2->loadPagination();
            $pagination = $pages->processMainPagination($count, 'threads_per_page');
            $this->xpdo->setPlaceholder('discuss2.pagination', $pagination);
        }

        $hydrated = $this->xpdo->discuss2->createTree($hydrated);
        $threads = $this->_treeToView($hydrated);
        $this->xpdo->setPlaceholder('discuss2.content', $threads);
    }

    private function _getSubBoards() {
        $subBoardContainer = $this->xpdo->getOption('subboard_container', $this->xpdo->discuss2->forumConfig, 'sample.subboardcontainer');

        // Keeping query bit more lighweight and stripping of unnecessary fields
        $fieldsToLoad = array('id', 'pagetitle', 'longtitle', 'description', 'parent',
            'alias', 'pub_date', 'parent', 'introtext', 'content', 'createdby', 'createdon', 'class_key');
        $c = $this->xpdo->newQuery('disBoard');
        $c->setClassAlias('Board');
        $c->distinct();
        $c->select(array(
            $this->xpdo->getSelectColumns('disBoard', 'Board', 'Board_', $fieldsToLoad),
            'lastpost_pagetitle' => 'Post.pagetitle',
            'lastpost_content' => 'Post.content',
            'lastpost_id' => 'Post.id',
            'lastpost_author' => 'Post.createdby',
            'lastpost_username' => 'User.username',
            'lastpost_display_name' => 'Profile.display_name',
            'lastpost_use_display_name' => 'Profile.use_display_name',
            'lastpost_thread_id' => 'Post.parent',
            'lastpost_parent' => 'Board.id', // Setting this to Board to show as last post on it
            'lastpost_class_key' => 'Post.class_key',
            'lastpost_createdon' => 'Post.createdon',
        ));
        $c->innerJoin('disClosure', 'c', "{$this->xpdo->escape('c')}.{$this->xpdo->escape('ancestor')} = {$this->xpdo->escape('Board')}.{$this->xpdo->escape('id')} ");
        $cSub = $this->xpdo->newQuery('disPost');
        $cSub->setClassAlias('subPost');
        $cSub->select(array("MAX({$this->xpdo->escape('subPost')}.{$this->xpdo->escape('id')})"));
        $cSub->leftJoin('disClosure', 'c2', "{$this->xpdo->escape('subPost')}.{$this->xpdo->escape('id')} = {$this->xpdo->escape('c2')}.{$this->xpdo->escape('descendant')}");
        $cSub->where(array(
            "{$this->xpdo->escape('c2')}.{$this->xpdo->escape('ancestor')} = {$this->xpdo->escape('Board')}.{$this->xpdo->escape('id')}",
            "{$this->xpdo->escape('subPost')}.{$this->xpdo->escape('class_key')} = 'disPost'"
        ));
        $cSub->prepare();
        $c->leftJoin('disPost', 'Post', "{$this->xpdo->escape('Post')}.{$this->xpdo->escape('parent')} = {$this->xpdo->escape('c')}.{$this->xpdo->escape('descendant')}
            AND {$this->xpdo->escape('Post')}.{$this->xpdo->escape('class_key')} = 'disPost'
            AND Post.deleted = 0
            AND Post.published = 1
            AND Post.id = ({$cSub->toSQL()})");

        $c->leftJoin('disUser', 'User', "{$this->xpdo->escape('User')}.{$this->xpdo->escape('id')} = {$this->xpdo->escape('Post')}.{$this->xpdo->escape('createdby')}");
        $c->leftJoin('disUserProfile', 'Profile', "{$this->xpdo->escape('Profile')}.{$this->xpdo->escape('internalKey')} = {$this->xpdo->escape('User')}.{$this->xpdo->escape('id')}");

        $c->where(array(
            'Board.parent' => $this->id,
            'Board.class_key' => 'disBoard',
            'Board.published' => 1,
            'Board.deleted' => 0
        ));
        $c->sortby("{$this->xpdo->escape('Board')}.{$this->xpdo->escape('menuindex')}", 'ASC');
        $c->prepare();
        $this->xpdo->log(xPDO::LOG_LEVEL_ERROR, $c->toSQL());
        $rows = self::_loadRows($this->xpdo, 'disCategory', $c);
        $rows = $rows->fetchAll(PDO::FETCH_ASSOC);
        if (count($rows) == 0) {
            return;
        }
        $hydrated = array();

        foreach ($rows as $row) {
            $hydrated = $this->xpdo->discuss2->hydrateRow($row, $hydrated);
        }
        $disCategory = $this->xpdo->newObject('disBoard');
        foreach ($hydrated as $k => $board) {
            if ($board['class_key'] == 'disBoard') {
                $disCategory->id = $board['id'];
                $disCategory->_policies = array();
                if ($disCategory->checkPolicy('load') !== true) {
                    unset($hydrated[$k]);
                }
            }
        }
        reset($hydrated);
        $subBoards = $this->xpdo->discuss2->createTree($hydrated);
        $subBoards = $this->_subTreeToView($subBoards);

        $this->xpdo->setPlaceholder('discuss2.subboards', $this->xpdo->discuss2->getChunk($subBoardContainer, array('boards' => implode("", $subBoards))));
    }

    private function _treeToView($tree) {
        $threads = array();
        $threadRow = $this->xpdo->getOption('thread_row_chunk', $this->xpdo->discus2s->forumConfig, 'sample.threadRow');
        $threadsContainer = $this->xpdo->getOption('thread_row_container', $this->xpdo->discuss2->forumConfig, 'sample.threadContainer');
        $pages = $this->xpdo->discuss2->loadPagination();
        $perPage = $this->xpdo->getOption('threads_per_page', $this->xpdo->discuss2->forumConfig, '20');
        $parser = $this->xpdo->discuss2->loadParser();

        $actionsContainer = $this->xpdo->getOption('thread_actions_container', $this->xpdo->discuss2->forumConfig, 'sample.actionsContainer');

        foreach ($tree as $thread) {
            if (isset($thread['disPost'])) {
                $lastPost = reset($thread['disPost']);
                unset($thread['disPost']);
                $thread['lastpost.pagetitle'] = $parser->parse($lastPost['pagetitle']);
                $thread['lastpost.content']  = $parser->parse($lastPost['content'] );
                $thread['lastpost.id'] = $lastPost['id'];
                $thread['lastpost.author_id'] = $lastPost['author'];
                $thread['lastpost.author_username'] = ($lastPost['use_display_name'] == 1) ? $lastPost['display_name'] : $lastPost['username'];
                $thread['lastpost.createdon'] = $lastPost['createdon'];
                $thread['link'] = $this->xpdo->discuss2->makeUrl($thread['id']);
                $thread['lastpost.link'] = $this->xpdo->discuss2->getLastPostLink($thread['id'], $thread['lastpost.id']);
                if ($thread['posts'] > $perPage) {
                    $thread['thread_pagination'] = $pages->processThreadPagination($thread['id'], $thread['posts'], 'posts_per_page');
                }
                $thread['actions'] = $this->xpdo->getChunk($actionsContainer, array('actions' => implode("",$this->getThreadActions($thread['id']))));
            }
            $threads[] = $this->xpdo->discuss2->getChunk($threadRow,$thread);
        }
        return $this->xpdo->discuss2->getChunk($threadsContainer, array(
            'threads' => implode("\n", $threads)
        ));
    }

    private function _subTreeToView($tree) {
        $subBoardRow = $this->xpdo->getOption('subboard_row', $this->xpdo->discuss2->forumConfig, 'sample.subboardrow');
        $boards = array();
        $parser = $this->xpdo->discuss2->loadParser();
        foreach ($tree as $board) {
            if (isset($board['disPost'])) {
                $lastPost = reset($board['disPost']);
                unset($board['disPost']);
                $board['lastpost.pagetitle'] = $parser->parse($lastPost['pagetitle']);
                $board['lastpost.content'] = $parser->parse($lastPost['content']);
                $board['lastpost.id'] = $lastPost['id'];
                $board['lastpost.author_id'] = $lastPost['author'];
                $board['lastpost.author_username'] = ($lastPost['use_display_name'] == 1) ? $lastPost['display_name'] : $lastPost['username'];
                $board['lastpost.link'] = $this->xpdo->discuss2->getLastPostLink($lastPost['thread_id'], $lastPost['id']);
            }
            $board['link'] = $this->xpdo->discuss2->makeUrl($board['id']);
            // TODO: Add read/undead threads check
            $boards[] = $this->xpdo->discuss2->getChunk($subBoardRow,$board);
        }
        return $boards;
    }

    public function save($cacheFlag = null) {
        $isNew = $this->isNew();
        $this->cacheable = false;
        $this->set('isfolder', true);
        $saved = parent::save($cacheFlag);
        if ($isNew && $saved) {
            $closure = $this->xpdo->newObject('disClosure');
            $closSaved = $closure->createClosure(intval($this->id), intval($this->parent));
            $resGroup = $this->_saveModGroup();
            $this->joinGroup($resGroup->id);
            $cm = $this->xpdo->getCacheManager();
            $cm->refresh();
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