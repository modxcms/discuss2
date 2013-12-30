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
        $c->setClassAlias('Board');
        $c->distinct();
        $c->select(array(
            $this->xpdo->getSelectColumns('disBoard', 'Board', 'Board_', $fieldsToLoad),
            $this->xpdo->getSelectColumns('disBoard', 'subBoard', 'subBoard_', $fieldsToLoad),
            'lastpost_pagetitle' => 'Post.pagetitle',
            'lastpost_content' => 'Post.content',
            'lastpost_id' => 'Post.id',
            'lastpost_author' => 'Post.createdby',
            'lastpost_username' => 'User.username',
            'lastpost_display_name' => 'Profile.display_name',
            'lastpost_use_display_name' => 'Profile.use_display_name',
            'lastpost_thread_id' => 'Post.parent',
            'lastpost_parent' => 'c.ancestor', // Setting this to Board to show as last post on it
            'lastpost_class_key' => 'Post.class_key',
            'lastpost_createdon' => 'Post.createdon',
            'subPost_pagetitle' => 'subPost.pagetitle',
            'subPost_content' => 'subPost.content',
            'subPost_id' => 'subPost.id',
            'subPost_author' => 'subPost.createdby',
            'subPost_username' => 'User2.username',
            'subPost_display_name' => 'Profile2.display_name',
            'subPost_use_display_name' => 'Profile2.use_display_name',
            'subPost_thread_id' => 'subPost.parent',
            'subPost_parent' => 'c2.ancestor', // Setting this to Board to show as last subPost on it
            'subPost_class_key' => 'subPost.class_key',
            'subPost_createdon' => 'subPost.createdon'
        ));
        $c->innerJoin('disClosure', 'c', "{$this->xpdo->escape('c')}.{$this->xpdo->escape('ancestor')} = {$this->xpdo->escape('Board')}.{$this->xpdo->escape('id')} ");
        $c->leftJoin('disBoard', 'subBoard', "{$this->xpdo->escape('subBoard')}.{$this->xpdo->escape('parent')} = {$this->xpdo->escape('Board')}.{$this->xpdo->escape('id')}
            AND {$this->xpdo->escape('subBoard')}.{$this->xpdo->escape('class_key')} = 'disBoard'");

        $cSub = $this->xpdo->newQuery('disPost');
        $cSub->setClassAlias('postPrimary');
        $cSub->select(array("MAX({$this->xpdo->escape('postPrimary')}.{$this->xpdo->escape('id')})"));
        $cSub->leftJoin('disClosure', 'c3', "{$this->xpdo->escape('postPrimary')}.{$this->xpdo->escape('id')} = {$this->xpdo->escape('c3')}.{$this->xpdo->escape('descendant')}");
        $cSub->where(array(
            "{$this->xpdo->escape('c3')}.{$this->xpdo->escape('ancestor')} = {$this->xpdo->escape('Board')}.{$this->xpdo->escape('id')}",
            "{$this->xpdo->escape('postPrimary')}.{$this->xpdo->escape('class_key')} = 'disPost'"));
        $cSub->prepare();

        $c->leftJoin('disPost', 'Post', "{$this->xpdo->escape('Post')}.{$this->xpdo->escape('parent')} = {$this->xpdo->escape('c')}.{$this->xpdo->escape('descendant')}
            AND {$this->xpdo->escape('Post')}.{$this->xpdo->escape('class_key')} = 'disPost'
            AND{$this->xpdo->escape('Post')}.{$this->xpdo->escape('id')} = ({$cSub->toSQL()})");

        $cSub2 = $this->xpdo->newQuery('disPost');
        $cSub2->setClassAlias('postSecondary');
        $cSub2->select(array("MAX({$this->xpdo->escape('postSecondary')}.{$this->xpdo->escape('id')})"));
        $cSub2->leftJoin('disClosure', 'c4', "{$this->xpdo->escape('postSecondary')}.{$this->xpdo->escape('id')} = {$this->xpdo->escape('c4')}.{$this->xpdo->escape('descendant')}");
        $cSub2->where(array(
            "{$this->xpdo->escape('c4')}.{$this->xpdo->escape('ancestor')} = {$this->xpdo->escape('subBoard')}.{$this->xpdo->escape('id')}",
            "{$this->xpdo->escape('postSecondary')}.{$this->xpdo->escape('class_key')} = 'disPost'"
        ));

        $cSub2->prepare();
        $c->leftJoin('disClosure', 'c2', "{$this->xpdo->escape('c2')}.{$this->xpdo->escape('ancestor')} = {$this->xpdo->escape('subBoard')}.{$this->xpdo->escape('id')} ");
        $c->leftJoin('disPost', 'subPost', "{$this->xpdo->escape('subPost')}.{$this->xpdo->escape('parent')} = {$this->xpdo->escape('c2')}.{$this->xpdo->escape('descendant')}
            AND {$this->xpdo->escape('subPost')}.{$this->xpdo->escape('class_key')} = 'disPost'
            AND subPost.id = ({$cSub2->toSQL()})");

        $c->leftJoin('disUser', 'User', "{$this->xpdo->escape('User')}.{$this->xpdo->escape('id')} = {$this->xpdo->escape('Post')}.{$this->xpdo->escape('createdby')}");
        $c->leftJoin('disUserProfile', 'Profile', "{$this->xpdo->escape('Profile')}.{$this->xpdo->escape('internalKey')} = {$this->xpdo->escape('User')}.{$this->xpdo->escape('id')}");

        $c->leftJoin('disUser', 'User2', "{$this->xpdo->escape('User2')}.{$this->xpdo->escape('id')} = {$this->xpdo->escape('subPost')}.{$this->xpdo->escape('createdby')}");
        $c->leftJoin('disUserProfile', 'Profile2', "{$this->xpdo->escape('Profile2')}.{$this->xpdo->escape('internalKey')} = {$this->xpdo->escape('User2')}.{$this->xpdo->escape('id')}");

        $c->where(array(
            'Board.parent' => $this->id,
            'Board.published' => 1,
            'Board.deleted' => 0,
        ));
        $c->sortby("{$this->xpdo->escape('Board')}.{$this->xpdo->escape('menuindex')}", 'ASC');
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

        $boards = $this->xpdo->discuss2->createTree($hydrated);
        $boards = $this->_treeToView($boards);
        $this->xpdo->setPlaceholder('discuss2.content', implode("\n",$boards));
    }

    private function _treeToView($tree) {
        $boardContainer = $this->xpdo->getOption('subBoardRow', $this->xpdo->discuss2->forumConfig, 'sample.subBoardRow');
        $boardRow = $this->xpdo->getOption('boardRow', $this->xpdo->discuss2->forumConfig, 'sample.boardRow');
        $subBoardContainer = $this->xpdo->getOption('categories_subboard_container', $this->xpdo->discuss2->forumConfig, 'sample.subBoardContainer');
        $subBoardRow = $this->xpdo->getOption('subBoardRow', $this->xpdo->discuss2->forumConfig, 'sample.subBoardRow');
        $boards = array();
        $parser = $this->xpdo->discuss2->loadParser();
        foreach ($tree as $board) {
            $subBoards = array();
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
            if (!empty($board['disBoard'])) {
                foreach($board['disBoard'] as $subBoard) {
                    if (isset($subBoard['disPost'])) {
                        $lastPost = reset($subBoard['disPost']);
                        unset($subBoard['disPost']);
                        $subBoard['lastpost.pagetitle'] = $parser->parse($lastPost['pagetitle']);
                        $subBoard['lastpost.content'] = $parser->parse($lastPost['content']);
                        $subBoard['lastpost.id'] = $lastPost['id'];
                        $subBoard['lastpost.author_id'] = $lastPost['author'];
                        $subBoard['lastpost.author_username'] = ($lastPost['use_display_name'] == 1) ? $lastPost['display_name'] : $lastPost['username'];
                        $subBoard['lastpost.link'] = $this->xpdo->discuss2->getLastPostLink($lastPost['thread_id'], $lastPost['id']);
                    }
                    $subBoard['link'] = $this->xpdo->discuss2->makeUrl($subBoard['id']);
                    $subBoards[] = $this->xpdo->discuss2->getChunk($subBoardRow,$subBoard);
                }
            };
            $subBoardsChunk = $this->xpdo->discuss2->getChunk($subBoardContainer, array('boards' => implode("\n", $subBoards)));
            // TODO: Add read/undead threads check
            $boards[] = $this->xpdo->discuss2->getChunk($boardRow,array_merge($board, array('subBoards' => $subBoardsChunk)));
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
            $this->joinGroup($resGroup);
        } else if ($saved) {
            if ($this->parentChanged !== null) {
                // TODO: Add category move to fire closure changes
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