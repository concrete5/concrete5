<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php
if (is_array($image)) {
    $image = $image[0];
}
?>
<div class="ccm-gathering-masthead-image-left ccm-gathering-masthead-image ccm-gathering-scaled-image">
	<a href="#" data-overlay="gathering-item">
		<img class="float-start" src="<?=$image->getSrc()?>" alt="<?php echo t('Preview Image') ?>" />
	</a>
	<div class="ccm-gathering-tile-title-description float-start">
		<div class="ccm-gathering-tile-headline"><a href="<?=$link?>"><?=$title?></a></div>
		<div class="ccm-gathering-tile-description">
		<?=$description?>
		</div>
	</div>
	<div class="clearfix" style="clear: both;"></div>
</div>
