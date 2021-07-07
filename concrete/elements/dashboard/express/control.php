<?php
defined('C5_EXECUTE') or die("Access Denied.");

$final_label = $control->getDisplayLabel();
$original_label = $control->getControlLabel();
$type_name = $control->getControlType()->getDisplayName();
$c = Page::getCurrentPage();
?>

<tr class="ccm-item-set-item" data-field-set-control="<?=$control->getID()?>">
	<td style="width: 20%; white-space: nowrap"><?= $final_label ?></td>
	<td style="width: 100%;">
		<span class="text-muted"><?= $type_name ?></span>
		<?php

        if ($final_label != $original_label) {
            ?>
			<span class="text-muted">(<?= $original_label ?>)</span>
		<?php 
        } ?>
	</td>
        <td>
		<span class="text-muted"><?php if ($control->isRequired()) { echo t('Required'); } ?></span>
	</td>
	<td>
		<ul class="ccm-item-set-controls">
			<li><a href="#" data-command="move-control" style="cursor: move"><i class="fas fa-arrows-alt"></i></a></li>
			<li><a data-command="edit-control" href="<?=URL::to('/dashboard/system/express/entities/forms', 'edit_control', $control->getId())?>" dialog-height="450" dialog-width="600" dialog-title="<?=t('Edit Control')?>" class="dialog-launch"><i class="fas fa-pencil-alt"></i></a></li>
			<li><a href="#" data-dialog="delete-set-control-<?=$control->getId()?>" data-dialog-title="<?=t('Delete Control')?>"><i class="fas fa-trash"></i></a></li>
		</ul>

		<div style="display: none">
		<div id="ccm-dialog-delete-set-control-<?=$control->getID()?>" class="ccm-ui">
			<form method="post" action="<?=URL::to($c, 'delete_set_control', $control->getFieldSet()->getForm()->getID())?>">
				<?=Core::make("token")->output('delete_set_control')?>
				<input type="hidden" name="field_set_control_id" value="<?=$control->getID()?>">
				<p><?=t('Are you sure you want to delete this control? This cannot be undone.')?></p>
				<div class="dialog-buttons">
					<button class="btn btn-secondary float-start" onclick="jQuery.fn.dialog.closeTop()"><?=t('Cancel')?></button>
					<button class="btn btn-danger float-end" onclick="$('#ccm-dialog-delete-set-control-<?=$control->getId()?> form').submit()"><?=t('Delete Control')?></button>
				</div>
			</form>
		</div>
		</div>

	</td>
</tr>

