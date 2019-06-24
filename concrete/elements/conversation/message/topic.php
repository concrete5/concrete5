<?php
defined('C5_EXECUTE') or die('Access Denied.');
$class = 'ccm-conversation-message ccm-conversation-message-topic';
if ($message->isConversationMessageDeleted()) {
    $class .= ' ccm-conversation-message-deleted';
}
if (!$message->isConversationMessageApproved()) {
    $class .= ' ccm-conversation-message-flagged';
}
$cnvMessageID = $message->getConversationMessageID();
if ((!$message->isConversationMessageDeleted() && $message->isConversationMessageApproved()) || $message->conversationMessageHasActiveChildren()) {
    ?>
	<div data-conversation-message-id="<?=$message->getConversationMessageID()?>" data-conversation-message-level="<?=$message->getConversationMessageLevel()?>" class="<?=$class?>">
		<a id="cnvMessage<?=$cnvMessageID?>"></a>

		<h2><?=$message->getConversationMessageSubject()?></h2>


		<div class="ccm-conversation-message-body">
			<?=$message->getConversationMessageBodyOutput()?>
		</div>
		<div class="ccm-conversation-message-controls">
			<div class="message-attachments">
				<?php
                if (count($message->getAttachments($message->getConversationMessageID()))) {
                    foreach ($message->getAttachments($message->getConversationMessageID()) as $attachment) {
                        $file = File::getByID($attachment['fID']);
                        if (is_object($file)) {
                            ?>
						<p rel="<?php echo $attachment['cnvMessageAttachmentID'];
                            ?>"><a href="<?php echo $file->getDownloadURL() ?>"><?php echo $file->getFileName() ?></a>
							<?php if (!$message->isConversationMessageDeleted()) {
    ?>
								<a rel="<?php echo $attachment['cnvMessageAttachmentID'];
    ?>" class="attachment-delete ccm-conversation-message-admin-control" href="#">Delete</a>
							<?php 
}
                            ?>
						</p>
					<?php 
                        }
                    }
                }
    ?>
			</div>
			<?php if (!$message->isConversationMessageDeleted() && $message->isConversationMessageApproved()) {
    ?>
			<ul>
				<!-- <li class="ccm-conversation-message-admin-control"><a href="#" data-submit="flag-conversation-message" data-conversation-message-id="<?=$message->getConversationMessageID()?>"><?=t('Flag As Spam')?></a></li>
				<li class="ccm-conversation-message-admin-control"><a href="#" data-submit="delete-conversation-message" data-conversation-message-id="<?=$message->getConversationMessageID()?>"><?=t('Delete')?></a></li> -->
				
				<?php if ($enablePosting && $displayMode == 'threaded') {
    ?>
					<li><a href="#" data-toggle="conversation-reply" data-post-parent-id="<?=$message->getConversationMessageID()?>"><?=t('Reply')?></a></li>
				<?php 
}
    ?>
			</ul>
			<?php 
}
    ?>
			
		<?=$message->getConversationMessageDateTimeOutput();
    Loader::element('conversation/social_share', array('cID' => $cID, 'message' => $message));
    ?>
		
		<?php if ($enableCommentRating) {
    $ratingTypes = ConversationRatingType::getList();
    foreach ($ratingTypes as $ratingType) {
        ?>
				<a title="Rate this Message"><?=$ratingType->outputRatingTypeHTML();
        ?></a>
				<span class="ccm-conversation-message-rating-score" data-msg-rating="<?=$message->getConversationMessageID()?>" data-msg-rating-type="<?=$ratingType->getConversationRatingTypeHandle()?>"><?=$message->getConversationMessageRating($ratingType);
        ?></span>
			 <?php 
    }
    ?>
		<?php 
}
    ?>
		</div>
	</div>
	<?php

}
?>
