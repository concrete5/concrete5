<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="ccm-ui ccm-search-fields-advanced-dialog" data-dialog="advanced-search">

    <?php
        $tabs = [
            ['tab-content-fields', t('Filters'), true],
            ['tab-content-columns', t('Customize Results')]
        ];
        if ($supportsSavedSearch) {
            $tabs[] = ['tab-content-search-presets', t('Search Presets')];
        }
        echo Core::make('helper/concrete/ui')->tabs($tabs);
    ?>
    <form data-form="advanced-search" method="<?=$controller->getSubmitMethod()?>" action="<?=$controller->getSubmitAction()?>">
        <div class="tab-content">

            <div class="tab-pane active" id="tab-content-fields">

                <p class="mb-0"><small class="text-muted"><?=t('Add custom search fields based on your needs.')?></small></p>

                <?php
                    print $searchFieldSelectorElement->render();
                ?>

            </div>

            <div class="tab-pane" id="tab-content-columns">

                <p><small class="text-muted"><?=t('Customize the columns, column order, sort and number of results for your search.')?></small></p>

                <?php
                    print $customizeElement->render();
                ?>
            </div>

            <?php if ($supportsSavedSearch) { ?>

                <div class="tab-pane" id="tab-content-search-presets">
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
                        <p class="mb-0"><small class="text-muted"><?=t('No search presets found.')?></small></p>
                    <?php } ?>
                </div>

            <?php } ?>
        </div>

    </form>
    <div class="dialog-buttons">
        <?php if ($supportsSavedSearch) { ?>
            <button type="button" data-button-action="save-search-preset" class="btn btn-secondary float-end"><?=t('Save Search')?></button>
        <?php } ?>
        <button type="button" onclick="$('form[data-form=advanced-search]').trigger('submit')" class="btn btn-primary float-end"><?=t('Search')?></button>
    </div>

</div>

<?php if ($supportsSavedSearch) { ?>
<script type="text/javascript">
    $(function() {
        $('div[data-dialog=advanced-search]').concreteAdvancedSearchPresetSelector()
    })
</script>
<?php } ?>

<?php if ($supportsSavedSearch) { ?>

    <div style="display: none">
        <div data-dialog="save-search-preset" class="ccm-ui">
            <form data-form="save-preset" action="<?=$controller->getSavePresetAction()?>" method="post">
                <?= $form->hidden('presetID'); ?>
                <div class="form-group">
                    <?=$form->label('presetName', t('Name'))?>
                    <?=$form->text('presetName')?>
                </div>
            </form>
            <div class="dialog-buttons">
                <button class="btn btn-secondary" onclick="jQuery.fn.dialog.closeTop()"><?=t('Cancel')?></button>
                <button class="btn btn-primary float-end ms-auto" data-button-action="save-search-preset-submit"><?=t('Save Preset')?></button>
            </div>
        </div>
    </div>

<?php } ?>
