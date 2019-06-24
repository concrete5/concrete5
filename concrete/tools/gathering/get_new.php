<?php

defined('C5_EXECUTE') or die('Access Denied.');

$nh = Loader::helper('validation/numbers');
if ($_POST['gaID'] && $nh->integer($_POST['gaID'])) {
    $gathering = Gathering::getByID($_POST['gaID']);
    if (is_object($gathering) && Loader::helper('validation/token')->validate('update_gathering_items', $_POST['editToken'])) {
        $showTileControls = ($_POST['showTileControls'] && Loader::helper('validation/token')->validate('update_gathering_items', $_POST['editToken']));
        $item = GatheringItem::getByID($_POST['newerThan']);
        if (is_object($item) && $item->getGatheringID() == $gathering->getGatheringID()) {
            $gathering->generateGatheringItems();

            $list = new GatheringItemList($gathering);
            $list->sortByDateAscending();  // we sort by date ascending so we can get the ones closest to the last one we've seen, so in case we have to keep refreshing we can do so.
      $list->setItemsPerPage($_REQUEST['itemsPerPage']);
            $list->filterByPublicDate($item->getGatheringItemPublicDateTime(), '>');
            $items = $list->getPage();
            foreach ($items as $item) {
                Loader::element('gathering/tile', array("item" => $item, 'showTileControls' => $showTileControls));
            }
        }
    }
}
