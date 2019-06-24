<?php

defined('C5_EXECUTE') or die('Access Denied.');
if (Loader::helper('validation/token')->validate()) {
    if ($_REQUEST['cID'] && Loader::helper('validation/numbers')->integer($_REQUEST['cID'])) {
        $c = Page::getByID($_REQUEST['cID'], 'RECENT');
        if (is_object($c) && !$c->isError()) {
            $c->forceCheckIn();
        }
    }
}
