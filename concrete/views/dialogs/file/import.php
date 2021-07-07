<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Entity\File\File;
use Concrete\Core\Page\Page;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Tree\Node\Type\FileFolder;
use Concrete\Core\Entity\File\StorageLocation\StorageLocation;
use Concrete\Core\Utility\Service\Identifier;
use Concrete\Core\Validation\CSRF\Token;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Application\Service\UserInterface;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;
use Concrete\Controller\Dialog\File\Import;
use Concrete\Core\View\DialogView;

/* @var Import $controller */
/* @var DialogView $view */
/* @var Token $token */
/* @var Form $form */
/* @var UserInterface $ui */
/* @var ResolverManagerInterface $resolverManager */
/* @var string $formID */
/* @var FileFolder|null $currentFolder */
/* @var Page|null $originalPage */
/* @var StorageLocation $incomingStorageLocation */
/* @var string $incomingPath */
/* @var array $incomingContents */
/* @var string|null $incomingContentsError */
/* @var File|null $replacingFile */

$app = Application::getFacadeApplication();
/** @var Identifier $idHelper */
$idHelper = $app->make(Identifier::class);
$dropZoneId = "ccm-drop-zone-" . $idHelper->getString();
?>

<div class="ccm-ui ccm-file-manager-import-files" id="<?php echo $formID; ?>">
    <?php
        echo $ui->tabs([
            ['local', t('Your Computer'), true],
            ['incoming', t('Incoming Directory')],
            ['remote', t('Remote Files')],
        ]);
    ?>

    <div class="tab-content">
        <div class="tab-pane fade show active" id="local" role="tabpanel" aria-labelledby="local-tab">

            <concrete-file-uploader <?php if (isset($replacingFile) && $replacingFile) { ?>replace-file-id="<?=$replacingFile->getFileID()?>"<?php } ?> :max-files="1"></concrete-file-uploader>

        </div>

        <div class="tab-pane fade" id="incoming" role="tabpanel" aria-labelledby="incoming-tab">
            <?php if ($incomingContentsError !== null): ?>
                <div class="alert alert-danger">
                    <?php echo nl2br(h($incomingContentsError)); ?>
                </div>
            <?php elseif (empty($incomingContents)): ?>
                <div class="alert alert-info">
                    <?php echo t('No files found in %s for the storage location "%s".', h($incomingPath), h($incomingStorageLocation->getName())); ?>
                </div>
            <?php else: ?>
                <form id="ccm-file-add-incoming-form"
                      method="POST"
                      action="<?php echo $resolverManager->resolve(['/ccm/system/file/import_incoming']) ?>">

                    <?php
                        echo $token->output('import_incoming');

                        if ($currentFolder instanceof FileFolder) {
                            echo $form->hidden("currentFolder", $currentFolder->getTreeNodeID());
                        }

                        if ($originalPage instanceof Page) {
                            echo $form->hidden("ocID", $originalPage->getCollectionID());
                        }

                        if ($replacingFile instanceof File) {
                            echo $form->hidden("fID", $replacingFile->getFileID());
                        }
                    ?>

                    <table class="incoming_file_table table table-striped">
                        <thead>
                            <tr>
                                <th class="incoming_file_table-checkbox">
                                    <?php
                                    if (!$replacingFile instanceof File) {
                                        $form->checkbox($idHelper->getString(), null, false, ["class" => "ccm-check-all-incoming"]);
                                    }
                                    ?>
                                </th>

                                <th class="incoming_file_table-thumbnail">
                                    &nbsp;
                                </th>

                                <th class="incoming_file_table-filename">
                                    <?php echo t('Filename'); ?>
                                </th>

                                <th class="incoming_file_table-size">
                                    <?php echo t('Size'); ?>
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($incomingContents as $index => $file): ?>
                                <tr>
                                    <td class="incoming_file_table-checkbox">
                                        <?php
                                            $key = $formID . "-incoming-" . $index;

                                            if ($file['allowed']) {
                                                if ($replacingFile === null) { ?>
                                                    <input type="checkbox" id="<?=$key?>" class="ccm-file-select-incoming" name="send_file[]" value="<?=h($file['basename'])?>">
                                                <?php } else { ?>
                                                    <input type="radio" required="required" id="<?=$key?>" class="ccm-file-select-incoming" name="send_file[]"  value="<?=h($file['basename'])?>">
                                            <?php }
                                            } else {
                                                if ($replacingFile === null) { ?>
                                                    <input disabled="disabled" title="<?=t('File extension not allowed.')?>" type="checkbox" id="<?=$key?>" value="" class="launch-tooltip ccm-file-select-incoming" name="send_file[]">
                                                <?php
                                                } else { ?>
                                                    <input disabled="disabled" title="<?=t('File extension not allowed.')?>" type="radio" id="<?=$key?>" value="" class="launch-tooltip ccm-file-select-incoming" name="send_file[]">
                                                <?php }
                                            }
                                            ?>
                                    </td>

                                    <td class="incoming_file_table-thumbnail">
                                        <?php echo $form->label($key, $file['thumbnail']); ?>
                                    </td>

                                    <td class="incoming_file_table-filename">
                                        <?php
                                        if ($file['allowed']) {
                                            echo $form->label($key, h($file['basename']));
                                        } else {
                                            echo $form->label($key, h($file['basename']), [
                                                "class" => "text-danger launch-tooltip",
                                                "title" => t('Invalid file extension')
                                            ]);
                                        }
                                        ?>
                                    </td>

                                    <td class="incoming_file_table-size">
                                        <?php echo $file['displaySize']; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <div class="form-check">
                        <?php
                            echo $form->checkbox('removeFilesAfterPost', 1);
                            echo $form->label(
                                "removeFilesAfterPost",
                                t(
                                    /*i18n: %1$s is a directory name, %2$s is a storage location name */
                                    'Remove files from %1$s directory of "%2$s" storage location.',
                                    '<code>' . h($incomingPath) . '</code>',
                                    $incomingStorageLocation->getDisplayName()
                                ),

                                [
                                    "class" => "form-check-label"
                                ]
                            );
                        ?>
                    </div>
                </form>
            <?php endif; ?>
        </div>

        <div class="tab-pane fade" id="remote" role="tabpanel" aria-labelledby="remote-tab">
            <form id="ccm-file-add-remote-form" method="POST"
                  action="<?php echo $resolverManager->resolve(['/ccm/system/file/import_remote']) ?>">

                <?php
                    echo $token->output('import_remote', true);

                    if ($currentFolder instanceof FileFolder) {
                        echo $form->hidden("currentFolder", $currentFolder->getTreeNodeID());
                    }

                    if ($originalPage instanceof Page) {
                        echo $form->hidden("ocID", $originalPage->getCollectionID());
                    }

                    if ($replacingFile instanceof File) {
                        echo $form->hidden("fID", $replacingFile->getFileID());
                        echo $form->text('url_upload', '', [
                            'placeholder' => t('Enter URL to valid file'),
                            'required' => 'required'
                        ]);

                    } else {
                        echo $form->textarea('url_upload', '', [
                            'placeholder' => t('Enter URL to valid file(s), one URL per line'),
                            'required' => 'required'
                        ]);
                    }
                ?>
            </form>
        </div>
    </div>
    <div class="dialog-buttons">
        <button class="btn btn-secondary float-start" data-dialog-action="cancel">
            <?php echo t('Cancel') ?>
        </button>

        <button type="button" id="ccm-import-files-button" @click="uploadFiles" class="btn btn-primary float-end">
            <?php echo t('Import') ?>
        </button>
    </div>

</div>

<script type="text/javascript">

    ConcreteEvent.subscribe('FileManagerSelectFile', function(e, files) {
        setTimeout(function() {
            window.location.reload()
        }, 1000)
    })

    Concrete.Vue.activateContext('backend', function (Vue, config) {
        new Vue({
            el: 'div.ccm-file-manager-import-files',
            components: config.components,
            methods: {
                uploadFiles() {
                    ConcreteEvent.publish('FileUploaderUploadSelectedFiles')
                }
            }
        })
    })

</script>
