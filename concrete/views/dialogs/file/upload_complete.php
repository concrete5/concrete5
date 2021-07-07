<?php

/** @noinspection PhpUnusedParameterInspection */
/** @noinspection PhpComposerExtensionStubsInspection */

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Controller\Dialog\File\Bulk\Properties as BulkPropertiesController;
use Concrete\Controller\Dialog\File\Properties;
use Concrete\Core\View\View;

/** @var BulkPropertiesController $bulkPropertiesController */
/** @var Properties $propertiesController */
/** @var array $filesets */
/** @var array $fileIDs */
/** @var bool $canEditFiles */

?>

<div class="ccm-ui" id="ccm-file-manager-upload-complete">
    <div class="alert alert-success">
        <?php echo t2('%s file uploaded', '%s files uploaded', count($files)) ?>

        <button
                data-action="choose-file"
                type="button"
                class="float-end btn btn-success btn-xs d-none">

            <?php echo t2('Choose file', 'Choose files', count($files)) ?>
        </button>

    </div>

    <fieldset>
        <legend>
            <?php echo t('Properties') ?>
        </legend>

        <?php if (count($files) > 1): ?>
            <p>
                <?php echo t('Properties like name, description and tags are unavailable when uploading multiple files.') ?>
            </p>
        <?php else: ?>
            <div data-container="editable-core-properties">
                <?php
                    /** @noinspection PhpUnhandledExceptionInspection */
                    View::element('files/properties', [
                        'fv' => $files[0]->getVersion(),
                        'mode' => 'bulk'
                    ]);
                ?>
            </div>
        <?php endif; ?>
    </fieldset>

    <fieldset>
        <legend>
            <?php echo t('Sets') ?>

            <button
                type="button"
                data-action="manage-file-sets"
                class="btn btn-xs float-end btn-secondary">

                <?php echo t('Add/Remove Sets') ?>
            </button>
        </legend>

        <section data-container="file-set-list">
            &nbsp;
        </section>
    </fieldset>

    <fieldset data-container="editable-attributes">
        <legend>
            <?php echo t('Custom Attributes') ?>
        </legend>

        <section>
            <?php
            /** @noinspection PhpUnhandledExceptionInspection */
            View::element('attribute/editable_list', [
                'attributes' => $attributes,
                'objects' => $files,
                'saveAction' => $bulkPropertiesController->action('update_attribute'),
                'clearAction' => $bulkPropertiesController->action('clear_attribute'),
                'permissionsCallback' => function ($ak, $permissionsArguments) use ($canEditFiles) {
                    return $canEditFiles; // this is fine because you can't even access this interface without being able to edit every file.
                },
            ]); ?>
        </section>
    </fieldset>
</div>

<script type="text/template" class="upload-complete-file-sets">
    <% if (filesets.length > 0) { %>
        <% _.each(filesets, function(fileset) { %>
            <div>
                <%-fileset.fsDisplayName%>
            </div>
        <% }) %>
    <% } else { %>
        <p>
            <?php echo t('None') ?>
        </p>
    <% } %>
</script>

<!--suppress JSUnusedAssignment, JSUnresolvedVariable -->
<script type="text/javascript">
    (function($) {
        $(function () {
            let _sets = _.template($('script.upload-complete-file-sets').html());
            let filesets = <?php echo json_encode($filesets)?>;
            let fID = <?php echo json_encode($fileIDs)?>;

            <?php if (count($files) == 1): ?>
                $('[data-container=editable-core-properties]').concreteEditableFieldContainer({
                    data: [
                        <?php foreach ($files as $f) :?>
                            {'name': 'fID[]', 'value': '<?php echo $f->getFileID()?>'},
                        <?php endforeach; ?>
                    ],
                    url: '<?php echo $propertiesController->action('save')?>'
                });
            <?php endif; ?>

            $('[data-container=editable-attributes]').concreteEditableFieldContainer({
                data: [
                    <?php foreach ($files as $f): ?>
                        {'name': 'fID[]', 'value': '<?php echo $f->getFileID()?>'},
                    <?php endforeach; ?>
                ]
            });

            $('button[data-action=manage-file-sets]').on('click', function () {
                <?php
                    $data = '';

                    for ($i = 0; $i < count($files); ++$i) {
                        $f = $files[$i];
                        $data .= 'fID[]=' . $f->getFileID();

                        if ($i + 1 < count($files)) {
                            $data .= '&';
                        }
                    }
                ?>

                $.fn.dialog.open({
                    width: '500',
                    height: '400',
                    href: CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/file/bulk/sets',
                    modal: true,
                    data: '<?php echo $data; ?>',
                    title: ccmi18n_filemanager.sets
                });
            });

            $("[data-container=file-set-list]").html(
                _sets({
                    filesets: filesets
                })
            );

            ConcreteEvent.subscribe('FileSetBulkUpdateRequestComplete', function (e, data) {
                $("[data-container=file-set-list]").html(_sets({
                    filesets: data.filesets
                }));
            });

            ConcreteEvent.subscribe('FileManagerUploadCompleteDialogOpen', function (e, data) {
                if (data.filemanager && data.filemanager.options.mode === 'choose') {
                    $('button[data-action=choose-file]').removeClass("d-none");
                }
            });

            ConcreteEvent.subscribe('FileManagerUploadCompleteDialogClose', function (e, data) {
                if (data.filemanager) {
                    data.filemanager.refreshResults();
                }
            });

            $('button[data-action=choose-file]').on('click', function () {
                ConcreteEvent.publish('FileManagerSelectFile', {
                    fID: fID
                });

                jQuery.fn.dialog.closeTop();
            });
        });
    })(jQuery);
</script>
