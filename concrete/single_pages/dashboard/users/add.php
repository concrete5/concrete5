<?php defined('C5_EXECUTE') or die('Access Denied.');
$ih = Loader::helper('concrete/ui');
?>

<form method="post" action="<?php echo $view->action('submit'); ?>">
    <?= $form->getAutocompletionDisabler() ?>
	<fieldset>
		<legend><?php echo t('Basic Details'); ?></legend>
		<div class="form-group">
			<label for="uName" class="control-label"><?php echo t('Username'); ?></label>
			<div class="input-group">
				<?php echo $form->text('uName', array('autofocus' => 'autofocus', 'autocomplete' => 'off')); ?>
				<span class="input-group-addon"><i class="fa fa-asterisk"></i></span>
			</div>
		</div>

		<div class="form-group">
			<label for="uPassword" class="control-label"><?php echo t('Password'); ?></label>
			<div class="input-group">
				<?php echo $form->password('uPassword', array('autocomplete' => 'off')); ?>
				<span class="input-group-addon"><i class="fa fa-asterisk"></i></span>
			</div>
		</div>

		<div class="form-group">
			<label for="uEmail" class="control-label"><?php echo t('Email Address'); ?></label>
			<div class="input-group">
				<?php echo $form->email('uEmail'); ?>
				<span class="input-group-addon"><i class="fa fa-asterisk"></i></span>
			</div>
		</div>

		<?php if (count($locales)) { // "> 1" because en_US is always available ?>
		<div class="form-group">
			<label for="uEmail" class="control-label"><?php echo t('Language'); ?></label>
			<div>
				<?php echo $form->select('uDefaultLanguage', $locales, Localization::activeLocale()); ?>
			</div>
		</div>
		<?php } ?>
	</fieldset>

<?php if (count($attribs) > 0) { ?>
	<fieldset>
		<legend><?php echo t('Registration Data'); ?></legend>

	<?php foreach ($attribs as $ak) {
		if (in_array($ak->getAttributeKeyID(), $assignment->getAttributesAllowedArray())) { ?>
		<div class="form-group">
        	<label class="control-label"><?php echo $ak->getAttributeKeyDisplayName(); ?></label>
        	<div>
                <?php $ak->render(new \Concrete\Core\Attribute\Context\DashboardFormContext(), null, false); ?>
            </div>
        </div>
        <?php } ?>
    <?php } ?>

	</fieldset>
<?php } ?>

	<fieldset>
		<legend><?php echo t('Groups'); ?></legend>
		<div class="form-group">
			<label class="control-label"><?php echo t('Place this user into groups'); ?></label>

		<?php foreach ($gArray as $g) {
			$gp = new Permissions($g);
			if ($gp->canAssignGroup()) { ?>
			<div class="checkbox">
				<label>
					<input type="checkbox" name="gID[]" value="<?php echo $g->getGroupID(); ?>"
					<?php if (isset($_POST['gID']) && is_array($_POST['gID']) && in_array($g->getGroupID(), $_POST['gID'])) {
					?> checked <?php } ?>>

					<?php echo $g->getGroupDisplayName(); ?>
				</label>
			</div>
		<?php }
		} ?>

		</div>
    </fieldset>
	<?php echo $token->output('submit');?>

	<div class="ccm-dashboard-form-actions-wrapper">
		<div class="ccm-dashboard-form-actions">
			<a href="<?php echo View::url('/dashboard/users/search'); ?>" class="btn btn-default pull-left"><?php echo t('Cancel'); ?></a>
			<?php echo Loader::helper("form")->submit('add', t('Add'), array('class' => 'btn btn-primary pull-right')); ?>
		</div>
	</div>
</form>
