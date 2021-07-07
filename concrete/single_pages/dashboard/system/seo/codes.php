<?php
defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Controller\SinglePage\Dashboard\System\Seo\Codes $controller
 * @var Concrete\Core\Form\Service\Form $form
 * @var Concrete\Core\Validation\CSRF\Token $token
 * @var string $tracking_code_footer
 * @var string $tracking_code_header
 */
?>

<div class="alert alert-info">
    <?= t('Any HTML you paste here will be inserted at either the bottom or top of every page in your website automatically.') ?>
</div>

<form id="tracking-code-form" action="<?= $controller->action('save') ?>" method="post">
    <?php $token->output('update_tracking_code') ?>

    <div class="form-group">
        <?= $form->label('tracking_code_header', t('Header Tracking Codes')) ?>
        <?= $form->textarea('tracking_code_header', $tracking_code_header, ['style' => 'height: 250px;', 'class' => 'font-monospace', 'spellcheck' => 'false']) ?>
    </div>

    <div class="form-group">
        <?= $form->label('tracking_code_footer', t('Footer Tracking Codes')) ?>
        <?= $form->textarea('tracking_code_footer', $tracking_code_footer, ['style' => 'height: 250px;', 'class' => 'font-monospace', 'spellcheck' => 'false']) ?>
    </div>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <button type="submit" class="btn btn-primary float-end"><?= t('Save') ?></button>
        </div>
    </div>
</form>
