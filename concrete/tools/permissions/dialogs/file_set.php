<?php

defined('C5_EXECUTE') or die('Access Denied.');

if ($_REQUEST['fsID'] > 0) {
    $fs = FileSet::getByID($_REQUEST['fsID']);
} else {
    $fs = FileSet::getGlobal();
}
$fsp = new Permissions($fs);
if ($fsp->canEditFileSetPermissions()) {
    Loader::element('permission/details/file_set', array("fileset" => $fs));
}
