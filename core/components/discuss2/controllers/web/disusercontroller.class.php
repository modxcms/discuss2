<?php

if (!class_exists('disWebController')) {
    require_once dirname(__FILE__) . '/diswebcontroller.class.php';
}

class disUserController extends disWebController {
    public $user;
    public $controllerPath = "user/";

    public $privateActions = array(
        'edit/profile' => array(
            'key' => 'discuss2.edit_profile',
            'chunk' => 'user_pages_edit_profile'
        ),
        'view/subscriptions' => array(
            'key' => 'discuss2.subscriptions',
            'chunk' => 'user_pages_subscriptions'
        ),
        /*'view/messages' => array(
            'key' => 'discuss2.messages',
            'chunk' => 'user_pages_pms'
        ),
        'merge/account' => array(
            'key' => 'discuss2.merge_account',
            'chunk' => 'user_merge'
        )*/
    );

    public $publicActions = array(
        'view/profile' => array(
            'key' => 'discuss2.view_profile',
            'chunk' => 'user_pages_profile'
        ),
        'view/posts' => array(
            'key' => 'discuss2.view_posts',
            'chunk' => 'view_posts'
        ),
        'view/stats' => array(
            'key' => 'discuss2.view_stats',
            'chunk' => 'user_view_stats'
        ),
        'send/pm' => array(
            'key' => 'discuss2.send_pm',
            'chunk' => 'user_pages_send_pm'
        ),
    );

    public $accessibleActions = array(
        /*'discuss2.ban' => array(
            'action' => 'ban/user',
            'key' => 'discuss2.ban_user',
            'chunk' => 'pages_ban_user'
        )*/
    );

    public function init() {
        $username = $this->modx->request->parameters['GET']['username'];
        $c = $this->modx->newQuery('disUser');
        $c->where(array('username' => $username));
        $usr = $this->modx->getObject('disUser', $c);
        if ($usr instanceof modUser) {
            $this->user = $usr;
        } else {
            $this->modx->sendErrorPage();
        }
        return true;
    }

    public function process() {
        $actionsContainer = $this->modx->getOption('user_actions_container', $this->discuss->forumConfig, 'sample.userActionContainer');
        $actionsItem = $this->modx->getOption('user_actions_item', $this->discuss->forumConfig, 'sample.userActionItem');
        $actions = array();
        $i = 0;
        foreach ($this->publicActions as $key => $action) {
            $class = $i % 2 ? 'even' : 'odd';
            $class .= $i == 0 ? ' first' : ($i + 1) == count($this->publicActions) ? ' last' : '';
            $class .= $key == $this->action ? ' active' : '';
            $class .= ' dis-' . str_replace('/', '-', $key);
            $actions[] = $this->discuss->getChunk($actionsItem, array(
                'action' => $this->discuss->makeUrl($this->modx->resource->id, $key),
                'title' => $this->modx->lexicon($action['key']),
                'class' => $class
            ));
            $i++;
        }
        $this->modx->setPlaceholder('discuss2.user.public_actions', $this->discuss->getChunk($actionsContainer, array('actions' => implode("\n", $actions))));
        $actions = array();
        $i = 0;
        if ($this->modx->user->id == $this->user->id) {
            foreach ($this->privateActions as $key => $action) {
                $class = $i % 2 ? 'even' : 'odd';
                $class .= $i == 0 ? ' first' : ($i + 1) == count($this->publicActions) ? ' last' : '';
                $class .= $key == $this->action ? ' active' : '';
                $class .= ' dis-' . str_replace('/', '-', $key);
                $actions[] = $this->discuss->getChunk($actionsItem, array(
                    'action' => $this->discuss->makeUrl($this->modx->resource->id, $key),
                    'title' => $this->modx->lexicon($action['key']),
                    'class' => $class
                ));
                $i++;
            }
            $this->modx->setPlaceholder('discuss2.user.private_actions', $this->discuss->getChunk($actionsContainer, array('actions' => implode("\n", $actions))));
        }
        $actions = array();
        $i = 0;
        if ($this->modx->user->isMember(array('Discuss2 Global Moderators', 'Administrator')) || $this->modx->user->sudo == 1) {
            foreach ($this->privateActions as $key => $action) {
                $class = $i % 2 ? 'even' : 'odd';
                $class .= $i == 0 ? ' first' : ($i + 1) == count($this->publicActions) ? ' last' : '';
                $class .= $action['action'] == $this->action ? ' active' : '';
                $class .= ' dis-' . str_replace('/', '-', $action['action'] );
                $actions[] = $this->discuss->getChunk($actionsItem, array(
                    'action' => $this->discuss->makeUrl($this->modx->resource->id, $action['action']),
                    'title' => $this->modx->lexicon($key),
                    'class' => $class
                ));
                $i++;
            }
            $this->modx->setPlaceholder('discuss2.user.moderator_actions', $this->discuss->getChunk($actionsContainer, array('actions' => implode("\n", $actions))));
        }
    }
}