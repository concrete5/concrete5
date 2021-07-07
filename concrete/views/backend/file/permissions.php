<?php

use Concrete\Core\View\View;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Entity\File\File $file
 * @var string $permissionsModel
 * @var Concrete\Core\Entity\File\StorageLocation\StorageLocation[] $storageLocations
 * @var Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface $resolverManager
 * @var Concrete\Core\Validation\CSRF\Token $token
 * @var Concrete\Core\Form\Service\Form $form
 * @var Concrete\Core\Application\Service\UserInterface $ui
 */
$tabs = [];
if ($permissionsModel !== 'simple') {
    $tabs[] = ['ccm-file-permissions-advanced', t('Permissions'), true];
}
$tabs[] = ['ccm-file-password', t('Protect with Password'), $permissionsModel === 'simple'];
$tabs[] = ['ccm-file-storage', t('Storage Location')];
?>
<div id="ccm-file-permissions-dialog-wrapper">
    <?= $ui->tabs($tabs) ?>
    <div class="tab-content">
        <?php
        if ($permissionsModel !== 'simple') {
            ?>
            <div class="tab-pane show active" id="ccm-file-permissions-advanced" role="tabpanel">
                <?php
                View::element('permission/lists/file', ['f' => $file]);
                ?>
            </div>
            <?php
        }
        ?>
        <div class="tab-pane<?= $permissionsModel === 'simple' ? ' show active' : '' ?>" id="ccm-file-password" role="tabpanel">
            <h4><?= t('Requires Password to Access') ?></h4>
            <p><?= t('Leave the following form field blank in order to allow everyone to download this file.') ?></p>
            <form method="POST" data-dialog-form="file-password" action="<?= h($resolverManager->resolve(['/ccm/system/file/permissions/set_password'])) ?>">
                <?php $token->output("set_password_{$file->getFileID()}"); ?>
                <?= $form->hidden('fID', $file->getFileID()) ?>
                <?= $form->text('fPassword', $file->getPassword()) ?>
                <div id="ccm-file-password-buttons" style="display: none">
                    <button type="button" onclick="jQuery.fn.dialog.closeTop()" class="btn btn-default pull-left"><?= t('Cancel') ?></button>
                    <button type="button" onclick="$('form[data-dialog-form=file-password]').submit()" class="btn btn-primary float-end"><?= t('Save Password') ?></button>
                </div>
            </form>
            <div class="help-block">
                <p>
                    <?= t('Users who access files through the file manager will not be prompted for a password.') ?>
                </p>
                <p>
                    <?= t('File passwords are stored in the database in plain text, they are not to be used for serious security concerns. Instead use a private storage location and user permissions.') ?>
                </p>
            </div>
        </div>
        <div class="tab-pane" id="ccm-file-storage" role="tabpanel">
            <h4><?= t('Choose File Storage Location') ?></h4>
            <form method="POST" data-dialog-form="file-storage" action="<?= h($resolverManager->resolve(['/ccm/system/file/permissions/set_location'])) ?>">
                <?php $token->output("set_location_{$file->getFileID()}"); ?>
                <?= $form->hidden('fID', $file->getFileID())?>
                <div class="help-block">
                    <p><?= t('All versions of a file will be moved to the selected location.') ?></p>
                </div>
                <div class="form-group">
                    <?php
                    foreach ($storageLocations as $fsl) {
                        ?>
                        <div class="form-check">
                            <?= $form->radio('fslID', $fsl->getID(), $file->getStorageLocationID() == $fsl->getID(), ['id' => "ccm-file-storage-{$fsl->getID()}"]) ?>
                            <label class="form-check-label" for="<?= "ccm-file-storage-{$fsl->getID()}" ?>"><?= $fsl->getDisplayName() ?></label>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </form>
            <div id="ccm-file-storage-buttons" style="display: none">
                <button type="button" onclick="jQuery.fn.dialog.closeTop()" class="btn btn-default pull-left"><?= t('Cancel') ?></button>
                <button type="button" onclick="$('form[data-dialog-form=file-storage]').submit()" class="btn btn-primary float-end"><?= t('Save Location') ?></button>
            </div>
        </div>
    </div>
</div>
<script>
$(document).ready (function() {
    var $wrapper = $('#ccm-file-permissions-dialog-wrapper'),
        $dialog = $wrapper.closest('.ui-dialog-content');
    function setupButtons() {
        var id = $wrapper.find('.tab-pane.active').attr('id'),
            $buttons = id ? $('#' + id + '-buttons') : [];
        if ($buttons.length === 0) {
            $dialog.jqdialog('option', 'buttons', false);
        } else {
            $dialog.jqdialog('option', 'buttons', [{}]);
            $dialog.parent().find(".ui-dialog-buttonset").remove();
            $dialog.parent().find(".ui-dialog-buttonpane").empty('');
            $buttons.clone().show().appendTo($dialog.parent().find('.ui-dialog-buttonpane').addClass('ccm-ui'));
        }
    }
    setupButtons();
    $wrapper.find('a[data-bs-toggle="tab"]').on('shown.bs.tab', function() {
        setupButtons();
    });
});
</script>
