<?php

use Concrete\Core\View\View;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Controller\Frontend\Conversations\EditMessage $controller
 * @var Concrete\Core\View\View $view
 * @var Concrete\Core\Form\Service\Form $form
 * @var Concrete\Core\Validation\CSRF\Token $token
 * @var Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface $resolverManager
 * @var Concrete\Core\Conversation\Message\Message $message
 * @var int $cID
 * @var string $blockAreaHandle
 * @var int $bID
 * @var Concrete\Core\Conversation\Editor\Editor $editor
 * @var bool $attachmentsEnabled
 * @var Concrete\Core\User\UserInfo|null $userInfo
 */

?>
<div class="ccm-conversation-edit-message" data-conversation-message-id="<?= $message->getConversationMessageID() ?>">
    <form method="post" class="aux-reply-form">
        <div class="ccm-conversation-avatar"><?= $userInfo === null ? '' : $userInfo->getUserAvatar()->output() ?></div>
        <div class="ccm-conversation-message-form">
            <div class="ccm-conversation-errors alert alert-danger"></div>
            <?php
            $editor->outputConversationEditorReplyMessageForm();
            if ($message->getConversationMessageReview()) {
                View::element(
                    'conversation/message/review',
                    [
                        'review' => $message->getConversationMessageReview(),
                    ]
                );
            }
            ?>
            <button type="button" data-post-message-id="<?= $message->getConversationMessageID() ?>" data-submit="update-conversation-message" class="float-end btn btn-primary btn-sm"><?= t('Save') ?></button>
            <?php
            if ($attachmentsEnabled) {
                ?>
                <button type="button" class="float-end btn btn-info btn-sm ccm-conversation-attachment-toggle" title="<?= t('Attach Files') ?>"><i class="far fa-image"></i></button>
                <?php
            }
            ?>
            <button type="button" data-post-message-id="<?= $message->getConversationMessageID() ?>" data-submit="cancel-update" class="cancel-update float-end btn btn-secondary btn-sm"><?= t('Cancel') ?></button>
            <?= $form->hidden('cID', $cID) ?>
            <?= $form->hidden('blockAreaHandle', $blockAreaHandle) ?>
            <?= $form->hidden('bID', $bID) ?>
        </div>
    </form>
    <?php
    if ($attachmentsEnabled) {
        ?>
        <div class="ccm-conversation-attachment-container">
            <form action="<?= h($resolverManager->resolve(['/ccm/frontend/conversations/add_file'])) ?> ?>" class="dropzone" id="file-upload-reply">
                <div class="ccm-conversation-errors alert alert-danger"></div>
                <?php $token->output('add_conversations_file') ?>
                <?= $form->hidden('cID', $cID) ?>
                <?= $form->hidden('blockAreaHandle', $blockAreaHandle) ?>
                <?= $form->hidden('bID', $bID) ?>
            </form>
        </div>
        <?php
    }
    ?>
</div>
