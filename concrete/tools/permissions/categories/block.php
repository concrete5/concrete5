<?php

defined('C5_EXECUTE') or die('Access Denied.');

use \Concrete\Core\Permission\Access\Entity\Entity as PermissionAccessEntity;
use \Concrete\Core\Permission\Access\Entity\GroupEntity as GroupPermissionAccessEntity;
use \Concrete\Core\Permission\Duration as PermissionDuration;

$c = Page::getByID($_REQUEST['cID']);
$a = Area::get($c, $_REQUEST['arHandle']);
if (is_object($a)) {
    $ax = $a;
    $cx = $c;
    if ($a->isGlobalArea()) {
        $ax = STACKS_AREA_NAME;
        $cx = Stack::getByName($_REQUEST['arHandle']);
    }

    $b = Block::getByID($_REQUEST['bID'], $cx, $ax);
    $p = new Permissions($b);
    // we're updating the groups for a particular block
    if ($p->canEditBlockPermissions()) {
        $nvc = $cx->getVersionToModify();
        if ($a->isGlobalArea()) {
            $xvc = $c->getVersionToModify(); // we need to create a new version of THIS page as well.
            $xvc->relateVersionEdits($nvc);
        }
        $b->loadNewCollection($nvc);

        if ($_REQUEST['task'] == 'add_access_entity' && Loader::helper("validation/token")->validate('add_access_entity')) {
            $pk = PermissionKey::getByID($_REQUEST['pkID']);
            $pk->setPermissionObject($b);
            $pa = PermissionAccess::getByID($_REQUEST['paID'], $pk);
            $pe = PermissionAccessEntity::getByID($_REQUEST['peID']);
            $pd = PermissionDuration::getByID($_REQUEST['pdID']);
            $pa->addListItem($pe, $pd, $_REQUEST['accessType']);
        }

        if ($_REQUEST['task'] == 'revert_to_area_permissions' && Loader::helper("validation/token")->validate('revert_to_area_permissions')) {
            $b->revertToAreaPermissions();
        }

        if ($_REQUEST['task'] == 'override_area_permissions' && Loader::helper("validation/token")->validate('override_area_permissions')) {
            $b->doOverrideAreaPermissions();
        }

        if ($_REQUEST['task'] == 'remove_access_entity' && Loader::helper("validation/token")->validate('remove_access_entity')) {
            $pk = PermissionKey::getByID($_REQUEST['pkID']);
            $pk->setPermissionObject($b);
            $pa = PermissionAccess::getByID($_REQUEST['paID'], $pk);
            $pe = PermissionAccessEntity::getByID($_REQUEST['peID']);
            $pa->removeListItem($pe);
        }

        if ($_REQUEST['task'] == 'save_permission' && Loader::helper("validation/token")->validate('save_permission')) {
            $pk = PermissionKey::getByID($_REQUEST['pkID']);
            $pk->setPermissionObject($b);
            $pa = PermissionAccess::getByID($_REQUEST['paID'], $pk);
            $pa->save($_POST);
        }

        if ($_REQUEST['task'] == 'display_access_cell' && Loader::helper("validation/token")->validate('display_access_cell')) {
            $pk = PermissionKey::getByID($_REQUEST['pkID']);
            $pk->setPermissionObject($b);
            $pa = PermissionAccess::getByID($_REQUEST['paID'], $pk);
            Loader::element('permission/labels', array('pk' => $pk, 'pa' => $pa));
        }

        if ($_REQUEST['task'] == 'save_permission_assignments' && Loader::helper("validation/token")->validate('save_permission_assignments')) {
            $permissions = PermissionKey::getList('block');
            foreach ($permissions as $pk) {
                $paID = $_POST['pkID'][$pk->getPermissionKeyID()];
                $pk->setPermissionObject($b);
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
    if ($p->canScheduleGuestAccess()) {
        if ($_REQUEST['task'] == 'set_timed_guest_access' && Loader::helper("validation/token")->validate('set_timed_guest_access')) {
            if (!$b->overrideAreaPermissions()) {
                $b->doOverrideAreaPermissions();
            }
            $pk = PermissionKey::getByHandle('view_block');
            $pk->setPermissionObject($b);
            $pa = $pk->getPermissionAccessObject();
            if (!is_object($pa)) {
                $pa = PermissionAccess::create($pk);
            } elseif ($pa->isPermissionAccessInUse()) {
                $pa = $pa->duplicate();
            }

            $pe = GroupPermissionAccessEntity::getOrCreate(Group::getByID(GUEST_GROUP_ID));
            $pd = PermissionDuration::createFromRequest();
            $pa->addListItem($pe, $pd, PermissionKey::ACCESS_TYPE_INCLUDE);
            $pt = $pk->getPermissionAssignmentObject();
            $pt->assignPermissionAccess($pa);
        }
    }
}
