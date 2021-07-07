<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Application\Service\UserInterface;
use Concrete\Core\File\Set\SetList;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Form\Service\Widget\Color;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Permission\Response\Response;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Url;

/** @var array|string[] $folders */
/** @var array $selectedSets */
/** @var array $searchPropertiesSelected */
/** @var array $expandablePropertiesSelected */
/** @var array $viewPropertiesDoNotDisplay */
/** @var array $viewPropertiesDisplay */
/** @var array $viewPropertiesDisplaySortable */
/** @var array $orderByOptions */
/** @var string $setIds */
/** @var int $folderID */
/** @var string $setMode */
/** @var int $onlyCurrentUser */
/** @var string $tags */
/** @var array $viewProperties */
/** @var array $expandableProperties */
/** @var array $searchProperties */
/** @var string $orderBy */
/** @var int $displayLimit */
/** @var bool $displayOrderDesc */
/** @var int $addFilesToSetID */
/** @var int $maxThumbWidth */
/** @var int $maxThumbHeight */
/** @var bool $enableSearch */
/** @var string $heightMode */
/** @var string $downloadFileMethod */
/** @var int $fixedHeightSize */
/** @var string $headerBackgroundColor */
/** @var string $headerBackgroundColorActiveSort */
/** @var string $headerTextColor */
/** @var bool $allowFileUploading */
/** @var bool $allowInPageFileManagement */
/** @var string $tableName */
/** @var string $tableDescription */
/** @var bool $tableStriped */
/** @var string $rowBackgroundColorAlternate */
/** @var bool $hideFolders */

$app = Application::getFacadeApplication();
/** @var Color $color */
$color = $app->make(Color::class);
/** @var Form $form */
$form = $app->make(Form::class);
/** @var UserInterface $userInterface */
$userInterface = $app->make(UserInterface::class);

$fileSetService = new SetList();
$fileSetList = ['0' => t('None')];

foreach ($fileSetService->get() as $fileSet) {
    $filePermissionChecker = new Checker($fileSet);
    $responseObject = $filePermissionChecker->getResponseObject();

    if ($responseObject instanceof Response) {
        /** @noinspection PhpUnhandledExceptionInspection */
        if ($responseObject->validate("add_files") &&
            $responseObject->validate("search_files")) {

            $fileSetList[$fileSet->getFileSetID()] = $fileSet->getFileSetName();
        }
    }
}

/** @noinspection PhpParamsInspection */
$folders = [0 => '* Any folder'] + (array) $folders;

echo $userInterface->tabs([
    ['sources', t('Sources'), true],
    ['header', t('Header')],
    ['results', t('Results')]
]);

$hideFolders = isset($hideFolders) ? $hideFolders : false;
?>

