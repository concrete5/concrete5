<?php
defined('C5_EXECUTE') or die('Access Denied.');
?>
<div class="ccm-dashboard-header-buttons btn-group">
    <?php if (count($siteTypes) > 1) { ?>
    <div class="btn-group">
        <button type="button" class="btn btn-default dropdown-toggle" data-button="attribute-type" data-toggle="dropdown">
            <?=$currentSiteType->getSiteTypeName()?> <span class="caret"></span>
        </button>
        <ul class="dropdown-menu">
            <?php foreach($siteTypes as $type) { ?>
                <li><a href="<?=$view->action('view', $type->getSiteTypeID())?>"><?=$type->getSiteTypeName()?></a></li>
            <?php } ?>
        </ul>
    </div>
    <?php } ?>
    <a href="<?=$view->url('/dashboard/pages/types/organize', isset($siteTypeID) ? $siteTypeID : null)?>" class="btn btn-default"><?=t('Order &amp; Group')?></a>
    <a href="<?=$view->url('/dashboard/pages/types/add', isset($siteTypeID) ? $siteTypeID : null)?>" class="btn btn-primary"><?=t('Add Page Type')?></a>
</div>




