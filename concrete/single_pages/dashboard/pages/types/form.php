<?php defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Page\Type\Composer\FormLayoutSetControl as PageTypeComposerFormLayoutSetControl;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;

$resolverManager = app(ResolverManagerInterface::class);
?>

<div style="display: none">
	<div id="ccm-page-type-composer-add-set" class="ccm-ui">
		<form method="post" action="<?= $view->action('add_set', $pagetype->getPageTypeID()) ?>">
			<?php $token->output('add_set') ?>
			<div class="form-group">
				<?= $form->label('ptComposerFormLayoutSetName', tc('Name of a set', 'Set Name')) ?>
				<?= $form->text('ptComposerFormLayoutSetName') ?>
			</div>
			<div class="form-group">
				<?= $form->label('ptComposerFormLayoutSetDescription', tc('Description of a set', 'Set Description')) ?>
				<?= $form->textarea('ptComposerFormLayoutSetDescription') ?>
			</div>
		</form>
		<div class="dialog-buttons">
			<button class="btn btn-secondary float-start" onclick="jQuery.fn.dialog.closeTop()"><?= t('Cancel') ?></button>
			<button class="btn btn-primary float-end" onclick="$('#ccm-page-type-composer-add-set form').submit()"><?= t('Add Set') ?></button>
		</div>
	</div>
</div>

<div class="ccm-dashboard-header-buttons btn-group">
	<a href="#" data-dialog="add_set" class="btn btn-secondary"><?= t('Add Set') ?></a>
    <a href="<?=URL::to('/dashboard/pages/types') ?>" class="btn btn-secondary"><?= t('Back to List') ?></a>
</div>
<div class="ccm-pane-body ccm-pane-body-footer">

<p class="lead"><?= $pagetype->getPageTypeDisplayName() ?></p>

<?php if (count($sets) > 0) {
    // @var Concrete\Core\Page\Type\Composer\FormLayoutSet $set
    foreach ($sets as $set) {
        ?>
		<div class="ccm-item-set card " data-page-type-composer-form-layout-control-set-id="<?= $set->getPageTypeComposerFormLayoutSetID() ?>">
			<div class="card-header">
				<ul class="ccm-item-set-controls" style=" float: right;">
					<li><a href="<?= h($resolverManager->resolve(['/ccm/system/page/type/composer/form/add_control']) . '?ptComposerFormLayoutSetID=' . $set->getPageTypeComposerFormLayoutSetID()) ?>" dialog-title="<?=t('Add Form Control') ?>" dialog-width="640" dialog-height="400" data-command="add-form-set-control"><i class="fas fa-plus"></i></a></li>
					<li><a href="#" data-command="move_set" style="cursor: move"><i class="fas fa-arrows-alt"></i></a></li>
					<li><a href="#" data-edit-set="<?= $set->getPageTypeComposerFormLayoutSetID() ?>"><i class="fas fa-edit"></i></a></li>
					<li><a href="#" data-delete-set="<?= $set->getPageTypeComposerFormLayoutSetID() ?>"><i class="fas fa-trash-alt"></i></a></li>
				</ul>
				<div class="ccm-item-set-name" ><?php
                    if ($set->getPageTypeComposerFormLayoutSetDisplayName()) {
                        echo $set->getPageTypeComposerFormLayoutSetDisplayName();
                    } else {
                        echo t('(No Name)');
                    } ?></div>
			</div>

			<div style="display: none">
				<div data-delete-set-dialog="<?= $set->getPageTypeComposerFormLayoutSetID() ?>">
					<form data-delete-set-form="<?= $set->getPageTypeComposerFormLayoutSetID() ?>" action="<?= $view->action('delete_set', $set->getPageTypeComposerFormLayoutSetID()) ?>" method="post">
						<?= t('Delete this form layout set? This cannot be undone.') ?>
						<?php $token->output('delete_set') ?>
					</form>
					<div class="dialog-buttons">
						<button class="btn btn-secondary float-start" onclick="jQuery.fn.dialog.closeTop()"><?= t('Cancel') ?></button>
						<button class="btn btn-danger float-end" onclick="$('form[data-delete-set-form=<?= $set->getPageTypeComposerFormLayoutSetID() ?>]').submit()"><?=t('Delete Set') ?></button>
					</div>
				</div>
			</div>

			<div style="display: none">
				<div data-edit-set-dialog="<?= $set->getPageTypeComposerFormLayoutSetID() ?>" class="ccm-ui">
					<form data-edit-set-form="<?= $set->getPageTypeComposerFormLayoutSetID() ?>" action="<?= $view->action('update_set', $set->getPageTypeComposerFormLayoutSetID()) ?>" method="post">
                        <div class="form-group">
                            <?= $form->label('ptComposerFormLayoutSetName', tc('Name of a set', 'Set Name')) ?>
                            <?= $form->text('ptComposerFormLayoutSetName', $set->getPageTypeComposerFormLayoutSetName()) ?>
                        </div>
                        <div class="form-group">
                            <?= $form->label('ptComposerFormLayoutSetDescription', tc('Description of a set', 'Set Description')) ?>
                            <?= $form->textarea('ptComposerFormLayoutSetDescription', $set->getPageTypeComposerFormLayoutSetDescription()) ?>
                        </div>
                        <div class="dialog-buttons">
                            <button class="btn btn-secondary float-start" onclick="jQuery.fn.dialog.closeTop();"><?= t('Cancel') ?></button>
                            <button class="btn btn-primary float-end" onclick="$('form[data-edit-set-form=<?= $set->getPageTypeComposerFormLayoutSetID() ?>]').submit();"><?=t('Update Set') ?></button>
                        </div>
                        <?php $token->output('update_set') ?>
					</form>
				</div>
			</div>

			<table class="table table-hover" style="width: 100%;">
				<tbody class="ccm-item-set-inner">
					<?php $controls = PageTypeComposerFormLayoutSetControl::getList($set);
                        foreach ($controls as $cnt) {
                            echo View::element('page_types/composer/form/layout_set/control', ['control' => $cnt]);
                        }
                    ?>
				</tbody>
			</table>
		</div>

	<?php
    } ?>
<?php
} else {
        ?>
	<p><?= t('You have not added any composer form layout control sets.') ?></p>
<?php
    } ?>

