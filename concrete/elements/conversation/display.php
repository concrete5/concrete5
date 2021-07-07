<?php defined('C5_EXECUTE') or die('Access Denied.');

$app = \Concrete\Core\Support\Facade\Application::getFacadeApplication();

if (!is_array($messages)) {
    $messages = array();
}

$u = $app->make(Concrete\Core\User\User::class);
$ui = UserInfo::getByID($u->getUserID());

$page = Page::getByID($cID);
$ms = \Concrete\Core\Multilingual\Page\Section\Section::getBySectionOfSite($page);
if (is_object($ms)) {
   Localization::changeLocale($ms->getLocale());
}

$editor = \Concrete\Core\Conversation\Editor\Editor::getActive();
$editor->setConversationObject($args['conversation']);

$val = $app->make('token');
$form = $app->make('helper/form');
?>

<?php View::element('conversation/message/add_form', array(
    'blockAreaHandle' => $blockAreaHandle,
    'cID' => $cID,
    'bID' => $bID,
    'editor' => $editor,
    'addMessageLabel' => $addMessageLabel,
    'attachmentsEnabled' => $attachmentsEnabled,
    'displayForm' => $displayForm,
    'displayPostingForm' => $displayPostingForm,
    'position' => 'top',
    'enablePosting' => $enablePosting,
    'conversation' => $conversation,
    'enableTopCommentReviews' => $enableTopCommentReviews
)); ?>

<div class="ccm-conversation-message-list ccm-conversation-messages-<?=$displayMode?>">
	<div class="ccm-conversation-delete-message" data-dialog-title="<?=t('Delete Message')?>" data-cancel-button-title="<?=t('Cancel')?>" data-confirm-button-title="<?=t('Delete Message')?>">
		<?=t('Remove this message? Replies to it will not be removed.')?>
	</div>
	<div class="ccm-conversation-delete-attachment" data-dialog-title="<?=t('Delete Attachment')?>" data-cancel-button-title="<?=t('Cancel')?>" data-confirm-button-title="<?=t('Delete Attachment')?>">
		<?=t('Remove this attachment?')?>
	</div>
	<div class="ccm-conversation-message-permalink" data-dialog-title="<?=t('Link')?>" data-cancel-button-title="<?=t('Close')?>"></div>
	<div class="ccm-conversation-messages-header">
		<?php if ($enableOrdering) { ?>
    		<select class="form-select float-end ccm-sort-conversations" data-sort="conversation-message-list">
    			<option value="date_asc" <?php if ($orderBy == 'date_asc') { ?>selected="selected"<?php } ?>><?=t('Earliest First')?></option>
    			<option value="date_desc" <?php if ($orderBy == 'date_desc') { ?>selected="selected"<?php } ?>><?=t('Most Recent First')?></option>
    			<option value="rating" <?php if ($orderBy == 'rating') { ?>selected="selected"<?php } ?>><?=t('Highest Rated')?></option>
    		</select>
		<?php } ?>

		<?php View::element('conversation/count_header', array('conversation' => $conversation))?>
	</div>

	<div class="ccm-conversation-no-messages well well-small" <?php if (count($messages) > 0) { ?>style="display: none" <?php } ?>><?=t('No messages in this conversation.')?></div>

	<div class="ccm-conversation-messages">
	<?php foreach ($messages as $m) {
        View::element('conversation/message', array(
            'cID' => $cID,
            'message' => $m,
            'bID' => $bID,
            'page' => $page,
            'blockAreaHandle' => $blockAreaHandle,
            'enablePosting' => $enablePosting,
            'displayMode' => $displayMode,
            'enableCommentRating' => $enableCommentRating,
            'displaySocialLinks' => $displaySocialLinks,
            'dateFormat' => $dateFormat,
            'customDateFormat' => $customDateFormat
        ));
    } ?>
	</div>

	<?php if ($totalPages > $currentPage) { ?>
    	<div class="ccm-conversation-load-more-messages">
    		<button class="btn btn-large" type="button" data-load-page="conversation-message-list" data-total-pages="<?=$totalPages?>" data-next-page="<?=$currentPage + 1?>" ><?=t('Load More')?></button>
    	</div>
	<?php } ?>
</div>

<?php View::element('conversation/message/add_form', array(
    'blockAreaHandle' => $blockAreaHandle,
    'cID' => $cID,
    'bID' => $bID,
    'editor' => $editor,
    'addMessageLabel' => $addMessageLabel,
    'attachmentsEnabled' => $attachmentsEnabled,
    'displayForm' => $displayForm,
    'displayPostingForm' => $displayPostingForm,
    'position' => 'bottom',
    'enablePosting' => $enablePosting,
    'conversation' => $conversation,
    'enableTopCommentReviews' => $enableTopCommentReviews
));
