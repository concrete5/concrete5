<?php

defined('C5_EXECUTE') or die('Access Denied.');
use \Concrete\Core\Permission\Access\Entity\Entity as PermissionAccessEntity;
use \Concrete\Core\Permission\Duration as PermissionDuration;
use \Concrete\Core\Permission\Key\PageKey as PagePermissionKey;
use \Concrete\Core\Workflow\Workflow as Workflow;
use \Concrete\Core\Workflow\Request\ChangePagePermissionsRequest as ChangePagePermissionsPageWorkflowRequest;
use \Concrete\Core\Workflow\Request\ChangePagePermissionsInheritanceRequest as ChangePagePermissionsInheritancePageWorkflowRequest;
use \Concrete\Core\Workflow\Request\ChangeSubpageDefaultsInheritanceRequest as ChangeSubpageDefaultsInheritancePageWorkflowRequest;
use \Concrete\Core\Permission\Set as PermissionSet;

$pages = array();
if (is_array($_REQUEST['cID'])) {
    foreach ($_REQUEST['cID'] as $cID) {
        $c = Page::getByID($cID);
        $cp = new Permissions($c);
        if ($cp->canEditPagePermissions()) {
            $pages[] = $c;
        }
    }
} else {
    $c = Page::getByID($_REQUEST['cID']);
    $cp = new Permissions($c);
    if ($cp->canEditPagePermissions()) {
        $pages[] = $c;
    }
}

