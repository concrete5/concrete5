<?php

use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;

defined('C5_EXECUTE') or die('Access Denied.');
/**
 * @var Concrete\Core\Page\Type\Composer\Control\Control $control
 * @var Concrete\Core\Form\Service\Form $form
 * @var string $label
 * @var string $description
 */
$resolverManager = app(ResolverManagerInterface::class);
?>

<div class="form-group">
    <?= $form->label('', $label) ?>
    <?php if ($control->isPageTypeComposerFormControlRequiredOnThisRequest()) { ?>
        <span class="label label-info"><?= t('Required') ?></span>
    <?php } ?>

	<?php if ($description) { ?>
	    <i class="fas fa-question-circle launch-tooltip" title="" data-original-title="<?= $description ?>"></i>
	<?php } ?>

	<div data-composer-field="name">
		<?= $form->text($this->field('name'), $control->getPageTypeComposerControlDraftValue(), ['autofocus' => 'autofocus']) ?>
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
				token: '<?= app('helper/validation/token')->generate('get_url_slug') ?>',
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
					<?= json_encode((string) $resolverManager->resolve(['/ccm/system/page/url_slug'])) ?>,
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
