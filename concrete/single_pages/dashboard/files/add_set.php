<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>
<?php $ih = Loader::helper('concrete/ui'); ?>

    <form method="post" id="file-sets-add" action="<?=$view->url('/dashboard/files/add_set', 'do_add')?>">

	<?=$validation_token->output('file_sets_add');?>

	<div class="form-group">
		<?=Loader::helper("form")->label('file_set_name', t('Name'))?>
		<?=$form->text('file_set_name', '', array('autofocus' => 'autofocus'))?>
	</div>


	<div class="ccm-dashboard-form-actions-wrapper">
	<div class="ccm-dashboard-form-actions">
		<a href="<?=View::url('/dashboard/files/sets')?>" class="btn btn-default pull-left"><?=t('Cancel')?></a>
		<?=Loader::helper("form")->submit('add', t('Add'), array('class' => 'btn btn-primary pull-right'))?>
	</div>
	</div>
	</form>
