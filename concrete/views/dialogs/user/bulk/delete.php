<?php defined('C5_EXECUTE') or die('Access Denied.');

if (! is_array($users) || count($users) ==0) {
	?>
	<div class="alert-message info">
		<?php  echo t("No users are eligible for this operation"); ?>
	</div>
<?php
} else {
	if ($excluded) {
		?>
		<div class="alert-message info">
			<?php  echo t("Users you don't have permission to bulk-delete have been removed from this list."); ?>
		</div>
	<?php
	}
?>
	<p><?php echo t('Are you sure you would like to delete the following users?'); ?>?</p>

	<form method="post" data-dialog-form="save-file-set" action="<?= $controller->action('submit'); ?>">
		<?php
		foreach ($users as $ui) {
			?>
			<input type="hidden" name="item[]" value="<?= $ui->getUserID(); ?>"/>
		<?php
		} ?>

		<div class="ccm-ui">
			<?php View::element('users/confirm_list', ['users' => $users]); ?>
		</div>

		<div class="dialog-buttons">
			<button class="btn btn-default pull-left" data-dialog-action="cancel"><?= t('Cancel'); ?></button>
			<button type="button" data-dialog-action="submit" class="btn btn-primary pull-right"><?= t('Delete'); ?></button>
		</div>

	</form>

<?php }