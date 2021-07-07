<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Page\View\PageView $view
 * @var bool $canRead
 * @var int $nodeID (if $canView is true)
 * @var bool $includeSystemPages (if $canView is true)
 */

if (!$canRead) {
    ?>
    <p><?= t('You do not have access to the dashboard sitemap.') ?></p>
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
        ?>
    </div>
</div>
<div class="ccm-pane-body">
    <div id="ccm-flat-sitemap-container" data-sitemap="container"></div>
</div>
<div class="ccm-pane-footer" id="ccm-explore-paging-footer"></div>
<script>
$(function () {
    var my_url = <?= json_encode($view->action('')) ?>;
    $('div#ccm-flat-sitemap-container').concreteSitemap({
        displayNodePagination: true,
        cParentID: '<?= $nodeID ?>',
        displaySingleLevel: true,
        includeSystemPages: <?= $includeSystemPages ? 'true' : 'false' ?>,
        persist: false,
        onDisplaySingleLevel: function (node) {
            if (window && window.history && window.history.pushState) {
                window.history.pushState(
                    {
                        key: node.data.cID
                    },
                    'title',
                    my_url + '/-/' + node.data.cID
                );
            }
        }
    });
    $(window).on('popstate', function (event) {
        var redirect;
        if (event.originalEvent.state && event.originalEvent.state.key) {
            redirect = my_url + '/-/' + event.originalEvent.state.key;
        } else {
            return true;
        }
        window.location = redirect;
        $.fn.dialog.showLoader();
        return false;
    });
});
</script>
