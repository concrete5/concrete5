<?php

defined('C5_EXECUTE') or die('Access Denied.');
$c = Page::getByPath('/dashboard/system/permissions/users');
$cp = new Permissions($c);
if ($cp->canViewPage()) {
    Loader::element('permission/details/user');
}
