<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>
<?php if (isset($remoteItem) && is_object($remoteItem)) { ?>

<div class="ccm-block-desktop-featured-addon">
	<div class="ccm-block-desktop-featured-addon-inner">

	<h6><?=t('Featured Add-On')?></h6/>

	<img src="<?=$remoteItem->getRemoteIconURL()?>" width="80" height="80" />
	<h3><?=$remoteItem->getName()?></h3>
	<p><?=$remoteItem->getDescription()?></p>
	<a href="<?=$remoteItem->getRemoteURL()?>" class="btn btn-default btn-lg"><?=t('Learn More')?></a>

	</div>
</div>

<?php } ?>