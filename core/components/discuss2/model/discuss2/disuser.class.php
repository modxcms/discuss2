<?php
/**
 * @package Discuss
 * @subpackage mysql
 */
class disUser extends modUser {

    /**
     * Wrap extended used tables to object
     */
    public static function & _loadRows(& $xpdo, $className, $criteria) {
        // Modify criteria partially. Loads all extra fields for disUser
        $criteria->query['columns'] = array();
        $criteria->select(array(
            $xpdo->getSelectColumns('disUser', 'disUser'),
            $xpdo->getSelectColumns('modUserProfile', 'Profile'),
            $xpdo->getSelectColumns('disUserProfile', 'disProfile', '', array('internalKey'), true)
        ));
        $criteria->leftJoin('modUserProfile', 'Profile');
        $criteria->leftJoin('disUserProfile', 'disProfile');
        $criteria->prepare();
        $rows= null;
        $rows = parent::_loadRows($xpdo, $className, $criteria);
        return $rows;
    }

    /**
     * Load an instance of an modDisUser or derivative class.
     */
    public static function load(xPDO & $xpdo, $className, $criteria, $cacheFlag= true) {
        $instance= null;
        $fromCache= false;
        if ($className= $xpdo->loadClass($className)) {
            if (!is_object($criteria)) {
                $criteria= $xpdo->getCriteria($className, $criteria, $cacheFlag);
                $criteria->prepare();
            }
            if (is_object($criteria)) {
                //$criteria = $xpdo->addDerivativeCriteria($className, $criteria); Do not want to assign class_key
                $row= null;
                if ($xpdo->_cacheEnabled && $criteria->cacheFlag && $cacheFlag) {
                    $row= $xpdo->fromCache($criteria, $className);
                }
                if ($row === null || !is_array($row)) {
                    if ($rows= disUser :: _loadRows($xpdo, $className, $criteria)) {
                        $row= $rows->fetch(PDO::FETCH_ASSOC);
                        $rows->closeCursor();
                    }
                } else {
                    $fromCache= true;
                }
                if (!is_array($row)) {
                    if ($xpdo->getDebug() === true) $xpdo->log(xPDO::LOG_LEVEL_DEBUG, "Fetched empty result set from statement: " . print_r($criteria->sql, true) . " with bindings: " . print_r($criteria->bindings, true));
                } else {
                    $instance= disUser :: _loadInstance($xpdo, $className, $criteria, $row);
                    if (is_object($instance)) {
                        if (!$fromCache && $cacheFlag && $xpdo->_cacheEnabled) {
                            $xpdo->toCache($criteria, $instance, $cacheFlag);
                            if ($xpdo->getOption(xPDO::OPT_CACHE_DB_OBJECTS_BY_PK) && ($cacheKey= $instance->getPrimaryKey()) && !$instance->isLazy()) {
                                $pkCriteria = $xpdo->newQuery($className, $cacheKey, $cacheFlag);
                                $xpdo->toCache($pkCriteria, $instance, $cacheFlag);
                            }
                        }
                        if ($xpdo->getDebug() === true) $xpdo->log(xPDO::LOG_LEVEL_DEBUG, "Loaded object instance: " . print_r($instance->toArray('', true), true));
                    }
                }
            } else {
                $xpdo->log(xPDO::LOG_LEVEL_ERROR, 'No valid statement could be found in or generated from the given criteria.');
            }
        } else {
            $xpdo->log(xPDO::LOG_LEVEL_ERROR, 'Invalid class specified: ' . $className);
        }
        return $instance;
    }

    /**
     * Responsible for loading an instance into a collection.
     *
     * @static
     * @param xPDO &$xpdo A valid xPDO instance.
     * @param array &$objCollection The collection to load the instance into.
     * @param string $className Name of the class.
     * @param mixed $criteria A valid primary key, criteria array, or xPDOCriteria instance.
     * @param boolean|integer $cacheFlag Indicates if the objects should be cached and
     * optionally, by specifying an integer value, for how many seconds.
     */
    public static function _loadCollectionInstance(xPDO & $xpdo, array & $objCollection, $className, $criteria, $row, $fromCache, $cacheFlag=true) {
        $loaded = false;
        if ($obj= disUser :: _loadInstance($xpdo, $className, $criteria, $row)) {
            if (($cacheKey= $obj->getPrimaryKey()) && !$obj->isLazy()) {
                if (is_array($cacheKey)) {
                    $pkval= implode('-', $cacheKey);
                } else {
                    $pkval= $cacheKey;
                }
                /* set OPT_CACHE_DB_COLLECTIONS to 2 to cache instances by primary key from collection result sets */
                if ($xpdo->getOption(xPDO::OPT_CACHE_DB_COLLECTIONS, array(), 1) == 2 && $xpdo->_cacheEnabled && $cacheFlag) {
                    if (!$fromCache) {
                        $pkCriteria = $xpdo->newQuery($className, $cacheKey, $cacheFlag);
                        $xpdo->toCache($pkCriteria, $obj, $cacheFlag);
                    } else {
                        $obj->_cacheFlag= true;
                    }
                }
                $objCollection[$pkval]= $obj;
                $loaded = true;
            } else {
                $objCollection[]= $obj;
                $loaded = true;
            }
        }
        return $loaded;
    }

