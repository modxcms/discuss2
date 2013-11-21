<?php
/**
 * @package Discuss
 * @subpackage mysql
 */
$xpdo_meta_map['disCategory']= array (
  'package' => 'discuss2',
  'version' => '1.1',
  'extends' => 'modResource',
  'fields' => 
  array (
    'class_key' => 'disCategory',
  ),
  'fieldMeta' => 
  array (
    'class_key' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '100',
      'phptype' => 'string',
      'default' => 'disCategory',
    ),
  ),
  'composites' => 
  array (
    'Board' => 
    array (
      'class' => 'disBoard',
      'local' => 'id',
      'foreign' => 'parent',
      'cardinality' => 'many',
      'owner' => 'local',
      'criteria' => 
      array (
        'foreign' => 
        array (
          'class_key' => 'disBoard',
        ),
      ),
    ),
  ),
);
