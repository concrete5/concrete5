<?php

defined('C5_EXECUTE') or die('Access Denied.');
use Concrete\Core\Block\Events\BlockDelete;
use Concrete\Core\Page\Stack\Pile\PileContent;

# Filename: _process.php
# Author: Andrew Embler (andrew@concrete5.org)
# -------------------
# _process.php is included at the top of the dispatcher and basically
# checks to see if a any submits are taking place. If they are, then
# _process makes sure that they're handled correctly

// if we don't have a valid token we die

// ATTENTION! This file is legacy and needs to die. We are moving it's various pieces into
// controllers.
$valt = Loader::helper('validation/token');
$token = '&' . $valt->getParameter();

// If the user has checked out something for editing, we'll increment the lastedit variable within the database
$u = new User();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $u->refreshCollectionEdit($c);
}

$securityHelper = Loader::helper('security');

if (isset($_GET['atask']) && $_GET['atask'] && $valt->validate()) {
    switch ($_GET['atask']) {
        case 'add_stack':
            $a = Area::get($c, $_GET['arHandle']);
            $cx = $c;
            $ax = $a;

            if ($a->isGlobalArea()) {
                $cx = Stack::getByName($_REQUEST['arHandle']);
                $ax = Area::get($cx, STACKS_AREA_NAME);
            }
            $obj = new stdClass();

            $ap = new Permissions($ax);
            $stack = Stack::getByID($_REQUEST['stID']);
            if (is_object($stack)) {
                if ($ap->canAddStackToArea($stack)) {
                    // we've already run permissions on the stack at this point, at least for viewing the stack.
                    $btx = BlockType::getByHandle(BLOCK_HANDLE_STACK_PROXY);
                    $nvc = $cx->getVersionToModify();
                    if ($a->isGlobalArea()) {
                        $xvc = $c->getVersionToModify(); // we need to create a new version of THIS page as well.
                        $xvc->relateVersionEdits($nvc);
                    }
                    $data['stID'] = $stack->getCollectionID();
                    $nb = $nvc->addBlock($btx, $ax, $data);

                    $obj->aID = $a->getAreaID();
                    $obj->arHandle = $a->getAreaHandle();
                    $obj->cID = $c->getCollectionID();
                    $obj->bID = $nb->getBlockID();
                    $obj->error = false;

                    $db = null;
                    if (isset($_REQUEST['dragAreaBlockID']) && Core::make('helper/validation/numbers')->integer($_REQUEST['dragAreaBlockID'], 1)) {
                        $db = Block::getByID(
                            $_REQUEST['dragAreaBlockID'],
                            isset($this->pageToModify) ? $this->pageToModify : null,
                            isset($this->areaToModify) ? $this->areaToModify : null
                        );
                        if (is_object($db) && !$db->isError()) {
                            $nb->moveBlockToDisplayOrderPosition($db);
                        }
                    }
                    if (!is_object($db)) {
                        $nb->moveBlockToDisplayOrderPosition(false);
                    }
                } else {
                    $obj->error = true;
                    $obj->response = array(t('The stack contains invalid block types.'));
                }
            } else {
                $obj->error = true;
                $obj->response = array(t('Invalid stack.'));
            }

            echo Loader::helper('json')->encode($obj);
            exit;

            break;

    }
}

if (isset($_REQUEST['ctask']) && $_REQUEST['ctask'] && $valt->validate()) {
    switch ($_REQUEST['ctask']) {
        case 'check-out-add-block':
        case 'check-out':
        case 'check-out-first':
            if ($cp->canEditPageContents() || $cp->canEditPageProperties() || $cp->canApprovePageVersions()) {
                // checking out the collection for editing
                $u = new User();
                $u->loadCollectionEdit($c);

                if ($_REQUEST['ctask'] == 'check-out-add-block') {
                    setcookie("ccmLoadAddBlockWindow", "1", -1, DIR_REL . '/');
                    header(
                        'Location: ' . \Core::getApplicationURL() . '/' . DISPATCHER_FILENAME . '?cID=' . $c->getCollectionID());
                    exit;
                    break;
                }
            }
            break;

        case 'approve-recent':
            if ($cp->canApprovePageVersions()) {
                $u = new User();
                $pkr = new \Concrete\Core\Workflow\Request\ApprovePageRequest();
                $pkr->setRequestedPage($c);
                $v = CollectionVersion::get($c, "RECENT");
                $pkr->setRequestedVersionID($v->getVersionID());
                $pkr->setRequesterUserID($u->getUserID());
                $u->unloadCollectionEdit($c);
                $response = $pkr->trigger();
                header(
                    'Location: ' . \Core::getApplicationURL() . '/' . DISPATCHER_FILENAME . '?cID=' . $c->getCollectionID());
                exit;
            }
            break;

        case 'publish-now':
            if ($cp->canApprovePageVersions()) {
                $v = CollectionVersion::get($c, "SCHEDULED");
                $v->approve(false);

                header('Location: ' . \Core::getApplicationURL() . '/' . DISPATCHER_FILENAME .
                    '?cID=' . $c->getCollectionID());

                exit;
            }
            break;
    }
}

