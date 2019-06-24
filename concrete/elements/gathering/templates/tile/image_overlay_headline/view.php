<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>
<?php
if (is_array($image)) {
    $image = $image[0];
}

?>
<div class="ccm-gathering-image-overlay-headline-byline">
		<img src="<?=$image->getSrc()?>" alt="<?php echo t('Preview Image') ?>" />
	<div class="ccm-gathering-tile-image-overlay-headline-byline-description">
		<p class="overlay-title"><?=$title; ?></p>
		<p class="overlay-byline"><?php echo tc(/*i18n: %s is the name of the author */ 'Authored', 'by %s', $author); ?></p>
	</div>
	<div class="clearfix" style="clear: both;"></div>
</div>