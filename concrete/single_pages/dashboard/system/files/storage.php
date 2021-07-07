<?php
defined('C5_EXECUTE') or die('Access Denied.');

/* @var Concrete\Controller\SinglePage\Dashboard\System\Files\Storage $controller */
/* @var Concrete\Core\Form\Service\Form $form */
/* @var Concrete\Core\Page\Page $c */
/* @var Concrete\Core\Page\View\PageView $view */
/* @var Concrete\Core\Validation\CSRF\Token $token */

switch ($controller->getTask()) {
    case 'select_type':
    case 'add':
    case 'edit':
    case 'update':
    case 'delete':
        /* @var Concrete\Core\Entity\File\StorageLocation\Type\Type $type */
        if (!isset($location) || !is_object($location)) {
            $location = null;
        }
        if ($location !== null) {
            /* @var Concrete\Core\Entity\File\StorageLocation\StorageLocation $location */
            $fslName = $location->getName();
            $fslIsDefault = $location->isDefault();
            $hasFiles = $location->hasFiles();
            $method = 'update';
            if (!$fslIsDefault && $type->getHandle() != 'default' && !$hasFiles) { ?>
                <div class="ccm-dashboard-header-buttons">
                    <form method="post" action="<?= $view->action('delete'); ?>">
                        <input type="hidden" name="fslID" value="<?= $location->getID(); ?>" />
                        <?= $token->output('delete'); ?>
                        <button type="button" class="btn btn-danger" data-action="delete-location"><?= t('Delete Location'); ?></button>
                    </form>
                </div>
            <?php } ?>
            <?php if (!$fslIsDefault && $hasFiles) { ?>
                <div class="alert alert-info">
                    <?= t('You can not delete this storage location because it contains files.'); ?>
                </div>
            <?php } ?>
        <?php } else {
            $fslName = '';
            $fslIsDefault = false;
            $method = 'add';
        }
        ?>
        <form method="post" action="<?= $view->action($method); ?>" id="ccm-attribute-key-form">
            <?= $token->output($method); ?>
            <input type="hidden" name="fslTypeID" value="<?= $type->getID(); ?>" />
            <?php if ($location !== null) { ?>
                <input type="hidden" name="fslID" value="<?= $location->getID(); ?>" />
            <?php } ?>
            <fieldset>
                <legend><?= t('Basics'); ?></legend>
                <div class="form-group">
                    <?= $form->label('fslName', t('Name')); ?>
                    <div class="input-group">
                        <?= $form->text('fslName', $fslName); ?>
                        <span class="input-group-text"><i class="fa fa-asterisk"></i></span>
                    </div>
                </div>
                <?php
                if ($fslIsDefault) {
                    $args = ['disabled' => 'disabled'];
                } else {
                    $args = [];
                }
                ?>
                <div class="form-group">
                    <?= $form->label('fslIsDefault', t('Default Location')); ?>
                    <div class="form-check">
                        <?= $form->radio('fslIsDefault', 1, $fslIsDefault, $args); ?>
                        <label>
                            <?= t('Yes, make this the default storage location for new files.'); ?>
                        </label>
                    </div>
                    <div class="form-check">
                        <?= $form->radio('fslIsDefault', 0, $fslIsDefault, $args); ?>
                        <label>
                            <?= t('No, this is not the default storage location.'); ?>
                        </label>
                    </div>
                </div>
            </fieldset>
            <?php if ($type->hasOptionsForm()) { ?>
                <fieldset>
                    <legend><?= t('Options %s Storage Type', $type->getName()); ?></legend>
                    <?php $type->includeOptionsForm($location); ?>
                </fieldset>
            <?php } ?>
            <div class="ccm-dashboard-form-actions-wrapper">
                <div class="ccm-dashboard-form-actions">
                    <a href="<?= URL::to($c); ?>" class="btn float-start btn-secondary"><?= t('Back'); ?></a>
                    <?php if ($location !== null) { ?>
                        <button type="submit" class="btn btn-primary float-end"><?= t('Save'); ?></button>
                    <?php } else { ?>
                        <button type="submit" class="btn btn-primary float-end"><?= t('Add'); ?></button>
                    <?php } ?>
                </div>
            </div>
        </form>
        <script>
            $(function() {
                $('button[data-action=delete-location]').on('click', function(e) {
                    e.preventDefault();
                    if (confirm(<?= json_encode(t('Delete this storage location? All files using it will have their storage location reset to the default.')); ?>)) {
                        $(this).closest('form').submit();
                    }
                });
            });
        </script>
        <?php
        break;

    default: ?>
        <h3><?= t('Storage Locations'); ?></h3>
        <ul class="item-select-list">
            <?php foreach ($locations as $location) { ?>
                <li>
                    <a href="<?= $view->action('edit', $location->getID()); ?>"><i class="fa fa-hdd-o"></i> <?= $location->getDisplayName(); ?></a>
                </li>
            <?php } ?>
        </ul>
        <form method="get" action="<?= $view->action('select_type'); ?>" id="ccm-file-storage-location-type-form">
            <fieldset>
                <legend><?= t('Add Location'); ?></legend>
                <div class="form-group">
                    <label for="atID"><?= t('Choose Type'); ?></label>
                    <div class="row row-cols-auto g-0 align-items-center">
                        <div class="me-2 col-auto">
                            <?= $form->select('fslTypeID', $types); ?>
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-secondary"><?= t('Go'); ?></button>
                        </div>
                    </div>
                </div>
            </fieldset>
        </form>
        <?php
        break;
}
