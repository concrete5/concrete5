<?php

use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker;

defined('C5_EXECUTE') or die('Access Denied.');

// @var Concrete\Core\Page\View\PageView $view
// @var Concrete\Core\Form\Service\Form $form
// @var Concrete\Core\Validation\CSRF\Token $token
// @var Concrete\Core\Form\Service\Widget\Color $form_color
// @var Concrete\Controller\SinglePage\Dashboard\System\Files\Thumbnails $controller

if (isset($type)) {
    // @var Concrete\Core\Entity\File\Image\Thumbnail\Type\Type $type
    // @var array $sizingModes
    // @var array $sizingModeHelps
    // @var bool $allowConditionalThumbnails
    // @var array $fileSetOptions [if $allowConditionalThumbnails is true]
    // @var array $fileSets [if $allowConditionalThumbnails is true]
    // @var bool $manipulationLibrarySupportsAnimations
    if ($type->getID() !== null && !$type->isRequired()) {
        ?>
        <div class="ccm-dashboard-header-buttons">
            <form method="post" action="<?= $view->action('delete', $type->getID())?> ">
                <?php $token->output('thumbnailtype-delete-' . $type->getID()) ?>
                <button type="button" class="btn btn-danger" data-action="delete-type"><?= t('Delete Type') ?></button>
            </form>
        </div>
        <?php
    }
    ?>
    <form method="POST" action="<?= $view->action('save', $type->getID() ?: 'new') ?>">
        <?php $token->output('thumbnailtype-save-' . ($type->getID() ?: 'new')) ?>
        <div class="form-group">
            <?= $form->label('ftTypeHandle', t('Handle')) ?>
            <div class="input-group">
                <?= $form->text('ftTypeHandle', $type->getHandle(), ['required' => 'required', 'maxlength' => '255'] + ($type->getID() !== null && $type->isRequired() ? ['readonly' => 'readonly'] : [])) ?>
                <span class="input-group-text"><i class="fa fa-asterisk"></i></span>
            </div>
        </div>
        <div class="form-group">
            <?= $form->label('ftTypeName', t('Name')) ?>
            <div class="input-group">
                <?=$form->text('ftTypeName', $type->getName(), ['required' => 'required', 'maxlength' => '255']) ?>
                <span class="input-group-text"><i class="fa fa-asterisk"></i></span>
            </div>
        </div>
        <div class="form-group">
            <?= $form->label('ftTypeWidth', t('Width')) ?>
            <div class="input-group">
                <?= $form->number('ftTypeWidth', $type->getWidth() ?: '', ['min' => '1']) ?>
                <span class="input-group-text"><?= t('px') ?></span>
            </div>
        </div>
        <div class="form-group">
            <?= $form->label('ftTypeHeight', t('Height')) ?>
            <div class="input-group">
                <?=$form->text('ftTypeHeight', $type->getHeight() ?: '', ['min' => '1']) ?>
                <span class="input-group-text"><?= t('px') ?></span>
            </div>
        </div>
        <div class="form-group">
            <?= $form->label('ftTypeSizingMode', t('Sizing Mode')) ?>
            <?= $form->select('ftTypeSizingMode', $sizingModes, $type->getSizingMode()) ?>
            <p class="help-block" id="sizingmode-help"><span><?= $sizingModeHelps[$type->getSizingMode()] ?></span></p>
        </div>
        <div class="form-group">
            <?= $form->label('', t('Options')) ?>
            <div class="form-check">
                <?= $form->checkbox('ftUpscalingEnabled', '1', $type->isUpscalingEnabled()) ?>
                <label for="ftUpscalingEnabled" class="form-check-label">
                    <?= t('Allow upscaling images smaller than the thumbnail size') ?>
                </label>
            </div>
            <div class="form-check">
                <?= $form->checkbox('ftKeepAnimations', '1', $type->isKeepAnimations()) ?>
                <label for="ftKeepAnimations" class="form-check-label">
                    <?= t('Create animated thumbnails for animated images') ?>
                    <span class="small text-muted" id="ftKeepAnimations-warning" <?= $type->isKeepAnimations() ? '' : ' style="display: none"' ?>>
                        <br />
                        <i class="fa fa-exclamation-triangle" aria-hidden="true" style="color: red"></i>
                        <?php
                        if ($manipulationLibrarySupportsAnimations) {
                            ?>
                            <?= t('Creating animated thumbnails may require a lot of memory and may require more processing time.') ?>
                            <?php
                        } else {
                            $optionsPageName = t('Image Options');
                            $optionsPage = Page::getByPath('/dashboard/system/files/image_uploading');
                            if ($optionsPage && !$optionsPage->isError()) {
                                $optionsPageName = h(t($optionsPage->getCollectionName()));
                                $optionsPagePermissions = new Checker($optionsPage);
                                if ($optionsPagePermissions->canViewPage()) {
                                    $optionsPageName = '<a href="' . h($optionsPage->getCollectionLink()) . '" target="_blank">' . $optionsPageName . '</a>';
                                }
                            }
                            ?>
                            <?= t('This requires that concrete5 is configured to use the ImageMagick manipulation library.') ?>
                            <br />
                            <?= t(/*i18n: %s is the name of a page*/ 'You can configure it in the %s page.', $optionsPageName) ?>
                            <?php
                        }
                        ?>
                    </span>
                    <script>
                    $(document).ready(function() {
                        $('#ftKeepAnimations')
                            .on('change', function() {
                                $('#ftKeepAnimations-warning').toggle(this.checked);
                            })
                            .trigger('change')
                        ;
                    });
                    </script>
                </label>
            </div>
        </div>

        <div class="form-group">
            <?= $form->label('ftSaveAreaBackgroundColor', t('Image Editor Background Color')); ?>
            <div>
                <?= $form_color->output('ftSaveAreaBackgroundColor', $type->getSaveAreaBackgroundColor(), ['preferredFormat' => 'hex']); ?>
            </div>
            <p class="help-block"><?= t('Leave this empty to use a transparent background.'); ?></p>
        </div>

        <?php
        if ($allowConditionalThumbnails) {
            $selectedFileSets = [];
            foreach ($type->getAssociatedFileSets() as $associatedFileSet) {
                $fileSetID = $associatedFileSet->getFileSetID();
                if (isset($fileSets[$fileSetID])) {
                    $selectedFileSets[] = $fileSetID;
                }
            }
            $fileSetAttributes = [];
            if (empty($selectedFileSets)) {
                $fileSetOption = $controller::FILESETOPTION_ALL;
                $fileSetAttributes['disabled'] = 'disabled';
            } else {
                $fileSetOption = $type->isLimitedToFileSets() ? $controller::FILESETOPTION_ONLY : $controller::FILESETOPTION_ALL_EXCEPT;
            }
            ?>
            <div class="form-group">
                <?= $form->label('fileSetOption', t('Conditional Thumbnails')) ?>
                <?= $form->select('fileSetOption', $fileSetOptions, $fileSetOption, ['required' => 'required']) ?>
            </div>
            <div>
                <?= $form->label('fileSets', 'File Sets') ?>
                <div class="ccm-search-field-content">
                    <?= $form->selectMultiple('fileSets', $fileSets, $selectedFileSets, $fileSetAttributes) ?>
                </div>
            </div>
            <?php
        }
        ?>
        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
                <a href="<?= $view->action('') ?>" class="btn float-start btn-secondary"><?= t('Back') ?></a>
                <?php
                if ($type->getID() !== null) {
                    ?>
                    <button type="submit" class="btn btn-primary float-end"><?= t('Save') ?></button>
                    <?php
                } else {
                    ?>
                    <button type="submit" class="btn btn-primary float-end"><?= t('Add') ?></button>
                    <?php
                }
                ?>
            </div>
        </div>
    </form>
    <script>
    $(document).ready(function() {
        $('button[data-action=delete-type]').on('click', function(e) {
            e.preventDefault();
            if (window.confirm(<?= json_encode(t('Delete this thumbnail type?')) ?>)) {
                $(this).closest('form').submit();
            }
        });
        var sizingModeHelps = <?= json_encode($sizingModeHelps)?>;
        $('#ftTypeSizingMode')
            .on('change', function(e) {
                var mode = $(this).val();
                $('#sizingmode-help span').html(mode in sizingModeHelps ? sizingModeHelps[mode] : '');
            })
            .trigger('change')
        ;
        <?php

        if ($allowConditionalThumbnails) {
            ?>
            var $fileSets = $('#fileSets');
            $fileSets.selectpicker({
                width: '100%'
            });
            $('#fileSetOption')
                .on('change', function() {
                    if ($(this).val() === <?= json_encode($controller::FILESETOPTION_ALL) ?>) {
                        $fileSets.prop('disabled', true);
                    } else {
                        $fileSets.prop('disabled', false);
                    }
                    $fileSets.selectpicker('refresh');
                })
                .trigger('change')
            ;
            <?php
        }

        ?>
    });
    </script>
    <?php
} else {
    // @var Concrete\Core\Entity\File\Image\Thumbnail\Type\Type[] $types
    ?>
    <div class="ccm-dashboard-header-buttons btn-group">
        <a href="<?= $view->action('options')?>" class="btn btn-secondary"><?= t('Options') ?></a>
        <a href="<?= $view->action('edit', 'new')?>" class="btn btn-primary"><?= t('Add Type') ?></a>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th><?= t('Handle') ?></th>
                <th><?= t('Name') ?></th>
                <th><?= t('Width') ?></th>
                <th><?= t('Height') ?></th>
                <th><?= t('Sizing') ?></th>
                <th><?= t('Required') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($types as $type) {
                ?>
                <tr>
                    <td><a href="<?= $view->action('edit', $type->getID()) ?>"><?= h($type->getHandle()) ?></a></td>
                    <td><?= h($type->getDisplayName()) ?></td>
                    <td><?= $type->getWidth() ?: '<span class="text-muted">' . t('Automatic') . '</span>' ?></td>
                    <td><?= $type->getHeight() ?: '<span class="text-muted">' . t('Automatic') . '</span>' ?></td>
                    <td><?= h($type->getSizingModeDisplayName()) ?></td>
                    <td><?= $type->isRequired() ? t('Yes') : t('No') ?></td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
    <?php
}
