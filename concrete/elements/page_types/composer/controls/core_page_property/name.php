<?php
defined('C5_EXECUTE') or die('Access Denied.');
?>

<div class="form-group">
	<label class="control-label"><?=$label?></label>
    <?php if ($control->isPageTypeComposerFormControlRequiredOnThisRequest()) : ?>
        <span class="label label-info"><?= t('Required') ?></span>
    <?php endif; ?>
	<?php if ($description): ?>
	<i class="fa fa-question-circle launch-tooltip" title="" data-original-title="<?=$description?>"></i>
	<?php endif; ?>
	<div data-composer-field="name">
		<?=$form->text($this->field('name'), $control->getPageTypeComposerControlDraftValue(), ['autofocus' => 'autofocus'])?>
	</div>
</div>

<script type="text/javascript">
var concreteComposerAddPageTimer = false;
$(function() {
	var $urlSlugField = $('div[data-composer-field=url_slug] input');
	if ($urlSlugField.length) {
		$('div[data-composer-field=name] input').on('keyup', function() {
			var input = $(this);
			var send = {
				token: '<?=Loader::helper('validation/token')->generate('get_url_slug')?>',
				name: input.val()
			};
			var parentID = input.closest('form').find('input[name=cParentID]').val();
			if (parentID) {
				send.parentID = parentID;
			}
			clearTimeout(concreteComposerAddPageTimer);
			concreteComposerAddPageTimer = setTimeout(function() {
				$('.ccm-composer-url-slug-loading').show();
				$.post(
					'<?=REL_DIR_FILES_TOOLS_REQUIRED?>/pages/url_slug',
					send,
					function(r) {
						$('.ccm-composer-url-slug-loading').hide();
						$urlSlugField.val(r);
					}
				);
			}, 150);
		});
	}
});
</script>