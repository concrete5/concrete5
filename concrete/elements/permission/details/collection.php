<?php
defined('C5_EXECUTE') or die('Access Denied.');

$dh = Loader::helper('date');
$dt = Loader::helper('form/date_time');
$pk = PermissionKey::getByID($_REQUEST['pkID']);
$pk->setPermissionObject($c);
?>

<?php Loader::element("permission/detail", array('permissionKey' => $pk)); ?>


<script type="text/javascript">
var ccm_permissionDialogURL = '<?=REL_DIR_FILES_TOOLS_REQUIRED?>/edit_collection_popup?ctask=set_advanced_permissions&cID=<?=$c->getCollectionID()?>'; 
</script>