<?php
defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Controller\Dialog\Page\EditAlias $controller
 * @var Concrete\Core\View\DialogView $view
 * @var Concrete\Core\Form\Service\Form $form
 * @var string $customAliasName
 * @var string $aliasHandle
 **/

?>
<div class="ccm-ui">
    <form class="form-stacked" data-dialog-form="edit-alias" method="post" action="<?= $controller->action('submit') ?>">
        <div class="form-group">
            <?= $form->label('customAliasName', t('Name')) ?>
            <?= $form->text('customAliasName', $customAliasName, ['autofocus' => 'autofocus', 'placeholder' => t('Empty: use name of aliased page')]) ?>
        </div>
        <div class="form-group">
            <?= $form->label('aliasHandle', t('URL Slug'), ['class' => 'launch-tooltip form-label', 'title' => t('This page must always be available from at least one URL. This is that URL.')]) ?>
            <?= $form->text('aliasHandle', $aliasHandle, ['required' => 'required', 'maxlength' => 255]) ?>
        </div>
        <div class="dialog-buttons">
            <button class="btn btn-default pull-left" data-dialog-action="cancel"><?= t('Cancel') ?></button>
            <button type="button" data-dialog-action="submit" class="btn btn-primary pull-right"><?= t('Save') ?></button>
        </div>
    </form>
</div>

<script>
$(document).ready(function() {
    ConcreteEvent.unsubscribe('AjaxFormSubmitSuccess.ccmSitemapAliasSaved');
    ConcreteEvent.subscribe('AjaxFormSubmitSuccess.ccmSitemapAliasSaved', function(e, data) {
        if (data.form === 'edit-alias') {
            ConcreteEvent.publish('SitemapUpdatePageRequestComplete', {'cID': data.response.cID});
        }
    });
});
</script>
