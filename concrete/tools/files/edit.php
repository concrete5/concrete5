<?php

defined('C5_EXECUTE') or die('Access Denied.');
$u = new User();
$form = Loader::helper('form');

$ci = Loader::helper('concrete/urls');
$f = File::getByID($_REQUEST['fID']);
if (!is_object($f)) {
    die(t('File Not Found.'));
}
$fv = $f->getApprovedVersion();

$fp = new Permissions($f);
if (!$fp->canEditFileContents()) {
    die(t("Access Denied."));
}

$to = $fv->getTypeObject();
if ($to->getPackageHandle() != '') {
    Loader::packageElement('files/edit/' . $to->getEditor(), $to->getPackageHandle(), array('fv' => $fv));
} else {
    Loader::element('files/edit/' . $to->getEditor(), array('fv' => $fv));
}
