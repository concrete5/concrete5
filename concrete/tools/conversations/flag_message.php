<?php

defined('C5_EXECUTE') or die('Access Denied.');
use \Concrete\Core\Conversation\Message\Message as ConversationMessage;
use \Concrete\Core\Conversation\FlagType\FlagType as ConversationFlagType;

$ax = Loader::helper('ajax');
$vs = Loader::helper('validation/strings');
$ve = Loader::helper('validation/error');
$as = Loader::helper('validation/antispam');

if (!Loader::helper('validation/token')->validate('flag_conversation_message', $_POST['token'])) {
    $ve->add(t('Invalid conversation post token.'));
}

if (Loader::helper('validation/numbers')->integer($_POST['cnvMessageID']) && $_POST['cnvMessageID'] > 0) {
    $message = ConversationMessage::getByID($_POST['cnvMessageID']);
    if (!is_object($message)) {
        $ve->add(t('Invalid message object.'));
    } else {
        $mp = new Permissions($message);
        if (!$mp->canFlagConversationMessage()) {
            $ve->add(t('You do not have access to flag this message.'));
        }
    }
}

if (!$ve->has()) {
    $flagtype = ConversationFlagType::getByHandle('spam');
    $message->flag($flagtype);
    $message->unapprove();
    $author = $message->getConversationMessageAuthorObject();
    $email = $author->getEmail();
    $as->report($message->getConversationMessageBody(),
        $author->getName(),
        $author->getEmail(),
        $message->getConversationMessageSubmitIP(),
        $message->getConversationMessageSubmitUserAgent()
    );

    $r = Request::getInstance();
    $types = $r->getAcceptableContentTypes();
    if ($types[0] == 'application/json') {
        $r = new \Concrete\Core\Application\EditResponse();
        $r->setMessage(t('Message flagged successfully.'));
        $r->outputJSON();
    } else {
        Loader::element('conversation/message', array('message' => $message));
    }

} else {
    $ax->sendError($ve);
}
