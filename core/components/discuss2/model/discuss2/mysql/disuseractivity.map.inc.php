<?php
/**
 * @package Discuss
 * @subpackage mysql
 */
$xpdo_meta_map['disUserActivity']= array (
  'package' => 'discuss2',
  'version' => '1.1',
  'table' => 'activity',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'internalKey' => NULL,
    'createdon' => 'CURRENT_TIMESTAMP',
    'action' => NULL,
    'data' => NULL,
    'ip' => 0,
  ),
  'fieldMeta' => 
  array (
    'internalKey' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'null' => false,
    ),
    'createdon' => 
    array (
      'dbtype' => 'timestamp',
      'phptype' => 'timestamp',
      'null' => false,
      'default' => 'CURRENT_TIMESTAMP',
    ),
    'action' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
    ),
    'data' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'json',
      'null' => false,
    ),
    'ip' => 
    array (
      'dbtype' => 'int',
      'phptype' => 'int',
      'attributes' => 'unsigned',
      'default' => 0,
      'null' => false,
    ),
  ),
  'indexes' => 
  array (
    'createdon_idx' => 
    array (
      'alias' => 'createdon_idx',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'createdon' => 
        array (
          'collation' => 'A',
          'length' => '',
          'null' => false,
        ),
      ),
    ),
    'internalKey_idx' => 
    array (
      'alias' => 'internalKey_idx',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'internalKey' => 
        array (
          'collation' => 'A',
          'length' => '',
          'null' => false,
        ),
      ),
    ),
  ),
  'aggregates' => 
  array (
    'User' => 
    array (
      'class' => 'disUser',
      'local' => 'internalKey',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
