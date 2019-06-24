<?php defined('C5_EXECUTE') or die('Access Denied.');

$nh = Loader::helper('validation/numbers');
$form = Loader::helper('form');
$gaiID = intval($_REQUEST['gaiID']);
$gatTypeID = intval($_REQUEST['gatTypeID']);
$type = GatheringItemTemplateType::getByID($gatTypeID);
$nh = Loader::helper('validation/numbers');
$item = GatheringItem::getByID($gaiID);
if (is_object($item) && is_object($type)) {
    $gathering = $item->getGatheringObject();
    $agp = new Permissions($gathering);
    if ($agp->canEditGatheringItems() && Loader::helper('validation/token')->validate('edit_gathering_item_template', $_REQUEST['token'])) {
        $reloadItemTile = false;
        if ($type->getGatheringItemTemplateTypeHandle() == 'tile') {
            $reloadItemTile = true;
        }

        if ($_POST['task'] == 'update_item_template') {
            $template = GatheringItemTemplate::getByID($_POST['gatID']);
            $item->setGatheringItemTemplate($type, $template);
            if ($reloadItemTile) {
                $item->render($type);
            }
            exit;
        }

        $assignments = GatheringItemFeatureAssignment::getList($item);
        $features = array();
        foreach ($assignments as $as) {
            $f = $as->getFeatureObject();
            if (is_object($f)) {
                $features[] = $f;
            }
        }

        $templates = GatheringItemTemplate::getListByType($type);
        ?>


    <script type="text/javascript">
    $(function() {
      $('#ccm-dialog-gathering-item-templates .ccm-dialog-icon-item-grid-sets ul a').on('click', function() {
        $('#ccm-dialog-gathering-item-templates .ccm-overlay-icon-item-grid-list li').hide();
        $('#ccm-dialog-gathering-item-templates .ccm-overlay-icon-item-grid-list li[data-gathering-item-template-features~=' + $(this).attr('data-tab') + ']').show();
        $('#ccm-dialog-gathering-item-templates .ccm-dialog-icon-item-grid-sets ul a').removeClass('active');
        $(this).addClass('active');
        return false;
      });

      $($('#ccm-dialog-gathering-item-templates .ccm-dialog-icon-item-grid-sets ul a').get(0)).trigger('click');

      $('#ccm-dialog-gathering-item-templates').closest('.ui-dialog-content').addClass('ui-dialog-content-icon-item-grid');
      $('#ccm-dialog-gathering-item-templates .ccm-icon-item-grid-search input').focus();
      $('#ccm-dialog-gathering-item-templates .ccm-icon-item-grid-search input').liveUpdate('ccm-dialog-gathering-item-templates .ccm-overlay-icon-item-grid-list');
      
      $('#ccm-dialog-gathering-item-templates .ccm-icon-item-grid-search input').on('keyup', function() {
        if ($(this).val() == '') {
          $('#ccm-dialog-gathering-item-templates .ccm-dialog-icon-item-grid-sets ul a.active').click();
        }
      });
    });
    </script>

    <div class="ccm-ui ccm-dialog-icon-item-grid" id="ccm-dialog-gathering-item-templates">

    <div class="ccm-dialog-icon-item-grid-sets">

    <form class="form-inline ccm-icon-item-grid-search">
      <i class="icon-search"></i> <input type="search" />
    </form>

      <ul>
    <?php
    foreach ($features as $f) {
        ?>
      <li><a href="#" data-tab="<?=$f->getFeatureHandle()?>"><?=$f->getFeatureName()?></a></li>
    <?php 
    }
        ?> 
    </ul>

    </div>

    <div class="ccm-dialog-icon-item-grid-list-wrapper">
      
      <ul class="ccm-overlay-icon-item-grid-list">

      <?php foreach ($templates as $t) {
    if (!$item->itemSupportsGatheringItemTemplate($t)) {
        continue;
    }

    $templateFeatures = $t->getGatheringItemTemplateFeatureHandles();
    $sets = '';
    foreach ($templateFeatures as $tfHandle) {
        $sets .= $tfHandle . ' ';
    }
    $sets = trim($sets);

    ?>

        <li data-gathering-item-template-features="<?=$sets?>">
          <a href="javascript:void(0)" <?php if ($item->getGatheringItemTemplateID($type) == $t->getGatheringItemTemplateID()) {
    ?>class="ccm-gathering-item-template-selected"<?php 
}
    ?> onclick="$.fn.ccmgathering('updateItemTemplate', {gaiID: '<?=$gaiID?>', gatID: '<?=$t->getGatheringItemTemplateID()?>', gatTypeID: '<?=$gatTypeID?>', reloadItemTile: <?php if ($reloadItemTile) {
    ?>true<?php 
} else {
    ?>false<?php 
}
    ?>, 'updateToken': '<?=Loader::helper('validation/token')->generate('edit_gathering_item_template')?>'})"><p><img src="<?=$t->getGatheringItemTemplateIconSRC()?>" /><span><?=$t->getGatheringItemTemplateName()?></span></p></a>
        </li>
        
      <?php 
}
        ?>

      </ul>


    </div>


    <?php 
    }
}
?>