if (count($pages) > 0) {
    if ($_REQUEST['task'] == 'add_access_entity' && Loader::helper("validation/token")->validate('add_access_entity')) {
        $pk = PagePermissionKey::getByID($_REQUEST['pkID']);
        $pe = PermissionAccessEntity::getByID($_REQUEST['peID']);
        $pd = PermissionDuration::getByID($_REQUEST['pdID']);
        foreach ($pages as $c) {
            $pk->setPermissionObject($c);
            $pa = PermissionAccess::getByID($_REQUEST['paID'], $pk);
            $pa->addListItem($pe, $pd, $_REQUEST['accessType']);
        }
    }

    if ($_REQUEST['task'] == 'remove_access_entity' && Loader::helper("validation/token")->validate('remove_access_entity')) {
        $pk = PagePermissionKey::getByID($_REQUEST['pkID']);
        $pe = PermissionAccessEntity::getByID($_REQUEST['peID']);
        foreach ($pages as $c) {
            $pk->setPermissionObject($c);
            $pa = PermissionAccess::getByID($_REQUEST['paID'], $pk);
            $pa->removeListItem($pe);
        }
    }

    if ($_REQUEST['task'] == 'save_permission' && Loader::helper("validation/token")->validate('save_permission')) {
        $pk = PagePermissionKey::getByID($_REQUEST['pkID']);
        foreach ($pages as $c) {
            $pa = PermissionAccess::getByID($_REQUEST['paID'], $pk);
            if (is_object($pa)) {
                $pa->save($_POST);
                $pa->clearWorkflows();
                if (is_array($_POST['wfID'])) {
                    foreach ($_POST['wfID'] as $wfID) {
                        $wf = Workflow::getByID($wfID);
                        if (is_object($wf)) {
                            $pa->attachWorkflow($wf);
                        }
                    }
                }
            }
        }
    }

    if ($_REQUEST['task'] == 'change_permission_inheritance' && Loader::helper("validation/token")->validate('change_permission_inheritance')) {
        $deferred = false;
        $homeCID = Page::getHomePageID();
        foreach ($pages as $c) {
            if ($c->getCollectionID() == $homeCID) {
                continue;
            }

            $pkr = new ChangePagePermissionsInheritancePageWorkflowRequest();
            $pkr->setRequestedPage($c);
            $pkr->setPagePermissionsInheritance($_REQUEST['mode']);
            $pkr->setRequesterUserID($u->getUserID());
            $response = $pkr->trigger();
            if (!($response instanceof \Concrete\Core\Workflow\Progress\Response)) {
                $deferred = true;
            }
        }
        $obj = new stdClass();
        $obj->deferred = $deferred;
        echo Loader::helper('json')->encode($obj);
        exit;
    }

    if ($_REQUEST['task'] == 'change_subpage_defaults_inheritance' && Loader::helper("validation/token")->validate('change_subpage_defaults_inheritance')) {
        $deferred = false;
        foreach ($pages as $c) {
            $pkr = new ChangeSubpageDefaultsInheritancePageWorkflowRequest();
            $pkr->setRequestedPage($c);
            $pkr->setPagePermissionsInheritance($_REQUEST['inherit']);
            $pkr->setRequesterUserID($u->getUserID());
            $response = $pkr->trigger();
            if (!($response instanceof \Concrete\Core\Workflow\Progress\Response)) {
                $deferred = true;
            }
        }
        $obj = new stdClass();
        $obj->deferred = $deferred;
        echo Loader::helper('json')->encode($obj);
        exit;
    }

    if ($_REQUEST['task'] == 'display_access_cell' && Loader::helper("validation/token")->validate('display_access_cell')) {
        $pk = PermissionKey::getByID($_REQUEST['pkID']);
        $pk->setPermissionObject($c);
        $pa = PermissionAccess::getByID($_REQUEST['paID'], $pk);
        Loader::element('permission/labels', array('pk' => $pk, 'pa' => $pa));
    }

    if ($_REQUEST['task'] == 'save_permission_assignments' && Loader::helper("validation/token")->validate('save_permission_assignments')) {
        $u = new User();
        $permissions = PermissionKey::getList('page');
        $deferred = false;
        foreach ($pages as $c) {
            $pkr = new ChangePagePermissionsPageWorkflowRequest();
            $pkr->setRequestedPage($c);
            $ps = new PermissionSet();
            $ps->setPermissionKeyCategory('page');
            foreach ($permissions as $pk) {
                $paID = $_POST['pkID'][$pk->getPermissionKeyID()];
                $ps->addPermissionAssignment($pk->getPermissionKeyID(), $paID);
            }
            $pkr->setPagePermissionSet($ps);
            $pkr->setRequesterUserID($u->getUserID());
            $u->unloadCollectionEdit($c);
            $response = $pkr->trigger();
            if (!($response instanceof \Concrete\Core\Workflow\Progress\Response)) {
                $deferred = true;
            }
        }

        $r = new PageEditResponse();
        $r->setPage($c);
        if ($deferred) {
            $r->setMessage(t('Page permissions request saved successfully. You must approve this workflow request before the permissions are changed.'));
        } else {
            $r->setMessage(t('Page permissions saved successfully.'));
        }
        $r->outputJSON();
    }

    if ($_REQUEST['task'] == 'bulk_add_access' && Loader::helper('validation/token')->validate('bulk_add_access')) {
        if (is_array($_REQUEST['pkID'])) {
            $pkID = key($_REQUEST['pkID']);
            $pk = PermissionKey::getByID($pkID);
            $newPAID = $_REQUEST['pkID'][$pkID];
            $u = new User();
            $deferred = false;

            foreach ($pages as $c) {
                if ($_REQUEST['paReplaceAll'] == 'add') {
                    $pk->setPermissionObject($c);
                    $pa = $pk->getPermissionAccessObject();
                    if (is_object($pa)) {
                        // that means that we have to take the current $pa object, and the new $pa object, and merge them together into
                        // a third object, and try and assign that object
                        $orig = $pa->duplicate();
                        $newpa = PermissionAccess::getByID($newPAID, $pk);
                        $pa = $newpa->duplicate($orig);
                    } else {
                        // no current $pa object, which means we assign the new $pa object to this thing
                        $pk->setPermissionObject($c);
                        $pa = PermissionAccess::getByID($newPAID, $pk);
                    }
                } else {
                    $pa = PermissionAccess::getByID($newPAID, $pk);
                }
                $pkr = new ChangePagePermissionsPageWorkflowRequest();
                $pkr->setRequestedPage($c);
                $ps = new PermissionSet();
                $ps->setPermissionKeyCategory('page');
                $ps->addPermissionAssignment($pk->getPermissionKeyID(), $pa->getPermissionAccessID());
                $pkr->setPagePermissionSet($ps);
                $pkr->setRequesterUserID($u->getUserID());
                $u->unloadCollectionEdit($c);
                $response = $pkr->trigger();
                if (!($response instanceof \Concrete\Core\Workflow\Progress\Response)) {
                    $deferred = true;
                }
            }
        }
        exit;
    }

    if ($_REQUEST['task'] == 'bulk_remove_access' && Loader::helper('validation/token')->validate('bulk_remove_access')) {
        $pkID = $_REQUEST['pkID'];
        $pk = PermissionKey::getByID($pkID);

        $u = new User();
        $deferred = false;

        foreach ($pages as $c) {
            $pk->setPermissionObject($c);
            $pa = $pk->getPermissionAccessObject();
            $matches = array();
            if (is_object($pa)) {
                foreach ($_REQUEST['listItem'] as $li) {
                    $lii = explode(':', $li);
                    $peID = $lii[0];
                    $accessType = $lii[1];
                    $pdID = $lii[2];

                    $listItems = $pa->getAccessListItems($accessType);
                    foreach ($listItems as $as) {
                        $entity = $as->getAccessEntityObject();
                        $pd = $as->getPermissionDurationObject();
                        if ($entity->getAccessEntityID() == $peID && ((is_object($pd) && $pd->getPermissionDurationID() == $pdID) || (!is_object($pd) && $pdID == 0))) {
                            $matches[] = $as;
                        }
                    }
                }
                if (count($matches) > 0) {
                    $newpa = $pa->duplicate();
                    // remove the associated things.

                    $listItems = $newpa->getAccessListItems(PermissionKey::ACCESS_TYPE_ALL);
                    foreach ($listItems as $li) {
                        foreach ($matches as $as) {
                            $entity = $as->getAccessEntityObject();
                            $pd = $as->getPermissionDurationObject();
                            if ($entity->getAccessEntityID() == $peID &&
                                ((is_object($pd) && $pd->getPermissionDurationID() == $pdID) || (!is_object($pd) && $pdID == 0))) {
                                $newpa->removeListItem($entity);
                            }
                        }
                    }

                    $pkr = new ChangePagePermissionsPageWorkflowRequest();
                    $pkr->setRequestedPage($c);
                    $ps = new PermissionSet();
                    $ps->setPermissionKeyCategory('page');
                    $ps->addPermissionAssignment($pk->getPermissionKeyID(), $newpa->getPermissionAccessID());
                    $pkr->setPagePermissionSet($ps);
                    $pkr->setRequesterUserID($u->getUserID());
                    $u->unloadCollectionEdit($c);
                    $response = $pkr->trigger();
                    if (!($response instanceof \Concrete\Core\Workflow\Progress\Response)) {
                        $deferred = true;
                    }
                }
            }
        }

        exit;
    }
}
