<?php
/**
 * @package Discuss
 * @subpackage mysql
 */
class disThread extends modResource {
    public $showInContextMenu = false;
    private $validActions = array(
        'new',
        'remove',
        'edit'
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
        $this->_getContent();
        if (isset($this->xpdo->resource) && $this->xpdo->resource->id == $this->id) {
            $statsTable = $this->xpdo->getTableName('disThreadStatistics');
            $sql = "UPDATE {$statsTable} SET {$this->xpdo->escape('views')} = ({$this->xpdo->escape('views')} + 1) WHERE {$this->xpdo->escape('idx')} = {$this->id}";
            if (!$this->xpdo->exec($sql)) {
                $this->xpdo->log(xPDO::LOG_LEVEL_ERROR, 'Could not update view count for thread ID ' . $this->id);
            }
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
            'Post.parent' => $this->id
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
        $this->xpdo->setPlaceholder('discuss2.thread_actions', $this->_getThreadActions($this->createdby));
        $this->xpdo->setPlaceholder('discuss2.content', $posts);
    }

    private function _getThreadActions($authorId) {
        $u = $this->xpdo->discuss2->getUser();
        $actions = array(
            'discuss2.lock_thread',
            'discuss2.merge_thread',
            'discuss2.modify_thread',
            'discuss2.remove_thread',
            'discuss2.split_thread',
            'discuss2.stick_thread'
        );
        $threadActionsContainer = $this->xpdo->getOption('threadActionsContainer', $this->xpdo->discuss2->forumConfig, 'thread.ActionsContainer');
        $threadActionButton = $this->xpdo->getOption('threadActionChunk', $this->xpdo->discuss2->forumConfig, 'thread.Action');
        $buttons = array();
        foreach ($actions as $action) {
            if ($this->xpdo->hasPermission($action) || $u->sudo == true) {
                $buttons[$action] = $this->xpdo->discuss2->getChunk($threadActionButton, array(
                    'text' => $action,
                    'link' => $this->xpdo->makeUrl($this->id, '', array('action' => 'thread/' . $action))  ,
                    'action' => 'thread/'. $action
                ));
            }
        }
        return $this->xpdo->discuss2->getChunk($threadActionsContainer, array('actions' => implode("\n", $buttons)));
    }
    private function _getPostActions($authorId, $postId) {
        $u = $this->xpdo->discuss2->getUser();
        $actions = array(
            'discuss2.ban',
            'discuss2.modify_post',
            'discuss2.remove_post'
        );
        $postActionsContainer = $this->xpdo->getOption('postActionsContainer', $this->xpdo->discuss2->forumConfig, 'post.postActionsContainer');
        $postActionButton = $this->xpdo->getOption('postActionChunk', $this->xpdo->discuss2->forumConfig, 'post.postAction');
        $buttons = array();
        foreach ($actions as $action) {
            if ($this->xpdo->hasPermission($action) || $u->sudo == true) {
                $buttons[$action] = $this->xpdo->discuss2->getChunk($postActionButton, array(
                    'text' => $action,
                    'link' => $this->xpdo->makeUrl($this->id, '', array('action' => 'post/' . $action, 'pid' => $postId))  ,
                    'action' => 'post/'. $action,
                    'action-id' => $postId
                ));
            }
        }
        if ($authorId == $u->id) {
            $buttons['discuss2.modify_post'] = $this->xpdo->discuss2->getChunk($postActionButton, array(
                'text' => 'discuss2.modify_post',
                'link' => $this->xpdo->makeUrl($this->id, '', array('action' => 'post/' . 'discuss2.modify_post'))  ,
                'action' => 'post/'. 'discuss2.modify_post'
            ));
        }
        return $this->xpdo->discuss2->getChunk($postActionsContainer, array('actions' => implode($buttons)));
    }

    private function _treeToView($tree) {
        $posts = array();
        $postContainer = $this->xpdo->getOption('thread_posts_container', $this->xpdo->discuss2->forumConfig, 'thread.postsContainer');
        $postRow = $this->xpdo->getOption('thread_post_chunk', $this->xpdo->discuss2->forumConfig, 'board.threadrow');

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
            $post['actions'] = $this->_getPostActions($post['createdby'], $post['id']);
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
            $threadStat = $this->xpdo->newObject('disThreadStatistics');
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