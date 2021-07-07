<?php defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var $breadcrumb \Concrete\Core\Navigation\Breadcrumb\Dashboard\DashboardBreadcrumb
 */
$items = $breadcrumb->getItems();
$total = count($items);
if ($total > 1) {
?>
<ol class="breadcrumb">
    <?php
    for ($i = 0; $i < $total; $i++) {
        $item = $items[$i];
        $isActive = ($total - $i) == 1 ? true : false;
        ?>
        <li class="breadcrumb-item <?=$isActive ? 'active' : ''?>">
            <?php if (count($item->getChildren())) { ?>
                <span class="dropdown">
                <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown">
                    <?= $item->getName() ?>
                    <span class="caret"></span>
                </a>
                    <ul class="dropdown-menu" role="menu">
                        <?php
                        foreach ($item->getChildren() as $child) {
                            ?>
                            <li><a class="dropdown-item" href="<?= h($child->getUrl()); ?>"><?= $child->getName(); ?></a></li><?php
                        } ?>
                    </ul>
                </span>
            <?php } else { ?>
                <?php if ($isActive) { ?>
                    <?=$item->getName()?>
                <?php } else { ?>
                    <a href="<?=$item->getURL()?>"><?=$item->getName()?></a>
                <?php } ?>
            <?php } ?>
        </li>
    <?php }
    ?>
</ol>
<?php } ?>
