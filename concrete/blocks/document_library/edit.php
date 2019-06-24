<?php
defined('C5_EXECUTE') or die('Access Denied.');
?>

<?php
$color = Core::make('helper/form/color');
echo Core::make('helper/concrete/ui')->tabs(array(
    array('sources', t('Sources'), true),
    array('header', t('Header')),
    array('results', t('Results'))
));

?>

<div class="ccm-tab-content" id="ccm-tab-content-sources">
    <div class="form-group">
        <?=$form->label('folderID', t('File Folder'))?>
        <?php
        $folders = [0 => '* Any folder'] + iterator_to_array($folders);
        echo $form->select('folderID', $folders, empty($folderID) ? 0 : $folderID);
        ?>
    </div>
    <?php if (count($fileSets)) { ?>
        <div class="form-group">
            <?=$form->label('fileset', t('File Set'))?>
            <?php foreach($fileSets as $set) { ?>
                <div class="checkbox"><label>
                    <?=$form->checkbox('fsID[]', $set->getFileSetID(),
                        in_array($set->getFileSetID(), $selectedSets)
                    )?>
                    <?=$set->getFileSetDisplayName()?>
                </label></div>
            <?php } ?>
        </div>

        <div class="form-group">
            <?=$form->label('setMode', t('Files must be'))?>
            <div class="radio">
                <label>
                    <?=$form->radio('setMode', 'all', $setMode)?>
                    <?=t('in all sets')?>
                </label>
            </div>
            <div class="radio">
                <label>
                    <?=$form->radio('setMode', 'any', $setMode)?>
                    <?=t('in any sets')?>
                </label>
            </div>
        </div>
    <?php } else { ?>
        <?=$form->label('fileset', t('File Set'))?>
        <p class="text-muted"><?=t('No file sets have been created.')?></p>
        <br/>
    <?php } ?>

    <div class="form-group">
        <?=$form->label('tags', t('Filter By Tag (optional)'))?>
        <?=$form->text('tags', isset($tags) ? $tags : '')?>
    </div>

    <div class="form-group">
        <?=$form->label('audience', t('Audience Contribution'))?>
        <div class="checkbox"><label>
                <?=$form->checkbox('allowFileUploading', 1, !empty($allowFileUploading), array('data-options-toggle' => 'enable-uploads'))?>
                <?=t('Enable Uploads.')?>
            </label></div>
        <div class="checkbox"><label>
                <?=$form->checkbox('allowInPageFileManagement', 1, !empty($allowInPageFileManagement))?>
                <?=t('Enable File Property Editing.')?>
            </label></div>
        <div class="checkbox"><label>
                <?=$form->checkbox('onlyCurrentUser', 1, !empty($onlyCurrentUser))?>
                <?=t('Only show files owned by current logged-in user.')?>
            </label></div>

        <div class="text-muted"><?=t('Note: the above options are dependent on <a href="%s">file permissions</a>.', URL::to('/dashboard/system/files/permissions'))?></div>
    </div>

    <div class="form-group" data-options="enable-uploads">
        <label class="control-label" for="ccm-form-fileset"><?=t('Add Uploaded Files to Set')?></label>
            <?php

            $fsl = new \Concrete\Core\File\Set\SetList();
            $fileSets = $fsl->get();
            $sets = array('0' => t('None'));
            foreach($fileSets as $fileSet) {
                $fsp = new Permissions($fileSet);
                if ($fsp->canAddFiles() && $fsp->canSearchFiles()) {
                    $sets[$fileSet->getFileSetID()] = $fileSet->getFileSetName();
                }
            }
            print $form->select('addFilesToSetID', $sets, isset($addFilesToSetID) ? $addFilesToSetID : null);
            ?>
    </div>



</div>

