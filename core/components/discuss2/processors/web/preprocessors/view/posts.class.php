<?php

if (!class_exists('disPreProcessor')) {
    require_once dirname(dirname(__FILE__)).'/dispreprocessor.class.php';
}

class modViewPostsProcessor extends disPreProcessor {
    protected $visibility = 'public';

    public function process() {
        $c = $this->modx->newQuery('disPost');
        $c->select(array($this->modx->getSelectColumns('disPost', '', '')));
        $c->where(array(
            'createdby' => $this->discuss->controller->user->id,
            'class_key' => 'disPost'
        ));
        $c->sortby('createdon', 'DESC');


        $count = $this->modx->getCount('disPost', $c);
        if ($count > $this->discuss->forumConfig['threads_per_page']) {
            $pagination = $this->discuss->loadPagination();
            $pages = $pagination->processMainPagination($count, 'threads_per_page');
            $this->modx->setPlaceholder('discuss2.pagination', $pages);
        }

        $offset = isset($this->modx->request->parameters['GET']['page']) ? ($this->modx->request->parameters['GET']['page'] -1) * $this->modx->discuss2->forumConfig['threads_per_page']: 0;
        $c->limit($this->modx->discuss2->forumConfig['threads_per_page'], $offset);

        $c->prepare();
        $c->stmt->execute();
        return $this->success('OK', $c->stmt->fetchAll(PDO::FETCH_ASSOC));
    }
}