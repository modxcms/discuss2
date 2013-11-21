<?php
/**
 * @package Discuss
 * @subpackage mysql
 */
$xpdo_meta_map['disThread']= array (
  'package' => 'discuss2',
  'version' => '1.1',
  'extends' => 'modResource',
  'fields' => 
  array (
    'class_key' => 'disThreadDiscussion',
  ),
  'fieldMeta' => 
  array (
    'class_key' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '100',
      'phptype' => 'string',
      'default' => 'disThreadDiscussion',
    ),
  ),
  'composites' => 
  array (
    'Post' => 
    array (
      'class' => 'disPost',
      'local' => 'id',
      'foreign' => 'parent',
      'cardinality' => 'many',
      'owner' => 'local',
      'criteria' => 
      array (
        'foreign' => 
        array (
          'class_key' => 'disPost',
        ),
      ),
    ),
    'Views' => 
    array (
      'class' => 'disThreadStatistics',
      'local' => 'id',
      'foreign' => 'id',
      'owner' => 'local',
      'cardinality' => 'one',
    ),
  ),
  'aggregates' => 
  array (
    'Board' => 
    array (
      'class' => 'disBoard',
      'local' => 'parent',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
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
