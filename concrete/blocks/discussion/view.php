<?php defined('C5_EXECUTE') or die('Access Denied.');
$c = Page::getCurrentPage();
$canAccessComposer = false;
if (is_object($composer)) {
    $ccp = new Permissions($composer);
    if ($ccp->canAccessComposer()) {
        $canAccessComposer = true;
    }
}

$dh = Core::make('helper/date'); /* @var $dh \Concrete\Core\Localization\Service\Date */

if (is_object($discussion)) {
    ?>

	<div class="ccm-discussion" data-discussion-block-id="<?=$b->getBlockID()?>">

	<?php
    if ($enableNewTopics && $canAccessComposer) {
        ?>

		<div style="display: none">
			<div data-form="discussion">
				<form data-form="composer">
				<?=Loader::helper('concrete/composer')->display($composer)?>
				<div class="dialog-buttons">
				<button type="button" data-composer-btn="exit" class="btn btn-default pull-left"><?=t('Cancel')?></button>
				<button type="button" data-composer-btn="publish" class="btn btn-primary pull-right"><?=t('Post')?></button>
				</div>
				</form>
			</div>
		</div>

		<button class="pull-right btn" data-action="add-conversation" type="button"><?=t('New Topic')?></button>

	<?php 
    }
    ?>

		<?php if ($enableOrdering) {
    ?>
			<select name="orderBy" class="ccm-discussion-order-by" data-select="order">
				<option data-sort-url="<?=Loader::helper('url')->setVariable('orderBy', 'date_last_message')?>" value="date_last_message" <?php if ($reqOrderBy == 'date_last_message') {
    ?>selected<?php 
}
    ?>><?=t('Recent Comment')?></option>
				<option data-sort-url="<?=Loader::helper('url')->setVariable('orderBy', 'date')?>" value="date" <?php if ($reqOrderBy == 'date') {
    ?>selected<?php 
}
    ?>><?=t('Original Post')?></option>
				<option data-sort-url="<?=Loader::helper('url')->setVariable('orderBy', 'replies')?>" value="replies" <?php if ($reqOrderBy == 'replies') {
    ?>selected<?php 
}
    ?>><?=t('Activity')?></option>
			</select>
		<?php 
}
    ?>

		<h3><?=$c->getCollectionName()?></h3>
	
		<?php if (count($topics)) {
    ?>

			<?=$list->displaySummary()?>

			<ul class="ccm-discussion-topics">

			<?php foreach ($topics as $t) {
    $v = $t->getVersionObject();
    $fa = CollectionVersionFeatureAssignment::getFeature('conversation', $v);
    $replies = 0;
    if (is_object($fa)) {
        $fd = $fa->getFeatureDetailObject();
        $cnv = $fd->getConversationObject();
        if (is_object($cnv)) {
            $replies = $cnv->getConversationMessagesTotal();
        }
    }

    ?>
			<li>
				<div class="ccm-discussion-topic-replies">
					<?=t2('<em>%s</em> Reply', '<em>%s</em> Replies', $replies)?>
				</div>
				<div class="ccm-discussion-topic-details">
					<h3><a href="<?=Loader::helper('navigation')->getLinkToCollection($t)?>"><?=$t->getCollectionName()?></a></h3>
					<p><?=t(/*i18n: %s is a date/time*/'Topic Posted on %s.', $dh->formatDateTime($t->getCollectionDatePublic(), true))?>
					<?php if ($replies > 0) {
    ?>
						<?=t(/*i18n: %s is a date/time*/'Last Message Posted on %s.', $dh->formatDateTime($cnv->getConversationDateLastMessage(), true))?>
					<?php 
}
    ?>
					</p>
				</div>
			</li>
			<?php 
}
    ?>

			</ul>

			<?=$list->displayPagingV2()?>

		<?php 
} else {
    ?>
			<div class="well"><?=t('No topics have been posted.')?></div>
		<?php 
}
    ?>
	<?php 
} ?>

	</div>


<script type="text/javascript">
$(function() {

	var $db = $('div[data-discussion-block-id=<?=$b->getBlockID()?>]'),
		$dialog = $db.find('div[data-form=discussion]'),
		$addTopic = $db.find('button[data-action=add-conversation]'),
		$orderBy = $db.find('select[data-select=order]');

	$orderBy.on('change', function() {

		window.location.href = $(this).find('option:selected').attr('data-sort-url');
	});
	$('form[data-form=composer]').ccmcomposer({
		onExit: function() {
			$dialog.dialog('close');
		},
		autoSaveEnabled: false,
		publishReturnMethod: 'ajax',
		onPublish: function(r) {
			window.location.href = r.redirectURL;
		}
	});

	$addTopic.on('click', function() {
		$dialog.dialog({
			modal: true,
			width: 400,
			height: 540,
			title: '<?=t("Add Topic")?>',
			open: function() {
				var $buttons = $dialog.find('.dialog-buttons').hide().clone(true,true);
				$(this).dialog('option', 'buttons', [{}]);
				$(this).closest('.ui-dialog').find('.ui-dialog-buttonset').html('').append($buttons.show());
			}
		});
	});
});
</script>