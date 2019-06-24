<?php

defined('C5_EXECUTE') or die('Access Denied.');
use \Concrete\Core\Permission\Access\Entity\Entity as PermissionAccessEntity;
use \Concrete\Core\Permission\Duration as PermissionDuration;

$c = Page::getByID($_REQUEST['cID']);
$a = Area::get($c, $_GET['arHandle']);
$ax = $a;
$cx = $c;
if ($a->isGlobalArea()) {
    $cx = Stack::getByName($_REQUEST['arHandle']);
    $ax = Area::get($cx, STACKS_AREA_NAME);
}
if (is_object($a)) {
    $ap = new Permissions($a);
    if ($ap->canEditAreaPermissions()) {
        if ($_REQUEST['task'] == 'add_access_entity' && Loader::helper("validation/token")->validate('add_access_entity')) {
            $pk = PermissionKey::getByID($_REQUEST['pkID']);
            $pk->setPermissionObject($ax);
            $pa = PermissionAccess::getByID($_REQUEST['paID'], $pk);
            $pe = PermissionAccessEntity::getByID($_REQUEST['peID']);
            $pd = PermissionDuration::getByID($_REQUEST['pdID']);
            $pa->addListItem($pe, $pd, $_REQUEST['accessType']);
        }

        if ($_REQUEST['task'] == 'revert_to_page_permissions' && Loader::helper("validation/token")->validate('revert_to_page_permissions')) {
            $ax->revertToPagePermissions();
        }

        if ($_REQUEST['task'] == 'override_page_permissions' && Loader::helper("validation/token")->validate('override_page_permissions')) {
            $ax->overridePagePermissions();
        }

        if ($_REQUEST['task'] == 'remove_access_entity' && Loader::helper("validation/token")->validate('remove_access_entity')) {
            $pk = PermissionKey::getByID($_REQUEST['pkID']);
            $pk->setPermissionObject($ax);
            $pa = PermissionAccess::getByID($_REQUEST['paID'], $pk);
            $pe = PermissionAccessEntity::getByID($_REQUEST['peID']);
            $pa->removeListItem($pe);
        }

        if ($_REQUEST['task'] == 'save_permission' && Loader::helper("validation/token")->validate('save_permission')) {
            $pk = PermissionKey::getByID($_REQUEST['pkID']);
            $pk->setPermissionObject($ax);
            $pa = PermissionAccess::getByID($_REQUEST['paID'], $pk);
            $pa->save($_POST);
        }

        if ($_REQUEST['task'] == 'display_access_cell' && Loader::helper("validation/token")->validate('display_access_cell')) {
            $pk = PermissionKey::getByID($_REQUEST['pkID']);
            $pa = PermissionAccess::getByID($_REQUEST['paID'], $pk);
            Loader::element('permission/labels', array('pk' => $pk, 'pa' => $pa));
        }

        if ($_REQUEST['task'] == 'save_permission_assignments' && Loader::helper("validation/token")->validate('save_permission_assignments')) {
            $permissions = PermissionKey::getList('area');
            foreach ($permissions as $pk) {
                $paID = $_POST['pkID'][$pk->getPermissionKeyID()];
                $pk->setPermissionObject($ax);
                $pt = $pk->getPermissionAssignmentObject();
                $pt->clearPermissionAssignment();
                if ($paID > 0) {
                    $pa = PermissionAccess::getByID($paID, $pk);
                    if (is_object($pa)) {
                        $pt->assignPermissionAccess($pa);
                    }
                }
            }
        }
    }
}
