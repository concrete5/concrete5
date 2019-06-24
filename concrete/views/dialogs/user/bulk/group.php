<?php defined('C5_EXECUTE') or die('Access Denied.');

$form = Core::make('helper/form');

if ('add' == $function) {
    $button = t('Add');
    $label = t('Add the users below to Group(s)');
} else {
    $button = t('Remove');
    $label = t('Remove the users below from Group(s)');
}

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
			<?php  echo t("Users you don't have permission to assign groups to have been removed from this list."); ?>
		</div>
	<?php
	}
?>
	<form method="post" data-dialog-form="save-file-set" action="<?= $controller->action('submit'); ?>">
		<?php echo $form->label('groupIDs', $label); ?>
		<div class="form-group" data-form-row="user-groups">
			<div style="width: 100%">
				<?=$form->selectMultiple('groupIDs', $gArray, 0, ['style' => 'width: 100%']); ?>
			</div>
		</div>

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
			<button type="button" data-dialog-action="submit" class="btn btn-primary pull-right"><?= $button; ?></button>
		</div>

	</form>


	<script type="text/javascript">
		$(function() {
			$('#groupIDs').removeClass('form-control').selectize({
				plugins: ['remove_button']
			});
		});
	</script>
<?php }