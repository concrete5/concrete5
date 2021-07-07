<?php
defined('C5_EXECUTE') or die("Access Denied.");

/**
 * @var $urlHelper \Concrete\Core\Utility\Service\Url
 */
?>

    <div class="row row-cols-auto align-items-center">

    <?php
    if (!empty($itemsPerPageOptions)) { ?>
        <div class="btn-group">
            <button type="button" class="btn btn-secondary p-2 dropdown-toggle" data-bs-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false"><span
                        id="selected-option"><?= $itemsPerPage; ?></span>
            </button>
            <ul class="dropdown-menu">
                <li class="dropdown-header"><?= t('Items per page') ?></li>
                <?php foreach ($itemsPerPageOptions as $itemsPerPageOption) {
                    $url = $urlHelper->setVariable([
                        'itemsPerPage' => $itemsPerPageOption
                    ]);
                    ?>
                    <li data-items-per-page="<?= $itemsPerPageOption; ?>">
                        <a class="dropdown-item <?= ($itemsPerPageOption === $itemsPerPage) ? 'active' : ''; ?>"
                           href="<?=$url?>"><?= $itemsPerPageOption; ?></a>
                    </li>
                <?php } ?>
            </ul>
        </div>
    <?php }
    ?>

        <ul class="ccm-dashboard-header-icons">
            <?php if ($exportURL) { ?>
            <li>
                <a href="<?= $exportURL ?>" class="link-primary">
                    <i class="fa fa-download"></i> <?= t('Export to CSV') ?>
                </a>
            </li>
            <?php } ?>
            <?php if ($createURL) { ?>
            <li><a href="<?= $createURL ?>" class="link-primary"><i class="fa fa-plus"></i> <?= t('New %s',
                        $entity->getEntityDisplayName()) ?></a></li>
            <?php } ?>
        </ul>
</div>
