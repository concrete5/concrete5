<?php

defined('C5_EXECUTE') or die('Access Denied.');
use \Concrete\Core\Conversation\Message\MessageList as ConversationMessageList;
use \Concrete\Core\Conversation\Message\ThreadedList as ConversationMessageThreadedList;

$cnv = Conversation::getByID($_POST['cnvID']);
if (is_object($cnv)) {
    $enablePosting = ($_POST['enablePosting'] == 1) ? true : false;
    $enableOrdering = ($_POST['enableOrdering'] == 1) ? true : false;
    $currentPage = (Loader::helper('validation/numbers')->integer($_POST['page'])) ? $_POST['page'] : 1;

    if (in_array($_POST['displayMode'], array('flat'))) {
        $displayMode = $_POST['displayMode'];
    } else {
        $displayMode = 'threaded';
    }

    switch ($displayMode) {
        case 'flat':
            $ml = new ConversationMessageList();
            $ml->filterByConversation($cnv);
            break;
        default: // threaded
            $ml = new ConversationMessageThreadedList($cnv);
            break;
    }

    switch ($_POST['orderBy']) {
        case 'date_desc':
            $ml->sortByDateDescending();
            break;
        case 'date_asc':
            $ml->sortByDateAscending();
            break;
        case 'rating':
            $ml->sortByRating();
            break;
    }

    $ml->setItemsPerPage($_POST['itemsPerPage']);

    $summary = $ml->getSummary();
    $totalPages = $summary->pages;

    foreach ($ml->getPage($currentPage) as $message) {
        Loader::element('conversation/message', array('message' => $message, 'enablePosting' => $enablePosting, 'displayMode' => $displayMode));
    }
}
