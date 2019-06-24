<?php

defined('C5_EXECUTE') or die('Access Denied.');
use \Concrete\Core\Conversation\Message\Message as ConversationMessage;

$ax = Loader::helper('ajax');
$vs = Loader::helper('validation/strings');
$ve = Loader::helper('validation/error');

if (!Loader::helper('validation/token')->validate('delete_conversation_message', $_POST['token'])) {
    $ve->add(t('Invalid conversation post token.'));
}

if (Loader::helper('validation/numbers')->integer($_POST['cnvMessageID']) && $_POST['cnvMessageID'] > 0) {
    $message = ConversationMessage::getByID($_POST['cnvMessageID']);

    if (!is_object($message)) {
        $ve->add(t('Invalid message object.'));
    } else {
        $mp = new Permissions($message);
        if (!$mp->canDeleteConversationMessage()) {
            $ve->add(t('You do not have access to delete this message.'));
        }
    }
}

if (!$ve->has()) {
    $message->delete();

    $r = Request::getInstance();
    $r = new \Concrete\Core\Application\EditResponse();
    $r->setMessage(t('Message deleted successfully.'));
    $r->outputJSON();

} else {
    $ax->sendError($ve);
}