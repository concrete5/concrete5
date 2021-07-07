<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Entity\File\Version;
use Concrete\Core\File\Exception\InvalidDimensionException;
use Concrete\Core\Html\Service\Navigation;
use Concrete\Core\Localization\Service\Date;
use Concrete\Core\Page\Page;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Utility\Service\Number;
use Concrete\Core\Permission\Key\Key;

$canEditFileContentsPermissionsKey = Key::getByHandle("can_edit_file_contents");
$canEditFileContentsPermissionsKey->setPermissionObject($f);
$canEditFilePropertiesPermissionsKey = Key::getByHandle("can_edit_file_properties");
$canEditFilePropertiesPermissionsKey->setPermissionObject($f);

/** @var Version $fv */
$f = $fv->getFile();
$app = Application::getFacadeApplication();
/** @var Date $dh */
$dh = $app->make(Date::class);
/** @var Navigation $navHelper */
$navHelper = $app->make(Navigation::class);
/** @var Number $numHelper */
$numHelper = $app->make(Number::class);
$oc = $f->getOriginalPageObject();
$fsl = $f->getFileStorageLocationObject();

if (!isset($mode) || !$mode) {
    $mode = 'single';
}
?>

<?php if ($mode == 'single'): ?>
    <div class="row">
        <div class="col-md-3">
            <p>
                <?php echo t('ID') ?>
            </p>
        </div>

        <div class="col-md-9">
            <p>
                <?php echo $fv->getFileID() ?>

                <span class="color-gray">
                    (<?php echo t('Version') ?> <?php echo $fv->getFileVersionID() ?>)
                </span>
            </p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <p>
                <?php echo t('Filename') ?>
            </p>
        </div>

        <div class="col-md-9">
            <p>
                <?php echo h($fv->getFileName()) ?>
            </p>
        </div>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-md-3">
        <p>
            <?php echo t('URL to File') ?>
        </p>
    </div>

    <div class="col-md-9">
        <p class="overflow-hidden">
            <a target="_blank" class="editable-click" href="<?php echo $fv->getURL() ?>">
                <?php echo $fv->getURL() ?>
            </a>
        </p>
    </div>
</div>

<?php  if ($fv->getDownloadURL() !== $fv->getURL()): ?>
    <div class="row">
        <div class="col-md-3">
            <p>
                <?php echo t('Tracked URL') ?>
            </p>
        </div>

        <div class="col-md-9">
            <p class="overflow-hidden">
                <a target="_blank" class="editable-click" href="<?php echo $fv->getDownloadURL() ?>">
                    <?php echo $fv->getDownloadURL() ?>
                </a>
            </p>
        </div>
    </div>
<?php endif; ?>

<?php if ($mode == 'single'): ?>
    <?php if ($folder = $f->getFileFolderObject()): ?>
        <?php /** @var Node $folder */?>

        <div class="row">
            <div class="col-md-3">
                <p>
                    <?php echo t('Folder') ?>
                </p>
            </div>

            <div class="col-md-9">
                <a href="#" class="editable-click" data-action="jump-to-folder"
                   data-folder-id="<?php echo $folder->getTreeNodeID() ?>">

                    <?php
                        $folders = '';
                        $nodes = array_reverse($folder->getTreeNodeParentArray());

                        foreach ($nodes as $n) {
                            $folders .= $n->getTreeNodeName() . ' &gt; ';
                        }

                        $folders .= $folder->getTreeNodeName();

                        print h(trim($folders));
                    ?>
                </a>
            </div>
        </div>
    <?php endif; ?>

    <?php if (is_object($oc)): ?>
            <?php
                $fileManager = Page::getByPath('/dashboard/files/search');

                $ocName = $oc->getCollectionName();

                if (is_object($fileManager) && !$fileManager->isError()) {
                    if ($fileManager->getCollectionID() == $oc->getCollectionID()) {
                        $ocName = t('Dashboard File Manager');
                    }
                }
            ?>

            <div class="row">
                <div class="col-md-3">
                    <p>
                        <?php echo t('Page Added To') ?>
                    </p>
                </div>

                <div class="col-md-9">
                    <p>
                        <a href="<?php echo $navHelper->getLinkToCollection($oc) ?>" target="_blank">
                            <?php echo $ocName ?>
                        </a>
                    </p>
                </div>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-3">
                <p>
                    <?php echo t('Type') ?>
                </p>
            </div>

            <div class="col-md-9">
                <p>
                    <?php echo $fv->getType() ?>
                </p>
            </div>
        </div>

    <?php endif; ?>

