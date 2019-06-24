<?php

defined('C5_EXECUTE') or die('Access Denied.');
use \Concrete\Core\Conversation\Message\Message as ConversationMessage;

$ax = Loader::helper('ajax');
$vs = Loader::helper('validation/strings');
$ve = Loader::helper('validation/error');

if (Loader::helper('validation/numbers')->integer($_POST['cnvMessageAttachmentID']) && $_POST['cnvMessageAttachmentID'] > 0) {
    $message = ConversationMessage::getByAttachmentID($_POST['cnvMessageAttachmentID']);
    if (is_object($message)) {
        $mp = new Permissions($message);
        if ($mp->canEditConversationMessage()) {
            $message->removeFile($_POST['cnvMessageAttachmentID']);
            $attachmentDeleted = new stdClass();
            $attachmentDeleted->attachmentID = $_POST['cnvMessageAttachmentID'];
            echo Loader::helper('json')->encode($attachmentDeleted);
        }
    }
}
