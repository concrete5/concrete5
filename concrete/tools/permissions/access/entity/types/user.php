<?php

defined('C5_EXECUTE') or die('Access Denied.');
use \Concrete\Core\Permission\Access\Entity\UserEntity as UserPermissionAccessEntity;

if (Loader::helper('validation/token')->validate('process')) {
    $js = Loader::helper('json');
    $obj = new stdClass();
    $ui = UserInfo::getByID($_REQUEST['uID']);
    if (is_object($ui)) {
        $pae = UserPermissionAccessEntity::getOrCreate($ui);
        $obj->peID = $pae->getAccessEntityID();
        $obj->label = $pae->getAccessEntityLabel();
    }
    echo $js->encode($obj);
}
