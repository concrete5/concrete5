<?php defined('C5_EXECUTE') or die('Access Denied.');

$dh = Core::make('helper/date'); /* @var $dh \Concrete\Core\Localization\Service\Date */
?>

<h2><?=$c->getCollectionName(); ?></h2>

	<?php switch ($this->controller->getTask()) {
        case 'view_message': ?>

		<?=Loader::helper('concrete/ui')->tabs([
            [$view->action('view_mailbox', 'inbox'), t('Inbox'), 'inbox' == $box],
            [$view->action('view_mailbox', 'sent'), t('Sent'), 'sent' == $box],
        ], false); ?>

		<div id="ccm-private-message-detail">
		<?php
        $profileURL = $msg->getMessageRelevantUserObject()->getUserPublicProfileURL();
        if ($profileURL) {
            ?>
			<a href=""><?=$msg->getMessageRelevantUserObject()->getUserAvatar()->output(); ?></a>
			<a href="<?=$profileURL; ?>"><?=$msg->getMessageRelevantUserName(); ?></a>
		<?php
        } else {
            ?>
			<?=$msg->getMessageRelevantUserObject()->getUserAvatar()->output(); ?>
			<?=$msg->getMessageRelevantUserName(); ?>
		<?php
        } ?>

			<div id="ccm-private-message-actions">

			<div class="btn-toolbar">

			<div class="btn-group">
			<a href="<?=$backURL; ?>" class="btn btn-small"><i class="icon-arrow-left"></i> <?=t('Back to Messages'); ?></a>
			</div>

			<div class="btn-group">
			<a class="btn btn-small dropdown-toggle" data-toggle="dropdown" href="#">
			<i class="icon-cog"></i> <?=t('Action'); ?>
			&nbsp;
			<span class="caret"></span>
			</a>
			<ul class="dropdown-menu">
			<?php $u = new User(); ?>
			<?php if ($msg->getMessageAuthorID() != $u->getUserID()) {
            ?>
				<?php
                $mui = $msg->getMessageRelevantUserObject();
            if (is_object($mui)) {
                if ($mui->getUserProfilePrivateMessagesEnabled()) {
                    ?>
						<li><a href="<?=$view->action('reply', $box, $msg->getMessageID()); ?>"><?=t('Reply'); ?></a>
						<li class="divider"></li>
					<?php
                }
            } ?>
			<?php
        } ?>
			<li><a href="javascript:void(0)" onclick="if(confirm('<?=t('Delete this message?'); ?>')) { window.location.href='<?=$deleteURL; ?>'}; return false"><?=t('Delete'); ?></a>
			</ul>
			</div>
			</div>

			</div>

			<div id="ccm-private-message-subject-time">
				<strong><?=$subject; ?></strong>
				<time><?=$dateAdded; ?></time>
			</div>
			<br/>

			<div>
			<?=$msg->getFormattedMessageBody(); ?>
			</div>
		</div>


		<?php
            break;
        case 'view_mailbox': ?>

			<?=Loader::helper('concrete/ui')->tabs([
            [$view->action('view_mailbox', 'inbox'), t('Inbox'), 'inbox' == $mailbox],
            [$view->action('view_mailbox', 'sent'), t('Sent'), 'sent' == $mailbox],
        ], false); ?>


		<table class="ccm-profile-messages-list table-striped table" border="0" cellspacing="0" cellpadding="0">
		<thead>
		<tr>
			<th><?php if ('sent' == $mailbox) {
            ?><?=t('To'); ?><?php
        } else {
            ?><?=t('From'); ?><?php
        } ?></th>
			<th><?=t('Subject'); ?></th>
			<th><?=t('Sent At'); ?></th>
			<th><?=t('Status'); ?></th>
		</tr>
		</thead>
		<tbody>


		<?php
            if (is_array($messages)) {
                foreach ($messages as $msg) {
                    $profileURL = $msg->getMessageRelevantUserObject()->getUserPublicProfileURL(); ?>
				<tr>
					<td class="ccm-profile-message-from">

						<?php if ($profileURL) {
                        ?>

							<a href="<?=$profileURL; ?>"><?=$msg->getMessageRelevantUserObject()->getUserAvatar()->output(); ?></a>
							<a href="<?=$profileURL; ?>"><?=$msg->getMessageRelevantUserName(); ?></a>

						<?php
                    } else {
                        ?>

							<div><?=$msg->getMessageRelevantUserObject()->getUserAvatar()->output(); ?></div>
							<div><?=$msg->getMessageRelevantUserName(); ?></div>

						<?php
                    } ?>
					</td>
					<td class="ccm-profile-messages-item-name"><a href="<?=$view->url('/account/messages', 'view_message', $mailbox, $msg->getMessageID()); ?>"><?=$msg->getFormattedMessageSubject(); ?></a></td>
					<td style="white-space: nowrap"><?=$dh->formatDateTime($msg->getMessageDateAdded(), true); ?></td>
					<td style="white-space: nowrap"><?=$msg->getMessageStatus(); ?></td>
				</tr>



			<?php
                } ?>
		<?php
            } else {
                ?>
			<tr>
				<Td colspan="4"><?=t('No messages found.'); ?></td>
			</tr>
		<?php
            } ?>
		</tbody>
		</table>

		<div class="ccm-dashboard-form-actions-wrapper">
		    <div class="ccm-dashboard-form-actions">
				<a href="<?=URL::to('/account'); ?>" class="btn btn-default pull-left" /><?=t('Back to Account'); ?></a>
			</div>
		</div>


		<?php

            $messageList->displayPaging();
            break;
        case 'reply_complete': ?>

		<div class="alert alert-success"><?=t('Reply Sent.'); ?></div>
		<a href="<?=$view->url('/account/messages', 'view_message', $box, $msgID); ?>" class="btn btn-default"><?=t('Back to Message'); ?></a>

		<?php
            break;
        case 'send_complete':
            $profileURL = $recipient->getUserPublicProfileURL();
            ?>

		<div class="alert alert-success"><?=t('Message Sent.'); ?></div>

			<?php if ($profileURL) {
                ?>
				<a href="<?=$profileURL; ?>" class="btn btn-default"><?=t('Back to Profile'); ?></a>
			<?php
            } ?>
		<?php
            break;
        case 'over_limit': ?>
			<h2><?php echo t('Woops!'); ?></h2>
			<p><?php echo t("You've sent more messages than we can handle just now, that last one didn't go out.
			We've notified an administrator to check into this.
			Please wait a few minutes before sending a new message."); ?></p>
			<?php break;
        case 'send':
        case 'reply':
        case 'write':
            if (!isset($msgSubject)) {
                $msgSubject = '';
            }
            if (!isset($msgBody)) {
                $msgBody = '';
            }
            ?>
		<div id="ccm-profile-message-compose">
			<form method="post" action="<?=$view->action('send'); ?>">

			<?=$form->hidden("uID", $recipient->getUserID()); ?>
			<?php if ('reply' == $this->controller->getTask()) {
            ?>
				<?=$form->hidden("msgID", $msgID); ?>
				<?=$form->hidden("box", $box); ?>
			<?php
                $subject = t('Re: %s', $text->entities($msgSubject));
        } else {
            $subject = $text->entities($msgSubject);
        }
            ?>

			<h4><?=t('Send a Private Message'); ?></h4>

			<div class="form-group">
				<label class="control-label"><?=t("To"); ?></label>
				<input disabled="disabled" class="form-control" type="text" value="<?=$recipient->getUserName(); ?>" class="span5" />
			</div>

			<div class="form-group">
				<?=$form->label('subject', t('Subject')); ?>
				<?=$form->text('msgSubject', $subject, ['class' => 'span5']); ?>
			</div>

			<div class="form-group">
				<?=$form->label('body', t('Message')); ?>
				<?=$form->textarea('msgBody', $msgBody, ['rows' => 8, 'class' => 'span5']); ?>
			</div>

			<div class="ccm-dashboard-form-actions-wrapper">
			    <div class="ccm-dashboard-form-actions">
					<?=$form->submit('button_submit', t('Send Message'), ['class' => 'pull-right btn btn-primary']); ?>
					<?=$form->submit('button_cancel', t('Cancel'), ['class' => 'btn-default', 'onclick' => 'window.location.href=\'' . $backURL . '\'; return false']); ?>
				</div>
			</div>

			<?=$valt->output('validate_send_message'); ?>

			</form>

		</div>


		<?php break;

        default:
            // the inbox and sent box and other controls?>

			<table class="table table-striped" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<th class="ccm-profile-messages-item-mailbox"><?=t('Mailbox'); ?></th>
				<th><?=t('Messages'); ?></th>
				<th class="ccm-profile-mailbox-last-message"><?=t('Latest Message'); ?></th>
			</tr>
			<tr>
				<td class="ccm-profile-messages-item-mailbox"><a href="<?=$view->action('view_mailbox', 'inbox'); ?>"><?=t('Inbox'); ?></a></td>
				<td><?=$inbox->getTotalMessages(); ?></td>
				<td class="ccm-profile-mailbox-last-message"><?php
                $msg = $inbox->getLastMessageObject();
                if (is_object($msg)) {
                    echo t('<strong>%s</strong>, sent by %s on %s', $msg->getFormattedMessageSubject(), $msg->getMessageAuthorName(), $dh->formatDateTime($msg->getMessageDateAdded(), true));
                }
                ?></td>
			</tr>
			<tr>
				<td class="ccm-profile-messages-item-mailbox"><a href="<?=$view->action('view_mailbox', 'sent'); ?>"><?=t('Sent Messages'); ?></a></td>
				<td><?=$sent->getTotalMessages(); ?></td>
				<td class="ccm-profile-mailbox-last-message"><?php
                 $msg = $sent->getLastMessageObject();
                if (is_object($msg)) {
                    echo t('<strong>%s</strong>, sent by %s on %s', $msg->getFormattedMessageSubject(), $msg->getMessageAuthorName(), $dh->formatDateTime($msg->getMessageDateAdded(), true));
                }
                ?>
			</td>
			</tr>
			</table>

			<div class="ccm-dashboard-form-actions-wrapper">
			    <div class="ccm-dashboard-form-actions">
					<a href="<?=URL::to('/account');?>" class="btn btn-default" /><?=t('Back to Account');?></a>
				</div>
			</div>

		<?php
            break;
    } ?>
