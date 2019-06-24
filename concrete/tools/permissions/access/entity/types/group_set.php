<?php

defined('C5_EXECUTE') or die('Access Denied.');
use \Concrete\Core\Permission\Access\Entity\GroupSetEntity as GroupSetPermissionAccessEntity;

if (Loader::helper('validation/token')->validate('process')) {
    $js = Loader::helper('json');
    $obj = new stdClass();
    $gs = GroupSet::getByID($_REQUEST['gsID']);
    if (is_object($gs)) {
        $pae = GroupSetPermissionAccessEntity::getOrCreate($gs);
        $obj->peID = $pae->getAccessEntityID();
        $obj->label = $pae->getAccessEntityLabel();
    }
    echo $js->encode($obj);
}
