<?php
/**
 * @package Discuss
 * @subpackage mysql
 */
$xpdo_meta_map['disUserThreadPost']= array (
  'package' => 'discuss2',
  'version' => '1.1',
  'table' => 'user_thread_post',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'thread' => NULL,
    'content' => NULL,
    'author' => NULL,
    'createdon' => 'CURRENT_TIMESTAMP',
    'editedon' => NULL,
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
    ),
    'content' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => false,
    ),
    'author' => 
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
      'phptype' => 'datetime',
      'default' => 'CURRENT_TIMESTAMP',
      'null' => false,
    ),
    'editedon' => 
    array (
      'dbtype' => 'timestamp',
      'phptype' => 'datetime',
      'attributes' => 'ON UPDATE CURRENT_TIMESTAMP',
    ),
  ),
  'indexes' => 
  array (
    'createdon_idx' => 
    array (
      'alias' => 'createdon',
      'unique' => false,
      'primary' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'createdon' => 
        array (
          'null' => false,
          'collation' => 'A',
          'length' => '',
        ),
      ),
    ),
    'author_idx' => 
    array (
      'alias' => 'author',
      'unique' => false,
      'primary' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'author' => 
        array (
          'null' => false,
          'collation' => 'A',
          'length' => '',
        ),
      ),
    ),
    'thread_idx' => 
    array (
      'alias' => 'thread',
      'unique' => false,
      'primary' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'thread' => 
        array (
          'null' => false,
          'collation' => 'A',
          'length' => '',
        ),
      ),
    ),
  ),
);
