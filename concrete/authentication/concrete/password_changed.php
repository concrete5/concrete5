<?php defined('C5_EXECUTE') or die('Access denied.'); ?>

<div class="alert alert-success">
	<?= t('Successfully changed password'); ?>
</div>
<div class="d-grid">
	<a href="<?= URL::to('login', 'callback', 'concrete') ?>" class="btn btn-success">
		<?= t('Click here to log in'); ?>
	</a>
</div>
