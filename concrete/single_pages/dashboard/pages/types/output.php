<?php defined('C5_EXECUTE') or die('Access Denied.');
$pageTypeDefaultTemplateID = $pagetype->getPageTypeDefaultPageTemplateID();
$pageTypePageTemplateObjects = $pagetype->getPageTypePageTemplateObjects();
?>

<p class="lead"><?= $pagetype->getPageTypeDisplayName(); ?></p>

<table class="table table-striped">
<?php
foreach ($pageTypePageTemplateObjects as $pt) {
    ?>
    <tr>
        <td style="width: 1px"><a href="<?= $view->action('edit_defaults', $pagetype->getPageTypeID(), $pt->getPageTemplateID()); ?>" target="_blank"><?= $pt->getPageTemplateIconImage(); ?></a></td>
        <td style="vertical-align: middle"><p class="lead" style="margin-bottom: 0px"><?= $pt->getPageTemplateDisplayName(); ?></p></td>
        <td style="width: 250px; vertical-align: middle">
            <div class="btn-group float-end">
                <a href="<?= $view->action('edit_defaults', $pagetype->getPageTypeID(), $pt->getPageTemplateID()); ?>" class="btn btn-secondary btn-sm"><?= t('Edit'); ?></a>
                <a class="btn btn-sm btn-secondary dialog-launch" dialog-title="<?= t('Update Defaults'); ?>"
                   dialog-modal="true"
                   dialog-width="500"
                   dialog-height="auto"
                   href="<?= URL::to('/ccm/system/dialogs/type/update_from_type/' . $pagetype->getPageTypeID() . '/' . $pt->getPageTemplateID()); ?>">
                    <?= t('Publish to Child Pages'); ?>
                </a>
            </div>
        </td>
    </tr>
<?php
}
?>
</table>

<div class="ccm-dashboard-header-buttons">
    <a href="<?= URL::to('/dashboard/pages/types'); ?>" class="btn btn-secondary"><?= t('Back to List'); ?></a>
</div>
