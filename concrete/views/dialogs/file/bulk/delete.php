<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Controller\Dialog\File\Bulk\Delete;
use Concrete\Core\Entity\File\File;
use Concrete\Core\Permission\Key\FileKey;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Validation\CSRF\Token;

/** @var Delete $controller */
/** @var File $f */
$app = Application::getFacadeApplication();
/** @var Token $token */
$token = $app->make(Token::class);
?>

<div class="ccm-ui">
    <br>

    <?php if ($fcnt == 0): ?>
        <p>
            <?php echo t("You do not have permission to delete any of the selected files."); ?>
        </p>
    <?php else: ?>
        <div class="alert alert-warning"><?php echo t('Are you sure you want to delete the following files?') ?></div>

        <form data-dialog-form="delete-file" method="post" action="<?php echo $controller->action('delete_files') ?>">
            <?php $token->output('files/bulk_delete'); ?>

            <table class="table table-striped">
                <tbody>
                    <?php foreach ($files as $f): ?>
                        <?php
                            $pk = FileKey::getByHandle('delete_file');
                            $pk->setPermissionObject($f);
                        ?>

                        <?php if ($pk->validate()): ?>
                            <?php $fv = $f->getApprovedVersion(); ?>

                            <?php if (is_object($fv)): ?>
                                <?php echo $form->hidden('fID[]', $f->getFileID()) ?>

                                <tr>
                                    <td>
                                        <?php echo $fv->getType() ?>
                                    </td>

                                    <td class="ccm-file-list-filename">
                                        <div>
                                            <?php echo h($fv->getTitle()) ?>
                                        </div>
                                    </td>

                                    <td>
                                        <?php
                                            /** @noinspection PhpUnhandledExceptionInspection */
                                            echo $dh->formatDateTime($f->getDateAdded()->getTimestamp())
                                        ?>
                                    </td>

                                    <td>
                                        <?php echo $fv->getSize() ?>
                                    </td>

                                    <td>
                                        <?php echo $fv->getAuthorName() ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </form>

        <div class="dialog-buttons">
            <button class="btn btn-secondary float-start" data-dialog-action="cancel">
                <?php echo t('Cancel') ?>
            </button>

            <button type="button" data-dialog-action="submit" class="btn btn-danger float-end">
                <?php echo t('Delete') ?>
            </button>
        </div>

        <style type="text/css">
            .ccm-file-list-filename div {
                word-wrap: break-word;
                width: 150px;
            }
        </style>

        <script type="text/javascript">
            $(function () {
                ConcreteEvent.subscribe('AjaxFormSubmitSuccess', function (e, data) {
                    if (data.form === 'delete-file') {
                        ConcreteEvent.publish('FileManagerDeleteFilesComplete', {files: data.response.files});
                    }
                });
            });
        </script>
    <?php endif; ?>
</div>
