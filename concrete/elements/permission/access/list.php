<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<?php foreach ($accessTypes as $accessType => $title) {
    $list = $permissionAccess->getAccessListItems($accessType);
    ?>
	<a style="float: right" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/permissions/access_entity?accessType=<?=$accessType?>&pkCategoryHandle=<?=$pkCategoryHandle?>" dialog-width="510" dialog-height="500" dialog-title="<?=t('Add Access Entity')?>" class="dialog-launch btn btn-xs btn-default"><?=t('Add')?></a>

	<h4><?=$title?></h4>

<table class="ccm-permission-access-list table">
<?php if (count($list) > 0) {
    ?>

<?php foreach ($list as $pa) {
    $pae = $pa->getAccessEntityObject();
    $pdID = 0;
    if (is_object($pa->getPermissionDurationObject())) {
        $pdID = $pa->getPermissionDurationObject()->getPermissionDurationID();
    }

    ?>
<tr>
    <td>
    <a href="javascript:void(0)" class="icon-link pull-right" style="margin-left: 10px" onclick="ccm_deleteAccessEntityAssignment(<?=$pae->getAccessEntityID()?>)"><i class="fa fa-trash-o"></i></a>
	<a href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/permissions/access_entity?peID=<?=$pae->getAccessEntityID()?>&pdID=<?=$pdID?>&accessType=<?=$accessType?>" dialog-width="510" dialog-height="500" dialog-title="<?=t('Add Access Entity')?>" class="<?php if (!is_object($pa->getPermissionDurationObject())) {
    ?>icon-link<?php
}
    ?> pull-right dialog-launch"><i class="fa fa-clock-o <?php if (is_object($pa->getPermissionDurationObject())) {
    ?>text-info<?php
}
    ?>"></i></a>
    <?=$pae->getAccessEntityLabel()?>
    </td>
</tr>

<?php
}
    ?>

<?php
} else {
    ?>
	<tr>
	<td><?=t('None')?></td>
	</tr>
<?php
}
    ?>

</table>


<?php
} ?>
