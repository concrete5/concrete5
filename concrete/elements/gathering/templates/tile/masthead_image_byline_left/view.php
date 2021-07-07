<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php
if (is_array($image)) {
    $image = $image[0];
}
$ownerID = $this->page->vObj->cvAuthorUID;
$u = User::getByUserID($ownerID);
$ownerName = $u->getUserName();
?>
<div class="ccm-gathering-masthead-image-left ccm-gathering-masthead-image ccm-gathering-scaled-image">
	<a href="#" data-overlay="gathering-item">
		<img class="float-start" src="<?=$image->getSrc()?>" alt="<?php echo t('Preview Image') ?>" />
	</a>
	<div class="ccm-gathering-tile-title-description float-start">
		<div class="ccm-gathering-tile-headline"><a href="<?=$link?>"><?=$title?></a></div>
		<div class="ccm-gathering-tile-byline"><?php echo tc(/*i18n: %s is the name of the author */ 'Authored', 'By %s', '<span class="author-name">' . $ownerName . '</span>'); ?></div>
		<div class="ccm-gathering-tile-description">
		<?=$description?>
		</div>
	</div>
	<div class="clearfix" style="clear: both;"></div>
</div>
