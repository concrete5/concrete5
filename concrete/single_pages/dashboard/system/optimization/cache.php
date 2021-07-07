<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Controller\SinglePage\Dashboard\System\Optimization\Cache $controller
 * @var Concrete\Core\Form\Service\Form $form
 * @var Concrete\Core\Validation\CSRF\Token $token
 * @var Concrete\Core\Localization\Service\Date $dateService
 * @var bool $enableBlockCache
 * @var bool $enableThemeCssCache
 * @var bool $compressThemePreprocessorOutput
 * @var bool $generateLessSourcemap
 * @var bool $enableOverrideCache
 * @var string $fullPageCacheGlobal
 * @var string $fullPageCacheLifetime
 * @var int $defaultCacheLifetime
 * @var int|null $fullPageCacheCustomLifetime
 */

?>
<form method="post" action="<?= $controller->action('update_cache') ?>">
    <?php $token->output('update_cache') ?>

    <fieldset>
        <legend>
            <?= t('Block Cache') ?>
            <span class="launch-tooltip" data-bs-placement="right" title="<?= t('Stores the output of blocks which support block caching') ?>"><i class="fas fa-question-circle"></i></span>
        </legend>
        <div class="form-group">
            <div class="form-check">
                <?= $form->radio('ENABLE_BLOCK_CACHE', '0', $enableBlockCache ? '1' : '0', ['id' => 'ENABLE_BLOCK_CACHE-0']) ?>
                <label class="form-check-label" for="ENABLE_BLOCK_CACHE-0"><?= t('Off - Good for development of custom blocks.') ?></label>
            </div>
            <div class="form-check">
                <?= $form->radio('ENABLE_BLOCK_CACHE', '1', $enableBlockCache ? '1' : '0', ['id' => 'ENABLE_BLOCK_CACHE-1']) ?>
                <label class="form-check-label" for="ENABLE_BLOCK_CACHE-1"><?= t('On - Helps speed up a live site.') ?></label>
            </div>
        </div>
    </fieldset>

    <fieldset>
        <legend>
            <?= t('Theme CSS Cache') ?>
            <span class="launch-tooltip" data-bs-placement="right" title="<?= t('Caches the output of customized theme stylesheets for faster loading. Turn off if you are editing LESS files in your theme directly.') ?>"><i class="fas fa-question-circle"></i></span>
        </legend>
        <div class="form-group">
            <div class="form-check">
                <?= $form->radio('ENABLE_THEME_CSS_CACHE', '0', $enableThemeCssCache ? '1' : '0', ['id' => 'ENABLE_THEME_CSS_CACHE-0']) ?>
                <label class="form-check-label" for="ENABLE_THEME_CSS_CACHE-0"><?= t('Off - Good for active theme development when using LESS files.') ?></label>
            </div>
            <div class="form-check">
                <?= $form->radio('ENABLE_THEME_CSS_CACHE', '1', $enableThemeCssCache ? '1' : '0', ['id' => 'ENABLE_THEME_CSS_CACHE-1']) ?>
                <label class="form-check-label" for="ENABLE_THEME_CSS_CACHE-1"><?= t('On - Helps speed up a live site.') ?></label>
            </div>
        </div>
    </fieldset>

    <fieldset>
        <legend>
            <?= t('Compress LESS Output') ?>
            <span class="launch-tooltip" data-bs-placement="right" title="<?= t('Determines whether compiled LESS stylesheets should output as compressed CSS. Uncompressed stylesheets are slightly larger but easier to read.') ?>"><i class="fas fa-question-circle"></i></span>
        </legend>
        <div class="form-group">
            <div class="form-check">
                <?= $form->radio('COMPRESS_THEME_PREPROCESSOR_OUTPUT', '0', $compressThemePreprocessorOutput ? '1' : '0', ['id' => 'COMPRESS_THEME_PREPROCESSOR_OUTPUT-0']) ?>
                <label class="form-check-label" for="COMPRESS_THEME_PREPROCESSOR_OUTPUT-0"><?= t('Off - Good for debugging generated CSS output.') ?></label>
            </div>
            <div class="form-check ms-4">
                <?= $form->checkbox('GENERATE_LESS_SOURCEMAP', '1', $generateLessSourcemap) ?>
                <label class="form-check-label" for="GENERATE_LESS_SOURCEMAP"><?= t('enable source maps in generated CSS files') ?></label>
            </div>
            <div class="form-check">
                <?= $form->radio('COMPRESS_THEME_PREPROCESSOR_OUTPUT', '1', $compressThemePreprocessorOutput ? '1' : '0', ['id' => 'COMPRESS_THEME_PREPROCESSOR_OUTPUT-1']) ?>
                <label class="form-check-label" for="COMPRESS_THEME_PREPROCESSOR_OUTPUT-1"><?= t('On - Helps speed up a live site.') ?></label>
            </div>
        </div>
    </fieldset>

    <fieldset>
        <legend><?= t('Overrides Cache') ?>
            <span class="launch-tooltip" data-bs-placement="right" title="<?= t('Stores the location and existence of source code files.') ?>"><i class="fas fa-question-circle"></i></span>
        </legend>
        <div class="form-group">
            <div class="form-check">
                <?= $form->radio('ENABLE_OVERRIDE_CACHE', '0', $enableOverrideCache ? '1' : '0', ['id' => 'ENABLE_OVERRIDE_CACHE-0']) ?>
                <label class="form-check-label" for="ENABLE_OVERRIDE_CACHE-0"><?= t('Off - Good for development.') ?></label>
            </div>
            <div class="form-check">
                <?= $form->radio('ENABLE_OVERRIDE_CACHE', '1', $enableOverrideCache ? '1' : '0', ['id' => 'ENABLE_OVERRIDE_CACHE-1']) ?>
                <label class="form-check-label" for="ENABLE_OVERRIDE_CACHE-1"><?= t('On - Helps speed up a live site.') ?></label>
            </div>
        </div>
    </fieldset>

    <fieldset>
        <legend>
            <?= t('Full Page Caching') ?>
            <span class="launch-tooltip" data-bs-placement="right" title="<?= t('Stores the output of an entire page.') ?>"><i class="fas fa-question-circle"></i></span>
        </legend>
        <div class="form-group">
            <div class="form-check">
                <?= $form->radio('FULL_PAGE_CACHE_GLOBAL', '0', $fullPageCacheGlobal ?: '0', ['id' => 'FULL_PAGE_CACHE_GLOBAL-0']) ?>
                <label class="form-check-label" for="FULL_PAGE_CACHE_GLOBAL-0"><?= t('Off - Turn it on by hand for specific pages.') ?></label>
            </div>
            <div class="form-check">
                <?= $form->radio('FULL_PAGE_CACHE_GLOBAL', 'blocks', $fullPageCacheGlobal, ['id' => 'FULL_PAGE_CACHE_GLOBAL-blocks']) ?>
                <label class="form-check-label" for="FULL_PAGE_CACHE_GLOBAL-blocks"><?= t('On - If blocks on the particular page allow it.') ?></label>
            </div>
            <div class="form-check">
                <?= $form->radio('FULL_PAGE_CACHE_GLOBAL', 'all', $fullPageCacheGlobal, ['id' => 'FULL_PAGE_CACHE_GLOBAL-all']) ?>
                <label class="form-check-label" for="FULL_PAGE_CACHE_GLOBAL-all"><?= t('On - In all cases.') ?></label>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label form-label"><?= t('Expire Pages from the Cache') ?></label>
            <div class="form-check">
                <?= $form->radio('FULL_PAGE_CACHE_LIFETIME', 'default', $fullPageCacheLifetime, ['id' => 'FULL_PAGE_CACHE_LIFETIME-default']) ?>
                <label class="form-check-label" for="FULL_PAGE_CACHE_LIFETIME-default"><?= t('Every %s (default setting).', $dateService->describeInterval($defaultCacheLifetime)) ?></label>
            </div>
            <div class="form-check">
                <?= $form->radio('FULL_PAGE_CACHE_LIFETIME', 'forever', $fullPageCacheLifetime, ['id' => 'FULL_PAGE_CACHE_LIFETIME-forever']) ?>
                <label class="form-check-label" for="FULL_PAGE_CACHE_LIFETIME-forever"><?= t('Only when manually removed or the cache is cleared.') ?></label>
            </div>
            <div class="form-check">
                <?= $form->radio('FULL_PAGE_CACHE_LIFETIME', 'custom', $fullPageCacheLifetime, ['id' => 'FULL_PAGE_CACHE_LIFETIME-custom']) ?>
                <label class="form-check-label" for="FULL_PAGE_CACHE_LIFETIME-custom"><?= t('Every %s minutes', $form->number('FULL_PAGE_CACHE_LIFETIME_CUSTOM', $fullPageCacheCustomLifetime, ['min' => 1, 'class' => 'd-inline form-control-sm', 'style' => 'width: 5rem'])) ?></label>
            </div>
        </div>
    </fieldset>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <button class="float-end btn btn-primary" type="submit" ><?= t('Save') ?></button>
        </div>
    </div>

</form>

<script>
$(document).ready(function() {
    'use strict';
    function fullPageCacheLifetimeChanged() {
        $('#FULL_PAGE_CACHE_LIFETIME_CUSTOM').attr('disabled', $('input[name="FULL_PAGE_CACHE_LIFETIME"]:checked').val() !== 'custom');
    }
    $('input[name="FULL_PAGE_CACHE_LIFETIME"]').on('change', function() {
        fullPageCacheLifetimeChanged();
    });
    $('input[name="FULL_PAGE_CACHE_LIFETIME"][value="custom"]').on('click', function() {
        setTimeout(function() { $('#FULL_PAGE_CACHE_LIFETIME_CUSTOM').focus(); }, 0);
    });
    fullPageCacheLifetimeChanged();
});
</script>
