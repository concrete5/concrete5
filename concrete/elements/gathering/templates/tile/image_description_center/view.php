<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>
<?php
if (is_array($image)) {
    $image = $image[0];
}
?>
<div class="ccm-gathering-image-masthead-description ccm-gathering-centered-content ccm-gathering-scaled-image">
	<a href="#" data-overlay="gathering-item">
		<img src="<?=$image->getSrc()?>" alt="<?php echo t('Preview Image') ?>" />
	</a>
	<!-- <div class="ccm-gathering-tile-title-description">
		<div class="ccm-gathering-tile-description">
		<?=$description?>
		</div> 
	</div> --> 
	<div class="clearfix" style="clear: both;"></div>
</div>