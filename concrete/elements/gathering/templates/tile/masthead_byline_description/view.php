<?php defined('C5_EXECUTE') or die('Access Denied.');
$ownerID = $this->page->vObj->cvAuthorUID;
$u = User::getByUserID($ownerID);
$ownerName = $u->getUserName();
$nh = Loader::helper('navigation');
?>
<div class="ccm-gathering-masthead-byline-description">
	<div class="ccm-gathering-tile-title-description">
		<div class="ccm-gathering-tile-headline"><a href="<?=$link?>"><?=$title?></a></div>
		<div class="ccm-gathering-tile-byline"><?php echo tc(/*i18n: %s is the name of the author */ 'Authored', 'by %s', '<span class="author-name">' . $ownerName . '</span>'); ?></div>
		<div class="ccm-gathering-tile-description">
		<?=$description?>
		</div>
		<div class="ccm-gathering-tile-read-more"><a href="<?php echo $nh->getCollectionURL($this->page); ?>"><?php echo t('Read More') ?></a></div>
	</div>
	<div class="clearfix" style="clear: both;"></div>
</div>
