<?php
/**
 * @package Discuss
 * @subpackage mysql
 */
class disCategory extends modResource {
    public $showInContextMenu = false;

    function __construct(xPDO & $xpdo) {
        parent :: __construct($xpdo);
        $this->set('class_key','disCategory');
        $this->set('cacheable', false);
        $this->set('isfolder', true);
    }

    public static function getControllerPath(xPDO &$modx) {
        return $modx->getOption('discuss2.core_path',null,$modx->getOption('core_path').'components/discuss2/')
        .'controllers/category/';
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
            'text' => $this->xpdo->lexicon('discuss2.edit_disCategory'),
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
                'text' => $this->xpdo->lexicon('discuss2.category_unpublish'),
                'handler' => 'this.unpublishDocument',
            );
        } else {
            $menu[] = array(
                'text' => $this->xpdo->lexicon('discuss2.category_publish'),
                'handler' => 'this.publishDocument',
            );
        }
        if ($this->get('deleted')) {
            $menu[] = array(
                'text' => $this->xpdo->lexicon('discuss2.category_undelete'),
                'handler' => 'this.undeleteDocument',
            );
        } else {
            $menu[] = array(
                'text' => $this->xpdo->lexicon('discuss2.category_delete'),
                'handler' => 'this.deleteDocument',
            );
        }

        $node['menu'] = array('items' => $menu);
        $node['hasChildren'] = true;
        return $node;
    }

    public function process() {
        $this->_getContent();
        return parent::process();
    }

    private function _getContent() {
        // Keeping query bit more lighweight and stripping of unnecessary fields
        $fieldsToLoad = array('id', 'pagetitle', 'longtitle', 'description', 'parent',
            'alias', 'pub_date', 'parent', 'introtext', 'content', 'createdby', 'createdon', 'class_key');
        $c = $this->xpdo->newQuery('disBoard');
        $c->distinct();
        $c->select(array(
            $this->xpdo->getSelectColumns('disBoard', 'disBoard', 'Board_', $fieldsToLoad),
            $this->xpdo->getSelectColumns('disBoard', 'subBoard', 'SubBoard_', $fieldsToLoad),
            'lastpost_post_title' => 'Post.title',
            'lastpost_content' => 'Post.content',
            'lastpost_id' => 'Post.id',
            'lastpost_author' => 'Post.author',
            'lastpost_username' => 'Post.username',
            'lastpost_title' => 'Post.thread_title',
            'lastpost_thread_id' => 'Post.thread_id',
            'lastpost_parent' => 'Post.thread_parent',
            'lastpost_class_key' => 'Post.thread_class_key'
        ));
        $c->leftJoin('disBoard', 'subBoard', "{$this->xpdo->escape('subBoard')}.{$this->xpdo->escape('parent')} = {$this->xpdo->escape('disBoard')}.{$this->xpdo->escape('id')}
            AND {$this->xpdo->escape('subBoard')}.{$this->xpdo->escape('class_key')} = 'disBoard' ");

        $lastPostQ = $this->xpdo->newQuery('disPost');
        $lastPostQ->select(array(
            'title' => 'disPost.pagetitle',
            'content' => 'disPost.content',
            'id' => 'disPost.id',
            'author' => 'disPost.createdby',
            'username' => 'disUser.username',
            'thread_title' => 'disThread.pagetitle',
            'thread_parent' => 'disThread.parent',
            'thread_id' => 'disThread.id',
            'thread_class_key' => 'disThread.class_key'
        ));
        $lastPostQ->innerJoin('disThread', 'disThread', "{$this->xpdo->escape('disThread')}.{$this->xpdo->escape('id')} = {$this->xpdo->escape('disPost')}.{$this->xpdo->escape('parent')}");
        $lastPostQ->innerJoin('disUser', 'disUser', "{$this->xpdo->escape('disUser')}.{$this->xpdo->escape('id')} = {$this->xpdo->escape('disPost')}.{$this->xpdo->escape('createdby')}");
        $lastPostQ->where(array(
            'class_key' => 'disPost',
            "{$this->xpdo->escape('disPost')}.{$this->xpdo->escape('createdon')} = (SELECT MAX({$this->xpdo->escape('subPost')}.{$this->xpdo->escape('createdon')}) FROM {$this->xpdo->getTableName('disPost')} {$this->xpdo->escape('subPost')}
                WHERE {$this->xpdo->escape('subPost')}.{$this->xpdo->escape('parent')} = {$this->xpdo->escape('disThread')}.{$this->xpdo->escape('id')})"
        ));
        $lastPostQ->groupby('disPost.parent');
        $lastPostQ->sortby('disPost.createdon', 'DESC');

        $lastPostQ->prepare();
        $c->query['from']['joins'][] = array(
            'type' => xPDOQuery::SQL_JOIN_LEFT,
            'table' => "({$lastPostQ->toSQL()})",
            'alias' => 'Post',
            'conditions' => new xPDOQueryCondition(array('sql' => "{$this->xpdo->escape('Post')}.{$this->xpdo->escape('thread_parent')} = {$this->xpdo->escape('disBoard')}.{$this->xpdo->escape('id')}"))
        );
        $c->where(array('parent' => $this->id, 'published' => 1, 'deleted' => 0));
        $c->groupby('disBoard.id');
        $c->prepare();

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
        $boards = $this->xpdo->discuss2->createTree($hydrated);;
        $boards = $this->_treeToView($boards);

        $this->xpdo->setPlaceholder('discuss2.content', implode("\n",$boards));
    }

    private function _treeToView($tree) {
        $boardRow = $this->xpdo->getOption('boardRow', $this->config, 'category.boardRow');
        $subBoardRow = $this->xpdo->getOption('subBoardRow', $this->config, 'category.subBoardRow');
        $boards = array();
        foreach ($tree as $board) {
            $subBoards = array();
            if (isset($board['disThread'])) {
                $lastPost = reset($board['disThread']);
                unset($board['disThread']);
                $board['lastpost.title'] = $lastPost['post_title'];
                $board['lastpost.content'] = $lastPost['content'];
                $board['lastpost.id'] = $lastPost['id'];
                $board['lastpost.author_id'] = $lastPost['author'];
                $board['lastpost.author_uname'] = $lastPost['username'];
                $board['lastpost.thread_title'] = $lastPost['title'];
                $board['link'] = $this->xpdo->makeUrl($board['id']);
                $board['lastpost.link'] = $this->xpdo->makeUrl($lastPost['thread_id']);

            }
            if (!empty($board['disBoard'])) {
                foreach($board['disBoard'] as $subBoard) {
                    if (isset($subBoard['disThread'])) {
                        $lastPost = reset($subBoard['disThread']);
                        unset($subBoard['disThread']);
                        $board['lastpost.title'] = $lastPost['post_title'];
                        $board['lastpost.content'] = $lastPost['content'];
                        $board['lastpost.id'] = $lastPost['id'];
                        $board['lastpost.author_id'] = $lastPost['author'];
                        $board['lastpost.author_uname'] = $lastPost['username'];
                        $board['lastpost.thread_title'] = $lastPost['title'];
                        $board['link'] = $this->xpdo->makeUrl($board['id']);
                        $board['lastpost.link'] = $this->xpdo->makeUrl($lastPost['thread_id']);
                    }
                    $subBoards[] = $this->xpdo->discuss2->getChunk($subBoardRow,$subBoard);
                }
            };
            $boards[] = $this->xpdo->discuss2->getChunk($boardRow,array_merge($board, array('boards' => implode("\n", $subBoards))));
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