<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Support\Facade\Url;

/** @var $urlHelper Url */
?>

<div class="row row-cols-auto align-items-center">
    <select id="favoriteFolderSelector" class="selectpicker me-3" data-live-search="true" title="<?php echo h(t("Favorite Folders")); ?>"></select>

    <?php if (!empty($itemsPerPageOptions)) { ?>
        <div class="dropdown">
            <button
                type="button"
                class="btn btn-secondary p-2 dropdown-toggle"
                data-bs-toggle="dropdown"
                aria-haspopup="true"
                aria-expanded="false">

                <span id="selected-option">
                    <?php echo $itemsPerPage; ?>
                </span>
            </button>

            <ul class="dropdown-menu">
                <li class="dropdown-header">
                    <?php echo t('Items per page') ?>
                </li>

                <?php foreach ($itemsPerPageOptions as $itemsPerPageOption) { ?>
                    <?php
                        $url = $urlHelper->setVariable([
                            'itemsPerPage' => $itemsPerPageOption
                        ]);
                    ?>

                    <li data-items-per-page="<?php echo $itemsPerPageOption; ?>">
                        <a class="dropdown-item <?php echo ($itemsPerPageOption === $itemsPerPage) ? 'active' : ''; ?>" href="<?php echo $url ?>">
                            <?php echo $itemsPerPageOption; ?>
                        </a>
                    </li>
                <?php } ?>
            </ul>
        </div>
    <?php } ?>

    <ul class="ccm-dashboard-header-icons">
        <li>
            <a class="ccm-hover-icon" title="<?php echo h(t('Jump to Folder')) ?>" data-launch-dialog="navigate-file-manager" href="javascript:void(0);">
                <i class="fa fa-folder" aria-hidden="true"></i>
            </a>
        </li>

        <li>
            <a class="ccm-hover-icon" title="<?php echo h(t('New Folder')) ?>" href="javascript:void(0);" data-launch-dialog="add-file-manager-folder">
                <i class="fa fa-plus" aria-hidden="true"></i>
            </a>
        </li>

        <li>
            <a class="ccm-hover-icon" title="<?php echo h(t('Upload Files')) ?>" href="javascript:void(0);" id="ccm-file-manager-upload" data-dialog="add-files">
                <i class="fa fa-upload" aria-hidden="true"></i>
            </a>
        </li>
    </ul>

</div>

<?php
    $addFolderAction = Url::to('/ccm/system/dialogs/tree/node/add/file_folder');

    if (isset($currentFolder)) {
        $addFolderAction .= '?treeNodeID=' . $currentFolder->getTreeNodeID();
    }
?>

<script>
    $(function() {
        $('a[data-launch-dialog=add-file-manager-folder]').on('click', function (e) {
            e.preventDefault();

            $.fn.dialog.open({
                width: 550,
                height: 'auto',
                modal: true,
                title: '<?php echo t('Add Folder')?>',
                href: '<?php echo $addFolderAction?>'
            });
        });

        $('a[data-launch-dialog=navigate-file-manager]').on('click', function(e) {
            e.preventDefault();

            $.fn.dialog.open({
                width: '560',
                height: '500',
                modal: true,
                title: '<?php echo t('Jump to Folder')?>',
                href: '<?php echo Url::to('/ccm/system/dialogs/file/jump_to_folder')?>'
            });
        });

        $('a[data-dialog=add-files]').on('click', function(e) {
            e.preventDefault();

            $('table[data-search-results=files]').parent().concreteFileUploader().open();
        });
    });
</script>
