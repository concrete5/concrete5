<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Page\Type\Composer\FormLayoutSetControl $setControl
 * @var Concrete\Core\Page\Type\Composer\Control\Control $control
 * @var array $templates
 * @var Concrete\Core\Form\Service\Form $form
 * @var Concrete\Core\Validation\CSRF\Token $valt
 */

?>
<div class="ccm-ui">
    <form data-edit-set-form-control="<?= $setControl->getPageTypeComposerFormLayoutSetControlID() ?>" action="#" method="POST">
        <?php $valt->output('update_set_control') ?>
        <div class="form-group">
            <?= $form->label('ptComposerFormLayoutSetControlCustomLabel', t('Custom Label')) ?>
            <?= $form->text('ptComposerFormLayoutSetControlCustomLabel', $setControl->getPageTypeComposerFormLayoutSetControlCustomLabel()) ?>
        </div>
        <div class="form-group">
            <?= $form->label('ptComposerFormLayoutSetControlCustomTemplate', t('Custom Template')); ?>
            <?= $form->select('ptComposerFormLayoutSetControlCustomTemplate', $templates, $setControl->getPageTypeComposerFormLayoutSetControlCustomTemplate()) ?>
        </div>
        <div class="form-group">
            <?= $form->label('ptComposerFormLayoutSetControlDescription', t('Description')) ?>
            <?= $form->text('ptComposerFormLayoutSetControlDescription', $setControl->getPageTypeComposerFormLayoutSetControlDescription()) ?>
        </div>
        <?php
        if ($control->pageTypeComposerFormControlSupportsValidation()) {
            ?>
            <div class="form-group">
                <?= $form->label('ptComposerFormLayoutSetControlRequired', t('Required')) ?>
                <div class="form-check">
                    <?= $form->checkbox('ptComposerFormLayoutSetControlRequired', 1, $setControl->isPageTypeComposerFormLayoutSetControlRequired()) ?>
                    <?= $form->label('ptComposerFormLayoutSetControlRequired', t('Yes, require this form element')) ?>
                </div>
            </div>
            <?php
        }
        ?>
    </form>
    <div class="dialog-buttons">
        <button class="btn btn-secondary" onclick="jQuery.fn.dialog.closeTop()"><?= t('Cancel') ?></button>
        <button class="btn btn-primary float-end" data-submit-set-form="<?= $setControl->getPageTypeComposerFormLayoutSetControlID() ?>"><?= t('Save') ?></button>
    </div>
</div>
<script>
$(document).ready(function() {
    $('form[data-edit-set-form-control]').on('submit', function(e) {
        e.preventDefault();
        var ptComposerFormLayoutSetControlID = $(this).data('edit-set-form-control'),
            formData = $('form[data-edit-set-form-control=' + ptComposerFormLayoutSetControlID + ']').serializeArray();
        formData.push({
            'name': 'ptComposerFormLayoutSetControlID',
            'value': ptComposerFormLayoutSetControlID
        });
        jQuery.fn.dialog.showLoader();
        $.ajax({
            type: 'POST',
            data: formData,
            url: CCM_DISPATCHER_FILENAME + '/ccm/system/page/type/composer/form/edit_control/save',
            success: function(html) {
                jQuery.fn.dialog.hideLoader();
                jQuery.fn.dialog.closeTop();
                var data = $(html).html();
                $('tr[data-page-type-composer-form-layout-control-set-control-id=<?= $setControl->getPageTypeComposerFormLayoutSetControlID() ?>]').html(data);
                $('a[data-command=edit-form-set-control]').dialog();
            }
        });
    });
    $('button[data-submit-set-form]').on('click', function() {
        var ptComposerFormLayoutSetControlID = $(this).attr('data-submit-set-form');
        $('form[data-edit-set-form-control=' + ptComposerFormLayoutSetControlID + ']').trigger('submit');
    });
});
</script>
