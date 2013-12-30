<?php

class disPagination {
    private $modx;
    private $discuss;

    private $config;
    private $current;

    public function __construct(xPDO &$xpdo) {
        $this->modx = $xpdo;
        $this->discuss = &$this->modx->discuss2;

    }

    public function processMainPagination($count, $perPageVar) {
        $page = isset($this->modx->request->parameters['GET']['page']) ? $this->modx->request->parameters['GET']['page'] : 1;
        $perPage = $this->discuss->forumConfig[$perPageVar];
        if ($count < $perPage) {
            return ;
        }
        $pages = ceil($count / $perPage);

        if ($pages <= 1) {
            return '';
        } else if ($page >= $pages) {
            $page = $pages;
        } else if ($page < 1) {
            $page = 1;
        }
        $page = intval($page);

        $this->current = $page;
        $links = array();
        // Prev && First
        if ($this->modx->getOption('pagination_show_first_last', $this->discuss->forumConfig, 'Yes') == 'Yes' && $page > 1) {
            $links['first'] = array(
                'target' => 1,
                'text' => $this->modx->getOption('pagination_first_text', $this->discuss->forumConfig),
                'class' => $this->modx->getOption('pagination_first_class', $this->discuss->forumConfig)

            );
        }
        if ($this->modx->getOption('pagination_show_prev_next', $this->discuss->forumConfig, 'Yes') == 'Yes' && $page > 1) {
            $links['prev'] = array(
                'target' => ($page - 1),
                'text' => $this->modx->getOption('pagination_prev_text', $this->discuss->forumConfig),
                'class' => $this->modx->getOption('pagination_previous_class', $this->discuss->forumConfig)
            );
        }
        // First two
        $links = array_merge($links, range(1,2));

        // Middle links
        $links = array_unique(array_merge($links, range((($page - 1) <= 0) ? 1 : $page - 1, ($page >= $pages) ? $pages : ($page + 1))), SORT_REGULAR);

        // Last two
        $links = array_unique(array_merge($links, range(($pages -1), $pages)), SORT_REGULAR);
        // Added ellipses if needed
        if ($page > 4 && $pages >= 5) {
            array_splice($links, 2, 0, array(
                'target' => null,
                'text' => $this->modx->getOption('pagination_proximity_placeholder', $this->discuss->forumConfig)
            ));
        }
        if ($pages > 5 && $page < ($pages - 3)) {
            array_splice($links, (count($links) - 2), 0,  array(
                'target' => null,
                'text' => $this->modx->getOption('pagination_proximity_placeholder', $this->discuss->forumConfig)
            ));
        }

        // Next && Last
        if ($this->modx->getOption('pagination_show_prev_next', $this->discuss->forumConfig, 'Yes') == 'Yes' && $page < $pages) {
            $links['next'] = array(
                'target' => ($page + 1),
                'text' => $this->modx->getOption('pagination_next_text', $this->discuss->forumConfig),
                'class' => $this->modx->getOption('pagination_next_class', $this->discuss->forumConfig)
            );
        }
        if ($this->modx->getOption('pagination_show_first_last', $this->discuss->forumConfig, 'Yes') == 'Yes' && $page < $pages) {
            $links['last'] = array(
                'target' => $pages,
                'text' => $this->modx->getOption('pagination_last_text', $this->discuss->forumConfig),
                'class' => $this->modx->getOption('pagination_last_class', $this->discuss->forumConfig)
            );
        }

        $temp = array();
        $resourceId = $this->modx->resource->id;

        $pageVar = $this->modx->getOption('pagination_var', $this->discuss->forumConfig, 'page');

        $container = $this->modx->getOption('pagination_container', $this->discuss->forumConfig, 'sample.container');
        $item = $this->modx->getOption('pagination_item_chunk', $this->discuss->forumConfig, 'sample.item');
        $emptyitem = $this->modx->getOption('pagination_empty_item_chunk', $this->discuss->forumConfig, 'sample.empty_item');
        foreach($links as $link) {
            if (is_array($link)) {
                if ($link['target'] == null) {
                    $temp[] = $this->discuss->getChunk($emptyitem, array(
                        'text' => $link['text'],
                        'class' => 'empty'
                    ));
                } else {
                    $temp[] = $this->discuss->getChunk($item, array(
                        'link' => $this->modx->discuss2->makeUrl($resourceId, '', '',array($pageVar => $link['target'])),
                        'text' => $link['text'],
                        'class' => $link['class']
                    ));
                }

            } else {
                $temp[] = $this->discuss->getChunk($item, array(
                    'link' => $this->modx->discuss2->makeUrl($resourceId, '','',array($pageVar => $link)),
                    'text' => $link,
                    'class' => 'pagination-item'
                ));
            }
        }
        return $this->discuss->getChunk($container, array(
            'links' => implode("\n", $temp)
        ));
    }

    public function processThreadPagination($resource, $count, $perPageVar) {
        $container = $this->modx->getOption('pagination_container', $this->discuss->forumConfig, 'sample.container');
        $item = $this->modx->getOption('pagination_item_chunk', $this->discuss->forumConfig, 'sample.item');
        $emptyitem = $this->modx->getOption('pagination_empty_item_chunk', $this->discuss->forumConfig, 'sample.empty_item');

        $page = 1;
        $perPage = $this->discuss->forumConfig[$perPageVar];
        $pages = ceil($count / $perPage);

        if ($pages <= 1) {
            return '';
        } else if ($page > $pages) {
            $page = $pages;
        } else if ($page < 1) {
            $page = 1;
        }
        $page = intval($page);

        $this->current = $page;

        // First two
        $links = range(1,2);
        // Middle links
        $links = array_unique(array_merge($links, range((($page - 1) <= 0) ? 1 : $page - 1, ($page >= $pages) ? $pages : ($page + 1))), SORT_REGULAR);
        // Last two
        $links = array_unique(array_merge($links, range(($pages -1), $pages)), SORT_REGULAR);

        // Added ellipses if needed
        if ($page > 4 && $pages >= 5) {
            array_splice($links, 2, 0, array(
                'target' => null,
                'text' => $this->modx->getOption('pagination_proximity_placeholder', $this->discuss->forumConfig)
            ));
        }
        if ($pages > 5 && $page < ($pages - 3)) {
            array_splice($links, (count($links) - 2), 0,  array(
                'target' => null,
                'text' => $this->modx->getOption('pagination_proximity_placeholder', $this->discuss->forumConfig)
            ));
        }
        $temp = array();
        $resourceId = $this->modx->resource->id;
        $pageVar = $this->modx->getOption('pagination_var', $this->discuss->forumConfig, 'page');

        foreach($links as $link) {
            if (is_array($link)) {
                if ($link['target'] == null) {
                    $temp[] = $this->discuss->getChunk($emptyitem, array(
                        'text' => $link['text']
                    ));
                } else {
                    $temp[] = $this->discuss->getChunk($item, array(
                        'link' => $this->modx->discuss2->makeUrl($resourceId, '', '', array($pageVar => $link['target'])),
                        'text' => $link['text']
                    ));
                }

            } else {
                $temp[] = $this->discuss->getChunk($item, array(
                    'link' => $this->modx->discuss2->makeUrl($resourceId, '', '', array($pageVar => $link)),
                    'text' => $link['text']
                ));
            }
        }
        return $this->discuss->getChunk($container, array(
            'links' => implode("\n", $temp)
        ));
    }
}