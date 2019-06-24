<?php
defined('C5_EXECUTE') or die('Access Denied.');
?>

<legend><?=$category->getItemCategoryDisplayName()?></legend>

<ul class="list-unstyled">
    <?php foreach ($category->getItems($package) as $theme) {
        ?>
        <li>
            <div><a href="<?=$view->url('/dashboard/pages/themes/inspect', $theme->getThemeID())?>"><?=$theme->getThemeDisplayName()?></a></div>
            <div> <?= $theme->getThemeDisplayDescription();
                ?> </div>
        </li>
        <?php
    }
    ?>
</ul>
