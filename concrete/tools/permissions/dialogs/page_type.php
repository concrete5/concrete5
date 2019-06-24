<?php

defined('C5_EXECUTE') or die('Access Denied.');
$ch = Page::getByPath('/dashboard/pages/types', 'RECENT');
$chp = new Permissions($ch);
if ($_REQUEST['ptID'] > 0) {
    $pt = PageType::getByID($_REQUEST['ptID']);
    $fsp = new Permissions($fs);
    if ($chp->canViewPage()) {
        Loader::element('permission/details/page_type', array("pagetype" => $pt));
    }
}
