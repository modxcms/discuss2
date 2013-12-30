<?php
/**
 * @package Discuss
 * @subpackage mysql
 */
$xpdo_meta_map['disSubscription']= array (
  'package' => 'discuss2',
  'version' => '1.1',
  'table' => 'thread_subscriptions',
  'extends' => 'xPDOObject',
  'fields' => 
  array (
    'thread' => NULL,
    'user' => NULL,
  ),
  'fieldMeta' => 
  array (
    'thread' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'null' => false,
      'index' => 'pk',
    ),
    'user' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'null' => false,
      'index' => 'pk',
    ),
  ),
  'indexes' => 
  array (
    'PRIMARY' => 
    array (
      'alias' => 'PRIMARY',
      'primary' => true,
      'unique' => true,
      'type' => 'BTREE',
      'columns' => 
      array (
        'thread' => 
        array (
          'collation' => 'A',
          'length' => '',
          'null' => false,
        ),
        'user' => 
        array (
          'collation' => 'A',
          'length' => '',
          'null' => false,
        ),
      ),
    ),
  ),
);
