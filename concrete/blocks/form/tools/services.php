<?php

defined('C5_EXECUTE') or die('Access Denied.');
use \Concrete\Block\Form\MiniSurvey;

$miniSurvey = new MiniSurvey();
$bID = $_GET['bID'];
//Permissions Check
$bID = $_REQUEST['bID'];

if ($_GET['cID'] && $_GET['arHandle']) {
    $badPermissions = false;
    $c = Page::getByID($_GET['cID'], 'RECENT');
    $a = Area::get($c, $_GET['arHandle']);
    if (intval($_GET['bID']) == 0) {
        //add survey mode
        $ap = new Permissions($a);
        $bt = BlockType::getByID($_GET['btID']);
        if (!$ap->canAddBlock($bt)) {
            $badPermissions = true;
        }
    } else {
        //edit survey mode
        // this really ought to be refactored
        if (!$a->isGlobalArea()) {
            $b = Block::getByID($_REQUEST['bID'], $c, $a);
            if ($b->getBlockTypeHandle() == BLOCK_HANDLE_SCRAPBOOK_PROXY) {
                $b = Block::getByID($b->getController()->getOriginalBlockID());
                $b->setBlockAreaObject($a);
                $b->loadNewCollection($c);
                $bID = $b->getBlockID();
            }
        } else {
            $b = Block::getByID($_REQUEST['bID'], Stack::getByName($a->getAreaHandle()), STACKS_AREA_NAME);
            $b->setBlockAreaObject($a); // set the original area object
        }

        $bp = new Permissions($b);
        if (!$bp->canWrite()) {
            $badPermissions = true;
        }
    }
} else {
    $badPermissions = true;
}
if ($badPermissions) {
    echo t('Invalid Permissions');
    die;
}

switch ($_GET['mode']) {

    case 'addQuestion':
        $miniSurvey->addEditQuestion($_POST);
        break;

    case 'getQuestion':
        $miniSurvey->getQuestionInfo(intval($_GET['qsID']), intval($_GET['qID']));
        break;

    case 'delQuestion':
        $miniSurvey->deleteQuestion(intval($_GET['qsID']), intval($_GET['msqID']));
        break;

    case 'reorderQuestions':
        $miniSurvey->reorderQuestions(intval($_POST['qsID']), $_POST['qIDs']);
        break;

    case 'refreshSurvey':
    default:
        $showEdit = (isset($_REQUEST['showEdit']) && intval($_REQUEST['showEdit']) == 1) ? true : false;
        $miniSurvey->loadSurvey(intval($_GET['qsID']), $showEdit, intval($bID), explode(',', $_GET['hide']), 1, 1);
}
