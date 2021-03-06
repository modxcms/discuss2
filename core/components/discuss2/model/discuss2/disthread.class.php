<?php
/**
 * @package Discuss
 * @subpackage mysql
 */
class disThread extends modResource {
    public $showInContextMenu = false;
    public $actionsLink = array();

    public $validActions = array(
        'discuss2.modify_post' => 'modify/post',
        'discuss2.remove_post' => 'remove/post',
        'discuss2.lock_post' => 'lock/thread',
        'discuss2.stick_thread' => 'pin/thread',
        'discuss2.split_thread' => 'split/thread',
        'discuss2.can_post' => 'new/post'
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
        if (isset($this->xpdo->request->parameters['GET']['action']) && in_array($this->xpdo->request->parameters['GET']['action'], array_values($this->validActions))) {
            $contentChunk = false;
            $placeholders = array();
            if (!$this->xpdo->hasPermission(array_search($this->xpdo->request->parameters['GET']['action'], $this->validActions))) {
                $this->xpdo->sendUnauthorizedPage();
            }
            switch ($this->xpdo->request->parameters['GET']['action']) {
                case 'new/post' :
                    if (isset($this->xpdo->request->parameters['POST'][$this->xpdo->getOption('form_preview_var', $this->xpdo->discuss2->forumConfig, 'd2-preview')]) &&
                        $this->xpdo->request->parameters['POST'][$this->xpdo->getOption('form_preview_var', $this->xpdo->discuss2->forumConfig, 'd2-preview')] == $this->xpdo->lexicon('discuss2.preview')) {
                        $parser = $this->xpdo->discuss2->loadParser();
                        $this->xpdo->toPlaceholders(array(
                            'content' => $this->xpdo->request->parameters['POST']['content'],
                            'pagetitle' => $this->xpdo->request->parameters['POST']['pagetitle'],
                            'thread' => $this->xpdo->request->parameters['POST']['thread'],
                            'preview.pagetitle' => $parser->parse($this->xpdo->request->parameters['POST']['pagetitle']),
                            'preview.content' => $parser->parse($this->xpdo->request->parameters['POST']['content'])
                        ), 'form');
                    }
                    if (empty($placeholders)) {
                        $this->xpdo->toPlaceholder('form.pagetitle', $this->xpdo->lexicon('discuss2.re') . ' ' . $this->xpdo->resource->pagetitle);
                    }
                    $contentChunk = $this->xpdo->getOption('new_post_form', $this->xpdo->discuss2->forumConfig, 'sample.newPost');
                    break;
                case 'remove/post' :
                    if (!isset($this->xpdo->request->parameters['GET']['pid'])) {
                        break;
                    }
                    $obj = $this->xpdo->getObject('disPost', $this->xpdo->request->parameters['GET']['pid']);
                    $contentChunk = $this->xpdo->getOption('remove_post_form', $this->xpdo->discuss2->forumConfig, 'sample.removePost');
                    $placeholders = array(
                        'params'  => $this->xpdo->request->parameters,
                        'thread' => $obj->toArray(),
                        'action' => $this->xpdo->discuss2->makeUrl($this->xpdo->resource->id, '', 'remove/post', array('pid' => $this->xpdo->request->parameters['GET']['pid']))
                    );
                    break;
                case 'modify/post' :
                    if (!isset($this->xpdo->request->parameters['GET']['pid'])) {
                        break;
                    }
                    $obj = $this->xpdo->getObject('disPost', $this->xpdo->request->parameters['GET']['pid']);
                    $contentChunk = $this->xpdo->getOption('edit_post_form', $this->xpdo->discuss2->forumConfig, 'sample.editPost');
                    $placeholders = array(
                        'params'  => $this->xpdo->request->parameters,
                        'thread' => $obj->toArray(),
                        'action' => $this->xpdo->discuss2->makeUrl($this->xpdo->resource->id, '', 'modify/post', array('pid' => $this->xpdo->request->parameters['GET']['pid']))
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
        ));

        $c->where(array(
            'Post.parent' => $this->id,
            'Post.deleted' => 0,
            'Post.published' => 1
        ));

        $c->sortby('Post.id', 'ASC');
        $count = $this->xpdo->getCount('disPost', $c);

        $threaded = $this->xpdo->getOption('posts_threaded', $this->xpdo->discuss2->forumConfig, false);
        if ($threaded === 'true') {
            $c->select(array('postReplies_depth' => 'c.depth'));
            $c->select(array($this->xpdo->getSelectColumns('disPost', 'postReplies', 'postReplies_', $fieldsToLoad)));
            $depth = $this->xpdo->getOption('posts_depth', $this->xpdo->discuss2->forumConfig, 1);
            $c->leftJoin('disClosure', 'c', "{$this->xpdo->escape('c')}.{$this->xpdo->escape('ancestor')} = {$this->xpdo->escape('Post')}.{$this->xpdo->escape('id')}");
            $c->leftJoin('disPost', 'postReplies', "{$this->xpdo->escape('postReplies')}.{$this->xpdo->escape('id')} = {$this->xpdo->escape('c')}.{$this->xpdo->escape('descendant')}
            AND {$this->xpdo->escape('c')}.{$this->xpdo->escape('depth')} BETWEEN 0 AND {$depth}");
            $c->where(array(
                'postReplies.deleted' => 0,
                'postReplies.published' => 1
            ));
        }
        if ($this->xpdo->getOption('enable_posts_pagination', $this->xpdo->discuss2->forumConfig, true) == true) {
            $offset = isset($this->xpdo->request->parameters['GET']['page']) ? ($this->xpdo->request->parameters['GET']['page'] -1) * $this->xpdo->discuss2->forumConfig['posts_per_page']: 0;
            $c->limit($this->xpdo->discuss2->forumConfig['posts_per_page'], $offset);
        }


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
        $parser = $this->xpdo->discuss2->loadParser();
        $userids = array();
        foreach ($hydrated as &$post) {
            $post['pagetitle'] = $parser->parse($post['pagetitle']);
            $post['content'] = $parser->parse($post['content']);
            $userids[$post['createdby']] = $post['createdby'];
        }
        unset($post);
        $users = $this->xpdo->getCollection('disUser', array('id:IN' => array_unique(array_values($userids))));
        foreach ($users as $user) {
            $userids[$user->id] = $user->toArray();
        }
        $posts = $this->xpdo->discuss2->createTree($hydrated);
        $posts = $this->_treeToView($posts, $userids);

        if ($count > $this->xpdo->discuss2->forumConfig['posts_per_page'] && $this->xpdo->getOption('enable_posts_pagination', $this->xpdo->discuss2->forumConfig, true) == true) {
            $pages = $this->xpdo->discuss2->loadPagination();
            $pagination = $pages->processMainPagination($count, 'posts_per_page');
            $this->xpdo->setPlaceholder('discuss2.pagination', $pagination);
        }
        if ($this->xpdo->hasPermission('discuss2.can_post')) {
            $newPostForm = $this->xpdo->getOption('new_post_form', $this->xpdo->discuss2->forumConfig, 'sample.newPost');
            $this->xpdo->setPlaceholder('discuss2.form', $this->xpdo->discuss2->getChunk($newPostForm));
        }

        //$this->xpdo->setPlaceholder('discuss2.thread_actions', $this->_getThreadActions($this->createdby));
        $this->xpdo->setPlaceholder('discuss2.content', $posts);
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
                'link' => $x->discuss2->makeUrl($this->id, '', 'lock/thread', array('tid' => $thread)),
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

    public function getPostActions($userId, $postId) {
        $x = &$this->xpdo;
        $links = array();
        $actionChunk = $x->getOption('post_actions_item', $x->discuss->forumConfig, 'sample.actionsItem');
        if ($x->hasPermission('discuss2.remove_post')) {
            $links['actions.remove_post'] = $x->discuss2->getChunk($actionChunk, array(
                'link' => $x->makeUrl($this->id, '', 'remove/post', array('pid' => $postId)),
                'text' => $x->lexicon('discuss2.remove_post')));
        }
        if ($x->hasPermission('discuss2.modify_post') || $userId == $this->xpdo->user->id) {
            $links['actions.modify_post'] = $x->discuss2->getChunk($actionChunk, array(
                'link' => $x->discuss2->makeUrl($this->id, '', 'modify/post', array('pid' => $postId)),
                'text' => $x->lexicon('discuss2.edit_post')));
        }
        return $links;
    }

    private function _treeToView($tree, $users) {
        $posts = array();
        $postContainer = $this->xpdo->getOption('thread_posts_container', $this->xpdo->discuss2->forumConfig, 'sample.postsContainer');
        $postRow = $this->xpdo->getOption('thread_post_chunk', $this->xpdo->discuss2->forumConfig, 'sample.postrow');
        $actionsContainer = $this->xpdo->getOption('post_actions_container', $this->xpdo->discuss2->forumConfig, 'sample.actionsContainer');
        $threaded = $this->xpdo->getOption('posts_threaded',$this->xpdo->discuss2->forumConfig, false);
        if ($threaded) {
            $replyForm = $this->xpdo->getOption('post_reply_form', $this->xpdo->discuss2->forumConfig, 'sample.replyform');
            $maxDepth = $this->xpdo->getOption('posts_depth', $this->discuss2->forumConfig, 1);
        }

        $i = 1;
        foreach ($tree as $post) {
            $hasChildren = false;
            $post['user'] = $users[$post['createdby']];
            $post['actions'] = $this->xpdo->getChunk($actionsContainer, array('actions' => implode("", $this->getPostActions($post['createdby'], $post['id']))));
            if ($threaded == true && $this->xpdo->hasPermission('discuss2.can_post')) {
                if ($maxDepth > 1) {
                    $post['form'] = $this->xpdo->discuss2->getChunk($replyForm, array(
                        'parent' => $post['id'],
                        'action' => $this->xpdo->discuss2->makeUrl($this->id) . "#post-{$post['id']}",
                        'submitVar' => 'reply-' . $post['id'],
                        'pagetitle' => $post['pagetitle']
                    ));
                }
                if (!empty($post['disPost'])) {
                    $hasChildren = true;
                    $post['replies'] = $this->_treeToView($post['disPost'], $users);
                }
                $post['classes'] = 'depth-' . $post['depth'];
            }
            $this->xpdo->log(xPDO::LOG_LEVEL_ERROR, "I = {$i}\n".count($tree));
            if ($threaded == true && $this->xpdo->hasPermission('discuss2.can_post') && $maxDepth == 1 && $i == count($tree) && $hasChildren == false) {
                $post['form'] = $this->xpdo->discuss2->getChunk($replyForm, array(
                    'parent' => ($post['depth'] == 1) ? $post['parent'] : $post['id'],
                    'action' => $this->xpdo->discuss2->makeUrl($this->id) . "#post-{$post['id']}",
                    'submitVar' => 'reply-' . $post['id'],
                    'pagetitle' => $post['pagetitle']
                ));
            }
            $posts[] = $this->xpdo->discuss2->getChunk($postRow, $post);
            $i++;
        }

        if (!empty($posts)) {
            return $this->xpdo->discuss2->getChunk($postContainer, array('posts' => implode("\n", $posts), 'classes' => 'depth-' . $post['depth']));
        }

    }

    public function save($cacheFlag = null) {
        $isNew = $this->isNew();
        if ($isNew) {
            $this->alias = $this->cleanAlias($this->pagetitle);
            $this->uri = $this->getAliasPath($this->alias);
        }
        $this->cacheable = false;
        $this->set('isfolder', true);
        $saved = parent::save($cacheFlag);

        if ($isNew && $saved) {
            $this->save();
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

    public function remove(array $ancestors= array ()) {
        parent::remove();
    }

}