<div class="tab-content">
    <div class="tab-pane show active" id="sources" role="tabpanel">
        <div class="form-group">
            <?php echo $form->label('folderID', t('File Folder')) ?>
            <?php echo $form->select('folderID', $folders, empty($folderID) ? 0 : $folderID); ?>
        </div>

        <?php if (count($fileSets)) { ?>
            <div class="form-group">
                <?php echo $form->label('fileset', t('File Set')) ?>

                <?php foreach ($fileSets as $set) { ?>
                    <div class="form-check">
                        <?php echo $form->checkbox('fsID[]', $set->getFileSetID(), in_array($set->getFileSetID(), $selectedSets), ["id" => "fsID_" . $set->getFileSetID()]) ?>
                        <?php echo $form->label("fsID_" . $set->getFileSetID(), $set->getFileSetDisplayName(), ["class" => "form-check-label"]); ?>
                    </div>
                <?php } ?>
            </div>

            <div class="form-group">
                <?= $form->label('showFolders', t('Show Folders')) ?>

                <div class="form-check">
                    <label>
                        <?= $form->checkbox('showFolders', '1', !$hideFolders) ?>
                        <?= t('Show Folders') ?>
                    </label>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->label('setMode', t('Files must be')) ?>

                <div class="form-check">
                    <?php echo $form->radio('setMode', 'all', $setMode, ["name" => "setMode", "id" => "setModeAll"]) ?>
                    <?php echo $form->label("setModeAll", t('in all sets'), ["class" => "form-check-label"]); ?>
                </div>

                <div class="form-check">
                    <?php echo $form->radio('setMode', 'any', $setMode, ["name" => "setMode", "id" => "setModeAny"]) ?>
                    <?php echo $form->label("setModeAny", t('in any sets'), ["class" => "form-check-label"]); ?>
                </div>
            </div>
        <?php } else { ?>
            <?php echo $form->label('fileset', t('File Set')) ?>

            <p class="text-muted">
                <?php echo t('No file sets have been created.') ?>
            </p>

            <br/>
        <?php } ?>

        <div class="form-group">
            <?php echo $form->label('tags', t('Filter By Tag (optional)')) ?>
            <?php echo $form->text('tags', isset($tags) ? $tags : '') ?>
        </div>

        <div class="form-group">
            <?php echo $form->label('audience', t('Audience Contribution')) ?>

            <div class="form-check">
                <?php echo $form->checkbox('allowFileUploading', 1, !empty($allowFileUploading), ['data-options-toggle' => 'enable-uploads']) ?>
                <?php echo $form->label("allowFileUploading", t('Enable Uploads.'), ["class" => "form-check-label"]); ?>
            </div>

            <div class="form-check">
                <?php echo $form->checkbox('allowInPageFileManagement', 1, !empty($allowInPageFileManagement)) ?>
                <?php echo $form->label("allowInPageFileManagement", t('Enable File Property Editing.'), ["class" => "form-check-label"]); ?>
            </div>

            <div class="form-check">
                <?php echo $form->checkbox('onlyCurrentUser', 1, !empty($onlyCurrentUser)) ?>
                <?php echo $form->label("onlyCurrentUser", t('Only show files owned by current logged-in user.'), ["class" => "form-check-label"]); ?>
            </div>

            <div class="text-muted">
                <?php echo t('Note: the above options are dependent on %s.', '<a href="' . (string)Url::to('/dashboard/system/files/permissions') . '">' . t('file permissions') . '</a>') ?>
            </div>
        </div>

        <div class="form-group" data-options="enable-uploads">
            <?php echo $form->label('addFilesToSetID', t("Add Uploaded Files to Set")); ?>
            <?php echo $form->select('addFilesToSetID', $fileSetList, isset($addFilesToSetID) ? $addFilesToSetID : null); ?>
        </div>
    </div>

    <div class="tab-pane" id="header" role="tabpanel">
        <div class="form-check">
            <?php echo $form->checkbox('enableSearch', 1, !empty($enableSearch), ['data-options-toggle' => 'search']) ?>
            <?php echo $form->label("enableSearch", t('Enable Search'), ["class" => "form-check-label"]); ?>
        </div>

        <fieldset data-options="search">
            <legend>
                <?php echo t('Advanced Search Properties') ?>
            </legend>

            <?php foreach ($searchProperties as $key => $name) { ?>
                <div class="form-check">
                    <?php echo $form->checkbox('searchProperties[]', $key, in_array($key, $searchPropertiesSelected), ["id" => "searchProperties_" . $key]) ?>
                    <?php echo $form->label("searchProperties_" . $key, $name, ["class" => "form-check-label"]); ?>
                </div>
            <?php } ?>
        </fieldset>

        <div class="form-group">
            <?php echo $form->label('orderBy', t('Sort By')) ?>

            <div class="row row-cols-lg-auto align-items-center">
                <div class="col-auto">
                    <?php echo $form->select('orderBy', $orderByOptions, isset($orderBy) ? $orderBy : null); ?>
                </div>
                <div class="col-auto">
                    <div class="form-check form-check-inline">
                        <?php echo $form->checkbox('displayOrderDesc', 1, !empty($displayOrderDesc)) ?>
                        <?php echo $form->label("displayOrderDesc", t('Descending'), ["class" => "form-check-label"]); ?>
                    </div>
                </div>
            </div>
        </div>

        <fieldset>
            <legend>
                <?php echo t('Design') ?>
            </legend>

            <div class="form-group">
                <?php echo $form->label('headerBackgroundColor', t('Header Background')) ?>

                <div>
                    <?php $color->output('headerBackgroundColor', isset($headerBackgroundColor) ? $headerBackgroundColor : null) ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->label('headerBackgroundColorActiveSort', t('Header Background (Active Sort)')) ?>

                <div>
                    <?php $color->output('headerBackgroundColorActiveSort', isset($headerBackgroundColorActiveSort) ? $headerBackgroundColorActiveSort : null) ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->label('headerTextColor', t('Header Text Color')) ?>

                <div>
                    <?php $color->output('headerTextColor', isset($headerTextColor) ? $headerTextColor : null) ?>
                </div>
            </div>
        </fieldset>
    </div>

    <div class="tab-pane" id="results" role="tabpanel">
        <div class="form-group">
            <?php echo $form->label('tableName', t('Table Name')) ?>
            <?php echo $form->text('tableName', isset($tableName) ? $tableName : '', array('maxlength' => '128')) ?>
        </div>

        <div class="form-group">
            <?php echo $form->label('tableDescription', t('Table Description')) ?>
            <?php echo $form->text('tableDescription', isset($tableDescription) ? $tableDescription : '', array('maxlength' => '128')) ?>
        </div>

        <div class="form-group">
            <?php echo $form->label('displayLimit', t('Items Per Page')) ?>
            <?php echo $form->text('displayLimit', $displayLimit) ?>
        </div>

        <div class="form-group">
            <?php echo $form->label('', t('Download File Method')) ?>

            <div class="form-check">
                <?php echo $form->radio('downloadFileMethod', 'browser', $downloadFileMethod, ["name" => "downloadFileMethod", "id" => "downloadFileMethodBrowser"]) ?>
                <?php echo $form->label("downloadFileMethodBrowser", t('Display in browser (if possible)'), ["class" => "form-check-label"]); ?>
            </div>

            <div class="form-check">
                <?php echo $form->radio('downloadFileMethod', 'force', $downloadFileMethod, ["name" => "downloadFileMethod", "id" => "downloadFileMethodForce"]) ?>
                <?php echo $form->label("downloadFileMethodForce", t('Force Download'), ["class" => "form-check-label"]); ?>
            </div>
        </div>

        <div class="form-group">
            <?php echo $form->label('', t('Height Mode')) ?>

            <div class="form-check">
                <?php echo $form->radio('heightMode', 'auto', $heightMode, ["name" => "heightMode", "id" => "heightModeAuto"]) ?>
                <?php echo $form->label("heightModeAuto", t('Auto'), ["class" => "form-check-label"]); ?>
            </div>

            <div class="form-check">
                <?php echo $form->radio('heightMode', 'fixed', $heightMode, ["name" => "heightMode", "id" => "heightModeFixed"]) ?>
                <?php echo $form->label("heightModeFixed", t('Fixed'), ["class" => "form-check-label"]); ?>
            </div>
        </div>

        <div class="form-group" data-options="height-mode">
            <?php echo $form->label('fixedHeightSize', t('Fixed Height Size')) ?>
            <?php echo $form->text('fixedHeightSize', isset($fixedHeightSize) ? $fixedHeightSize : '') ?>
        </div>

        <fieldset>
            <legend>
                <?php echo t('Properties to Display') ?>
            </legend>

            <table style="width: auto" class="table table-striped">
                <tr>
                    <th>
                        <?php echo t("Properties") ?>
                    </th>

                    <th>
                        <?php echo t("Do Not Display") ?>
                    </th>

                    <th>
                        <?php echo t("Display") ?>
                    </th>

                    <th>
                        <?php echo t("Displayed & Sortable") ?>
                    </th>
                </tr>

                <?php foreach ($viewProperties as $key => $name) { ?>
                    <tr>
                        <td>
                            <?php echo $name ?>
                        </td>

                        <td style="text-align: center">
                            <div class="form-check">
                                <?php echo $form->radio('viewProperties[' . $key . ']', -1, in_array($key, $viewPropertiesDoNotDisplay), ['data-view-property' => $key]) ?>
                            </div>
                        </td>

                        <td style="text-align: center">
                            <div class="form-check">
                                <?php echo $form->radio('viewProperties[' . $key . ']', 1, in_array($key, $viewPropertiesDisplay), ['data-view-property' => $key]) ?>
                            </div>
                        </td>

                        <td style="text-align: center">
                            <div class="form-check">
                                <?php echo $form->radio('viewProperties[' . $key . ']', 5, in_array($key, $viewPropertiesDisplaySortable), ['data-view-property' => $key]) ?>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </fieldset>

        <fieldset data-options="thumbnail">
            <legend>
                <?php echo t('Thumbnail Settings') ?>
            </legend>

            <div class="form-group">
                <?php echo $form->label('maxThumbWidth', t('Width')) ?>
                <?php echo $form->text('maxThumbWidth', isset($maxThumbWidth) ? $maxThumbWidth : '') ?>
            </div>

            <div class="form-group">
                <?php echo $form->label('maxThumbHeight', t('Height')) ?>
                <?php echo $form->text('maxThumbHeight', isset($maxThumbHeight) ? $maxThumbHeight : '') ?>
            </div>
        </fieldset>

        <fieldset>
            <legend>
                <?php echo t('Expandable Properties') ?>
            </legend>

            <?php foreach ($expandableProperties as $key => $name) { ?>
                <div class="form-check">
                    <?php echo $form->checkbox('expandableProperties[]', $key, in_array($key, $expandablePropertiesSelected), ["id" => "expandableProperties_" . $key]) ?>
                    <?php echo $form->label("expandableProperties_" . $key, $name, ["class" => "form-check-label"]); ?>
                </div>
            <?php } ?>
        </fieldset>

        <fieldset>
            <legend>
                <?php echo t('Design') ?>
            </legend>

            <div class="form-group">
                <?php echo $form->label('', t('Table Striping')) ?>

                <div class="form-check">
                    <?php echo $form->radio('tableStriped', 0, isset($tableStriped) ? $tableStriped : null, ["name" => "tableStriped", "id" => "tableStripedOff"]) ?>
                    <?php echo $form->label("tableStripedOff", t('Off (all rows the same color)'), ["class" => "form-check-label"]); ?>
                </div>

                <div class="form-check">
                    <?php echo $form->radio('tableStriped', 1, isset($tableStriped) ? $tableStriped : null, ["name" => "tableStriped", "id" => "tableStripedOn"]) ?>
                    <?php echo $form->label("tableStripedOn", t('On (change color of alternate rows)'), ["class" => "form-check-label"]); ?>
                </div>
            </div>

            <div class="form-group" data-options="table-striped">
                <?php echo $form->label('rowBackgroundColorAlternate', t('Alternate Row Background Color')) ?>

                <div>
                    <?php $color->output('rowBackgroundColorAlternate', isset($rowBackgroundColorAlternate) ? $rowBackgroundColorAlternate : null) ?>
                </div>
            </div>
        </fieldset>
    </div>
</div>

<!--suppress EqualityComparisonWithCoercionJS -->
<script type="text/javascript">
    $(function () {
        $('input[type=checkbox][data-options-toggle]').on('change', function () {
            if ($(this).is(':checked')) {
                $('[data-options=' + $(this).attr('data-options-toggle') + ']').show();
            } else {
                $('[data-options=' + $(this).attr('data-options-toggle') + ']').hide();
            }
        }).trigger('change');

        $('input[type=radio][data-view-property=thumbnail]').on('change', function () {
            if ($('input[type=radio][data-view-property=thumbnail]:checked').val() != '-1') {
                $('[data-options=thumbnail]').show();
            } else {
                $('[data-options=thumbnail]').hide();
            }
        }).trigger('change');

        $('input[type=radio][name=heightMode]').on('change', function () {
            if ($('input[type=radio][name=heightMode]:checked').val() == 'fixed') {
                $('[data-options=height-mode]').show();
            } else {
                $('[data-options=height-mode]').hide();
            }
        }).trigger('change');

        $('input[type=radio][name=tableStriped]').on('change', function () {
            if ($('input[type=radio][name=tableStriped]:checked').val() == '1') {
                $('[data-options=table-striped]').show();
            } else {
                $('[data-options=table-striped]').hide();
            }
        }).trigger('change');
    });
</script>
