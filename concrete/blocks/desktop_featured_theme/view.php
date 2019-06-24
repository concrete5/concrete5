<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>
<?php if (isset($remoteItem) && is_object($remoteItem)) { ?>

	<div class="ccm-block-desktop-featured-theme">
		<div class="ccm-block-desktop-featured-theme-inner">

			<img src="<?=$remoteItem->getLargeThumbnail()->src?>" style="height: 250px" />

			<div class="ccm-block-desktop-featured-theme-description">

				<h6><?=t('Featured Theme')?></h6/>

				<h3><?=$remoteItem->getName()?></h3>
				<p><?=$remoteItem->getDescription()?></p>
				<div class="btn-group">
					<a href="<?=$remoteItem->getRemoteURL()?>" class="btn btn-info"><?=$remoteItem->getDisplayPrice()?></a>
					<a href="<?=$remoteItem->getRemoteURL()?>" class="btn btn-info"><?=t('Learn More')?></a>
				</div>
			</div>

		</div>
	</div>

<?php } ?>