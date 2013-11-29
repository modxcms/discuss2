<?php
/**
 * @package Discuss
 * @subpackage mysql
 */
class disThread extends modResource {
    public $showInContextMenu = false;
    public $actionsLink = array();

    public $validActions = array(
        'modify/post',
        'remove/post',
        'lock/thread',
        'pin/thread'
    );

    function __construct(xPDO & $xpdo) {
        parent :: __construct($xpdo);
        $this->set('class_key','disThread');
        $this->set('cacheable', false);
        $this->set('isfolder', true);
        $this->set('show_in_tree', false);
        $this->config = $this->xpdo->discuss2->forumConfig;
    }


    public function process() {
        $this->xpdo->lexicon->load('discuss2:front-end');
        if (isset($_GET['action']) && in_array($_GET['action'], $this->validActions)) {
            $contentChunk = false;
            $placeholders = array();
            switch ($_GET['action']) {
                case 'new/post' :
                    $contentChunk = $this->xpdo->getOption('new_post_form', $this->xpdo->discuss2->forumConfig, 'post.newPost');
                    break;
                case 'remove/post' :
                    if (!isset($this->xpdo->request->parameters['GET']['pid'])) {
                        break;
                    }

                    $contentChunk = $this->xpdo->getOption('remove_post_form', $this->xpdo->discuss2->forumConfig, 'post.removePost');
                    break;
                case 'modify/post' :
                    if (!isset($this->xpdo->request->parameters['GET']['pid'])) {
                        break;
                    }
                    $obj = $this->xpdo->getObject('disPost', $this->xpdo->request->parameters['GET']['pid']);
                    $contentChunk = $this->xpdo->getOption('edit_post_form', $this->xpdo->discuss2->forumConfig, 'post.editPost');
                    $placeholders = array(
                        'params'  => $this->xpdo->request->parameters,
                        'thread' => $obj->toArray(),
                        'action' => $this->xpdo->makeUrl($this->xpdo->resource->id, '', array('action' => 'modify/post', 'pid' => $this->xpdo->request->parameters['GET']['pid']))
                    );
                    break;
            }
            if ($contentChunk != false) {
                $content = $this->xpdo->discuss2->getChunk($contentChunk, $placeholders);
                $this->xpdo->setPlaceholder('discuss2.content', $content);
            }
        } else {
            if (isset($this->xpdo->resource) && $this->xpdo->resource->id == $this->id) {
                $statsTable = $this->xpdo->getTableName('disThreadProperty');
                $sql = "UPDATE {$statsTable} SET {$this->xpdo->escape('views')} = ({$this->xpdo->escape('views')} + 1) WHERE {$this->xpdo->escape('idx')} = {$this->id}";
                if (!$this->xpdo->exec($sql)) {
                    $this->xpdo->log(xPDO::LOG_LEVEL_ERROR, 'Could not update view count for thread ID ' . $this->id);
                }
            }
            //$this->getGlobalActions();
            $this->_getContent();
        }
        return parent::process();
    }

    private function _getContent() {
        // Keeping query bit more lighweight and stripping of unnecessary fields
        $fieldsToLoad = array('id', 'pagetitle', 'longtitle', 'description', 'parent',
            'alias', 'pub_date', 'parent', 'introtext', 'content', 'createdby', 'createdon', 'class_key');

        $c = $this->xpdo->newQuery('disPost');
        $c->setClassAlias('Post');
        $c->select(array(
            $this->xpdo->getSelectColumns('disPost', 'Post', 'Post_', $fieldsToLoad),
            $this->xpdo->getSelectColumns('disPost', 'postReplies', 'postReplies_', $fieldsToLoad),
        ));
        $c->leftJoin('disPost', 'postReplies', "{$this->xpdo->escape('postReplies')}.{$this->xpdo->escape('parent')} = {$this->xpdo->escape('Post')}.{$this->xpdo->escape('id')}");
        $c->where(array(
            'Post.parent' => $this->id,
            'Post.deleted' => 0
        ));
        $c->sortby('Post.id', 'ASC');
        $count = $this->xpdo->getCount('disPost', $c);
        $offset = isset($this->xpdo->request->parameters['GET']['page']) ? ($this->xpdo->request->parameters['GET']['page'] -1) * $this->xpdo->discuss2->forumConfig['posts_per_page']: 0;
        $c->limit($this->xpdo->discuss2->forumConfig['posts_per_page'], $offset);

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
        $posts = $this->xpdo->discuss2->createTree($hydrated);
        $posts = $this->_treeToView($posts);

        if ($count > $this->xpdo->discuss2->forumConfig['posts_per_page']) {
            $pages = $this->xpdo->discuss2->loadPagination();
            $pagination = $pages->processMainPagination($count, 'posts_per_page');
            $this->xpdo->setPlaceholder('discuss2.pagination', $pagination);
        }
        if ($this->xpdo->hasPermission('discuss2.can_post')) {
            $this->xpdo->setPlaceholder('discuss2.form', $this->xpdo->discuss2->getChunk('post.newPost'));
        }
        //$this->xpdo->setPlaceholder('discuss2.thread_actions', $this->_getThreadActions($this->createdby));
        $this->xpdo->setPlaceholder('discuss2.content', $posts);
    }

