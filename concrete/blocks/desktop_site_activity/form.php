<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>
<div class="form-group">
    <?php echo $form->label('types', t('Types to Display'));?>
    <div class="checkbox"><label><?=$form->checkbox('types[]', 'form_submissions', in_array('form_submissions', $types))?> <?=t('Form Submissions')?></label></div>
    <div class="checkbox"><label><?=$form->checkbox('types[]', 'survey_results', in_array('survey_results', $types))?> <?=t('Survey Results')?></label></div>
    <div class="checkbox"><label><?=$form->checkbox('types[]', 'signups', in_array('signups', $types))?> <?=t('New Users')?></label></div>
    <div class="checkbox"><label><?=$form->checkbox('types[]', 'conversation_messages', in_array('conversation_messages', $types))?> <?=t('Conversation Messages')?></label></div>
    <div class="checkbox"><label><?=$form->checkbox('types[]', 'workflow', in_array('workflow', $types))?> <?=t('Workflow Approvals')?></label></div>
</div>
