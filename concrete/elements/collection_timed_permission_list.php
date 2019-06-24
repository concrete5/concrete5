<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<div class="ccm-ui">
<?php
$assignments = $cp->getAllTimedAssignmentsForPage();
if (count($assignments) > 0) {
    ?>

<table class="ccm-permission-grid table table-striped">
<?php
foreach ($assignments as $ppc) {
    $pk = $ppc->getPermissionKeyObject();
    ?>
	<tr>
	<td>
	<strong><?=$pk->getPermissionKeyDisplayName()?></strong>
	<?=t('Permission on ')?><?php
        if ($pk instanceof AreaPermissionKey) {
            ?>
			<strong><?=$pk->getPermissionObject()->getAreaHandle() ?></strong>.
		<?php 
        } elseif ($pk instanceof BlockPermissionKey) {
            $bt = BlockType::getByID($pk->getPermissionObject()->getBlockTypeID());
            $obj = $pk->getPermissionObject();
            if ($obj->getBlockName() != '') {
                ?>

			<?=t('the %s block named <strong>%s</strong> in <strong>%s</strong> Area. ', t($bt->getBlockTypeName()), $obj->getBlockName(), $pk->getPermissionObject()->getAreaHandle())?>
			
			<?php 
            } else {
                ?>
			
			<?=t('<strong>%s Block</strong> in <strong>%s</strong> Area. ', t($bt->getBlockTypeName()), $pk->getPermissionObject()->getAreaHandle())?>
			
			<?php 
            }
            ?>		
		<?php 
        } else {
            ?>
			<strong><?=t('Entire Page')?></strong>.
		<?php 
        }
    ?>
		<?php
        $pd = $ppc->getDurationObject();
    ?>
		<div>
		<?php
        $assignee = t('Nobody');
    $pae = $ppc->getAccessEntityObject();
    if (is_object($pae)) {
        $assignee = $pae->getAccessEntityLabel();
    }
    ?>
		<?=t('Assigned to <strong>%s</strong>. ', $assignee)?>
		<?=$pd->getTextRepresentation()?>
		</div>
	</td>
	</tr>
<?php 
}
    ?>
</table>

<?php 
} else {
    ?>
	<p><?=t('No timed permission assignments')?></p>
<?php 
} ?>

</div>
