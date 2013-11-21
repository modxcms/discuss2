<?php
/**
 * @package Discuss
 * @subpackage mysql
 */
$xpdo_meta_map['disUser']= array (
  'package' => 'discuss2',
  'version' => '1.1',
  'extends' => 'modUser',
  'fields' => 
  array (
  ),
  'fieldMeta' => 
  array (
  ),
  'composites' => 
  array (
    'disProfile' => 
    array (
      'class' => 'disUserProfile',
      'local' => 'id',
      'foreign' => 'internalKey',
      'cardinality' => 'one',
      'owner' => 'local',
    ),
    'Network' => 
    array (
      'class' => 'disUserNetwork',
      'local' => 'id',
      'foreign' => 'internalKey',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'Mention' => 
    array (
      'class' => 'disUserMention',
      'local' => 'id',
      'foreign' => 'internalKey',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'IgnoreBoard' => 
    array (
      'class' => 'disUserIgnoreBoard',
      'local' => 'id',
      'foreign' => 'internalKey',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'Thread' => 
    array (
      'class' => 'disThread',
      'local' => 'id',
      'foreign' => 'createdby',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'Question' => 
    array (
      'class' => 'disThreadQuestion',
      'local' => 'id',
      'foreign' => 'createdby',
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
    'Discussion' => 
    array (
      'class' => 'disThreadDiscussion',
      'local' => 'id',
      'foreign' => 'createdby',
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
    'Post' => 
    array (
      'class' => 'disPost',
      'local' => 'id',
      'foreign' => 'createdby',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'Activity' => 
    array (
      'class' => 'disUserActivity',
      'local' => 'id',
      'foreign' => 'internalKey',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'Attachment' => 
    array (
      'class' => 'disPostAttachment',
      'local' => 'id',
      'foreign' => 'internalKey',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);
