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
    'createdby' => 0,
    'lastpost' => 'CURRENT_TIMESTAMP',
    'lastpostby' => 0,
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
    'createdby' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'null' => false,
      'attributes' => 'unsigned',
      'default' => 0,
      'phptype' => 'integer',
    ),
    'lastpost' => 
    array (
      'dbtype' => 'timestamp',
      'phptype' => 'datetime',
      'default' => 'CURRENT_TIMESTAMP',
      'attributes' => 'ON UPDATE CURRENT_TIMESTAMP',
      'null' => false,
    ),
    'lastpostby' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'null' => false,
      'attributes' => 'unsigned',
      'default' => 0,
      'phptype' => 'integer',
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
    'lastby_idx' => 
    array (
      'alias' => 'lastby',
      'unique' => false,
      'primary' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'lastpostby' => 
        array (
          'length' => '',
          'null' => false,
          'collation' => 'A',
        ),
      ),
    ),
    'createdby_idx' => 
    array (
      'alias' => 'createdby',
      'unique' => false,
      'primary' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'createdby' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
);
