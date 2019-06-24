<?php

defined('C5_EXECUTE') or die('Access Denied.');
$canRead = false;
use \Concrete\Core\Attribute\Key\Category as AttributeKeyCategory;

$ch = Page::getByPath('/dashboard/settings/attributes/sets');
$cp = new Permissions($ch);
if ($cp->canViewPage()) {
    $canRead = true;
}

if (!$canRead) {
    die(t("Access Denied."));
}

$db = Loader::db();
$akc = AttributeKeyCategory::getByID($_POST['categoryID']);
$uats = $_REQUEST['asID'];

if (is_array($uats)) {
    $manager = $akc->getController()->getSetManager();
    $manager->updateAttributeSetDisplayOrder($uats);
}
