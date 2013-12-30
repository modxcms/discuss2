<?php
/**
 * @package Discuss
 * @subpackage mysql
 */
$xpdo_meta_map['disUserProfile']= array (
  'package' => 'discuss2',
  'version' => '1.1',
  'table' => 'user_profile',
  'extends' => 'xPDOObject',
  'fields' => 
  array (
    'internalKey' => NULL,
    'firstname' => '',
    'surname' => '',
    'signature' => '',
    'title' => '',
    'posts' => 0,
    'show_email' => 0,
    'show_online' => 0,
    'display_name' => '',
    'use_display_name' => 0,
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
      'index' => 'pk',
    ),
    'firstname' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '64',
      'phptype' => 'string',
      'default' => '',
      'null' => false,
    ),
    'surname' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '64',
      'phptype' => 'string',
      'default' => '',
      'null' => false,
    ),
    'signature' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'default' => '',
    ),
    'title' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '32',
      'phptype' => 'string',
      'default' => '',
    ),
    'posts' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'default' => 0,
    ),
    'show_email' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'boolean',
      'attributes' => 'unsigned',
      'default' => 0,
    ),
    'show_online' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'boolean',
      'attributes' => 'unsigned',
      'default' => 0,
    ),
    'display_name' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '64',
      'phptype' => 'string',
      'default' => '',
    ),
    'use_display_name' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'boolean',
      'default' => 0,
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
        'internalKey' => 
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
    'User' => 
    array (
      'class' => 'disUser',
      'local' => 'internalKey',
      'foreign' => 'id',
      'cardinality' => 'one',
    ),
  ),
);
