<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<?php

    // grab all tags in use based on the path
    $ak = CollectionAttributeKey::getByHandle('tags');
    $akc = $ak->getController();
    $pp = false;

    $tagCounts = array();

    if ($baseSearchPath != '') {
        $pp = Page::getByPath($baseSearchPath);
    }
    $ttags = $akc->getOptionUsageArray($pp);
    $tags = array();
    foreach ($ttags as $t) {
        $tagCounts[] = $t->getSelectAttributeOptionUsageCount();
        $tags[] = $t;
    }
    shuffle($tags);
    $tagSizes = array();
    $count = count($tagCounts);
    foreach ($tagCounts as $tagCount => $pos) {
        $tagSizes[$pos] = setFontPx(($pos + 1) / $count);
    }

    function setFontPx($weight)
    {
        $tagMinFontPx = 10;
        $tagMaxFontPx = 24;

        $em = ($weight * ($tagMaxFontPx - $tagMinFontPx)) + $tagMinFontPx;
        $em = round($em);

        return $em;
    }
?>


<?php if ($title) {
    ?>
	<h3><?=h($title)?></h3>
<?php
} ?>

<div class="ccm-search-block-tag-cloud-wrapper ">

<ul id="ccm-search-block-tag-cloud-<?=$bID?>" class="ccm-search-block-tag-cloud">

<?php
    for ($i = 0; $i < $ttags->count(); ++$i) {
        $akct = $tags[$i];
        $qs = urlencode($akc->field('atSelectOptionID') . '[]') . '=' . urlencode($akct->getSelectAttributeOptionID());
        ?>
		<li><a style="font-size: <?=$tagSizes[$akct->getSelectAttributeOptionUsageCount()]?>px !important" href="<?=$view->url($resultTarget)?>?<?=$qs?>"><?=$akct->getSelectAttributeOptionValue()?></a>
		<span>(<?=$akct->getSelectAttributeOptionUsageCount()?>)</span>
		</li>
<?php
    } ?>
</ul>

<div class="ccm-spacer">&nbsp;</div>
</div>