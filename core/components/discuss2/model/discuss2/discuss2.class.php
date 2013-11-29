<?php

class Discuss2 {
    public $modx;

    public $config;
    public $forumConfig = array();
    public $user;

    public $parser;
    public $stats = null;
    public $pagination = null;
    public $breadcrumb = null;

    private $_cacheOptions = array();

    public function __construct(modX &$modx, array $config = array()) {
        $this->modx = $modx;
        $corePath = $this->modx->getOption('discuss2.core_path',$config,$this->modx->getOption('core_path').'components/discuss2/');
        $assetsUrl = $this->modx->getOption('discuss2.assets_url',$config,$this->modx->getOption('assets_url').'components/discuss2/');

        $this->config = array_merge(array(
            'assetsUrl' => $assetsUrl,
            'cssUrl' => $assetsUrl.'css/',
            'jsUrl' => $assetsUrl.'js/',
            'imagesUrl' => $assetsUrl.'images/',

            'connectorUrl' => $assetsUrl.'connector.php',

            'corePath' => $corePath,
            'modelPath' => $corePath.'model/',
            'elementsPath' => $corePath.'elements/',
            'snippetsPath' => $corePath.'elements/snippets/',
            'tvsPath' => $corePath.'elements/tvs/',
            'chunksPath' => $corePath.'elements/chunks/',
            'chunkSuffix' => '.chunk.tpl',
            'processorsPath' => $corePath.'processors/',
        ),$config);
    }

    public function init() {
        $this->loadConfig();
        $this->loadUser();
        $this->loadStats();
        if ($this->stats !== null) {
            $this->stats->process();
        }
        $this->buildPath();
    }

    public function buildPath() {
        if ($this->breadcrumb === null) {
            $this->breadcrumb = $this->modx->getService('disBreadcrumb','disBreadcrumb',$this->config['modelPath'].'discuss2/utils/');
        }
        $this->breadcrumb->process();
    }

    public function escapeJoin($conditions = array()) {
        foreach ($conditions as $key => $condition) {
            $parts = explode('=', $condition);
            foreach($parts as & $part) {
                if ($part == '=') {
                    continue;
                }
                $temp = explode('.', trim($part));
                $part = implode(".", array_map(array($this->modx, 'escape'), $temp));
            }
            $conditions[$key] = implode(" = ", $parts);
        }
        return $conditions;
    }

