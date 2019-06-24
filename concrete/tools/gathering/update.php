<?php

defined('C5_EXECUTE') or die('Access Denied.');

$nh = Loader::helper('validation/numbers');
if ($_POST['gaID'] && $nh->integer($_POST['gaID'])) {
    $gathering = Gathering::getByID($_POST['gaID']);
    if (is_object($gathering) && Loader::helper('validation/token')->validate('update_gathering_items', $_POST['editToken'])) {
        $agp = new Permissions($gathering);
        if ($agp->canEditGatheringItems()) {
            switch ($_POST['task']) {
          case 'resize':
            $agi = GatheringItem::getByID($_POST['gaiID']);
            $sw = intval($_POST['gaiSlotWidth']);
            $sh = intval($_POST['gaiSlotHeight']);
            if (!$sw) {
                $sw = 1;
            }
            if (!$sh) {
                $sh = 1;
            }

            $agi->setGatheringItemSlotWidth($sw);
            $agi->setGatheringItemSlotHeight($sh);
            break;
          case 'update_display_order':
            $displayOrder = 0;
            $batch = time();
            foreach ($_POST['gaiID'] as $gaiID) {
                $agi = GatheringItem::getByID($gaiID);
                $agi->setGatheringItemBatchTimestamp($batch);
                $agi->setGatheringItemBatchDisplayOrder($displayOrder);
                ++$displayOrder;
            }
            break;
          case 'move_to_new_gathering':
            $item = GatheringItem::getByID($_POST['gaiID']);
            $item->moveToNewGathering($gathering);
            $il = new GatheringItemList($gathering);
            $il->sortByDateDescending();
            $il->setItemsPerPage($_POST['itemsPerPage']);
            $c = Page::getByID($_POST['cID'], 'RECENT');
            if (is_object($c) && !$c->isError()) {
                Loader::element('gathering/display', array(
                'gathering' => $gathering,
                'list' => $il,
                'c' => $c,
              ));
            }
            break;
        }
        }
    }
}
