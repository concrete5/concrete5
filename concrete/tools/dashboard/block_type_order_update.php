<?php

defined('C5_EXECUTE') or die('Access Denied.');
$canRead = false;
$ch = Page::getByPath('/dashboard/blocks/types');
$cp = new Permissions($ch);
if ($cp->canRead()) {
    $canRead = true;
}

if (!$canRead) {
    die(t("Access Denied."));
}

$btID = intval($_REQUEST['btID']);
$btDisplayOrder = intval($_REQUEST['btDisplayOrder']);
if ($btID && $btDisplayOrder) {
    $bt = BlockType::getByID($btID);
    $bt->setBlockTypeDisplayOrder($btDisplayOrder);
}
