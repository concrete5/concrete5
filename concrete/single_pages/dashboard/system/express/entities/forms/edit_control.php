<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<form data-form="edit-control" data-form-control="<?=$control->getID()?>" action="<?=$view->action('save_control', $control->getID())?>" method="post">

    <?php
        $options = $control->getControlOptionsController();
        $element = Element::get($options->getElement());
        if ($element->exists()) {
            echo $options->render();
        }
    ?>

    <div class="form-group">
        <?=$form->label('customLabel', t('Custom Label'))?>
        <?=$form->text('customLabel', $control->getCustomLabel())?>
    </div>

    <?php if (is_object($type->getValidator())) {
    ?>
        <div class="form-group">
            <?=$form->label('isRequired', t('Required'))?>
            <div class="checkbox">
                <label><?=$form->checkbox('isRequired', 1, $control->isRequired())?> <?=t('Yes, require this form element')?></label>
            </div>
        </div>
    <?php 
} ?>
    <?=Loader::helper('validation/token')->output('save_control')?>
</form>
<div class="dialog-buttons">
    <button class="btn btn-default pull-left" data-dialog-action="cancel"><?=t('Cancel')?></button>
    <button type="button" data-action="submit-edit-control" class="btn btn-primary pull-right"><?=t('Save')?></button>
</div>

<script type="text/javascript">
    $(function() {
        $('form[data-form=edit-control]').each(function() {
            var controlID = $(this).attr('data-form-control');
            $(this).concreteAjaxForm({
                'dataType': 'html',
                success: function(r) {
                    var control = $('tr[data-field-set-control=' + controlID + ']');
                    control.replaceWith(r);
                    jQuery.fn.dialog.closeTop();
                }
            });
        });
        $('button[data-action=submit-edit-control]').on('click', function() {
            $('form[data-form=edit-control]').trigger('submit');
        });
    });
</script>