<?php if ($fv->getTypeObject()->supportsThumbnails()): ?>
    <?php try {
        $thumbnails = $fv->getThumbnails();
    ?>

    <?php } catch (InvalidDimensionException $e) { ?>

        <div class="row">
            <div class="col-md-3">
                <p>
                    <?php echo t('Thumbnails') ?>
                </p>
            </div>

            <div class="col-md-9">
                <p class="color-red">
                    <?php echo t('Invalid file dimensions, please rescan this file.') ?>

                    <?php if ($mode != 'preview' && $canEditFileContentsPermissionsKey->validate()): ?>
                        <a href="#" class="btn float-end btn-secondary btn-xs" data-action="rescan">
                            <?php echo t('Rescan') ?>
                        </a>
                    <?php endif; ?>
                </p>
            </div>
        </div>

    <?php } catch (Exception $e) { ?>

        <div class="row">
            <div class="col-md-3">
                <p>
                    <?php echo t('Thumbnails') ?>
                </p>
            </div>

            <div class="col-md-9">
                <p class="color-red">
                    <?php echo t('Unknown error retrieving thumbnails, please rescan this file.') ?>

                    <?php if ($mode != 'preview' && $canEditFileContentsPermissionsKey->validate()): ?>
                        <a href="#" class="btn float-end btn-secondary btn-xs" data-action="rescan">
                            <?php echo t('Rescan') ?>
                        </a>
                    <?php endif;?>
                </p>
            </div>
        </div>

    <?php } ?>

    <?php if ($thumbnails): ?>
        <div class="row">
            <div class="col-md-3">
                <p>
                    <?php echo t('Thumbnails') ?>
                </p>
            </div>

            <div class="col-md-9">
                <p>
                    <!--suppress HtmlUnknownAttribute -->
                    <a class="dialog-launch icon-link"
                       dialog-title="<?php echo t('Thumbnail Images') ?>"
                       dialog-width="90%"
                       dialog-height="70%"
                       href="<?php echo Url::to('/ccm/system/dialogs/file/thumbnails')->setQuery([
                           "fID" => $fv->getFileID(),
                           "fvID" => $fv->getFileVersionID()
                       ]) ?>">

                        <?php echo count($thumbnails) ?> <i class="fa fa-edit"></i>
                    </a>
                </p>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>

<?php if ($mode == 'single'): ?>

    <div class="row">
        <div class="col-md-3">
            <p>
                <?php echo t('Size') ?>
            </p>
        </div>

        <div class="col-md-9">
            <p>
                <?php
                    echo sprintf(
                        '%s (%s)',
                        $fv->getSize(),
                        t2(
                            /*i18n: %s is a number */
                    '%s byte',
                        '%s bytes',
                            $fv->getFullSize(),
                            $numHelper->format($fv->getFullSize())
                        )
                    );
                ?>
            </p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <p>
                <?php echo t('Date Added') ?>
            </p>
        </div>

        <div class="col-md-9">
            <p>
                <?php
                    /** @noinspection PhpUnhandledExceptionInspection */
                    echo t(
                        'Added by %s on %s',
                        sprintf(
                        '<strong>%s</strong>',
                            $fv->getAuthorName()
                        ),
                        $dh->formatDateTime($f->getDateAdded(), true)
                    )
                ?>
            </p>
        </div>
    </div>

    <?php if (is_object($fsl)): ?>
        <div class="row">
            <div class="col-md-3">
                <p>
                    <?php echo t('Storage Location') ?>
                </p>
            </div>

            <div class="col-md-9">
                <p>
                    <?php echo $fsl->getDisplayName() ?>
                </p>
            </div>
        </div>
    <?php endif; ?>

<?php endif; ?>

<div class="row">
    <div class="col-md-3">
        <p>
            <?php echo t('Title') ?>
        </p>
    </div>

    <div class="col-md-9">
        <p>
            <?php if ($canEditFilePropertiesPermissionsKey->validate()): ?>
                <span data-editable-field-type="xeditable" data-type="text" data-name="fvTitle">
                    <?php echo h($fv->getTitle()) ?>
                </span>
            <?php else: ?>
                <span>
                    <?php echo h($fv->getTitle()) ?>
                </span>
            <?php endif; ?>
        </p>
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        <p>
            <?php echo t('Description') ?>
        </p>
    </div>

    <div class="col-md-9">
        <p>
            <?php if ($canEditFilePropertiesPermissionsKey->validate()): ?>
                <span data-editable-field-type="xeditable" data-type="textarea" data-name="fvDescription">
                    <?php echo h($fv->getDescription()) ?>
                </span>
            <?php else: ?>
                <span>
                    <?php echo h($fv->getDescription()) ?>
                </span>
            <?php endif; ?>
        </p>
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        <p>
            <?php echo t('Tags') ?>
        </p>
    </div>

    <div class="col-md-9">
        <p>
            <?php if ($canEditFilePropertiesPermissionsKey->validate()): ?>
                <span data-editable-field-type="xeditable" data-type="textarea" data-name="fvTags">
                    <?php echo h($fv->getTags()) ?>
                </span>
            <?php else: ?>
                <span>
                    <?php echo h($fv->getTags()) ?>
                </span>
            <?php endif; ?>
        </p>
    </div>
</div>

<style type="text/css">
    .color-red {
        color: #cc3333;
    }

    .color-gray {
        color: #afafaf;
    }

    .overflow-hidden {
        overflow: hidden;
    }
</style>

<script>
    (function($) {
        $(function () {
            $('a[data-action=jump-to-folder]').on('click', function (e) {
                e.preventDefault();

                let folderID = $(this).data('folder-id');

                $.fn.dialog.closeTop();

                ConcreteEvent.publish('FileManagerJumpToFolder', {
                    'folderID': folderID
                });
            });

            $('[data-name=fvTitle]').on('save', function (e, params) {
                if (params.response && params.response.files.length === 1) {
                    ConcreteEvent.publish('FileManagerUpdateFileProperties', {
                        'file': params.response.files[0]
                    });
                }
            });
        });
    })(jQuery);
</script>
