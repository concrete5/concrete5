<?php
defined('C5_EXECUTE') or die('Access Denied.');
$c = $b->getBlockCollectionObject();
$arHandle = $b->getAreaHandle();
$pk = PermissionKey::getByID($_REQUEST['pkID']);
$pk->setPermissionObject($b);
?>

<?php Loader::element("permission/detail", array('permissionKey' => $pk)); ?>


<script type="text/javascript">
var ccm_permissionDialogURL = '<?=URL::to('/ccm/system/dialogs/block/permissions/detail')?>?bID=<?=$b->getBlockID()?>&arHandle=<?=urlencode($b->getAreaHandle())?>&cvID=<?=$c->getVersionID()?>&bID=<?=$b->getBlockID()?>&cID=<?=$c->getCollectionID()?>';
</script>