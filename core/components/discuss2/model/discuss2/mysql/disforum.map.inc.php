<?php
/**
 * @package Discuss
 * @subpackage mysql
 */
$xpdo_meta_map['disForum']= array (
  'package' => 'discuss2',
  'version' => '1.1',
  'extends' => 'modResource',
  'fields' => 
  array (
    'class_key' => 'disForum',
  ),
  'fieldMeta' => 
  array (
    'class_key' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '100',
      'phptype' => 'string',
      'default' => 'disForum',
    ),
  ),
  'composites' => 
  array (
    'Category' => 
    array (
      'class' => 'disCategory',
      'local' => 'id',
      'foreign' => 'parent',
      'cardinality' => 'many',
      'owner' => 'local',
      'criteria' => 
      array (
        'foreign' => 
        array (
          'class_key' => 'disCategory',
        ),
      ),
    ),
  ),
);
