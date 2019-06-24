<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>
<?php
$ap = new Permissions($item->getGatheringObject());
$type = GatheringItemTemplateType::getByHandle('tile');
$types = GatheringItemTemplateType::getList();
if ($item->canViewGatheringItem()) {
    ?>

<div data-block-type-handle="<?=BLOCK_HANDLE_GATHERING_ITEM_PROXY?>" data-gathering-item-batch-timestamp="<?=$item->getGatheringItemBatchTimestamp()?>" data-gathering-item-id="<?=$item->getGatheringItemID()?>" class="ccm-gathering-item h<?=$item->getGatheringItemSlotHeight()?> w<?=$item->getGatheringItemSlotWidth()?>">
  <div class="ccm-gathering-item-inner">
  <?php if ($showTileControls && $ap->canEditGatheringItems()) {
    ?>
  <div class="ccm-ui">
    <ul class="ccm-gathering-item-inline-commands ccm-ui">
      <li class="ccm-gathering-item-inline-move"><a data-inline-command="move-tile" href="#"><i class="fa fa-arrows"></i></a></li>
      <li class="ccm-gathering-item-inline-options"><a data-inline-command="options-tile" href="#" data-launch-menu="gathering-menu-<?=$item->getGatheringItemID()?>"><i class="fa fa-cog"></i></a></li>
    </ul>

    <div class="popover fade" data-menu="gathering-menu-<?=$item->getGatheringItemID()?>">
      <div class="arrow"></div>
      <div class="popover-inner">
      <ul class="dropdown-menu">
        <?php foreach ($types as $t) {
    ?>
          <li><a href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/gathering/item/template?gaiID=<?=$item->getGatheringItemID()?>&gatTypeID=<?=$t->getGatheringItemTemplateTypeID()?>&token=<?=Loader::helper('validation/token')->generate('edit_gathering_item_template')?>" class="dialog-launch" dialog-title="<?=t('Edit %s Template', $t->getGatheringItemTemplateTypeName())?>" dialog-width="660" dialog-height="430" ><?=t('Edit %s Template', $t->getGatheringItemTemplateTypeName())?></a></li>
        <?php 
}
    ?>
          <li class="divider"></li>
          <li><a href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/gathering/item/delete?gaiID=<?=$item->getGatheringItemID()?>&token=<?=Loader::helper('validation/token')->generate('delete_gathering_item')?>" class="dialog-launch" dialog-title="<?=t('Delete Item')?>" dialog-width="320" dialog-height="160"><?=t('Delete Tile')?></a></li>
      </ul>
      </div>
    </div>
  </div>

  <?php 
}
    ?>

  <div class="ccm-gathering-item-inner-render">
	  <?php $item->render($type);
    ?>
	</div>
</div>
</div>
<?php 
} ?>
