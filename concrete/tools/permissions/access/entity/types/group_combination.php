<?php

defined('C5_EXECUTE') or die('Access Denied.');
use \Concrete\Core\Permission\Access\Entity\GroupCombinationEntity as GroupCombinationPermissionAccessEntity;

if (Loader::helper('validation/token')->validate('process')) {
    $js = Loader::helper('json');
    $obj = new stdClass();
    if (count($_POST['gID']) > 0) {
        $groups = array();
        foreach ($_POST['gID'] as $gID) {
            $g = Group::getByID($gID);
            if (is_object($g)) {
                $groups[] = $g;
            }
        }
        $pae = GroupCombinationPermissionAccessEntity::getOrCreate($groups);
        $obj->peID = $pae->getAccessEntityID();
        $obj->label = $pae->getAccessEntityLabel();
    }
    echo $js->encode($obj);
}
