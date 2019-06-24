<?php

defined('C5_EXECUTE') or die('Access Denied.');

$nh = Loader::helper('validation/numbers');
if ($_POST['gaID'] && $nh->integer($_POST['gaID'])) {
    $gathering = Gathering::getByID($_POST['gaID']);
    if (is_object($gathering) && Loader::helper('validation/token')->validate('get_gathering_items', $_POST['loadToken'])) {
        $showTileControls = ($_POST['showTileControls'] && Loader::helper('validation/token')->validate('update_gathering_items', $_POST['editToken']));
        $list = new GatheringItemList($gathering);
        $list->sortByDateDescending();
        $list->setItemsPerPage($_REQUEST['itemsPerPage']);
        $items = $list->getPage($_REQUEST['page']);
        foreach ($items as $item) {
            Loader::element('gathering/tile', array("item" => $item, 'showTileControls' => $showTileControls));
        }
    }
}
