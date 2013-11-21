<?php
/**
 * @package Discuss
 * @subpackage mysql
 */
$xpdo_meta_map['disClosure']= array (
  'package' => 'discuss2',
  'version' => '1.1',
  'table' => 'closure',
  'extends' => 'xPDOObject',
  'fields' => 
  array (
    'ancestor' => NULL,
    'descendant' => NULL,
    'depth' => NULL,
  ),
  'fieldMeta' => 
  array (
    'ancestor' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'null' => false,
      'index' => 'pk',
    ),
    'descendant' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'null' => false,
      'index' => 'pk',
    ),
    'depth' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '3',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'null' => false,
    ),
  ),
  'indexes' => 
  array (
    'primary' => 
    array (
      'alias' => 'primary',
      'primary' => true,
      'unique' => true,
      'type' => 'BTREE',
      'columns' => 
      array (
        'ancestor' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
        'descendant' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
  'aggregates' => 
  array (
    'Ancestor' => 
    array (
      'class' => 'disBoard',
      'local' => 'ancestor',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'Descendant' => 
    array (
      'class' => 'disBoard',
      'local' => 'descendant',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