<div class="ccm-tab-content" id="ccm-tab-content-header">

    <div class="checkbox"><label>
            <?=$form->checkbox('enableSearch', 1, !empty($enableSearch), array('data-options-toggle' => 'search'))?>
            <?=t('Enable Search')?>
        </label>
    </div>

    <fieldset data-options="search">
        <legend><?=t('Advanced Search Properties')?></legend>

        <?php foreach($searchProperties as $key => $name) { ?>
            <div class="checkbox"><label>
                    <?=$form->checkbox('searchProperties[]', $key, in_array($key, $searchPropertiesSelected))?>
                    <?=$name?>
                </label>
            </div>
        <?php } ?>
    </fieldset>

    <div class="form-group">
        <?=$form->label('orderBy', t('Sort By'))?>
        <div class="form-inline">
            <?=$form->select('orderBy', $orderByOptions, isset($orderBy) ? $orderBy : null);?>
            <label class="checkbox-inline">
                <?=$form->checkbox('displayOrderDesc', 1, !empty($displayOrderDesc))?>
                <?=t('Descending')?>
            </label>
        </div>
    </div>

    <fieldset>
        <legend><?=t('Design')?></legend>
        <div class="form-group">
            <?=$form->label('headerBackgroundColor', t('Header Background'))?>
            <div>
                <?=$color->output('headerBackgroundColor', isset($headerBackgroundColor) ? $headerBackgroundColor : null)?>
            </div>
        </div>
        <div class="form-group">
            <?=$form->label('headerBackgroundColorActiveSort', t('Header Background (Active Sort)'))?>
            <div>
                <?=$color->output('headerBackgroundColorActiveSort', isset($headerBackgroundColorActiveSort) ? $headerBackgroundColorActiveSort : null)?>
            </div>
        </div>
        <div class="form-group">
            <?=$form->label('headerTextColor', t('Header Text Color'))?>
            <div>
                <?=$color->output('headerTextColor', isset($headerTextColor) ? $headerTextColor : null)?>
            </div>
        </div>
    </fieldset>

</div>

