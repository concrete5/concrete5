<?php
    defined('C5_EXECUTE') or die('Access Denied.');

$area = $b->getBlockAreaObject();
$_bx = Block::getByID($bOriginalID);
if (is_object($_bx)) {
    $_bx->setBlockAreaObject($area);
    $c = Page::getCurrentPage();
    $_bx->setProxyBlock($b);
    $_bx->loadNewCollection($c);
    $_bx->disableBlockContainer();
    $bv = new \Concrete\Core\Block\View\BlockView($_bx);
    $bv->setController($controller->getScrapbookBlockController());
    $bv->disableControls();
    $bv->render('view');
}