if (isset($_REQUEST['ptask']) && $_REQUEST['ptask'] && $valt->validate()) {

    // piles !
    switch ($_REQUEST['ptask']) {
        case 'delete_content':
            //personal scrapbook
            if ($_REQUEST['pcID'] > 0) {
                $pc = PileContent::get($_REQUEST['pcID']);
                $p = $pc->getPile();
                if ($p->isMyPile()) {
                    $pc->delete();
                }
                //global scrapbooks
            } elseif ($_REQUEST['bID'] > 0 && $_REQUEST['arHandle']) {
                $bID = intval($_REQUEST['bID']);
                $scrapbookHelper = Loader::helper('concrete/scrapbook');
                $globalScrapbookC = $scrapbookHelper->getGlobalScrapbookPage();
                $globalScrapbookA = Area::get($globalScrapbookC, $_REQUEST['arHandle']);
                $block = Block::getById($bID, $globalScrapbookC, $globalScrapbookA);
                if ($block) { //&& $block->getAreaHandle()=='Global Scrapbook'
                    $bp = new Permissions($block);
                    if (!$bp->canWrite()) {
                        throw new Exception(t('Access to block denied'));
                    } else {
                        $block->delete(1);
                    }
                }
            }
            die;
            break;
    }
}

if (isset($_REQUEST['processBlock']) && $_REQUEST['processBlock'] && $valt->validate()) {
    if ($_REQUEST['add'] || $_REQUEST['_add']) {
        // the user is attempting to add a block of content of some kind
        $a = Area::get($c, $_REQUEST['arHandle']);
        if (is_object($a)) {
            $ax = $a;
            $cx = $c;
            if ($a->isGlobalArea()) {
                $cx = Stack::getByName($_REQUEST['arHandle']);
                $ax = Area::get($cx, STACKS_AREA_NAME);
            }
            $ap = new Permissions($ax);
            if ($_REQUEST['btask'] == 'alias_existing_block') {
                if (is_array($_REQUEST['pcID'])) {

                    // we're taking an existing block and aliasing it to here
                    foreach ($_REQUEST['pcID'] as $pcID) {
                        $pc = PileContent::get($pcID);
                        $p = $pc->getPile();
                        if ($p->isMyPile()) {
                            if ($_REQUEST['deletePileContents']) {
                                $pc->delete();
                            }
                        }
                        if ($pc->getItemType() == "BLOCK") {
                            $bID = $pc->getItemID();
                            $b = Block::getByID($bID);
                            $b->setBlockAreaObject($ax);
                            $bt = BlockType::getByHandle($b->getBlockTypeHandle());
                            if ($ap->canAddBlock($bt)) {

                                $nvc = $cx->getVersionToModify();
                                if ($a->isGlobalArea()) {
                                    $xvc = $c->getVersionToModify(); // we need to create a new version of THIS page as well.
                                    $xvc->relateVersionEdits($nvc);
                                }

                                if (!$bt->isCopiedWhenPropagated()) {
                                    $btx = BlockType::getByHandle(BLOCK_HANDLE_SCRAPBOOK_PROXY);

                                    $data['bOriginalID'] = $bID;
                                    $nb = $nvc->addBlock($btx, $ax, $data);
                                } else {
                                    $nb = $b->duplicate($nvc);
                                    $nb->move($nvc, $ax);
                                }

                                $nb->refreshCache();
                            }
                        }
                    }
                } else {
                    if (isset($_REQUEST['bID'])) {
                        $b = Block::getByID($_REQUEST['bID']);
                        $b->setBlockAreaObject($ax);
                        $bt = BlockType::getByHandle($b->getBlockTypeHandle());

                        if ($ap->canAddBlock($bt)) {

                            $nvc = $cx->getVersionToModify();
                            if ($a->isGlobalArea()) {
                                $xvc = $c->getVersionToModify(); // we need to create a new version of THIS page as well.
                                $xvc->relateVersionEdits($nvc);
                            }

                            if (!$bt->isCopiedWhenPropagated()) {
                                $btx = BlockType::getByHandle(BLOCK_HANDLE_SCRAPBOOK_PROXY);
                                $data['bOriginalID'] = $_REQUEST['bID'];
                                $nb = $nvc->addBlock($btx, $ax, $data);
                            } else {
                                $nb = $b->duplicate($nvc);
                                $nb->move($nvc, $ax);
                            }

                            $nb->refreshCache();
                        }
                    }
                }

                $obj = new stdClass();
                if (is_object($nb)) {
                    if ($_REQUEST['dragAreaBlockID'] > 0 && Loader::helper('validation/numbers')
                            ->integer(
                                $_REQUEST['dragAreaBlockID'])
                    ) {
                        $db = Block::getByID(
                            $_REQUEST['dragAreaBlockID'],
                            $this->pageToModify,
                            $this->areaToModify);
                        if (is_object($db) && !$db->isError()) {
                            $nb->moveBlockToDisplayOrderPosition($db);
                        }
                    }
                    if (!is_object($db)) {
                        $nb->moveBlockToDisplayOrderPosition(false);
                    }
                    $nb->refreshCache();

                    $obj->aID = $a->getAreaID();
                    $obj->arHandle = $a->getAreaHandle();
                    $obj->cID = $c->getCollectionID();
                    $obj->bID = $nb->getBlockID();
                    $obj->error = false;
                } else {
                    $e = Loader::helper('validation/error');
                    $e->add(t('Invalid block.'));
                    $obj->error = true;
                    $obj->response = $e->getList();
                }
                echo Loader::helper('json')->encode($obj);
                exit;
            }
        }
    }
}
