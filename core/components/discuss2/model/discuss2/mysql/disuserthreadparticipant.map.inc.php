<?php
/**
 * @package Discuss
 * @subpackage mysql
 */
$xpdo_meta_map['disUserThreadParticipant']= array (
  'package' => 'discuss2',
  'version' => '1.1',
  'table' => 'user_thread_participants',
  'extends' => 'xPDOObject',
  'fields' => 
  array (
    'thread' => NULL,
    'userid' => NULL,
    'lastread' => '0',
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
    'userid' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'null' => false,
    ),
    'lastread' => 
    array (
      'dbtype' => 'timestamp',
      'phptype' => 'datetime',
      'null' => false,
      'default' => '0',
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
      ),
    ),
    'user_idx' => 
    array (
      'alias' => 'user',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'userid' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'read_idx' => 
    array (
      'alias' => 'read',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'lastread' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
);
