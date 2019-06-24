<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<?php $url = $type->getAccessEntityTypeToolsURL(); ?>

<script type="text/javascript">
    (function() {
        Concrete.event.unbind('UserSearchDialogSelectUser.core');
        Concrete.event.bind('UserSearchDialogSelectUser.core', function(event, data) {
            Concrete.event.unbind(event);
            $('#ccm-permissions-access-entity-form .btn-group').removeClass('open');
            $.getJSON('<?=$url?>', {
                'uID': data.uID
            }, function(r) {
                $.fn.dialog.closeTop();
                $('#ccm-permissions-access-entity-form input[name=peID]').val(r.peID);
                $('#ccm-permissions-access-entity-label').html('<div class="alert alert-info">' + r.label + '</div>');
            });
        });
    }());
</script>
