<?php
use Concrete\Core\Block\View\BlockView;

defined('C5_EXECUTE') or die('Access Denied.');

$bo = Block::getByID($bOriginalID);
$bp = new Permissions($bo);
$bo->setProxyBlock($b);
if ($bp->canWrite()) {
    $bv = new BlockView($bo);
    ?>

    <div class="ccm-ui">
        <div class="alert alert-info">
            <?= t("This block was copied from another location. Editing it will create a new instance of it.") ?>
        </div>
    </div>

    <?php

    $bv->setAreaObject($view->getAreaObject());
    $bv->addScopeItems($view->getScopeItems());
    $bv->render('edit');
}
