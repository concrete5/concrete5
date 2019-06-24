<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>  
<?php
if ($controller->getTask() == 'add') {
    $enableNewTopics = 1;
    $orderBy = 'date_last_message';
    $enableOrdering = 1;
    $itemsPerPage = 20;
}

$pagetypes = PageType::getList();
$types = array();
foreach ($pagetypes as $pt) {
    $types[$pt->getPageTypeID()] = $pt->getPageTypeName();
}

?>
<div class="form-horizontal">

<fieldset>
	<legend><?=t('Message List')?></legend>
	<div class="control-group">
		<label class="control-label"><?=t('Ordering')?></label>
		<div class="controls">
			<?=$form->select('orderBy', array('date_last_message' => t('Recent Comment'), 'date' => t('Original Post'), 'replies' => t('Activity')), $orderBy)?>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label"><?=t('Display Ordering Option in Page')?></label>
		<div class="controls">
		<?=$form->checkbox('enableOrdering', 1, $enableOrdering)?>
		</div>
	</div>
	<div class="control-group" data-row="itemsPerPage">
		<label class="control-label"><?=t('Topics Per Page')?></label>
		<div class="controls">
			<?=$form->text('itemsPerPage', $itemsPerPage, array('class' => 'span1'))?>
		</div>
	</div>
</fieldset>

<fieldset>
	<legend><?=t('Posting')?></legend>
	<div class="control-group">
		<label class="control-label"><?=t('Enable New Topics')?></label>
		<div class="controls">
            <div class="radio">
			<label>
				<?=$form->radio('enableNewTopics', 1, $enableNewTopics)?>
				<span><?=t('Yes, this conversation is open and new topics can be posted.')?></span>
			</label>
            </div>
            <div class="radio">
			<label>
				<?=$form->radio('enableNewTopics', 0, $enableNewTopics)?>
				<span><?=t('No, posting is disabled.')?></span>
			</label>
            </div>
		</div>
	</div>
	<div class="control-group" data-row="enableNewConversations">
		<label class="control-label"><?=t('Create Conversation using Page Type')?></label>
		<div class="controls" data-select="page">
			<?=$form->select('ptID', $types, $ptID)?>
		</div>
	</div>
</fieldset>
</div>

<script type="text/javascript">
$(function() {

	$('input[name=enableNewTopics]').on('change', function() {
		var pg = $('input[name=enableNewTopics]:checked');
		if (pg.val() == 1) {
			$('div[data-row=enableNewTopics]').show();
		} else {
			$('div[data-row=enableNewTopics]').hide();
		}
	}).trigger('change');
});
</script>