    /** Deprecated,
     * User getMergeConfig */
    public function getResourceForum($resourceId) {
        $c = $this->modx->newQuery('disForum');
        $c->innerJoin('disClosure', 'c', "disForum.id = c.ancestor AND c.descendant = {$this->modx->quote($resourceId)}");
        $c->where(array('class_key' => 'disForum'));
        $c->prepare();
        $obj = $this->modx->getObject('disForum', $c);
        if (!$obj instanceof disForum) {
            $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'Could not fetch Discuss2 forum resource for resource id : ' . $resourceId);
        }
        return $obj;
    }

    public function closestConfigParent($resourceId) {
        $c = $this->modx->newQuery('disForum');
        $c->select(array('id', 'class_key'));
        $c->innerJoin('disClosure', 'c', "disForum.id = c.ancestor AND c.descendant = {$resourceId}");
        $c->where(array('class_key' => 'disBoard'));
        $c->limit(1);
        $c->prepare();
        $c->stmt->execute();
        return $c->stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getMergeConfig($resourceId) {
        $c = $this->modx->newQuery('disForum');
        $c->select(array('properties'));
        $c->innerJoin('disClosure', 'closure', "{$this->modx->escape('closure')}.{$this->modx->escape('ancestor')} = {$this->modx->escape('disForum')}.{$this->modx->escape('id')}
            AND {$this->modx->escape('closure')}.{$this->modx->escape('descendant')} = $resourceId");
        $c->where(array('class_key:IN' => array('disBoard', 'disCategory', 'disForum')));
        $c->sortby("{$this->modx->escape('closure')}.{$this->modx->escape('depth')}", 'DESC');
        $c->prepare();
        $c->stmt->execute();
        $properties = array();
        foreach ($c->stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $properties = array_merge($properties, $this->modx->fromJSON($row['properties']));
        }

        return $properties;
    }

    public function getParentTemplate($resourceId) {
        $c = $this->modx->newQuery('modResource');
        $c->select(array(
            $this->modx->getSelectColumns('modResource', 'modResource', '', array('id', 'template'))
        ));
        $c->innerJoin('disClosure', 'c', "modResource.id = c.ancestor AND c.descendant = {$this->modx->quote($resourceId)}");
        $c->where(array('template:>' => 0));
        $c->sortby('id', 'DESC');
        $c->limit(1);
        $obj = $this->modx->getObject('modResource', $c);
        return $obj->template;
    }
    /**
     * Gets a Chunk and caches it; also falls back to file-based templates
     * for easier debugging.
     *
     * @access public
     * @param string $name The name of the Chunk
     * @param array $properties The properties for the Chunk
     * @return string The processed content of the Chunk
     */
    public function getChunk($name,array $properties = array()) {
        $chunk = null;
        if (!isset($this->chunks[$name])) {
            $chunk = $this->modx->getObject('modChunk',array('name' => $name),true);
            if (empty($chunk)) {
                $chunk = $this->_getTplChunk($name);
                if ($chunk == false) return false;
            }
            $this->chunks[$name] = $chunk->getContent();
        } else {
            $o = $this->chunks[$name];
            $chunk = $this->modx->newObject('modChunk');
            $chunk->setContent($o);
        }
        $chunk->setCacheable(false);
        return $chunk->process($properties);
    }

    /**
     * Returns a modChunk object from a template file.
     *
     * @access private
     * @param string $name The name of the Chunk. Will parse to name.chunk.tpl
     * @return modChunk/boolean Returns the modChunk object if found, otherwise
     * false.
     */
    private function _getTplChunk($name) {
        $chunk = false;
        if (strpos($name, '.') !== false) {
            $path = str_replace('.', '/', $name);
            $name = substr($name, strrpos($name, '/'));
        } else {
            $path = $name;
        }

        $f = $this->config['chunksPath'].strtolower($path).'.chunk.tpl';
        if (file_exists($f)) {
            $o = file_get_contents($f);
            $chunk = $this->modx->newObject('modChunk');
            $chunk->set('name',$name);
            $chunk->setContent($o);
        }
        return $chunk;
    }

    public function getLastPostLink($threadId, $postId) {
        $posts = $this->modx->getObject('disThreadProperty', $threadId);
        if ($posts instanceof xPDOObject) {
            $perPage = $this->forumConfig['posts_per_page'];
            if ($posts->posts <= $perPage) {
                return $this->modx->makeUrl($threadId) . "#post-{$postId}";

            }
            $pages = ceil($posts/$perPage);

            return $this->modx->makeUrl($threadId, '', array('page' => $pages)) . "#post-{$postId}";
        }
    }

    public function getUser() {
        return $this->user;
    }

    /**
     * Shortcut to MODX runProcessor with correct Discuss2 processor path
     *
     * @param string $action
     * @param array $scriptProperties
     * @param array $options
     * @return mixed
     */
    public function runProcessor($action = '',$scriptProperties = array(),$options = array()) {
        $options['processors_path'] = $this->config['processorsPath'];
        return $this->modx->runProcessor($action, $scriptProperties, $options);
    }

    public function loadConfig($id = null) {
        if (!empty($this->forumConfig)) {
            return $this->forumConfig;
        }
        if ($this->modx->resource->class_key == 'disPost' || $this->modx->resource instanceof disThread ) {
            $configToLoad = $this->closestConfigParent($this->modx->resource->id);
        } else {
            $configToLoad = array('id' => $this->modx->resource->id, 'class_key' => $this->modx->resource->class_key);
        }

        if (!$config = $this->modx->getCacheManager()->get("discuss2/configurations/{$configToLoad['class_key']}/{$configToLoad['class_key']}-{{$configToLoad['id']}}")) {
            $properties = $this->getMergeConfig($this->modx->resource->id);

            $this->forumConfig = $properties;
            $this->writeConfig($properties, $configToLoad['class_key'], $configToLoad['id']);
        }  else {
            if (!is_array($config)) {
                $this->forumConfig = $this->modx->fromJSON($config);
            }
            $this->forumConfig = $config;
        }

        return $this->forumConfig;
    }

    /**
     * Load the Parsing class for the post
     * @return disParser
     */
    public function loadParser() {
        if (empty($this->parser)) {
            $parserClass = $this->modx->getOption('parser_class',$this->forumConfig,'disBBCodeParser');
            $parserClassPath = $this->modx->getOption('parser_class_path',$this->forumConfig);
            if (empty($parserClassPath)) {
                $parserClassPath = $this->config['modelPath'].'discuss2/parser/';
            }
            $this->parser = $this->modx->getService('disParser',$parserClass,$parserClassPath);
        }
        return $this->parser;
    }

    public function loadStats() {
        if ($this->stats === null) {
            $this->stats = $this->modx->getService('disStats', 'disStats', $this->config['modelPath'].'discuss2/utils/');
        }
        return $this->stats;
    }
    /**
     * @param mixed $forum
     * @return bool
     */
    public function writeConfig($properties, $class, $id) {
        $cm = $this->modx->getCacheManager();
        if (is_string($properties)) {
            $properties = $this->modx->fromJSON($properties);
        }
        if (!$cm->set("discuss2/configurations/{$class}/{$class}-{$id}", $properties, 0)) {
            $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'Discuss2 could not write config file');
        }
    }

    public function loadUser() {
        if (!isset($this->modx->user) || $this->modx->user->hasSessionContext($this->modx->context->key)) {
            $this->modx->log(xPDO::LOG_LEVEL_ERROR, 'Could not load disUser : modUser is not initialized or has no session context');
            return false;
        }
        $this->user = $this->modx->getObject('disUser', $this->modx->user->id);
        if (!$this->user instanceof modUser) {

        }
    }

    public function loadPagination() {
        if ($this->pagination === null) {
            $this->pagination = $this->modx->getService('disPagination', 'dispagination', $this->config['modelPath'].'discuss2/utils/');
        }
        return $this->pagination;
    }
    /**
     * Similar to hydrateGraph but only returns assoc array and no objects
     */

    public function hydrateRow($row, &$instances = array(), $slices = 0) {
        $classAlias = substr(key($row), 0, strpos(key($row), '_'));
        $i = 0;
        if (!isset($row["{$classAlias}_id"])) {
            if ($slices < count($row)) {
                $row = array_slice($row, $slices);
                $classAlias = substr(key($row), 0, strpos(key($row), '_'));
            } else {
                return;
            }

        }
        foreach($row as $k => $v) {
            if (strpos($k, $classAlias) === 0) {
                $key = str_replace($classAlias."_", '', $k);
                $instances[$row[$classAlias."_id"]][$key] = $v;
            } else {
                reset($row);
                break;
            }
            $i++;
        }

        if ($i < count($row)) {
            $this->hydrateRow(array_slice($row, $i), $instances, $i);
        }
        return $instances;
    }

    public function createTree($itemList, $parentId = null) {
        if ($parentId == null) {
            $temp = reset($itemList);
            $itemList[] = array('id' => $temp['parent']);
            $parentId = $temp['parent'];
        }
        $result = array();
        foreach($itemList as $item) {
            if ($item['parent'] == $parentId) {
                $children = $this->createTree($itemList, $item['id']);
                if ($children) {
                    foreach ($children as $child) {
                        $item[$child['class_key']][] = $child;
                    }
                }
                $result[] = $item;
            }
        }
        return empty($result) ? false : $result;
    }
}