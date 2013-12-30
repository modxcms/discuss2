<?php
/**
 * @package Discuss
 * @subpackage mysql
 */
$xpdo_meta_map['disThreadProperty']= array (
  'package' => 'discuss2',
  'version' => '1.1',
  'table' => 'thread_properties',
  'extends' => 'xPDOObject',
  'fields' => 
  array (
    'idx' => NULL,
    'views' => 0,
    'posts' => 0,
    'answered' => 0,
    'sticky' => 0,
    'locked' => 0,
  ),
  'fieldMeta' => 
  array (
    'idx' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'null' => false,
    ),
    'views' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'null' => false,
      'default' => 0,
    ),
    'posts' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'null' => false,
      'default' => 0,
    ),
    'answered' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'null' => false,
      'default' => 0,
    ),
    'sticky' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'null' => false,
      'default' => 0,
    ),
    'locked' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'null' => false,
      'default' => 0,
    ),
  ),
  'indexes' => 
  array (
    'PRIMARY' => 
    array (
      'alias' => 'PRIMARY',
      'type' => 'BTREE',
      'primary' => true,
      'unique' => true,
      'columns' => 
      array (
        'idx' => 
        array (
          'collation' => 'A',
          'length' => '',
          'null' => false,
        ),
      ),
    ),
    'view_idx' => 
    array (
      'alias' => 'view_idx',
      'unique' => false,
      'primary' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'views' => 
        array (
          'collation' => 'A',
          'null' => false,
          'length' => '',
        ),
      ),
    ),
    'locked_idx' => 
    array (
      'alias' => 'locked',
      'type' => 'BTREE',
      'primary' => false,
      'unique' => false,
      'columns' => 
      array (
        'locked' => 
        array (
          'collation' => 'A',
          'length' => '',
          'null' => false,
        ),
      ),
    ),
    'sticky_idx' => 
    array (
      'alias' => 'sticky',
      'type' => 'BTREE',
      'primary' => false,
      'unique' => false,
      'columns' => 
      array (
        'sticky' => 
        array (
          'collation' => 'A',
          'length' => '',
          'null' => false,
        ),
      ),
    ),
    'answered_idx' => 
    array (
      'alias' => 'answered',
      'type' => 'BTREE',
      'primary' => false,
      'unique' => false,
      'columns' => 
      array (
        'answered' => 
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
    'Thread' => 
    array (
      'class' => 'disThread',
      'cardinality' => 'one',
      'foreign' => 'id',
      'local' => 'id',
      'owner' => 'foreign',
    ),
  ),
);
