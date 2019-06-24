<?php

defined('C5_EXECUTE') or die('Access Denied.');
$p = Page::getByPath('/dashboard/system/permissions/workflows');
$cp = new Permissions($p);
if ($cp->canViewPage()) {
    $workflow = Workflow::getByID($_REQUEST['wfID']);
    Loader::element('permission/details/basic_workflow', array('workflow' => $workflow));
}
