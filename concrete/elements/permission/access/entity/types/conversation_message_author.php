<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<?php $url = $type->getAccessEntityTypeToolsURL(); ?>

<script type="text/javascript">
ccm_choosePermissionAccessEntityConversationMessageAuthor = function() {
	$('#ccm-permissions-access-entity-form .btn-group').removeClass('open');
	$.getJSON('<?=$url?>', function(r) {
		$('#ccm-permissions-access-entity-form input[name=peID]').val(r.peID);	
		$('#ccm-permissions-access-entity-label').html('<div class="alert alert-info">' + r.label + '</div>');	
	});	
}
</script>