    /**
     * Load a collection of disUser instances.
     */
    public static function loadCollection(xPDO & $xpdo, $className, $criteria= null, $cacheFlag= true) {
        $objCollection= array ();
        $fromCache = false;
        if (!$className= $xpdo->loadClass($className)) return $objCollection;
        $rows= false;
        $fromCache= false;
        $collectionCaching = (integer) $xpdo->getOption(xPDO::OPT_CACHE_DB_COLLECTIONS, array(), 1);
        if (!is_object($criteria)) {
            $criteria= $xpdo->getCriteria($className, $criteria, $cacheFlag);
        }
        // No derivative wanted
        if ($collectionCaching > 0 && $xpdo->_cacheEnabled && $cacheFlag) {
            $rows= $xpdo->fromCache($criteria);
            $fromCache = (is_array($rows) && !empty($rows));
        }
        if (!$fromCache && is_object($criteria)) {
            $rows= disUser :: _loadRows($xpdo, $className, $criteria);
        }
        if (is_array ($rows)) {
            foreach ($rows as $row) {
                disUser :: _loadCollectionInstance($xpdo, $objCollection, $className, $criteria, $row, $fromCache, $cacheFlag);
            }
        } elseif (is_object($rows)) {
            $cacheRows = array();
            while ($row = $rows->fetch(PDO::FETCH_ASSOC)) {
                disUser :: _loadCollectionInstance($xpdo, $objCollection, $className, $criteria, $row, $fromCache, $cacheFlag);
                if ($collectionCaching > 0 && $xpdo->_cacheEnabled && $cacheFlag && !$fromCache) $cacheRows[] = $row;
            }
            if ($collectionCaching > 0 && $xpdo->_cacheEnabled && $cacheFlag && !$fromCache) $rows =& $cacheRows;
        }
        if (!$fromCache && $xpdo->_cacheEnabled && $collectionCaching > 0 && $cacheFlag && !empty($rows)) {
            $xpdo->toCache($criteria, $rows, $cacheFlag);
        }
        return $objCollection;
    }

    /**
     * Load a collection of modDisUser instances and a graph of related objects.
     */
    public static function loadCollectionGraph(xPDO & $xpdo, $className, $graph, $criteria, $cacheFlag) {
        $objCollection = array();
        if ($query= $xpdo->newQuery($className, $criteria, $cacheFlag)) {
            $query->bindGraph($graph);
            $rows = array();
            $fromCache = false;
            $collectionCaching = (integer) $xpdo->getOption(xPDO::OPT_CACHE_DB_COLLECTIONS, array(), 1);
            if ($collectionCaching > 0 && $xpdo->_cacheEnabled && $cacheFlag) {
                $rows= $xpdo->fromCache($query);
                $fromCache = !empty($rows);
            }
            if (!$fromCache) {
                if ($query->prepare()) {
                    if ($query->stmt->execute()) {
                        $objCollection= $query->hydrateGraph($query->stmt, $cacheFlag);
                    } else {
                        $xpdo->log(xPDO::LOG_LEVEL_ERROR, "Error {$query->stmt->errorCode()} executing query: {$query->sql} - " . print_r($query->stmt->errorInfo(), true));
                    }
                } else {
                    $xpdo->log(xPDO::LOG_LEVEL_ERROR, "Error {$xpdo->errorCode()} preparing statement: {$query->sql} - " . print_r($xpdo->errorInfo(), true));
                }
            } elseif (!empty($rows)) {
                $objCollection= $query->hydrateGraph($rows, $cacheFlag);
            }
        }
        return $objCollection;
    }

