<?php

defined('C5_EXECUTE') or die('Access Denied.');
use \Concrete\Core\Conversation\Message\Message as ConversationMessage;

$ax = Loader::helper('ajax');
$vs = Loader::helper('validation/strings');
$ve = Loader::helper('validation/error');
$as = Loader::helper('validation/antispam');
$pageObj = Page::getByID($_POST['cID']);
$areaObj = Area::get($pageObj, $_POST['blockAreaHandle']);
$blockObj = Block::getByID($_POST['bID'], $pageObj, $areaObj);

if (!Loader::helper('validation/token')->validate('edit_conversation_message', $_POST['token'])) {
    $ve->add(t('Invalid conversation post token.'));
}
if (!$vs->notempty($_POST['cnvMessageBody'])) {
    $ve->add(t('Your message cannot be empty.'));
}

if (Loader::helper('validation/numbers')->integer($_POST['cnvMessageID']) && $_POST['cnvMessageID'] > 0) {
    $message = ConversationMessage::getByID($_POST['cnvMessageID']);

    if (!is_object($message)) {
        $ve->add(t('Invalid message object.'));
    } else {
        $mp = new Permissions($message);
        if (!$mp->canEditConversationMessage()) {
            $ve->add(t('You do not have access to edit this message.'));
        }
    }
}

$canReview = $blockObj->getController()->enableTopCommentReviews && !$message->getConversationMessageParentID();
$messageAttachmentCount = count($message->getAttachments($_POST['cnvMessageID']));
$attachmentsToAddCount = count($_POST['attachments']);
$totalCurrentAttachments = intval($attachmentsToAddCount) + intval($messageAttachmentCount);
if ($_POST['attachments'] && $attachmentsToAddCount) {
    if (is_object($pp) && !$pp->canAddConversationMessageAttachments()) {
        $ve->add(t('You do not have permission to add attachments.'));
    } else {  // this will require more maths to calc vs existing attachments
        $maxFiles = $u->isRegistered() ? $blockObj->getController()->maxFilesRegistered : $blockObj->getController()->maxFilesGuest;
        if ($maxFiles > 0 && $totalCurrentAttachments > $maxFiles) {
            $ve->add(t('You have too many attachments.'));
        }
    }
}

if ($review = intval($_POST['review'])) {
    if (!$canReview) {
        $ve->add(t('Reviews have not been enabled for this discussion.'));
    } elseif ($review > 5 || $review < 1) {
        $ve->add(t('A review must be a rating between 1 and 5.'));
    }
}

if (!$ve->has()) {
    $message->setMessageBody($_POST['cnvMessageBody']);

    if ($review) {
        $message->setReview($review);
    }
    if ($_POST['attachments'] && count($_POST['attachments'])) {
        foreach ($_POST['attachments'] as $attachmentID) {
            $message->attachFile(File::getByID($attachmentID));
        }
    }

    $event = new \Concrete\Core\Conversation\Message\MessageEvent($message);

    $app = \Concrete\Core\Support\Facade\Application::getFacadeApplication();
    $app->make('director')->dispatch('on_conversations_message_update', $event);

    $conversationService = $app->make(\Concrete\Core\Conversation\ConversationService::class);
    $conversationService->trackReview($message, $blockObj);

    $ax->sendResult($message);
} else {
    $ax->sendError($ve);
}
