<?php defined('C5_EXECUTE') or die('Access Denied.');

$sh = Loader::helper('concrete/dashboard/sitemap');
?>

<div class="ccm-dashboard-header-buttons btn-group">
    <button type="button" class="btn btn-default dropdown-toggle" data-button="attribute-type" data-toggle="dropdown">
        <?=t('Options')?> <span class="caret"></span>
    </button>
    <ul class="dropdown-menu">
        <?php if ($includeSystemPages) { ?>
            <li><a href="<?=$view->action('include_system_pages', 0)?>"><span class="text-success"><i class="fa fa-check"></i> <?=t('Include System Pages in Sitemap')?></span></a></li>
        <?php } else { ?>
            <li><a href="<?=$view->action('include_system_pages', 1)?>"><?=t('Include System Pages in Sitemap')?></a></li>
        <?php } ?>


        <?php if ($displayDoubleSitemap) { ?>
            <li><a href="<?=$view->action('display_double_sitemap', 0)?>"><span class="text-success"><i class="fa fa-check"></i> <?=t('View 2-Up Sitemap')?></span></a></li>
        <?php } else { ?>
            <li><a href="<?=$view->action('display_double_sitemap', 1)?>"><?=t('View 2-Up Sitemap')?></a></li>
        <?php } ?>
    </ul>
</div>


<?php if ($sh->canRead()) { ?>

<?php
$u = new User();
if ($u->isSuperUser()) {
    if (Queue::exists('copy_page')) {
        $q = Queue::get('copy_page');
        if ($q->count() > 0) { ?>
		<div class="alert alert-warning">
			<?=t('Page copy operations pending.')?>
			<button class="btn btn-xs btn-default pull-right" onclick="ConcreteSitemap.refreshCopyOperations()"><?=t('Resume Copy')?></button>
		</div>
	<?php }
    }
}
    ?>

    <?php if ($displayDoubleSitemap) { ?>

        <div class="row">
            <div class="col-md-6">
                <div class="ccm-dashboard-full-sitemap-container" data-container="sitemap"></div>
            </div>
            <div class="col-md-6">
                <div class="ccm-dashboard-full-sitemap-container" data-container="sitemap" data-sitemap-index="1"></div>
            </div>
        </div>


    <?php } else {  ?>
        <div class="ccm-dashboard-full-sitemap-container" data-container="sitemap"></div>
    <?php } ?>

<?php
} else {
?>
<p><?=t("You do not have access to the sitemap."); ?></p>
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

    $('input[name=includeSystemPages]').on('click', function() {
        var $tree = $('div#ccm-full-sitemap-container div.ccm-sitemap-tree');
        $tree.fancytree('destroy');

        $('#ccm-full-sitemap-container').html('').concreteSitemap({
            includeSystemPages: $('input[name=includeSystemPages]').is(':checked')
        });
    });
});
</script>