    /**
     * Loads an instance from an associative array.
     *
     * @static
     * @param xPDO &$xpdo A valid xPDO instance.
     * @param string $className Name of the class.
     * @param xPDOQuery|string $criteria A valid xPDOQuery instance or relation alias.
     * @param array $row The associative array containing the instance data.
     * @return xPDOObject A new xPDOObject derivative representing a data row.
     */
    public static function _loadInstance(& $xpdo, $className, $criteria, $row) {
        $rowPrefix= '';
        if (is_object($criteria) && $criteria instanceof xPDOQuery) {
            $alias = $criteria->getAlias();
            $actualClass = $criteria->getClass();
        } elseif (is_string($criteria) && !empty($criteria)) {
            $alias = $criteria;
            $actualClass = $className;
        } else {
            $alias = $className;
            $actualClass= $className;
        }
        // Removed possibility to overload which class will be instantiated using class_key field
        /** @var xPDOObject $instance */
        $instance= $xpdo->newObject($actualClass);
        if (is_object($instance) && $instance instanceof xPDOObject) {
            $pk = $xpdo->getPK($actualClass);
            if ($pk) {
                if (is_array($pk)) $pk = reset($pk);
                if (isset($row["{$alias}_{$pk}"])) {
                    $rowPrefix= $alias . '_';
                }
                elseif ($actualClass !== $className && $actualClass !== $alias && isset($row["{$actualClass}_{$pk}"])) {
                    $rowPrefix= $actualClass . '_';
                }
                elseif ($className !== $alias && isset($row["{$className}_{$pk}"])) {
                    $rowPrefix= $className . '_';
                }
            } elseif (strpos(strtolower(key($row)), strtolower($alias . '_')) === 0) {
                $rowPrefix= $alias . '_';
            } elseif (strpos(strtolower(key($row)), strtolower($className . '_')) === 0) {
                $rowPrefix= $className . '_';
            }
            $parentClass = $className;
            $isSubPackage = strpos($className,'.');
            if ($isSubPackage !== false) {
                $parentClass = substr($className,$isSubPackage+1);
            }
            if (!$instance instanceof $parentClass) {
                $xpdo->log(xPDO::LOG_LEVEL_ERROR, "Instantiated a derived class {$actualClass} that is not a subclass of the requested class {$className}");
            }
            $instance->_lazy= $actualClass !== $className ? array_keys($xpdo->getFieldMeta($actualClass)) : array_keys($instance->_fieldMeta);
            $instance->fromArray($row, $rowPrefix, true, true);
            $instance->_dirty= array ();
            $instance->_new= false;
        }
        return $instance;
    }

    public function save($cacheFlag = false) {

        $related = array(
            'modUserProfile' => self::_loadInstance($this->xpdo, 'modUserProfile', 'modUserProfile', array('internalKey' => $this->get('id'))),
            'disUserProfile' => self::_loadInstance($this->xpdo, 'disUserProfile', 'disUserProfile', array('internalKey' => $this->get('id')))
        );
        if (!$related['disUserProfile'] instanceof xPDOObject) {
            $related['disUserProfile'] = $this->xpdo->newObject('disUserProfile', array('internalKey' => $this->id));
        }

        $values = array();
        $extended = array();
        foreach($this->_fields as $key => $value) {
            // Find which object(s) has the field
            foreach($related as $k => $v) {
                if (array_key_exists($key, $v->_fields)) {
                    $values[$k][$key] = $value;
                } else {
                    // Set field to extended for post processing after loop
                    $extended[$key] = $value;
                }
            }
        }
        // Remove modUser/disUser fields from extended
        $extended = array_diff_assoc($extended, $this->xpdo->getFields($this->_class));
        if (!empty($extended)) {
            $values['modUserProfile']['extended'] = $extended;
        }
        $tempFields = $this->_fields;
        $this->_fields = array_intersect_key($this->_fields, $this->xpdo->getFields($this->_class));
        $this->_dirty = array_intersect_key($this->_dirty, $this->xpdo->getFields($this->_class));
        foreach($related as $rel => $val) {
            $val->fromArray($values[$rel]);
            $saved = $related[$rel]->save($cacheFlag);
            if ($saved === false) {
                $this->xpdo->log(xPDO::LOG_LEVEL_ERROR, "Could not save related object of disUser {$rel}");
            }
        }
        $saved = parent::save($cacheFlag);
        $this->_fields= $tempFields;
        return $saved;

    }
}