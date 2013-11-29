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

        $node['menu'] = array('items' => $menu);
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
            'lastpost_post_title' => 'Post.title',
            'lastpost_content' => 'Post.content',
            'lastpost_id' => 'Post.id',
            'lastpost_author' => 'Post.author',
            'lastpost_username' => 'Post.username',
            'lastpost_title' => 'Post.thread_title',
            'lastpost_thread_id' => 'Post.thread_id',
            'lastpost_parent' => 'Post.thread_parent',
            'lastpost_class_key' => 'Post.thread_class_key',
            'lastpost_when' => 'Post.createdon'
        ));
        $c->leftJoin('disBoard', 'Board', "{$this->xpdo->escape('Board')}.{$this->xpdo->escape('parent')} = {$this->xpdo->escape('disCategory')}.{$this->xpdo->escape('id')}");

        $lastPostQ = $this->xpdo->newQuery('disPost');
        $lastPostQ->select(array(
            'title' => 'disPost.pagetitle',
            'content' => 'SUBSTRING(disPost.content, 1, '.$this->xpdo->getOption('post_excerpt', $this->xpdo->discuss2->forumConfig, 100).')',
            'id' => 'disPost.id',
            'author' => 'disPost.createdby',
            'createdon' => 'disPost.createdon',
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
            "{$this->xpdo->escape('disPost')}.{$this->xpdo->escape('id')} = (SELECT MAX({$this->xpdo->escape('subPost')}.{$this->xpdo->escape('id')}) FROM {$this->xpdo->getTableName('disPost')} {$this->xpdo->escape('subPost')}
                WHERE {$this->xpdo->escape('subPost')}.{$this->xpdo->escape('parent')} = {$this->xpdo->escape('disThread')}.{$this->xpdo->escape('id')})"
        ));
        $lastPostQ->sortby('disPost.createdon', 'DESC');
        $lastPostQ->limit(1);
        $lastPostQ->prepare();
        $c->query['from']['joins'][] = array(
            'type' => xPDOQuery::SQL_JOIN_LEFT,
            'table' => "({$lastPostQ->toSQL()})",
            'alias' => 'Post',
            'conditions' => new xPDOQueryCondition(array('sql' => "{$this->xpdo->escape('Post')}.{$this->xpdo->escape('thread_parent')} = {$this->xpdo->escape('Board')}.{$this->xpdo->escape('id')}"))
        );
        $c->where(array('parent' => $this->id, 'published' => 1, 'deleted' => 0));
        $c->groupby('disCategory.id, Board.id');
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

        reset($hydrated);
        $categories = $this->xpdo->discuss2->createTree($hydrated);
        $categories = $this->_treeToView($categories);
        $this->xpdo->setPlaceholder('discuss2.content', $categories);
    }

    private function _treeToView($tree) {
        $catsContainer =  $this->xpdo->getOption('categories_container', $this->xpdo->discuss2->forumConfig, 'category.categoriesContainer');
        $catChunk =  $this->xpdo->getOption('categories_category_chunk', $this->xpdo->discuss2->forumConfig, 'category.categoryChunk');
        $boardChunk =  $this->xpdo->getOption('categories_board_row', $this->xpdo->discuss2->forumConfig, 'category.boardRow');
        //$subboardChunk =  $this->xpdo->getOption('categories_subboard_row', $this->xpdo->discuss2->forumConfig, 'category.subboard_row');

        $categories = array();
        $parser = $this->xpdo->discuss2->loadParser();
        foreach ($tree as $category) {
            $boards = array();
            if (!empty($category['disBoard'])) {
                foreach($category['disBoard'] as $board) {
                    if (!empty($board['disThreadDiscussion'])) {
                        $board['disThread'] = $board['disThreadDiscussion'];
                        unset($board['disThreadDiscussion']);
                    } else if (!empty($board['disThreadQuestion'])) {
                        $board['disThread'] = $board['disThreadQuestion'];
                        unset($board['disThreadQuestion']);
                    }
                    if (!empty($board['disThread'])) {
                        $lastPost = reset($board['disThread']);
                        unset($board['disThread']);
                        $board['lastpost.title'] = $lastPost['post_title'];
                        $board['lastpost.content']  = $parser->parse($lastPost['content'] );
                        $board['lastpost.id'] = $lastPost['id'];
                        $board['lastpost.author_id'] = $lastPost['author'];
                        $board['lastpost.author_uname'] = $lastPost['username'];
                        $board['lastpost.thread_title'] = $lastPost['title'];
                        $board['lastpost.link'] = $this->xpdo->discuss2->getLastPostLink($board['id'], $board['lastpost.id']);
                        $board['lastpost.createdon'] = $lastPost['when'];
                    }

                    $board['link'] = $this->xpdo->makeUrl($board['id']);
                    $board = array_merge($board, $this->xpdo->discuss2->stats->getRepliesAndThreads($board['id']));
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

            }
        }
        return $saved;
    }

    public function moveClosure() {

    }
}