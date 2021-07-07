<?php defined('C5_EXECUTE') or die("Access Denied.");?>

<div class="ccm-dashboard-header-buttons">
	<a href="<?php echo View::url('/dashboard/system/basics/attributes')?>" class="btn btn-secondary"><?php echo t("Manage Attributes")?></a>
</div>

<form method="post" class="ccm-dashboard-content-form" action="<?=$view->action('update_sitename')?>">
	<?=$this->controller->token->output('update_sitename')?>

	<fieldset>
		<legend><?= t('Core Properties') ?></legend>
		<div class="form-group">
			<label for="SITE" class="launch-tooltip control-label form-label" data-bs-placement="right" title="<?=t('By default, site name is displayed in the browser title bar. It is also the default name for your project on concrete5.org')?>"><?=t('Site Name')?></label>
			<?=$form->text('SITE', $site->getSiteName(), array('class' => 'span4'))?>
		</div>
	</fieldset>

	<fieldset>
		<legend><?=t('Custom Attributes')?></legend>
		<?php
		if (count($attributes) > 0) {
			foreach ($attributes as $ak) {
				echo $renderer->render($ak);
			}
		} else { ?>
			<div class="ms-3">
				<p><?=t('You have not defined any <a href="%s">custom attributes</a> for this site.', URL::to('/dashboard/system/basics/attributes'))?></p>
			</div>
		<?php } ?>
	</fieldset>

	<div class="ccm-dashboard-form-actions-wrapper">
	<div class="ccm-dashboard-form-actions">
		<button class="float-end btn btn-primary" type="submit" ><?=t('Save')?></button>
	</div>
	</div>

</form>
