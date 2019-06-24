<?php

defined('C5_EXECUTE') or die('Access Denied.');

if (!is_object($b)) {
    echo '<div class="ccm-ui"><div class="alert alert-danger">';
    echo t("Unable to retrieve block object. If this block has been moved please reload the page.");
    echo '</div></div';
    exit;
}

if ($isGlobalArea) {
    echo '<div class="ccm-ui"><div class="alert alert-warning">';
    echo t(
        'This block is contained within a global area. Changing its content will change it everywhere that global area is referenced.');
    echo '</div></div>';
}

if ($c->isMasterCollection()) {
    echo '<div class="ccm-ui"><div class="alert alert-warning">';
    echo t('This is a global block.  Editing it here will change all instances of this block throughout the site.');
    //echo '[<a class="ccm-dialog-close">'.t('Close Window').'</a>]';
    echo '</div></div>';
}

if ($b->isAliasOfMasterCollection()) {
    echo '<div class="ccm-ui"><div class="alert alert-warning">';
    echo t(
        'This block is an alias of Page Defaults. Editing it here will "disconnect" it so changes to Page Defaults will no longer affect this block.');
    echo '</div></div>';
}

$bv->render('edit');
