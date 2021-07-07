<?php

use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;

defined('C5_EXECUTE') or die("Access Denied.");

$resolverManager = app(ResolverManagerInterface::class);

foreach ($accessTypes as $accessType => $title) {
    $list = $permissionAccess->getAccessListItems($accessType);
    ?>
	<a style="float: right" href="<?= h($resolverManager->resolve(['/ccm/system/permissions/access/entity']) . "?accessType={$accessType}&pkCategoryHandle={$pkCategoryHandle}") ?>" dialog-width="510" dialog-height="500" dialog-title="<?=t('Add Access Entity')?>" class="dialog-launch btn btn-sm btn-secondary"><?=t('Add')?></a>

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
    <a href="javascript:void(0)" class="icon-link float-end" style="margin-left: 10px" onclick="ccm_deleteAccessEntityAssignment(<?=$pae->getAccessEntityID()?>)"><i class="fas fa-trash"></i></a>
	<a href="<?= h($resolverManager->resolve(['/ccm/system/permissions/access/entity']) . "?peID={$pae->getAccessEntityID()}?>&pdID={$pdID}&accessType={$accessType}") ?>" dialog-width="510" dialog-height="500" dialog-title="<?=t('Add Access Entity')?>" class="<?php if (!is_object($pa->getPermissionDurationObject())) {
    ?>icon-link<?php
}
    ?> float-end dialog-launch"><i class="far fa-clock <?php if (is_object($pa->getPermissionDurationObject())) {
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
