<?php
defined('C5_EXECUTE') or die('Access Denied.');
?>

<div class="form-group" data-group="composer-description">
	<label class="control-label"><?=$label?></label>
    <?php if ($control->isPageTypeComposerControlRequiredByDefault() || $control->isPageTypeComposerFormControlRequiredOnThisRequest()) : ?>
        <span class="label label-info"><?= t('Required') ?></span>
    <?php endif; ?>
	<?php if ($description): ?>
	<i class="fa fa-question-circle launch-tooltip" title="" data-original-title="<?=$description?>"></i>
	<?php endif; ?>
	<?=$form->textarea($this->field('description'), $control->getPageTypeComposerControlDraftValue(), array('rows' => 3))?>
</div>

<script type="text/javascript">
	$(function() {
		autosize($('div[data-group=composer-description] textarea'));
	});
</script>