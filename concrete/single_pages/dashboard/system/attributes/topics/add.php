<?php defined('C5_EXECUTE') or die("Access Denied.");
$form = Core::make('helper/form');
?>

<form method="post" action="<?php echo $view->action('submit'); ?>" class="form-horizontal">
<?php if (PermissionKey::getByHandle('add_topic_tree')->validate()) { ?>
	<?php echo Loader::helper('validation/token')->output('submit'); ?>
	<div class="form-group">
		<?php echo $form->label('topicTreeName', t('Tree Name')); ?>
		<?php echo $form->text('topicTreeName', $topicTreeName); ?>
	</div>
<?php } else { ?>
	<p><?php echo t('You may not add topic trees.'); ?></p>
<?php } ?>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions ">
            <a href="<?php echo $view->url('/dashboard/system/attributes/topics'); ?>" class="btn btn-secondary float-start"><?php echo t("Cancel"); ?></a>
            <button type="submit" class="btn btn-primary float-end"><?php echo t('Add Topic Tree'); ?></button>
        </div>
    </div>
</form>
