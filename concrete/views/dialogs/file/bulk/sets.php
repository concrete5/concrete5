<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<form method="post" data-dialog-form="save-file-set" action="<?=$controller->action('submit')?>">
    <?php foreach ($files as $f) {
    ?>
        <input type="hidden" name="fID[]" value="<?=$f->getFileID()?>" />
    <?php 
} ?>

    <div class="ccm-ui">
        <?php Loader::element('files/bulk/add_to_sets', array('files' => $files))?>
    </div>

    <div class="dialog-buttons">
        <button class="btn btn-default pull-left" data-dialog-action="cancel"><?=t('Cancel')?></button>
        <button type="button" data-dialog-action="submit" class="btn btn-primary pull-right"><?=t('Save')?></button>
    </div>

</form>

<script type="text/javascript">
    $(function() {
        ConcreteEvent.unsubscribe('AjaxFormSubmitSuccess.updateFileSets');
        ConcreteEvent.subscribe('AjaxFormSubmitSuccess.updateFileSets', function(e, data) {
            if (data.form == 'save-file-set') {
                ConcreteEvent.publish('FileSetBulkUpdateRequestComplete', {
                    'filesets': data.response.sets
                });
            }
        });

    });
</script>