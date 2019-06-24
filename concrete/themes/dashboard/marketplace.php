<?php
defined('C5_EXECUTE') or die('Access Denied.');
$this->inc('elements/header.php'); ?>

<div id="ccm-marketplace-wrapper">

<header class="ccm-marketplace">

    <?php if ($controller->getTask() == 'view_detail') {
    ?>
        <div class="ccm-marketplace-nav">
            <nav>
            <li><a href="<?=$controller->action('view')?>"><i class="fa fa-chevron-left"></i> <?=t('Back')?></a></li>
            </nav>
        </div>
    <?php 
} else {
    ?>
        <form action="<?=$controller->action('view')?>" method="get">
            <input type="hidden" name="ccm_order_by" value="<?=$sort?>" />
        <div class="ccm-marketplace-nav">
            <nav>
            <li><a href="<?=URL::to('/dashboard/extend/themes')?>" <?php if ($type == 'themes') {
    ?>class="active"<?php 
}
    ?>><?=t('Themes')?></a></li>
            <li><a href="<?=URL::to('/dashboard/extend/addons')?>" <?php if ($type == 'addons') {
    ?>class="active"<?php 
}
    ?>><?=t('Add-Ons')?></a></li>
            </nav>
        </div>
        <div class="ccm-marketplace-search">
            <?=$form->select('marketplaceRemoteItemSetID', $sets, $selectedSet, array('style' => 'width: 150px'))?>
            <div class="ccm-marketplace-search-input">
                <i class="fa fa-search"></i>
                <input type="search" name="keywords" value="<?=$keywords?>" />
            </div>
            <button type="submit" class="btn btn-primary btn-sm"><?=t('Search')?></button>
        </div>
        </form>
    <?php 
} ?>
</header>


<?php if ($controller->getTask() != 'view_detail') {
    ?>
<header class="ccm-marketplace-list">
    <h1><?=$heading?></h1>
    <div class="ccm-marketplace-sort">
        <nav>
        <li><a href="<?=$list->getSortByURL('popularity')?>" <?php if ($sort == 'popularity') {
    ?>class="active"<?php 
}
    ?>><?=t('Most Popular')?></a></li>
        <li><a href="<?=$list->getSortByURL('recent')?>" <?php if ($sort == 'recent') {
    ?>class="active"<?php 
}
    ?>><?=t('Recent')?></a></li>
        <li><a href="<?=$list->getSortByURL('price')?>" <?php if ($sort == 'price') {
    ?>class="active"<?php 
}
    ?>><?=t('Price')?></a></li>
        <li><a href="<?=$list->getSortByURL('rating')?>" <?php if ($sort == 'rating') {
    ?>class="active"<?php 
}
    ?>><?=t('Rating')?></a></li>
        <li><a href="<?=$list->getSortByURL('skill_level')?>" <?php if ($sort == 'skill_level') {
    ?>class="active"<?php 
}
    ?>><?=t('Skill Level')?></a></li>
        </nav>
    </div>
</header>

<?php 
} ?>


<?php echo $innerContent; ?>

</div>


<?php $this->inc('elements/footer.php');
