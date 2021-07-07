<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>

<div class="ccm-ui">
<?php if ($c->getCollectionID() == 1) {
    ?>
	<div class="alert alert-danger"><?=t('You may not delete the home page.');
    ?></div>
	<div class="dialog-buttons"><input type="button" class="btn btn-default" value="<?=t('Cancel')?>" onclick="jQuery.fn.dialog.closeTop()" /></div>

<?php
} elseif ($numChildren > 0 && !$u->isSuperUser()) {
    ?>
	<div class="alert alert-danger"><?=t('Before you can delete this page, you must delete all of its child pages.')?></div>
	<div class="dialog-buttons"><input type="button" class="btn btn-default" value="<?=t('Cancel')?>" onclick="jQuery.fn.dialog.closeTop()" /></div>
<?php
} else {
    ?>
	<form method="post" data-dialog-form="delete-page" action="<?=$controller->action('submit')?>">
        <?php if ($sitemap) {
    ?>
            <input type="hidden" name="sitemap" value="1" />
        <?php
}
    ?>

		<div class="dialog-buttons">
		<button class="btn btn-secondary float-start" data-dialog-action="cancel"><?=t('Cancel')?></button>
		<button type="button" data-dialog-action="submit" class="btn btn-danger float-end"><?=t('Delete')?></button>
		</div>

		<?php if ($c->isSystemPage() && !$c->isPageDraft()) {
    ?>
			<div class="alert alert-danger"><?php echo t('Warning! This is a system page. Deleting it could potentially break your site. Please proceed with caution.') ?></div>
		<?php
}
    ?>
		<strong><?=t('Are you sure you wish to delete this page?')?></strong><br>
		<?php if ($u->isSuperUser() && $numChildren > 0) {
    ?>
			<p><strong><?=t2('This will remove %s child page.', 'This will remove %s child pages.', $numChildren, $numChildren)?></strong></p>
		<?php
}
    ?>

		<?php if (Config::get('concrete.misc.enable_trash_can')) {
    ?>
			<br><p><?=t('Deleted pages are moved to Trash in the System Pages section of the sitemap.')?></p>
		<?php
} else {
    ?>
			<p><?=t('This cannot be undone.')?></p>
		<?php
}
    ?>

	</form>

	<script type="text/javascript">
	$(function() {
        ConcreteEvent.unsubscribe('AjaxFormSubmitSuccess.sitemapDelete');
		ConcreteEvent.subscribe('AjaxFormSubmitSuccess.sitemapDelete', function(e, data) {
			if (data.form == 'delete-page') {
				ConcreteEvent.publish('SitemapDeleteRequestComplete', {'cID': '<?=$c->getCollectionID()?>'});
			}
		});
	});
	</script>

	<?php
}
?>

</div>
