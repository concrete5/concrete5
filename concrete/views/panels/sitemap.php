<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>
<div class="ccm-panel-content-inner">

<?php if (count($frequentPageTypes) || count($otherPageTypes)) {
    ?>
	<h5><?=t('New Page')?></h5>
	<ul class="ccm-panel-sitemap-list">
	<?php foreach ($frequentPageTypes as $pt) {
    ?>
		<li><a href="<?=URL::to('/ccm/system/page/', 'create', $pt->getPageTypeID())?>"><?=$pt->getPageTypeDisplayName()?></a></li>
	<?php 
}
    ?>
    <?php foreach ($otherPageTypes as $pt) {
    ?>
        <li data-page-type="other" <?php if (count($frequentPageTypes)) {
    ?>style="display: none"<?php 
}
    ?>><a href="<?=URL::to('/ccm/system/page/', 'create', $pt->getPageTypeID())?>"><?=$pt->getPageTypeDisplayName()?></a></li>
    <?php 
}
    ?>

    <?php if (count($frequentPageTypes) && count($otherPageTypes)) {
    ?>
        <li class="ccm-panel-sitemap-more-page-types"><a href="#" data-sitemap="show-more"><i class="fa fa-caret-down"></i> <?=t('More')?></a></li>
    <?php 
}
    ?>
	</ul>

    <script type="text/javascript">
    $(function() {
        $('a[data-sitemap=show-more]').on('click', function(e) {
            e.preventDefault();
            $('li[data-page-type=other]').show();
            $(this).parent().remove();
        });
    });
    </script>
<?php 
} ?>

    <?php if (count($drafts)) {
        ?>
        <h5><?=t('Page Drafts')?></h5>
        <ul class="ccm-panel-sitemap-list">
            <?php foreach ($drafts as $dc) {
                ?>
                <li><a href="<?=Loader::helper('navigation')->getLinkToCollection($dc)?>"><?php
                        if ($dc->getCollectionName()) {
                            echo $dc->getCollectionName() . ' ' . Core::make('date')->formatDateTime($dc->getCollectionDateAdded(), false);
                        } else {
                            echo t('(Untitled)') . ' ' . Core::make('date')->formatDateTime($dc->getCollectionDateAdded(), false);
                        }
                        ?></a></li>
                <?php
            }
            ?>
        </ul>
        <?php
    } ?>

    <?php
if ($canViewSitemap) {
    ?>
	<h5><?=t('Sitemap')?></h5>
	<div id="ccm-sitemap-panel-sitemap"></div>
	<script type="text/javascript">
	$(function() {
		$('#ccm-sitemap-panel-sitemap').concreteSitemap({
			onClickNode: function(node) {
				window.location.href = CCM_DISPATCHER_FILENAME + '?cID=' + node.data.cID;
			}
		});
	});
	</script>
<?php 
} ?>

</div>

