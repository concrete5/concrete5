<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<div class="ccm-ui ccm-search-fields-advanced-dialog" data-dialog="advanced-search">

    <?php
        $tabs = [
            ['fields', t('Filters'), true],
            ['columns', t('Customize Results')]
        ];
        if ($supportsSavedSearch) {
            $tabs[] = ['search-presets', t('Search Presets')];
        }
        echo Core::make('helper/concrete/ui')->tabs($tabs);
    ?>

    <form data-form="advanced-search" method="post" action="<?=$controller->getSubmitAction()?>">

        <div class="ccm-tab-content" id="ccm-tab-content-fields">

            <?php
                print $searchFieldSelectorElement->render();
            ?>

        </div>

        <div class="ccm-tab-content" id="ccm-tab-content-columns">
            <?php
                print $customizeElement->render();
            ?>
        </div>

        <?php if ($supportsSavedSearch) { ?>

            <div class="ccm-tab-content" id="ccm-tab-content-search-presets">
                <?php if (!empty($searchPresets)) { ?>
                    <table class="ccm-search-results-table ccm-search-presets-table">
                        <tbody>
                            <?php foreach ($searchPresets as $searchPreset) { ?>
                                <tr data-search-preset-id="<?= $searchPreset->getId(); ?>" data-search-preset-name="<?= $searchPreset->getPresetName(); ?>" data-action="<?= $controller->getSavedSearchBaseURL($searchPreset); ?>">
                                    <td style="padding-left: 15px;"><?= $searchPreset->getPresetName(); ?></td>
                                    <td valign="top" style="text-align: right; padding-right: 15px;">
                                        <div class="btn-group">
                                            <button type="button" data-button-action="edit-search-preset" dialog-title="<?= t('Edit Search Preset'); ?>" data-tree-action-url="<?= $controller->getSavedSearchEditURL($searchPreset); ?>" class="btn btn-info btn-xs"><?= t('Edit'); ?></button>
                                            <button type="button" data-button-action="delete-search-preset" dialog-title="<?= t('Delete Search Preset'); ?>" data-tree-action-url="<?= $controller->getSavedSearchDeleteURL($searchPreset); ?>" class="btn btn-danger btn-xs"><?= t('Delete'); ?></button>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                <?php } else { ?>
                    <p><?= t('There is no search preset.'); ?></p>
                <?php } ?>
            </div>

            <script type="text/javascript">
                $(function() {
                    $('div[data-dialog=advanced-search]').concreteAdvancedSearchPresetSelector();
                })
            </script>

        <?php } ?>

    </form>

    <div class="dialog-buttons">
        <button class="btn btn-default pull-left" data-dialog-action="cancel"><?=t('Cancel')?></button>
        <button type="button" onclick="$('form[data-form=advanced-search]').trigger('submit')" class="btn btn-primary pull-right"><?=t('Search')?></button>
        <?php if ($supportsSavedSearch) { ?>
            <button type="button" data-button-action="save-search-preset" class="btn btn-success pull-right"><?=t('Save as Search Preset')?></button>
        <?php } ?>
    </div>

</div>

<?php if ($supportsSavedSearch) { ?>

    <div style="display: none">
        <div data-dialog="save-search-preset" class="ccm-ui">
            <form data-form="save-preset" action="<?=$controller->action('save_preset')?>" method="post">
                <?= $form->hidden('presetID'); ?>
                <?= $form->hidden('objectID', $controller->getObjectID()); ?>
                <div class="form-group">
                    <?=$form->label('presetName', t('Name'))?>
                    <?=$form->text('presetName')?>
                </div>
            </form>
            <div class="dialog-buttons">
                <button class="btn btn-default pull-left" onclick="jQuery.fn.dialog.closeTop()"><?=t('Cancel')?></button>
                <button class="btn btn-primary pull-right" data-button-action="save-search-preset-submit"><?=t('Save Preset')?></button>
            </div>
        </div>
    </div>

<?php } ?>