    public function getThreadActions($thread) {
        $x = &$this->xpdo;
        $actionChunk = $x->getOption('thread_actions_item', $x->discuss->forumConfig, 'thread.actionsItem');
        if ($x->hasPermission('discuss2.remove_thread')) {
            $this->actionLinks['actions.remove_thread'] = $x->discuss2->getChunk($actionChunk, array(
                'link' => $x->makeUrl($this->id, '', array('action' => 'remove/thread', 'tid' => $thread)),
                'text' => $x->lexicon('discuss2.remove_thread')));
        }
        if ($x->hasPermission('discuss2.lock_thread')) {
            $this->actionLinks['actions.lock_thread'] = $x->discuss2->getChunk($actionChunk, array(
                'link' => $x->makeUrl($this->id, '', array('action' => 'lock/thread', 'tid' => $thread)),
                'text' => $x->lexicon('discuss2.lock_thread')));
        }
        if ($x->hasPermission('discuss2.modify_thread')) {
            $this->actionLinks['actions.modify_thread'] = $x->discuss2->getChunk($actionChunk, array(
                'link' => $x->makeUrl($this->id, '', array('action' => 'modify/thread', 'tid' => $thread)),
                'text' => $x->lexicon('discuss2.edit_thread')));
        }
        if ($x->hasPermission('discuss2.stick_thread')) {
            $this->actionLinks['actions.stick_thread'] = $x->discuss2->getChunk($actionChunk, array(
                'link' => $x->makeUrl($this->id, '', array('action' => 'pin/thread', 'tid' => $thread)),
                'text' => $x->lexicon('discuss2.pin_thread')));
        }
        return $this->actionLinks;
    }

    public function getPostActions($userId, $postId) {
        $x = &$this->xpdo;
        $links = array();
        $actionChunk = $x->getOption('post_actions_item', $x->discuss->forumConfig, 'post.actionsItem');
        if ($x->hasPermission('discuss2.remove_post')) {
            $links['actions.remove_post'] = $x->discuss2->getChunk($actionChunk, array(
                'link' => $x->makeUrl($this->id, '', array('action' => 'remove/post', 'pid' => $postId)),
                'text' => $x->lexicon('discuss2.remove_post')));
        }
        if ($x->hasPermission('discuss2.modify_post') || $userId == $this->xpdo->user->id) {
            $links['actions.modify_post'] = $x->discuss2->getChunk($actionChunk, array(
                'link' => $x->makeUrl($this->id, '', array('action' => 'modify/post', 'pid' => $postId)),
                'text' => $x->lexicon('discuss2.edit_post')));
        }
        return $links;
    }

    private function _treeToView($tree) {
        $posts = array();
        $postContainer = $this->xpdo->getOption('thread_posts_container', $this->xpdo->discuss2->forumConfig, 'thread.postsContainer');
        $postRow = $this->xpdo->getOption('thread_post_chunk', $this->xpdo->discuss2->forumConfig, 'board.threadrow');
        $actionsContainer = $this->xpdo->getOption('post_actions_container', $this->xpdo->discuss2->forumConfig, 'post.actionsContainer');

        $parser = $this->xpdo->discuss2->loadParser();
        foreach ($tree as &$post) {
            $post['content'] = $parser->parse($post['content']);
            $userids[$post['createdby']] = $post['createdby'];
        }
        unset($post);
        $users = $this->xpdo->getCollection('disUser', array('id:IN' => array_values($userids)));
        foreach ($users as $user) {
            $userids[$user->id] = $user->toArray();
        }
        foreach ($tree as $post) {
            $post['user'] = $userids[$post['createdby']];
            $post['actions'] = $this->xpdo->getChunk($actionsContainer, array('actions' => implode("", $this->getPostActions($post['createdby'], $post['id']))));
            $posts[] = $this->xpdo->discuss2->getChunk($postRow, $post);
        }
        if (!empty($posts)) {
            return $this->xpdo->discuss2->getChunk($postContainer, array('posts' => implode("\n", $posts)));
        }

    }

    public function save($cacheFlag = null) {
        $isNew = $this->isNew();
        $this->cacheable = false;
        $this->set('isfolder', true);
        $saved = parent::save($cacheFlag);
        if ($isNew && $saved) {
            $threadStat = $this->xpdo->newObject('disThreadProperty');
            $threadStat->fromArray(array(
                'idx' => $this->id,
                'posts' => 0,
                'views' => 0
            ));
            $threadStat->save();
            $this->xpdo->cacheManager->refresh();
            $closure = $this->xpdo->newObject('disClosure');
            $closSaved = $closure->createClosure(intval($this->id), intval($this->parent));
        } else if ($saved) {
            if ($this->parentChanged !== null) {

            }
        }
        return $saved;
    }

    public function remove() {}

}