<div class="ccm-tab-content" id="ccm-tab-content-results">
    <div class="form-group">
        <?=$form->label('tableName', t('Table Name'))?>
        <?=$form->text('tableName', isset($tableName) ? $tableName : '', array('maxlength' => '128'))?>
    </div>
    <div class="form-group">
        <?=$form->label('tableDescription', t('Table Description'))?>
        <?=$form->text('tableDescription', isset($tableDescription) ? $tableDescription : '', array('maxlength' => '128'))?>
    </div>
    <div class="form-group">
        <?=$form->label('displayLimit', t('Items Per Page'))?>
        <?=$form->text('displayLimit', $displayLimit)?>
    </div>
    <div class="form-group">
        <?=$form->label('', t('Download File Method'))?>
        <div class="radio"><label><?=$form->radio('downloadFileMethod', 'browser', $downloadFileMethod)?>
            <?=t('Display in browser (if possible)')?></label>
        </div>
        <div class="radio"><label><?=$form->radio('downloadFileMethod', 'force', $downloadFileMethod)?>
            <?=t('Force Download')?></label>
        </div>
    </div>
    <div class="form-group">
        <?=$form->label('', t('Height Mode'))?>
        <div class="radio"><label><?=$form->radio('heightMode', 'auto', $heightMode)?>
            <?=t('Auto')?></label>
        </div>
        <div class="radio"><label><?=$form->radio('heightMode', 'fixed', $heightMode)?>
            <?=t('Fixed')?></label>
        </div>
    </div>
    <div class="form-group" data-options="height-mode">
        <?=$form->label('fixedHeightSize', t('Fixed Height Size'))?>
        <?=$form->text('fixedHeightSize', isset($fixedHeightSize) ? $fixedHeightSize : '')?>
    </div>

    <fieldset>
        <legend><?=t('Properties to Display')?></legend>
        <table style="width: auto" class="table table-striped">
            <tr>
                <th><?=t("Properties")?></th>
                <th><?=t("Do Not Display")?></th>
                <th><?=t("Display")?></th>
                <th><?=t("Displayed & Sortable")?></th>
            </tr>

            <?php foreach($viewProperties as $key => $name) { ?>
                <tr>
                    <td><?=$name?></td>
                    <td style="text-align: center"><?=$form->radio('viewProperties['.$key.']', -1, in_array($key, $viewPropertiesDoNotDisplay), array('data-view-property' => $key))?></td>
                    <td style="text-align: center"><?=$form->radio('viewProperties['.$key.']', 1, in_array($key, $viewPropertiesDisplay), array('data-view-property' => $key))?></td>
                    <td style="text-align: center"><?=$form->radio('viewProperties['.$key.']', 5, in_array($key, $viewPropertiesDisplaySortable), array('data-view-property' => $key))?></td>
                </tr>
            <?php } ?>
        </table>
    </fieldset>

    <fieldset data-options="thumbnail">
        <legend><?=t('Thumbnail Settings')?></legend>
        <div class="form-group">
            <?=$form->label('maxThumbWidth', t('Width'))?>
            <?=$form->text('maxThumbWidth', isset($maxThumbWidth) ? $maxThumbWidth : '')?>
        </div>
        <div class="form-group">
            <?=$form->label('maxThumbHeight', t('Height'))?>
            <?=$form->text('maxThumbHeight', isset($maxThumbHeight) ? $maxThumbHeight : '')?>
        </div>
    </fieldset>

    <fieldset>
        <legend><?=t('Expandable Properties')?></legend>

        <?php foreach($expandableProperties as $key => $name) { ?>
            <div class="checkbox"><label>
                <?=$form->checkbox('expandableProperties[]', $key, in_array($key, $expandablePropertiesSelected))?>
                <?=$name?>
            </label>
            </div>
        <?php } ?>
    </fieldset>

    <fieldset>
        <legend><?=t('Design')?></legend>
        <div class="form-group">
            <?=$form->label('', t('Table Striping'))?>
            <div class="radio">
                <label>
                    <?=$form->radio('tableStriped', 0, isset($tableStriped) ? $tableStriped : null)?>
                    <?=t('Off (all rows the same color)')?>
                </label>
            </div>
            <div class="radio">
                <label>
                    <?=$form->radio('tableStriped', 1, isset($tableStriped) ? $tableStriped : null)?>
                    <?=t('On (change color of alternate rows)')?>
                </label>
            </div>
        </div>

        <div class="form-group" data-options="table-striped">
            <?=$form->label('rowBackgroundColorAlternate', t('Alternate Row Background Color'))?>
            <div>
                <?=$color->output('rowBackgroundColorAlternate', isset($rowBackgroundColorAlternate) ? $rowBackgroundColorAlternate : null)?>
            </div>
        </div>
    </fieldset>

</div>


<script type="text/javascript">
    $(function() {
        $('input[type=checkbox][data-options-toggle]').on('change', function() {
            var option = $(this).attr('data-options-toggle');
            if ($(this).is(':checked')) {
                $('[data-options=' + option + ']').show();
            } else {
                $('[data-options=' + option + ']').hide();
            }
        }).trigger('change');
        $('input[type=radio][data-view-property=thumbnail]').on('change', function() {
            var value = $('input[type=radio][data-view-property=thumbnail]:checked').val();
            if (value != '-1') {
                $('[data-options=thumbnail]').show();
            } else {
                $('[data-options=thumbnail]').hide();
            }
        }).trigger('change');
        $('input[type=radio][name=heightMode]').on('change', function() {
            var value = $('input[type=radio][name=heightMode]:checked').val();
            if (value == 'fixed') {
                $('[data-options=height-mode]').show();
            } else {
                $('[data-options=height-mode]').hide();
            }
        }).trigger('change');
        $('input[type=radio][name=tableStriped]').on('change', function() {
            var value = $('input[type=radio][name=tableStriped]:checked').val();
            if (value == '1') {
                $('[data-options=table-striped]').show();
            } else {
                $('[data-options=table-striped]').hide();
            }
        }).trigger('change');
    });
</script>
