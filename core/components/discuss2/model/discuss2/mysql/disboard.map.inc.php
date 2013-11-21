<?php
/**
 * @package Discuss
 * @subpackage mysql
 */
$xpdo_meta_map['disBoard']= array (
  'package' => 'discuss2',
  'version' => '1.1',
  'extends' => 'modResource',
  'fields' => 
  array (
    'class_key' => 'disBoard',
  ),
  'fieldMeta' => 
  array (
    'class_key' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '100',
      'phptype' => 'string',
      'default' => 'disBoard',
    ),
  ),
  'composites' => 
  array (
    'Thread' => 
    array (
      'class' => 'disThread',
      'local' => 'id',
      'foreign' => 'parent',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'Discussion' => 
    array (
      'class' => 'disThreadDiscussion',
      'local' => 'id',
      'foreign' => 'parent',
      'cardinality' => 'many',
      'owner' => 'local',
      'criteria' => 
      array (
        'foreign' => 
        array (
          'class_key' => 'disThreadDiscussion',
        ),
      ),
    ),
    'Question' => 
    array (
      'class' => 'disThreadQuestion',
      'local' => 'id',
      'foreign' => 'parent',
      'cardinality' => 'many',
      'owner' => 'local',
      'criteria' => 
      array (
        'foreign' => 
        array (
          'class_key' => 'disThreadQuestion',
        ),
      ),
    ),
    'Descendant' => 
    array (
      'class' => 'disBoardClosure',
      'local' => 'id',
      'foreign' => 'ancestor',
      'cardinality' => 'one',
      'owner' => 'local',
    ),
  ),
  'aggregates' => 
  array (
    'Ancestor' => 
    array (
      'class' => 'disBoardClosure',
      'local' => 'id',
      'foreign' => 'descendant',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'Category' => 
    array (
      'class' => 'disCategory',
      'local' => 'parent',
      'foreign' => 'id',
      'cardinality' => 'many',
      'owner' => 'foreign',
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
