<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<div class="ccm-dashboard-header-buttons btn-group">
    <a href="<?=$backURL?>" class="btn btn-default"><?=t("Back")?></a>
    <?php if (isset($editURL) && $editURL) { ?>
        <a href="<?=$editURL?>" class="btn btn-default"><?=t("Edit")?></a>
    <?php } ?>
    <?php foreach($subEntities as $entity) { ?>
        <a href="<?=URL::to($c->getCollectionPath(), 'create_entry', $entity->getID(), $entry->getID())?>" class="btn btn-default"><?=t('Add %s', $entity->getEntityDisplayName())?></a>
    <?php } ?>
    <?php if ($allowDelete) { ?>
        <button type="button" class="btn btn-danger" data-dialog="delete-entry"><?= t('Delete') ?></button>
    <?php } ?>
</div>

<?php if ($allowDelete) { ?>
<div style="display: none">
    <div id="ccm-dialog-delete-entry" class="ccm-ui">
        <form method="post" action="<?=$view->action('delete_entry')?>">
            <?=Core::make("token")->output('delete_entry')?>
            <input type="hidden" name="entry_id" value="<?=$entry->getID()?>">
            <p><?=t('Are you sure you want to delete this entry? This cannot be undone.')?></p>
            <div class="dialog-buttons">
                <button class="btn btn-default pull-left" onclick="jQuery.fn.dialog.closeTop()"><?=t('Cancel')?></button>
                <button class="btn btn-danger pull-right" onclick="$('#ccm-dialog-delete-entry form').submit()"><?=t('Delete Entry')?></button>
            </div>
        </form>
    </div>

</div>
<?php } ?>

<?php

if (is_object($renderer)) {
    ?>


    <?php
        echo $renderer->render($entry);
    ?>

<?php 
} ?>

<?php if ($allowDelete) { ?>
<script type="text/javascript">
    $(function() {
        $('[data-dialog]').on('click', function() {
            var $element = $('#ccm-dialog-' + $(this).attr('data-dialog'));
            if ($(this).attr('data-dialog-title')) {
                var title = $(this).attr('data-dialog-title');
            } else {
                var title = $(this).text();
            }
            jQuery.fn.dialog.open({
                element: $element,
                modal: true,
                width: 320,
                title: title,
                height: 'auto'
            });
        });
    });
</script>
<?php } ?>