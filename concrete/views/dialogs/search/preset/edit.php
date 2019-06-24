<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<div class="ccm-ui">
    <form method="post" data-dialog-form="edit-search-preset" class="form-horizontal" action="<?= $controller->action('edit_search_preset'); ?>">
        <?= $token->output('edit_search_preset'); ?>
        <?= $form->hidden('presetID', $searchPreset->getId()); ?>
        <?= $form->hidden('objectID', $controller->getObjectID()); ?>
        <div class="form-group">
            <?= $form->label('presetName', t('Name')); ?>
            <?= $form->text('presetName', $searchPreset->getPresetName()); ?>
        </div>

        <div class="dialog-buttons">
            <button class="btn btn-default" data-dialog-action="cancel"><?= t('Cancel'); ?></button>
            <button class="btn btn-primary pull-right" data-dialog-action="submit" type="submit"><?= t('Save Search Preset'); ?></button>
        </div>
    </form>

    <script type="text/javascript">
        $(function() {
            ConcreteEvent.unsubscribe('AjaxFormSubmitSuccess.SavedSearchUpdated');
            ConcreteEvent.subscribe('AjaxFormSubmitSuccess.SavedSearchUpdated', function(e, data) {
                if (data.form == 'edit-search-preset') {
                    ConcreteEvent.publish('SavedSearchUpdated', { 'preset': data.response });
                }
            });
        });
    </script>

</div>
