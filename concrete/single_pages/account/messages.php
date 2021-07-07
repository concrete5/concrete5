<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Application\Service\UserInterface;
use Concrete\Core\Localization\Service\Date;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\User\PrivateMessage\Mailbox;
use Concrete\Core\User\PrivateMessage\PrivateMessage;
use Concrete\Core\User\PrivateMessage\PrivateMessageList;
use Concrete\Core\User\User;
use Concrete\Core\User\UserInfo;
use Concrete\Core\Validation\CSRF\Token;
use Concrete\Core\View\View;

/** @var int $msgID */
/** @var UserInfo $recipient */
/** @var View $view */
/** @var string $mailbox * */
/** @var string $box * */
/** @var Mailbox $inbox * */
/** @var Mailbox $sent * */
/** @var PrivateMessage $msg * */
/** @var PrivateMessageList $messageList * */
/** @var PrivateMessage[] $messages * */
/** @var string $backURL */
/** @var string $deleteURL */
/** @var string $dateAdded */

$app = Application::getFacadeApplication();
/** @var Date $dh */
$dh = $app->make(Date::class);
/** @var UserInterface $userInterface */
$userInterface = $app->make(UserInterface::class);
/** @var Token $token */
$token = $app->make(Token::class);

?>

<?php if ($this->controller->getTask() === 'view_message') { ?>
    <?php echo $userInterface->tabs([
        [$view->action('view_mailbox', 'inbox'), t('Inbox'), 'inbox' == $box],
        [$view->action('view_mailbox', 'sent'), t('Sent'), 'sent' == $box],
    ], false); ?>

    <div class="mt-4" id="ccm-private-message-detail">
        <?php $profileURL = $msg->getMessageRelevantUserObject()->getUserPublicProfileURL(); ?>

        <?php if ($profileURL) { ?>
            <a href="javascript:void(0);">
                <?php echo $msg->getMessageRelevantUserObject()->getUserAvatar()->output(); ?>
            </a>

            <a href="<?php echo h($profileURL); ?>">
                <?php echo $msg->getMessageRelevantUserName(); ?>
            </a>
        <?php } else { ?>
            <?php echo $msg->getMessageRelevantUserObject()->getUserAvatar()->output(); ?>
            <?php echo $msg->getMessageRelevantUserName(); ?>
        <?php } ?>

        <div id="ccm-private-message-actions" style="margin: 15px 0">
            <div class="btn-toolbar">
                <div class="btn-group">
                    <a href="<?php echo h($backURL); ?>" class="btn btn-secondary">
                        <i class="icon-arrow-left"></i> <?php echo t('Back to Messages'); ?>
                    </a>

                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <?php echo t('Action'); ?>
                    </button>

                    <ul class="dropdown-menu">
                        <?php $u = $app->make(User::class); ?>

                        <?php if ($msg->getMessageAuthorID() != $u->getUserID()) { ?>
                            <?php $mui = $msg->getMessageRelevantUserObject(); ?>

                            <?php if (is_object($mui)) { ?>
                                <?php if ($mui->getUserProfilePrivateMessagesEnabled()) { ?>
                                    <a href="<?php echo $view->action('reply', $box, $msg->getMessageID()); ?>"
                                       class="dropdown-item">
                                        <?php echo t('Reply'); ?>
                                    </a>

                                    <li class="dropdown-divider"></li>
                                <?php } ?>
                            <?php } ?>
                        <?php } ?>

                        <a href="javascript:void(0)" class="dropdown-item"
                           onclick="if(confirm('<?php echo t('Delete this message?'); ?>')) { window.location.href='<?php echo $deleteURL; ?>'} return false">
                            <?php echo t('Delete'); ?>
                        </a>
                    </ul>
                </div>
            </div>

        </div>

        <div id="ccm-private-message-subject-time">
            <strong>
                <?php echo $subject; ?>
            </strong>

            <time>
                <?php echo $dateAdded; ?>
            </time>
        </div>

        <br/>

        <div>
            <?php echo $msg->getFormattedMessageBody(); ?>
        </div>
    </div>

<?php } else if ($this->controller->getTask() === 'view_mailbox') { ?>
    <?php echo $userInterface->tabs([
        [$view->action('view_mailbox', 'inbox'), t('Inbox'), 'inbox' == $mailbox],
        [$view->action('view_mailbox', 'sent'), t('Sent'), 'sent' == $mailbox],
    ], false); ?>

    <table class="mt-4 ccm-profile-messages-list table-striped table">
        <thead>
        <tr>
            <th>

            </th>

            <th>
                <?php if ('sent' == $mailbox) { ?>
                    <?php echo t('To'); ?>
                <?php } else { ?>
                    <?php echo t('From'); ?>
                <?php } ?>
            </th>

            <th>
                <?php echo t('Subject'); ?>
            </th>

            <th>
                <?php echo t('Sent At'); ?>
            </th>

            <th>
                <?php echo t('Status'); ?>
            </th>
        </tr>
        </thead>

        <tbody>
        <?php if (is_array($messages)) { ?>
            <?php foreach ($messages as $msg) { ?>
                <?php $profileURL = $msg->getMessageRelevantUserObject()->getUserPublicProfileURL(); ?>
                <tr>
                    <td class="ccm-profile-message-from">
                        <?php if ($profileURL) { ?>
                            <a href="<?php echo $profileURL; ?>">
                                <?php echo $msg->getMessageRelevantUserObject()->getUserAvatar()->output(); ?>
                            </a>
                        <?php } else { ?>
                            <div>
                                <?php echo $msg->getMessageRelevantUserObject()->getUserAvatar()->output(); ?>
                            </div>
                        <?php } ?>
                    </td>

                    <td class="ccm-profile-message-from">
                        <?php if ($profileURL) { ?>
                            <a href="<?php echo $profileURL; ?>">
                                <?php echo $msg->getMessageRelevantUserName(); ?>
                            </a>
                        <?php } else { ?>
                            <div>
                                <?php echo $msg->getMessageRelevantUserName(); ?>
                            </div>
                        <?php } ?>
                    </td>

                    <td class="ccm-profile-messages-item-name">
                        <a href="<?php /** @noinspection PhpDeprecationInspection */
                        echo $view->url('/account/messages', 'view_message', $mailbox, $msg->getMessageID()); ?>">
                            <?php echo $msg->getFormattedMessageSubject(); ?>
                        </a>
                    </td>

                    <td style="white-space: nowrap">
                        <?php /** @noinspection PhpUnhandledExceptionInspection */
                        echo $dh->formatDateTime($msg->getMessageDateAdded(), true); ?>
                    </td>

                    <td style="white-space: nowrap">
                        <?php echo $msg->getMessageStatus(); ?>
                    </td>
                </tr>
            <?php } ?>
        <?php } else { ?>
            <tr>
                <td colspan="4">
                    <?php echo t('No messages found.'); ?>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <a href="<?php echo (string)Url::to('/account'); ?>" class="btn btn-secondary float-start">
                <?php echo t('Back to Account'); ?>
            </a>
        </div>
    </div>

    <?php $messageList->displayPaging(); ?>

<?php } else if ($this->controller->getTask() === 'reply_complete') { ?>

    <div class="alert alert-success">
        <?php echo t('Reply Sent.'); ?>
    </div>

    <a href="<?php /** @noinspection PhpDeprecationInspection */
    echo $view->url('/account/messages', 'view_message', $box, $msgID); ?>" class="btn btn-secondary">
        <?php echo t('Back to Message'); ?>
    </a>

<?php } else if ($this->controller->getTask() === 'send_complete') { ?>
    <?php $profileURL = $recipient->getUserPublicProfileURL(); ?>

    <div class="alert alert-success">
        <?php echo t('Message Sent.'); ?>
    </div>

    <?php if ($profileURL) { ?>
        <a href="<?php echo h($profileURL); ?>" class="btn btn-secondary">
            <?php echo t('Back to Profile'); ?>
        </a>
    <?php } ?>

<?php } else if ($this->controller->getTask() === 'over_limit') { ?>
    <h2>
        <?php echo t('Woops!'); ?>
    </h2>

    <p>
        <?php echo t("You've sent more messages than we can handle just now, that last one didn't go out.
			We've notified an administrator to check into this.
			Please wait a few minutes before sending a new message."); ?>
    </p>

<?php } else if (in_array($this->controller->getTask(), ['send', 'reply', 'write'])) { ?>
    <?php
    if (!isset($msgSubject)) {
        $msgSubject = '';
    }

    if (!isset($msgBody)) {
        $msgBody = '';
    }
    ?>

    <div id="ccm-profile-message-compose">
        <form method="post" action="<?php echo $view->action('send'); ?>">
            <?php echo $form->hidden("uID", $recipient->getUserID()); ?>

            <?php if ('reply' == $this->controller->getTask()) { ?>
                <?php echo $form->hidden("msgID", $msgID); ?>
                <?php echo $form->hidden("box", $box); ?>
                <?php $subject = t('Re: %s', $text->entities($msgSubject)); ?>
            <?php } else { ?>
                <?php $subject = $text->entities($msgSubject); ?>
            <?php } ?>

            <h4>
                <?php echo t('Send a Private Message'); ?>
            </h4>

            <div class="form-group">
                <?php echo $form->label("", t("To")); ?>
                <?php echo $form->text("", $recipient->getUserName(), ["disabled" => "disabled", "class" => "span5"]); ?>
            </div>

            <div class="form-group">
                <?php echo $form->label('subject', t('Subject')); ?>
                <?php echo $form->text('msgSubject', $subject, ['class' => 'span5']); ?>
            </div>

            <div class="form-group">
                <?php echo $form->label('body', t('Message')); ?>
                <?php echo $form->textarea('msgBody', $msgBody, ['rows' => 8, 'class' => 'span5']); ?>
            </div>

            <div class="ccm-dashboard-form-actions-wrapper">
                <div class="ccm-dashboard-form-actions">
                    <?php echo $form->submit('button_submit', t('Send Message'), ['class' => 'float-end btn btn-primary']); ?>
                    <?php echo $form->submit('button_cancel', t('Cancel'), ['class' => 'btn-secondary', 'onclick' => 'window.location.href=\'' . $backURL . '\'; return false']); ?>
                </div>
            </div>

            <?php echo $token->output('validate_send_message'); ?>
        </form>
    </div>

<?php } else { ?>
    <table class="table table-striped">
        <tr>
            <th class="ccm-profile-messages-item-mailbox">
                <?php echo t('Mailbox'); ?>
            </th>

            <th>
                <?php echo t('Messages'); ?>
            </th>

            <th class="ccm-profile-mailbox-last-message">
                <?php echo t('Latest Message'); ?>
            </th>
        </tr>

        <tr>
            <td class="ccm-profile-messages-item-mailbox">
                <a href="<?php echo $view->action('view_mailbox', 'inbox'); ?>">
                    <?php echo t('Inbox'); ?>
                </a>
            </td>

            <td>
                <?php echo $inbox->getTotalMessages(); ?>
            </td>

            <td class="ccm-profile-mailbox-last-message">
                <?php $msg = $inbox->getLastMessageObject(); ?>

                <?php if (is_object($msg)) { ?>
                    <?php /** @noinspection PhpUnhandledExceptionInspection */
                    echo t('<strong>%s</strong>, sent by %s on %s', $msg->getFormattedMessageSubject(), $msg->getMessageAuthorName(), $dh->formatDateTime($msg->getMessageDateAdded(), true)); ?>
                <?php } ?>
            </td>
        </tr>

        <tr>
            <td class="ccm-profile-messages-item-mailbox">
                <a href="<?php echo $view->action('view_mailbox', 'sent'); ?>">
                    <?php echo t('Sent Messages'); ?>
                </a>
            </td>

            <td>
                <?php echo $sent->getTotalMessages(); ?>
            </td>

            <td class="ccm-profile-mailbox-last-message">
                <?php $msg = $sent->getLastMessageObject(); ?>

                <?php if (is_object($msg)) { ?>
                    <?php /** @noinspection PhpUnhandledExceptionInspection */
                    echo t('<strong>%s</strong>, sent by %s on %s', $msg->getFormattedMessageSubject(), $msg->getMessageAuthorName(), $dh->formatDateTime($msg->getMessageDateAdded(), true)); ?>
                <?php } ?>
            </td>
        </tr>
    </table>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <a href="<?php echo (string)Url::to('/account'); ?>" class="btn btn-secondary">
                <?php echo t('Back to Account'); ?>
            </a>
        </div>
    </div>
<?php } ?>
