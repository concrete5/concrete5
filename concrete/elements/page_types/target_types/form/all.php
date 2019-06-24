<?php

defined('C5_EXECUTE') or die('Access Denied.');
$form = Loader::helper('form');
$cParentID = false;
if (is_object($target)) {
    $cParentID = $target->getCollectionID();
}

$relevantPage = null;
$tree = null;

if (is_object($control->getPageObject())) {
    $relevantPage = $control->getPageObject();
} else if ($control->getTargetParentPageID()) {
    $relevantPage = \Page::getByID($control->getTargetParentPageID());
    $tree = $relevantPage->getSiteTreeObject();
}

if (is_object($pagetype) && $pagetype->getPageTypePublishTargetTypeID() == $configuration->getPageTypePublishTargetTypeID()) {
    $configuredTarget = $pagetype->getPageTypePublishTargetObject();

    $ps = Loader::helper('form/page_selector');
    if ($configuredTarget->getSelectorFormFactor() == 'sitemap_in_page') {
        if ($configuredTarget->getStartingPointPageID()) {
            $siteMapParentID = $configuredTarget->getStartingPointPageID();
        } else {
            $siteMapParentID = Page::getHomePageID($relevantPage);
        }
        echo $ps->selectFromSitemap('cParentID', $cParentID, $siteMapParentID, $tree);
    } else {
        echo $ps->selectPage('cParentID', $cParentID);
    }
}
