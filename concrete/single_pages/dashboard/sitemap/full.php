<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Page\View\PageView $view
 * @var bool $canRead
 * @var bool $includeSystemPages
 * @var bool $displayDoubleSitemap
 */

if (!$canRead) {
    ?>
    <p><?= t('You do not have access to the sitemap.') ?></p>
    <?php
    return;
}
?>
<div class="ccm-dashboard-header-buttons">
    <button type="button" class="btn btn-secondary dropdown-toggle" data-button="attribute-type" data-bs-toggle="dropdown">
        <?= t('Options') ?> <span class="caret"></span>
    </button>
    <div class="dropdown-menu">
        <?php
        if ($includeSystemPages) {
            ?>
            <a class="dropdown-item" href="<?= $view->action('include_system_pages', 0) ?>"><span class="text-success"><i class="fa fa-check"></i> <?= t('Include System Pages in Sitemap') ?></span></a>
            <?php
        } else {
            ?>
            <a class="dropdown-item" href="<?= $view->action('include_system_pages', 1) ?>"><?= t('Include System Pages in Sitemap') ?></a>
            <?php
        }
        if ($displayDoubleSitemap) {
            ?>
            <a class="dropdown-item" href="<?= $view->action('display_double_sitemap', 0) ?>"><span class="text-success"><i class="fa fa-check"></i> <?= t('View 2-Up Sitemap') ?></span></a>
            <?php
        } else {
            ?>
            <a class="dropdown-item" href="<?= $view->action('display_double_sitemap', 1) ?>"><?= t('View 2-Up Sitemap') ?></a>
            <?php
        }
        ?>
    </div>
</div>
<?php
if ($displayDoubleSitemap) {
    ?>
    <div class="row">
        <div class="col-md-6">
            <div class="ccm-dashboard-full-sitemap-container" data-container="sitemap"></div>
        </div>
        <div class="col-md-6">
            <div class="ccm-dashboard-full-sitemap-container" data-container="sitemap" data-sitemap-index="1"></div>
        </div>
    </div>
    <?php
} else {
    ?>
    <div class="ccm-dashboard-full-sitemap-container" data-container="sitemap"></div>
    <?php
}
?>
<script>
$(function() {
    $('div[data-container=sitemap]').each(function() {
        var $my = $(this);
        $my.concreteSitemap({
            includeSystemPages: <?= $includeSystemPages ? 1 : 0 ?>,
            sitemapIndex: parseInt($my.data('sitemap-index'), 10) || 0
        });
    });
});
</script>
