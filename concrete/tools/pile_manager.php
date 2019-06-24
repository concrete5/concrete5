<?php

use Concrete\Core\Page\Stack\Pile\Pile;
use Concrete\Core\Page\Stack\Pile\PileContent;

defined('C5_EXECUTE') or die('Access Denied.');

if (!$u->isRegistered()
    || (!Core::make('token')->validate('tools/clipboard/from') && !Core::make('token')->validate('tools/clipboard/to'))
) {
    die(t("Access Denied."));
}

$p = false;
$c = Page::getByID($_REQUEST['cID']);
// add a block to a pile
$cp = new Permissions($c);
if (!$cp->canViewPage()) {
    die(t("Access Denied."));
}

if (Request::request('task', 'default') === 'delete') {
    $pileContent = PileContent::get(Request::request('pcID', 0));
    if (!$pileContent->getPile()->isMyPile()) {
        die(t("Access Denied."));
    }
    $pileContent->delete();
} else {
    $u = new User();

    $a = Area::get($c, $_REQUEST['arHandle']);
    if ($a->isGlobalArea()) {
        $ax = STACKS_AREA_NAME;
        $cx = Stack::getByName($_REQUEST['arHandle']);
    } else {
        $cx = $ax = null;
    }
    $b = Block::getByID($_REQUEST['bID'], $cx, $ax);
    if ($b->getBlockTypeHandle() == BLOCK_HANDLE_SCRAPBOOK_PROXY) {
        $bi = $b->getInstance();
        $b = Block::getByID($bi->getOriginalBlockID());
    }
    $ap = new Permissions($a);
    if (!$ap->canViewArea()) {
        die(t("Access Denied."));
    }
    $obj = &$b;

    $p = Pile::getDefault();
    $p->add($obj);
    $added = true;
}
