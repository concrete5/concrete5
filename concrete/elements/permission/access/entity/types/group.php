<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>
<?php $url = $type->getAccessEntityTypeToolsURL(); ?>

<script type="text/javascript">
$(function() {
	ConcreteEvent.unsubscribe('SelectGroup.core');
	ConcreteEvent.subscribe('SelectGroup.core', function(e, data) {
		jQuery.fn.dialog.closeTop();
		$('#ccm-permissions-access-entity-form .btn-group').removeClass('open');
		$.getJSON('<?=$url?>', {
			'gID': data.gID
		}, function(r) {
			$('#ccm-permissions-access-entity-form input[name=peID]').val(r.peID);
			$('#ccm-permissions-access-entity-label').html('<div class="alert alert-info">' + r.label + '</div>');
		});
	});
});
</script>
