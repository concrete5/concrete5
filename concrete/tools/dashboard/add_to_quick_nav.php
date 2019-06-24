<?php

defined('C5_EXECUTE') or die('Access Denied.');
$ih = Loader::helper('validation/numbers');
$dh = Loader::helper('concrete/dashboard');
$ish = Loader::helper('concrete/ui');
$canAdd = false;

if ($ih->integer($_REQUEST['cID'])) {
    $c = Page::getByID($_REQUEST['cID']);
    if (is_object($c) && (!$c->isError())) {
        $cp = new Permissions($c);
        if ($dh->inDashboard($c)) {
            if ($cp->canViewPage()) {
                $canAdd = true;
            }
        }
    }
}

$ish->clearInterfaceItemsCache();

if ($canAdd) {
    $u = new User();
    $r = new stdClass();
    if (Loader::helper('validation/token')->validate('access_quick_nav', $_REQUEST['token'])) {
        $qn = \Concrete\Core\Application\Service\DashboardMenu::getMine();
        if ($qn->contains($c)) {
            $qn->remove($c);
            $task = 'add';
        } else {
            $qn->add($c);
            $task = 'remove';
        }

        $u->saveConfig('QUICK_NAV_BOOKMARKS', serialize($qn));

        echo $dh->getDashboardAndSearchMenus();
        exit;
    }
}
