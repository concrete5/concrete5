<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php
if (is_object($configuration)) {
    $flickrFeedTags = $configuration->getFlickrFeedTags();
}
?>
<div class="control-group">
	<label class="control-label form-label"><?=t('Tags')?></label>
	<div class="controls">
		<?=$form->text($source->optionFormKey('flickrFeedTags'), $flickrFeedTags)?>
	</div>
</div>
