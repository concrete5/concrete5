<?php
defined('C5_EXECUTE') or die("Access Denied.");
$app = \Core::make('app');
if ($a->isGlobalArea()) {
    $c = Page::getCurrentPage();
    $cID = $c->getCollectionID();
} else {
    $cID = $b->getBlockCollectionID();
    $c = $b->getBlockCollectionObject();
}

$p = new Permissions($b);
$showMenu = false;
if ($a->showControls() && $p->canViewEditInterface() && $view->showControls()) {
    $showMenu = true;
}

$css = $b->getCustomStyle();
$pt = $c->getCollectionThemeObject();

if ($showMenu) {
    ?>
    <div data-container="block">
<?php
} ?>

<?php if (is_object($css) && $b->getBlockTypeHandle() == BLOCK_HANDLE_LAYOUT_PROXY) {
    ?>
    <?php // in this instance, the css container comes OUTSIDE any theme container ?>
    <div class="<?php echo $css->getContainerClass(); ?>"
    <?php if ($css->getCustomStyleID()) { ?>
    id="<?php echo $css->getCustomStyleID(); ?>"
    <?php } ?>
    <?php if ($css->getCustomStyleElementAttribute()) { ?>
    <?php echo $css->getCustomStyleElementAttribute(); ?>
    <?php } ?>
    >
<?php
} ?>

<?php
if (
    $pt->supportsGridFramework()
    && $a->isGridContainerEnabled()
    && !$b->ignorePageThemeGridFrameworkContainer()
) {
    $gf = $pt->getThemeGridFrameworkObject();
    echo $gf->getPageThemeGridFrameworkContainerStartHTML();
    /* No need to add a row if the block is of the 'core_area_layout' type as the area block will add its own row */
    if ($b->getBlockTypeHandle() != BLOCK_HANDLE_LAYOUT_PROXY) {
        echo $gf->getPageThemeGridFrameworkRowStartHTML();
        printf('<div class="%s">', $gf->getPageThemeGridFrameworkColumnClassesForSpan(
            min($a->getAreaGridMaximumColumns(), $gf->getPageThemeGridFrameworkNumColumns())
        ));
    }
}

if ($showMenu) {
    $arHandle = $a->getAreaHandle();

    $btw = BlockType::getByID($b->getBlockTypeID());
    if ($btw->getBlockTypeHandle() == BLOCK_HANDLE_LAYOUT_PROXY) {
        $class = 'ccm-block-edit-layout ccm-block-edit';
    } else {
        $class = 'ccm-block-edit';
    }

    $class .= ($b->isAliasOfMasterCollection() || $b->getBlockTypeHandle() == BLOCK_HANDLE_SCRAPBOOK_PROXY) ? " ccm-block-alias" : "";

    if ($b->getBlockTypeHandle() == BLOCK_HANDLE_STACK_PROXY) {
        $class .= ' ccm-block-stack ';
    }
    $editInline = false;
    if ($btw->supportsInlineEdit()) {
        $editInline = true;
    }
    $aID = $a->getAreaID();
    $btHandle = $btw->getBlockTypeHandle();
    if ($btw->getBlockTypeHandle() == BLOCK_HANDLE_SCRAPBOOK_PROXY) {
        $_bi = $b->getInstance();
        $_bo = Block::getByID($_bi->getOriginalBlockID());
        $btOriginal = \Concrete\Core\Block\BlockType\BlockType::getByHandle($_bo->getBlockTypeHandle());
        $btHandle = $btOriginal->getBlockTypeHandle();
    }

    ?>

    <div
        data-cID="<?=$c->getCollectionID()?>"
        data-area-id="<?=$a->getAreaID()?>"
        data-block-id="<?=$b->getBlockID()?>"
        data-block-type-wraps="<?= intval(!$b->ignorePageThemeGridFrameworkContainer(), 10) ?>"
        class="<?=$class?>"
        data-block-type-handle="<?=$btHandle?>"
        data-launch-block-menu="block-menu-b<?=$b->getBlockID()?>-<?=$a->getAreaID()?>"
        data-dragging-avatar="<?=h('<p><img src="' . Loader::helper('concrete/urls')->getBlockTypeIconURL($btw) . '" /><span>' . t($btw->getBlockTypeName()) . '</span></p>')?>"
        <?php if ($btw->getBlockTypeHandle() == BLOCK_HANDLE_LAYOUT_PROXY) {
    ?> data-block-menu-handle="none"<?php
}
    ?>
        >

    <?php if (is_object($css) && $b->getBlockTypeHandle() != BLOCK_HANDLE_LAYOUT_PROXY) {
    ?>
    <div class="<?php echo $css->getContainerClass(); ?>"
    <?php if ($css->getCustomStyleID()) { ?>
    id="<?php echo $css->getCustomStyleID(); ?>"
    <?php } ?>
    <?php if ($css->getCustomStyleElementAttribute()) { ?>
    <?php echo $css->getCustomStyleElementAttribute(); ?>
    <?php } ?>
    >
    <?php
}
    ?>

        <ul class="ccm-edit-mode-inline-commands ccm-ui">
            <?php if ($p->canEditBlock() && $btw->getBlockTypeHandle() != BLOCK_HANDLE_LAYOUT_PROXY) {
    ?>
                <li><a data-inline-command="move-block" href="#"><i class="fa fa-arrows"></i></a></li>
            <?php
}
    ?>
        </ul>

        <div class="ccm-ui">
            <?php
            $factory = $app->make('Concrete\Core\Block\Menu\Manager');
            $menu = $factory->getMenu([$b, $c, $a]);
            print $factory->deliverMenu($menu);
            ?>
        </div>

<?php
} else {
    ?>
    <?php if (is_object($css) && $b->getBlockTypeHandle() != BLOCK_HANDLE_LAYOUT_PROXY) {
    ?>
    <div class="<?php echo $css->getContainerClass(); ?>"
    <?php if ($css->getCustomStyleID()) { ?>
    id="<?php echo $css->getCustomStyleID(); ?>"
    <?php } ?>
    <?php if ($css->getCustomStyleElementAttribute()) { ?>
    <?php echo $css->getCustomStyleElementAttribute(); ?>
    <?php } ?>
    >
    <?php
}
    ?>
<?php
} ?>
