<?php

defined('C5_EXECUTE') or die('Access Denied');

/**
 * @var Concrete\Controller\SinglePage\Dashboard\System\Seo\Excluded $controller
 * @var Concrete\Core\Form\Service\Form $form
 * @var Concrete\Core\Validation\CSRF\Token $token
 * @var string[] $defaultExcludedWords
 * @var string[] $excludedWords
 */

if ($excludedWords !== $defaultExcludedWords) {
    ?>
    <div class="d-none">
        <div data-dialog-wrapper="ccm-seowords-reset">
            <form method="post" action="<?= $controller->action('reset') ?>">
                <?php $token->output('reset') ?>
                <p><?= t('Are you sure you want to reset the reserved word list to the default value?') ?></p>
                <div class="dialog-buttons">
                    <button class="btn btn-secondary float-start" onclick="jQuery.fn.dialog.closeTop()"><?=t('Cancel')?></button>
                    <button class="btn btn-danger float-end" onclick="$('div[data-dialog-wrapper=ccm-seowords-reset] form').submit()"><?= t('Reset') ?></button>
                </div>
            </form>
        </div>
    </div>
    <?php
}
?>
<form method="POST" id="url-form" action="<?= $controller->action('save') ?>">
    <?php $token->output('excluded_words_save') ?>

	<div class="form-group">
        <?= $form->label('SEO_EXCLUDE_WORDS', t('Reserved Words')) ?>
        <?= $form->textarea('SEO_EXCLUDE_WORDS', implode(', ', $excludedWords), ['class' => 'font-monospace', 'rows' => '5', 'spellcheck' => 'false']) ?>
        <div class="text-muted">
            <?= t('Separate reserved words with a comma. These words will be automatically removed from URL slugs. To remove no words from URLs, delete all the words above.') ?>
        </div>
	</div>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <div class="float-end">
                <?php
                if ($excludedWords !== $defaultExcludedWords) {
                    ?>
                    <a href="javascript:void(0)" class="btn btn-danger" data-dialog="ccm-seowords-reset"><?= t('Reset To Default') ?></a>
                    <?php
                }
                ?>
                <button type="submit" class="btn btn-primary"><?= t('Save') ?></button>
            </div>
        
        </div>
    </div>
</form>
