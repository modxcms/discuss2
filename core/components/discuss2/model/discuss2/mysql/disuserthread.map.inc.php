<?php
/**
 * @package Discuss
 * @subpackage mysql
 */
$xpdo_meta_map['disUserThread']= array (
  'package' => 'discuss2',
  'version' => '1.1',
  'table' => 'user_thread',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'title' => NULL,
    'messages' => 0,
    'createdon' => 'CURRENT_TIMESTAMP',
    'lastpost' => 'CURRENT_TIMESTAMP',
  ),
  'fieldMeta' => 
  array (
    'title' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
    ),
    'messages' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'null' => false,
      'attributes' => 'unsigned',
      'default' => 0,
      'phptype' => 'integer',
    ),
    'createdon' => 
    array (
      'dbtype' => 'timestamp',
      'phptype' => 'datetime',
      'default' => 'CURRENT_TIMESTAMP',
      'null' => false,
    ),
    'lastpost' => 
    array (
      'dbtype' => 'timestamp',
      'phptype' => 'datetime',
      'default' => 'CURRENT_TIMESTAMP',
      'null' => false,
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
    'lastpost_idx' => 
    array (
      'alias' => 'lastpost',
      'unique' => false,
      'primary' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'lastpost' => 
        array (
          'null' => false,
          'collation' => 'A',
          'length' => '',
        ),
      ),
    ),
  ),
);
