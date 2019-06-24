<?php
defined('C5_EXECUTE') or die('Access Denied.');
$form = Loader::helper('form');
$cParentID = false;
if (is_object($target)) {
    $cParentID = $target->getCollectionID();
}
if (is_object($pagetype) && $pagetype->getPageTypePublishTargetTypeID() == $configuration->getPageTypePublishTargetTypeID()) {
    $configuredTarget = $pagetype->getPageTypePublishTargetObject();
    $cID = $configuredTarget->getParentPageID();
    $pc = Page::getByID($cID, 'ACTIVE');
    ?>
<span class="checkbox"><?=t('This page will be published beneath <a href="%s">%s</a>.', Loader::helper('navigation')->getLinkToCollection($pc), $pc->getCollectionName())?></label>
<?php 
} ?>