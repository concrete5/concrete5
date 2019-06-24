<?php
defined('C5_EXECUTE') or die('Access Denied.');
$cnvMessageSubject = '';
if (is_object($message)) {
    $cnvMessageSubject = $message->getConversationMessageSubject();
}
?>

<div class="ccm-discussion-form">
<div class="ccm-conversation-errors alert alert-danger"></div>
<div class="control-group">
	<?=$form->label($this->field('cnvMessageSubject'), t('Subject'))?>
	<div class="controls">
		<?=$form->text($this->field('cnvMessageSubject'), $cnvMessageSubject);?>
	</div>
</div>

<div class="control-group">
	<?=$form->label($this->field('cnvMessageBody'), t('Message'))?>
	<div class="controls">
	<?php
    $editor = ConversationEditor::getActive();
    $editor->setConversationEditorInputName($this->field('cnvMessageBody'));
    if (is_object($message)) {
        $editor->setConversationMessageObject($message);
    }
    $editor->outputConversationEditorAddMessageForm(); ?>
	</div>
</div>


</div>