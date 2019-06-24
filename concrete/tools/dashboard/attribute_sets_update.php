<?php

defined('C5_EXECUTE') or die('Access Denied.');
use \Concrete\Core\Attribute\Set as AttributeSet;

$canRead = false;
$ch = Page::getByID($_REQUEST['cID']);
$path = $ch->getCollectionPath();
if (strpos($path, '/dashboard') === 0) {
    $cp = new Permissions($ch);
    if ($cp->canViewPage()) {
        $canRead = true;
    }
}

if (!$canRead) {
    die(t("Access Denied."));
}

// this should be cleaned up.... yeah
$db = Loader::db();
// update order of collections

$uats = $_REQUEST['akID_' . $_REQUEST['asID']];

if (is_array($uats)) {
    $as = AttributeSet::getByID($_REQUEST['asID']);
    $as->updateAttributesDisplayOrder($uats);
}
