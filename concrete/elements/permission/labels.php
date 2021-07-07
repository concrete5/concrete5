<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>
<?php
if (!isset($pa)) {
    $pa = $pk->getPermissionAccessObject();
}
$assignments = [];
$paID = 0;
if (is_object($pa)) {
    $paID = $pa->getPermissionAccessID();
    $assignments = $pa->getAccessListItems(PermissionKey::ACCESS_TYPE_ALL);
}

?>
<div class="ccm-permission-access-line">
<?php
$str = '';

if (count($assignments) > 0) {
    for ($i = 0; $i < count($assignments); $i++) {
        $class = '';
        $as = $assignments[$i];
        $entity = $as->getAccessEntityObject();
        $pd = $as->getPermissionDurationObject();
        $pdTitle = '';

        if ($as->getAccessType() == PermissionKey::ACCESS_TYPE_EXCLUDE) {
            if (is_object($pd)) {
                $class = 'bg-warning';
                $pdTitle = 'title="' . $pd->getTextRepresentation() . '"';
            } else {
                $class = 'bg-danger';
            }
        } else {
            if (is_object($pd)) {
                $class = 'bg-info';
                $pdTitle = 'title="' . $pd->getTextRepresentation() . '"';
            }
        }

        if (!$class) {
            $class = 'bg-secondary';
        }
        $str .= '<span class="badge ' . $class . '" ' . $pdTitle . '>' . $entity->getAccessEntityLabel() . '</span> ';
    }
}

?>
<?php if (!$str) {
    ?>
	<span style="color: #ccc"><?=t('None'); ?></span>
<?php
} else {
        ?>
	<?=$str; ?>
<?php
    } ?>

<input type="hidden" name="pkID[<?=$pk->getPermissionKeyID(); ?>]" value="<?=$paID; ?>" data-pkID="<?=$pk->getPermissionKeyID(); ?>" />
</div>

<script type="text/javascript">
$(function() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('.ccm-permission-access-line span[title]'))
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl, { container: '#ccm-tooltip-holder' })
    })
	$('.ccm-permission-grid-cell .ccm-permission-access-line').draggable({
		helper: 'clone'	
	});
	$('.ccm-permission-grid-cell').droppable({
		accept: '.ccm-permission-access-line',
		hoverClass: 'ccm-permissions-grid-cell-active',
		drop: function(ev, ui) {
			var srcPKID = $(ui.draggable).find('input').attr('data-pkID');
			$('#ccm-permission-grid-name-' + srcPKID + ' a').attr('data-duplicate', '1');
			
			var paID = $(ui.draggable).find('input').val();
			var pkID = $(this).attr('id').substring(25);

			$(ui.draggable).clone().appendTo($('#ccm-permission-grid-cell-' + pkID).html(''));
			$('#ccm-permission-grid-name-' + pkID + ' a').attr('data-paID', paID).attr('data-duplicate', '1');
			$('#ccm-permission-grid-cell-' + pkID + ' input[type=hidden]').attr('name', 'pkID[' + pkID + ']');	
			$('#ccm-permission-grid-cell-' + pkID + ' div.ccm-permission-access-line').draggable({
				helper: 'clone'
			});			
		}
		
	});
});
</script>
