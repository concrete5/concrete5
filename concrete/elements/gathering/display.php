<?php defined('C5_EXECUTE') or die('Access Denied.');

$items = $list->getPage();
$paginator = $list->getPagination();

if (!is_object($c)) {
    $c = Page::getCurrentPage();
}

$pt = $c->getCollectionThemeObject();
$agp = new Permissions($gathering);
if ($showTileControls && $agp->canEditGatheringItems()) {
    $showTileControls = true;
} else {
    $showTileControls = false;
}

?>

<div class="ccm-gathering-wrapper">

<div data-gathering-id="<?=$gathering->getGatheringID()?>" data-gathering-current-page="1" class="<?php if ($showTileControls) {
    ?>ccm-gathering-edit<?php 
} else {
    ?>ccm-gathering-view<?php 
} ?> ccm-gathering-grid">
    <?php foreach ($items as $item) {
    ?>
      <?=Loader::element('gathering/tile', array('item' => $item, 'showTileControls' => $showTileControls))?>
    <?php 
} ?>
</div>

<div class="ccm-gathering-load-more">
  <button class="btn-large btn" data-gathering-button="gathering-load-more-items"><?=t('Load More')?></button>
</div>

</div>

<script type="text/javascript">
$(function() {
  $('div[data-gathering-id=<?=$gathering->getGatheringID()?>]').ccmgathering({
    totalPages: '<?=$paginator->getTotalPages()?>',
    'itemsPerPage': '<?=$itemsPerPage?>', 
    'gutter': <?=$pt->getThemeGatheringGridItemMargin()?>,
    columnWidth: <?=$pt->getThemeGatheringGridItemWidth()?>,
    rowHeight: <?=$pt->getThemeGatheringGridItemHeight()?>,
    gaID: <?=$gathering->getGatheringID()?>,
    showTileControls: '<?=$showTileControls?>',
    loadToken: '<?=Loader::helper('validation/token')->generate('get_gathering_items')?>',
    editToken: '<?=Loader::helper('validation/token')->generate('update_gathering_items')?>',
    titleEditTemplate: '<?=t('Edit Gathering Template')?>'
  });
});
</script>

<style type="text/css">
<?php for ($i = 1; $i <= 8; ++$i) {
    ?>
  div.w<?=$i?> {
    width: <?=(($i * $pt->getThemeGatheringGridItemWidth()) + ($pt->getThemeGatheringGridItemMargin() * ($i - 1)))?>px;
  }

  div.h<?=$i?> {
    height: <?=(($i * $pt->getThemeGatheringGridItemHeight()) + ($pt->getThemeGatheringGridItemMargin() * ($i - 1)))?>px;
  }
<?php 
} ?>
</style>
