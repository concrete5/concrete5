<?php

defined('C5_EXECUTE') or die("Access Denied.");

if ($a->isGlobalArea()) {
    $c = Page::getCurrentPage();
    $cID = $c->getCollectionID();
} else {
    $cID = $b->getBlockCollectionID();
    $c = $b->getBlockCollectionObject();
}

$blockStyle = $b->getCustomStyle();
?>

<?php
if (
    $pt->supportsGridFramework()
    && $b->getBlockAreaObject()->isGridContainerEnabled()
    && !$b->ignorePageThemeGridFrameworkContainer()
) {
    $gf = $pt->getThemeGridFrameworkObject();
	/* The 'core_area_layout' block deals with it's own row */
    if ($b->getBlockTypeHandle() != BLOCK_HANDLE_LAYOUT_PROXY) {
        echo '</div>';
        echo $gf->getPageThemeGridFrameworkRowEndHTML();
    }
    echo $gf->getPageThemeGridFrameworkContainerEndHTML();
}

$p = new Permissions($b);
$showMenu = false;
if ($a->showControls() && $p->canViewEditInterface() && $view->showControls()) {
    ?>
<?php if (is_object($blockStyle)) {
    ?>
    </div>
<?php 
}
    ?>
    </div>
    </div>
<?php 
} elseif (is_object($blockStyle)) {
    ?>
    </div>
<?php 
} ?>