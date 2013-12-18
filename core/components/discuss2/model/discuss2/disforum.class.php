<?php
/**
 * @package Discuss
 * @subpackage mysql
 */
class disForum extends modResource {
    public $showInContextMenu = true;
    public $tree = array();
    public $config = array();
    private $parentChanged = null;

    function __construct(xPDO & $xpdo) {
        parent :: __construct($xpdo);
        $this->set('class_key','disForum');
        $this->set('cacheable', false);
        $this->set('isfolder', true);
    }

    public static function getControllerPath(xPDO &$modx) {
        return $modx->getOption('discuss2.core_path',null,$modx->getOption('core_path').'components/discuss2/').'controllers/forum/';
    }

    public function getContextMenuText() {
        $this->xpdo->lexicon->load('discuss2:default');
        return array(
            'text_create' => $this->xpdo->lexicon('discuss2.create_disForum'),
            'text_create_here' => $this->xpdo->lexicon('discuss2.create_disForum_here'),
        );
    }

    public function getResourceTypeName() {
        $this->xpdo->lexicon->load('discuss2:default');
        return $this->xpdo->lexicon('discuss2.disForum');
    }

    public function prepareTreeNode(array $node = array()) {
        $this->xpdo->lexicon->load('discuss2:default');
        $menu[] = array(
            'text' => '<b>'.$this->get('pagetitle').'</b>',
            'handler' => 'Ext.emptyFn',
        );
        $menu[] = '-';
        $menu[] = array(
            'text' => $this->xpdo->lexicon('create_document_here'),
            'handler' => "function(itm,e) {
				var at = this.cm.activeNode.attributes;
		        var p = itm.usePk ? itm.usePk : at.pk;

	            Ext.getCmp('modx-resource-tree').loadAction(
	                'a='+MODx.action['resource/create']
	                + '&class_key='+'modResource'
	                + '&parent='+p
	                + (at.ctx ? '&context_key='+at.ctx : '')
                );
        	}",
        );
        $menu[] = '-';
        $menu[] = array(
            'text' => $this->xpdo->lexicon('discuss2.edit_disForum'),
            'handler' => 'this.editResource',
        );
        $menu[] = array(
            'text' => $this->xpdo->lexicon('discuss2.create_disCategory'),
            'handler' => "function(itm,e) {
				var at = this.cm.activeNode.attributes;
		        var p = itm.usePk ? itm.usePk : at.pk;

	            Ext.getCmp('modx-resource-tree').loadAction(
	                'a='+MODx.action['resource/create']
	                + '&class_key='+'disCategory'
	                + '&parent='+p
	                + (at.ctx ? '&context_key='+at.ctx : '')
                );
        	}",
        );

        $menu[] = '-';
        if ($this->get('published')) {
            $menu[] = array(
                'text' => $this->xpdo->lexicon('discuss2.forum_unpublish'),
                'handler' => 'this.unpublishDocument',
            );
        } else {
            $menu[] = array(
                'text' => $this->xpdo->lexicon('discuss2.forum_publish'),
                'handler' => 'this.publishDocument',
            );
        }
        if ($this->get('deleted')) {
            $menu[] = array(
                'text' => $this->xpdo->lexicon('discuss2.forum_undelete'),
                'handler' => 'this.undeleteDocument',
            );
        } else {
            $menu[] = array(
                'text' => $this->xpdo->lexicon('discuss2.forum_delete'),
                'handler' => 'this.deleteDocument',
            );
        }

        $node['menu']['items'] = $menu;

        $node['hasChildren'] = true;

        return $node;
    }

    public function getBoards($criteria = null, $asObjects = false, $cacheFlag = true) {
        $c = $this->xpdo->newQuery('disCategory');
        if ($criteria instanceof xPDOCriteria) {
            $c->wrap($criteria);
        } else if (!empty($criteria)) {
            $c->where($criteria);
        }
        $c->innerJoin('disClosure', 'd', "{$this->xpdo->escape('d')}.{$this->xpdo->escape('ancestor')} = {$this->id} AND
            {$this->xpdo->escape('disBoard')}.{$this->xpdo->escape('parent')} = {$this->xpdo->escape('d')}.{$this->xpdo->escape('descendant')}");
        $c->where(array('class_key:!=' => 'disPost', 'class_key:!=' => 'disCategory'));

        if ($asObjects === true) {
            return $this->xpdo->getCollection('disBoard', $c);
        }
        if ($c->prepare() && $c->stmt->execute()) {
            return $c->stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $this->xpdo->log(xPDO::LOG_LEVEL_ERROR, "Error " . $c->stmt->errorCode() . " executing statement: \n" . print_r($c->stmt->errorInfo(), true));
        }
        return false;
    }

    public function getCategories($criteria = null, $asObjects = false, $cacheFlag = true) {
        $c = $this->xpdo->newQuery('disCategory');
        if ($criteria instanceof xPDOCriteria) {
            $c->wrap($criteria);
        } else if (!empty($criteria)) {
            $c->where($criteria);
        }

        if ($asObjects === true) {
            return $this->xpdo->getCollection('disCategory', $c);
        }
        if ($c->prepare() && $c->stmt->execute()) {
            return $c->stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $this->xpdo->log(xPDO::LOG_LEVEL_ERROR, "Error " . $c->stmt->errorCode() . " executing statement: \n" . print_r($c->stmt->errorInfo(), true));
        }
        return false;
    }

    public function process() {
        $this->_getContent();
        return parent::process();
    }

    private function _getContent() {
        // Keeping query bit more lighweight and stripping of unnecessary fields
        $fieldsToLoad = array('id', 'pagetitle', 'longtitle', 'description', 'parent',
            'alias', 'pub_date', 'parent', 'introtext', 'content', 'createdby', 'createdon', 'class_key');
        $c = $this->xpdo->newQuery('disCategory');
        $c->distinct();
        $c->select(array(
            $this->xpdo->getSelectColumns('disCategory', 'disCategory', 'disCategory_', $fieldsToLoad),
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
            'lastpost_parent' => 'Board.id', // Setting this to Board to show as last post on it
            'lastpost_class_key' => 'Post.class_key',
            'lastpost_createdon' => 'Post.createdon'
        ));
        $c->innerJoin('disClosure', 'c', "{$this->xpdo->escape('c')}.{$this->xpdo->escape('ancestor')} = {$this->xpdo->escape('disCategory')}.{$this->xpdo->escape('id')} ");
        $c->leftJoin('disBoard', 'Board', "{$this->xpdo->escape('Board')}.{$this->xpdo->escape('parent')} = {$this->xpdo->escape('disCategory')}.{$this->xpdo->escape('id')}
            AND {$this->xpdo->escape('Board')}.{$this->xpdo->escape('class_key')} = 'disBoard' AND {$this->xpdo->escape('c')}.{$this->xpdo->escape('depth')}  = 1");
        $c->leftJoin('disBoard', 'subBoard', "{$this->xpdo->escape('subBoard')}.{$this->xpdo->escape('parent')} = {$this->xpdo->escape('Board')}.{$this->xpdo->escape('id')}
            AND {$this->xpdo->escape('subBoard')}.{$this->xpdo->escape('published')} = 1
            AND {$this->xpdo->escape('subBoard')}.{$this->xpdo->escape('deleted')} = 0
            AND {$this->xpdo->escape('subBoard')}.{$this->xpdo->escape('class_key')} = 'disBoard'");
        $c->leftJoin('disClosure', 'c2', "{$this->xpdo->escape('c2')}.{$this->xpdo->escape('ancestor')} = {$this->xpdo->escape('Board')}.{$this->xpdo->escape('id')} ");
        $cSub = $this->xpdo->newQuery('disPost');
        $cSub->setClassAlias('subPost');
        $cSub->select(array("MAX({$this->xpdo->escape('subPost')}.{$this->xpdo->escape('id')})"));
        $cSub->leftJoin('disClosure', 'c3', "{$this->xpdo->escape('subPost')}.{$this->xpdo->escape('id')} = {$this->xpdo->escape('c3')}.{$this->xpdo->escape('descendant')}");
        $cSub->where(array(
            "{$this->xpdo->escape('c3')}.{$this->xpdo->escape('ancestor')} = {$this->xpdo->escape('Board')}.{$this->xpdo->escape('id')}",
            "{$this->xpdo->escape('subPost')}.{$this->xpdo->escape('class_key')} = 'disPost'"
        ));
        $cSub->prepare();
        $c->leftJoin('disPost', 'Post', "{$this->xpdo->escape('Post')}.{$this->xpdo->escape('parent')} = {$this->xpdo->escape('c2')}.{$this->xpdo->escape('descendant')}
            AND {$this->xpdo->escape('Post')}.{$this->xpdo->escape('class_key')} = 'disPost'
            AND {$this->xpdo->escape('Post')}.{$this->xpdo->escape('published')} = 1
            AND {$this->xpdo->escape('Post')}.{$this->xpdo->escape('deleted')} = 0
            AND {$this->xpdo->escape('Post')}.{$this->xpdo->escape('id')} = ({$cSub->toSQL()})");
        $c->leftJoin('disUser', 'User', "{$this->xpdo->escape('User')}.{$this->xpdo->escape('id')} = {$this->xpdo->escape('Post')}.{$this->xpdo->escape('createdby')}");
        $c->leftJoin('disUserProfile', 'Profile', "{$this->xpdo->escape('Profile')}.{$this->xpdo->escape('internalKey')} = {$this->xpdo->escape('User')}.{$this->xpdo->escape('id')}");


        $c->where(array(
            'parent' => $this->id,
            'published' => 1,
            'deleted' => 0,
            'Board.id IS NOT NULL',
            'Board.deleted' => 0,
            'Board.published' => 1,
        ));
        $c->sortby("{$this->xpdo->escape('disCategory')}.{$this->xpdo->escape('menuindex')}", 'ASC');
        $c->prepare();

        $rows = self::_loadRows($this->xpdo, 'disCategory', $c);
        $rows = $rows->fetchAll(PDO::FETCH_ASSOC);
        if (count($rows) == 0) {
            return;
        }
        $hydrated = array();

        foreach ($rows as $row) {
            $this->xpdo->discuss2->hydrateRow($row, $hydrated);
        }


        $principal  = $this->xpdo->newObject('modResource');
        foreach ($hydrated as $k => $cat) {
            $principal->id = $cat['id'];
            $principal->_policies = array();
            if ($principal->checkPolicy('load') !== true) {
                unset($hydrated[$k]);
            }
        }
        $categories = $this->xpdo->discuss2->createTree($hydrated);
        $categories = $this->_treeToView($categories);
        $this->xpdo->setPlaceholder('discuss2.content', $categories);
    }

    private function _treeToView($tree) {
        $catsContainer =  $this->xpdo->getOption('categories_container', $this->xpdo->discuss2->forumConfig, 'category.categoriesContainer');
        $catChunk =  $this->xpdo->getOption('categories_category_chunk', $this->xpdo->discuss2->forumConfig, 'category.categoryChunk');
        $boardChunk =  $this->xpdo->getOption('categories_board_row', $this->xpdo->discuss2->forumConfig, 'category.boardRow');
        $subBoardChunk =  $this->xpdo->getOption('categories_subboard_row', $this->xpdo->discuss2->forumConfig, 'category.subboardRow');
        $subBoardContainer = $this->xpdo->getOption('categories_subboard_container', $this->xpdo->discuss2->forumConfig, 'category.subBoardContainer');

        $categories = array();
        $parser = $this->xpdo->discuss2->loadParser();
        foreach ($tree as $category) {
            $boards = array();
            if (!empty($category['disBoard'])) {
                foreach($category['disBoard'] as $board) {
                    $subBoards = array();
                    if (!empty($board['disPost'])) {
                        $lastPost = reset($board['disPost']);
                        unset($board['disThread']);
                        $board['lastpost.pagetitle'] = $parser->parse($lastPost['pagetitle']);
                        $board['lastpost.content']  = $parser->parse($lastPost['content'] );
                        $board['lastpost.id'] = $lastPost['id'];
                        $board['lastpost.author_id'] = $lastPost['author'];
                        $board['lastpost.author_username'] = ($lastPost['use_display_name'] == 1) ? $lastPost['display_name'] : $lastPost['username'];
                        $board['lastpost.link'] = $this->xpdo->discuss2->getLastPostLink($lastPost['parent'], $board['lastpost.id']);
                        $board['lastpost.createdon'] = $lastPost['createdon'];
                    }
                    if (!empty($board['disBoard'])) {
                        foreach($board['disBoard'] as $subBoard) {
                            $subBoards[] = $this->xpdo->discuss2->getChunk($subBoardChunk, $subBoard);
                        }
                    }
                    $board['link'] = $this->xpdo->makeUrl($board['id']);
                    $board = array_merge($board, $this->xpdo->discuss2->stats->getRepliesAndThreads($board['id']));
                    // TODO: Add read/undead threads check
                    $board['subBoards'] = $this->xpdo->discuss2->getChunk($subBoardContainer, array('boards' => implode("",$subBoards)));
                    $boards[] = $this->xpdo->discuss2->getChunk($boardChunk,$board);
                }
            }

            $category['link'] = $this->xpdo->makeUrl($category['id']);
            $categories[] = $this->xpdo->discuss2->getChunk($catChunk,array_merge($category, array('boards' => implode("\n", $boards))));
        }
        $catOut = $this->xpdo->discuss2->getChunk($catsContainer, array('categories' => implode("\n", $categories)));
        return $catOut;
    }

    public function set($k, $v= null, $vType= '') {
        if ($k == 'parent' && !$this->isNew() && $v != $this->parent) {
            $parentChanged = $this->parent;
        }
        parent::set($k, $v, $vType);
    }

    public function save($cacheFlag = null) {
        $isNew = $this->isNew();
        $this->cacheable = false;
        $this->set('isfolder', true);
        $saved = parent::save($cacheFlag);
        if ($isNew && $saved) {
            $closure = $this->xpdo->newObject('disClosure');
            $closSaved = $closure->createClosure(intval($this->id), intval($this->parent));
        } else if ($saved) {
            if ($this->parentChanged !== null) {
                // TODO: Add forum move to fire closure changes
            }
        }
        return $saved;
    }
}