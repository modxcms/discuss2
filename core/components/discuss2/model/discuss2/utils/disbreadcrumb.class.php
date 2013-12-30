<?php

class disBreadcrumb {
    public $modx;
    public $discuss;

    public function __construct(modX &$modx) {
        $this->modx = $modx;
        $this->discuss = &$modx->discuss2;
    }

    public function getPath($resourceId) {
        $c = $this->modx->newQuery('modResource');
        $c->setClassAlias('resource');
        $c->select(array(
            $this->modx->getSelectColumns('modResource', 'resource', '', array('id', 'pagetitle'))
        ));
        $c->innerJoin('disClosure', 'closure', "{$this->modx->escape('closure')}.{$this->modx->escape('ancestor')} = {$this->modx->escape('resource')}.{$this->modx->escape('id')} AND
            {$this->modx->escape('closure')}.{$this->modx->escape('descendant')} = {$this->modx->quote($resourceId)}");
        $c->prepare();
        $pathItems = $this->modx->getCollection('modResource', $c);
        if (count($pathItems) == 0) {
            return ;
        }
        $container = $this->modx->getOption('breadcrumbs_container', $this->discuss->forumConfig, 'sample.breadcrumbContainer');
        $bcItem = $this->modx->getOption('breadcrumbs_item', $this->discuss->forumConfig, 'sample.breadcrumbItem');
        $i = 0;
        foreach ($pathItems as $item) {
            $classes = array('item');
            if ($i == 0) {
                $classes[] = 'first';
            } else if (($i + 1) == count($pathItems)) {
                $classes[] = 'last';
            } else {
                $classes[] = 'intermediate';
            }
            $i++;
            $crumbs[] = $this->discuss->getChunk($bcItem,
                array('classes' => implode(' ', $classes),
                'text' => $item->pagetitle,
                'link' => $this->modx->discuss2->makeUrl($item->id)));
        }
        $trail = $this->discuss->getChunk($container, array('trail' => implode("", $crumbs)));
        $this->modx->setPlaceholder('discuss2.trail', $trail);
    }

    public function process() {
        $this->getPath($this->modx->resource->id);
    }
}