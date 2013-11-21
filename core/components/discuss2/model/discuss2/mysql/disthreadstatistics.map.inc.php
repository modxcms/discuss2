<?php
/**
 * @package Discuss
 * @subpackage mysql
 */
$xpdo_meta_map['disThreadStatistics']= array (
  'package' => 'discuss2',
  'version' => '1.1',
  'table' => 'thread_statistics',
  'extends' => 'xPDOObject',
  'fields' => 
  array (
    'idx' => NULL,
    'views' => 0,
    'posts' => 0,
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
