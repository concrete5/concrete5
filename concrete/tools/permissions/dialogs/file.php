<?php

defined('C5_EXECUTE') or die('Access Denied.');

if ($_REQUEST['fID'] > 0) {
    $f = File::getByID($_REQUEST['fID']);
    $fp = new Permissions($f);
    if ($fp->canEditFilePermissions()) {
        Loader::element('permission/details/file', array("f" => $f));
    }
}
