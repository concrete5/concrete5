<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>
<?php $rowCount = 0;
for ($i = 0; $i < count($categories); ++$i) {
    $cat = $categories[$i];
    if ($rowCount == 3 || $i == 0) {
        $offset = '';
        ?>
        <div class="row">
        <?php
        $rowCount = 0;
    }
    ?>

    <div class="col-md-4 ccm-dashboard-section-menu">
        <h2><?=t($cat->getCollectionName())?></h2>


        <?php
        $show = array();
    $subcats = $cat->getCollectionChildrenArray(true);
    foreach ($subcats as $catID) {
        $subcat = Page::getByID($catID, 'ACTIVE');
        $catp = new Permissions($subcat);
        if ($catp->canRead() && !$subcat->getAttribute("exclude_nav")) {
            $show[] = $subcat;
        }
    }
    ?>

        <ul class="list-unstyled">

        <?php if (count($show) > 0) {
    ?>

            <?php foreach ($show as $subcat) {
    ?>

                <li><a href="<?=Loader::helper('navigation')->getLinkToCollection($subcat, false, true)?>"><i class="<?=$subcat->getAttribute('icon_dashboard')?>"></i> <?=t($subcat->getCollectionName())?></a></li>

            <?php 
}
    ?>

        <?php 
} else {
    ?>

            <li><a href="<?=Loader::helper('navigation')->getLinkToCollection($cat, false, true)?>"><i class="<?=$cat->getAttribute('icon_dashboard')?>"></i> <?=t('Home')?></a></li>

        <?php 
}
    ?>

        </ul>
    </div>
    <?php if ($rowCount == 2 || $i == count($categories)) {
    ?>
        </div>
    <?php 
}
    ++$rowCount;
} ?>