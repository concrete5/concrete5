<?php

defined('C5_EXECUTE') or die('Access Denied');

/**
 * @var Concrete\Controller\SinglePage\Dashboard\System\Seo\Searchindex $controller
 * @var Concrete\Core\Form\Service\Form $form
 * @var Concrete\Core\Validation\CSRF\Token $token
 * @var array $availableAreaIndexMethods
 * @var string $areaIndexMethod
 * @var string[] $availableAreas
 * @var string[] $selectedAreas
 */
?>

<div class="d-none">
    <div data-dialog-wrapper="ccm-searchindex-clear">
        <form method="post" action="<?= $controller->action('clear_index') ?>">
            <?php $token->output('clear_index') ?>
            <p><?= t('Once the index is clear, you must reindex your site from the Automated Jobs page.') ?></p>
            <div class="dialog-buttons">
                <button class="btn btn-secondary float-start" onclick="jQuery.fn.dialog.closeTop()"><?=t('Cancel')?></button>
                <button class="btn btn-danger float-end" onclick="$('div[data-dialog-wrapper=ccm-searchindex-clear] form').submit()"><?= t('Clear Index') ?></button>
            </div>
        </form>
    </div>
</div>

<form method="POST" id="ccm-search-index-manage" action="<?= $controller->action('save') ?>">
    <?php $token->output('update_search_index') ?>

    <div class="form-group">
        <?= $form->label('areaIndexMethod', t('Indexing Method')) ?>
        <?= $form->select('areaIndexMethod', $availableAreaIndexMethods, $areaIndexMethod, ['required' => 'required']) ?>
	</div>

    <div class="form-group">
        <?= $form->label('', t('Areas')) ?>
        <?php
        foreach ($availableAreas as $arHandle) {
            $elementID = preg_replace('/\W/', '-', "ccm-index-area{$arHandle}");
            ?>
            <div class="form-check">
                <?= $form->checkbox('arHandle[]', h($arHandle), in_array($arHandle, $selectedAreas, true), ['id' => $elementID]) ?>
                <label class="form-check-label" for="<?= $elementID ?>"><?= h($arHandle) ?></label>
            </div>
            <?php
        }
        ?>
    </div>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <div class="float-end">
                <a href="javascript:void(0)" class="btn btn-danger" data-dialog="ccm-searchindex-clear"><?= t('Clear Search Index') ?></a>
                <button type="submit" class="btn btn-primary"><?= t('Save') ?></button>
            </div>
        </div>
    </div>
</form>
