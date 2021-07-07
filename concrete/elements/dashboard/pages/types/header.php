<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>
<div class="ccm-dashboard-header-buttons btn-group">
    <?php if (count($siteTypes) > 1) { ?>
    <div class="btn-group">
        <button type="button" class="btn-sm btn btn-secondary dropdown-toggle" data-button="attribute-type" data-bs-toggle="dropdown">
            <?=$currentSiteType->getSiteTypeName()?> <span class="caret"></span>
        </button>
        <ul class="dropdown-menu">
            <?php foreach($siteTypes as $type) { ?>
                <li><a href="<?=$view->action('view', $type->getSiteTypeID())?>"><?=$type->getSiteTypeName()?></a></li>
            <?php } ?>
        </ul>
    </div>
    <?php } ?>
    <a href="<?=$view->url('/dashboard/pages/types/organize', isset($siteTypeID) ? $siteTypeID : null)?>" class="btn btn-sm btn-secondary"><?=t('Order &amp; Group')?></a>
    <a href="<?=$view->url('/dashboard/pages/types/add', isset($siteTypeID) ? $siteTypeID : null)?>" class="btn btn-sm btn-primary"><?=t('Add Page Type')?></a>
</div>




