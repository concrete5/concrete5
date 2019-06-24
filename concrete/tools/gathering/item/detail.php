<?php defined('C5_EXECUTE') or die('Access Denied.');

$nh = Loader::helper('validation/numbers');
$form = Loader::helper('form');
$gaiID = intval($_REQUEST['gaiID']);
$nh = Loader::helper('validation/numbers');
$item = GatheringItem::getByID($gaiID);
$type = GatheringItemTemplateType::getByHandle('detail');
if (is_object($item) && Loader::helper('validation/token')->validate('get_gathering_items', $_REQUEST['token'])) {
    $item->render($type);
    ?>
<?php 
} ?>