</div>


<script type="text/javascript">

var Composer = {

    deleteFromLayoutSetControl: function(ptComposerFormLayoutSetControlID) {
        jQuery.fn.dialog.showLoader();
        var formData = [{
            'name': 'token',
            'value': '<?= $token->generate('delete_set_control') ?>'
        }, {
            'name': 'ptComposerFormLayoutSetControlID',
            'value': ptComposerFormLayoutSetControlID
        }];

        $.ajax({
            type: 'post',
            data: formData,
            url: '<?= $view->action('delete_set_control') ?>',
            success: function() {
                jQuery.fn.dialog.hideLoader();
                jQuery.fn.dialog.closeAll();
                $('tr[data-page-type-composer-form-layout-control-set-control-id=' + ptComposerFormLayoutSetControlID + ']').remove();
            }
        });
    }

}

$(function() {
	$('a[data-dialog=add_set]').on('click', function() {
		jQuery.fn.dialog.open({
			element: '#ccm-page-type-composer-add-set',
			modal: true,
			width: 320,
			title: '<?= t('Add Control Set') ?>',
			height: 'auto'
		});
	});
	$('a[data-delete-set]').on('click', function() {
		var ptComposerFormLayoutSetID = $(this).attr('data-delete-set');
        jQuery.fn.dialog.open({
            element: 'div[data-delete-set-dialog=' + ptComposerFormLayoutSetID + ']',
            modal: true,
            width: 320,
            title: '<?=t('Delete Control Set'); ?>',
            height: 'auto'
        });
	});
	$('a[data-edit-set]').on('click', function() {
		var ptComposerFormLayoutSetID = $(this).attr('data-edit-set');
        jQuery.fn.dialog.open({
            element: 'div[data-edit-set-dialog=' + ptComposerFormLayoutSetID + ']',
            modal: true,
            width: 320,
            title: '<?=t('Update Control Set'); ?>',
            height: 'auto'
        });
	});
	$('div.ccm-pane-body').sortable({
		handle: 'a[data-command=move_set]',
		items: '.ccm-item-set',
		cursor: 'move',
		axis: 'y',
		stop: function() {
			var formData = [{
				'name': 'token',
				'value': '<?= $token->generate('update_set_display_order') ?>'
			}, {
				'name': 'ptID',
				'value': <?= $pagetype->getPageTypeID(); ?>
			}];
			$('.ccm-item-set').each(function() {
				formData.push({'name': 'ptComposerFormLayoutSetID[]', 'value': $(this).attr('data-page-type-composer-form-layout-control-set-id')});
			});
			$.ajax({
				type: 'post',
				data: formData,
				url: '<?= $view->action('update_set_display_order') ?>',
				success: function() {

				}
			});
		}
	});
	$('a[data-command=add-form-set-control]').dialog();
	$('a[data-command=edit-form-set-control]').dialog();

	$('.ccm-item-set-inner').sortable({
		handle: 'a[data-command=move-set-control]',
		items: '.ccm-item-set-control',
		cursor: 'move',
		axis: 'y',
		helper: function(e, ui) { // prevent table columns from collapsing
			ui.addClass('active');
			ui.children().each(function () {
				$(this).width($(this).width());
			});
			return ui;
		},
		stop: function(e, ui) {
			ui.item.removeClass('active');

			var formData = [{
				'name': 'token',
				'value': '<?= $token->generate('update_set_control_display_order') ?>'
			}, {
				'name': 'ptComposerFormLayoutSetID',
				'value': $(this).parent().parent().attr('data-page-type-composer-form-layout-control-set-id')
			}];

			$(this).find('.ccm-item-set-control').each(function() {
				formData.push({'name': 'ptComposerFormLayoutSetControlID[]', 'value': $(this).attr('data-page-type-composer-form-layout-control-set-control-id')});
			});

			$.ajax({
				type: 'post',
				data: formData,
				url: '<?= $view->action('update_set_control_display_order') ?>',
				success: function() {}
			});
		}
	});

	$('.ccm-item-set-inner').on('click', 'a[data-delete-set-control]', function() {
		var ptComposerFormLayoutSetControlID = $(this).attr('data-delete-set-control');
        jQuery.fn.dialog.open({
            element: 'div[data-delete-set-control-dialog=' + ptComposerFormLayoutSetControlID + ']',
            modal: true,
            width: 320,
            title: '<?=t('Delete Control'); ?>',
            height: 'auto'
        });
		return false;
	});
});
</script>
