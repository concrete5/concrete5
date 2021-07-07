<?php
defined('C5_EXECUTE') or die('Access Denied.');

/* @var Concrete\Core\Page\View\PageView $view */
/* @var Concrete\Core\Validation\CSRF\Token $token */
/* @var Concrete\Core\Form\Service\Form $form */

/* @var array $thumbnail_generation_strategies */
/* @var string $thumbnail_generation_strategy */
/* @var array $thumbnail_formats */
/* @var string $thumbnail_format */
/* @var bool $create_high_dpi_thumbnails */
/* @var string|null $imageOptionsURL */
?>
<form method="post" action="<?=$view->action('submit')?>">

    <?php $token->output('thumbnails-options') ?>

    <div class="form-group">
        <?= $form->label('thumbnail_generation_strategy', t('Thumbnail Generation Strategy')) ?>
        <?php
        foreach ($thumbnail_generation_strategies as $id => $name) {
            ?>
            <div class="form-check">
                <?= $form->radio('thumbnail_generation_strategy', $id, $id === $thumbnail_generation_strategy, ['required' => 'required']) ?>
                <label><?= h($name) ?></label>
            </div>
            <?php
        }
        ?>
    </div>

    <div class="form-group">
        <?= $form->label('thumbnail_format', t('Thumbnails Format')) ?>
        <?php
        foreach ($thumbnail_formats as $id => $name) {
            ?>
            <div class="form-check">
                <?= $form->radio('thumbnail_format', $id, $id === $thumbnail_format, ['required' => 'required']) ?>
                <label><?= h($name) ?></label>
            </div>
            <?php
        }
        ?>
    </div>

    <div class="form-group">
        <?= $form->label('create_high_dpi_thumbnails', t('Create high DPI thumbnails')) ?>
        <div class="form-check">
            <?= $form->checkbox('create_high_dpi_thumbnails', 1, $create_high_dpi_thumbnails) ?>
            <label><?= t('Create high DPI thumbnails') ?></label>
        </div>
    </div>

    <?php
    if ($imageOptionsURL !== null) {
        ?>
        <div class="alert alert-info">
            <?= t('Looking for other image options? You can find them <a href="%s">here</a>.', $imageOptionsURL) ?>
        </div>
        <?php
    }
    ?>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <a href="<?=URL::to('/dashboard/system/files/thumbnails')?>" class="btn btn-secondary"><?=t('Cancel')?></a>
            <button class="float-end btn btn-primary" type="submit"><?=t('Save')?></button>
        </div>
    </div>

</form>
