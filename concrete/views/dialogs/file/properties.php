<?php
defined('C5_EXECUTE') or die("Access Denied.");
/**
 * @var $file \Concrete\Core\Entity\File\Version
 */
?>
<form method="post" data-dialog-form="save-file-properties" action="<?php echo $controller->action('submit') ?>">
    <div class="ccm-ui">
        <fieldset>
            <legend><?= t('Basic Information'); ?></legend>
                <div class="form-group">
                    <?= $form->label('title', t('Title')); ?>
                    <?= $form->text('title', $file->getTitle()); ?>
                </div>
                <div class="form-group">
                    <?= $form->label('description', t('Description')); ?>
                    <?= $form->textarea('description', $file->getDescription()); ?>
                </div>
            <div class="form-group">
                <?= $form->label('tags', t('Tags')); ?>
                <?= $form->textarea('tags', $file->getTags()); ?>
            </div>
        </fieldset>
        <fieldset>
            <legend><?= t('Custom Attributes'); ?></legend>
            <?php
            $keySelector->render();
            ?>
        </fieldset>
        <div class="dialog-buttons">
            <button class="btn btn-secondary float-start" data-dialog-action="cancel">
                <?php echo t('Cancel') ?>
            </button>

            <button type="button" data-dialog-action="submit" class="btn btn-primary float-end">
                <?php echo t('Save') ?>
            </button>
        </div>
    </div>
</form>

<script>
    $(function() {
        ConcreteEvent.subscribe('AjaxFormSubmitSuccess', function(e, data) {
            if (data.form === 'save-file-properties') {
                window.location.reload();
            }
        });
    })
</script>
