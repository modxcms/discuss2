<?php
/**
 * @package Discuss
 * @subpackage mysql
 */
$xpdo_meta_map['disPost']= array (
  'package' => 'discuss2',
  'version' => '1.1',
  'extends' => 'modResource',
  'fields' => 
  array (
    'class_key' => 'disPost',
  ),
  'fieldMeta' => 
  array (
    'class_key' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '100',
      'phptype' => 'string',
      'default' => 'disPost',
    ),
  ),
  'composites' => 
  array (
    'ChildPost' => 
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
  ),
  'aggregates' => 
  array (
    'ParentPost' => 
    array (
      'class' => 'disPost',
      'local' => 'parent',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
      'criteria' => 
      array (
        'foreign' => 
        array (
          'class_key' => 'disPost',
        ),
      ),
    ),
    'Thread' => 
    array (
      'class' => 'disThread',
      'local' => 'parent',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
