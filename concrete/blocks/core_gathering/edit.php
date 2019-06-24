<?php
defined('C5_EXECUTE') or die('Access Denied.');
?>

<ul class="ccm-inline-toolbar ccm-ui">
	<li class="ccm-inline-toolbar-icon-cell"><a href="#" data-gathering-refresh="<?=$gathering->getGatheringID()?>"><i class="fa fa-refresh"></i></a></li>
	<li class="ccm-inline-toolbar-icon-cell"><a href="<?=Loader::helper('concrete/urls')->getBlockTypeToolsURL($bt)?>/edit?cID=<?=$c->getCollectionID()?>&arHandle=<?=Loader::helper('text')->entities($a->getAreaHandle())?>&bID=<?=$b->getBlockID()?>&tab=sources" class="dialog-launch" dialog-title="<?=t('Data Sources')?>" dialog-width="<?=$controller->getInterfaceWidth()?>" dialog-height="<?=$controller->getInterfaceHeight()?>"><i class="fa fa-filter"></i></a></li>
	<li class="ccm-inline-toolbar-icon-cell"><a href="<?=Loader::helper('concrete/urls')->getBlockTypeToolsURL($bt)?>/edit?cID=<?=$c->getCollectionID()?>&arHandle=<?=Loader::helper('text')->entities($a->getAreaHandle())?>&bID=<?=$b->getBlockID()?>&tab=output" class="dialog-launch" dialog-title="<?=t('Output')?>" dialog-width="<?=$controller->getInterfaceWidth()?>" dialog-height="<?=$controller->getInterfaceHeight()?>"><i class="fa fa-resize-full"></i></a></li>
	<li class="ccm-inline-toolbar-icon-cell"><a href="<?=Loader::helper('concrete/urls')->getBlockTypeToolsURL($bt)?>/edit?cID=<?=$c->getCollectionID()?>&arHandle=<?=Loader::helper('text')->entities($a->getAreaHandle())?>&bID=<?=$b->getBlockID()?>&tab=posting" class="dialog-launch" dialog-title="<?=t('Posting')?>" dialog-width="<?=$controller->getInterfaceWidth()?>" dialog-height="<?=$controller->getInterfaceHeight()?>"><i class="fa fa-pencil"></i></a></li>
	<li class="ccm-inline-toolbar-button ccm-inline-toolbar-button-save"><button type="button" data-toolbar-button-action="exit-gathering"><?=t("Done")?></button></li>
</ul>

<script type="text/javascript">
$(function() {
	$('button[data-toolbar-button-action=exit-gathering]').on('click', function() {
		ConcreteEvent.fire('EditModeExitInline');
	});
});
</script>

<?php
Loader::element('gathering/display', array(
    'gathering' => $gathering,
    'list' => $itemList,
    'itemsPerPage' => $itemsPerPage,
    'showTileControls' => true,
));
