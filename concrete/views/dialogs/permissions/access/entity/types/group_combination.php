<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var string $url
 * @var Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface $resolverManager
 * @var Concrete\Core\Form\Service\Widget\DateTime $dt
 */

?>
<div class="ccm-ui">
    <form method="POST" action="<?= h($url) ?>" id="ccm-permission-access-entity-combination-groups-form">
        <p><?= t('Only users who are members of ALL selected groups will be eligible for this permission.') ?></p>
        <table id="ccm-permissions-access-entity-combination-groups" class="table">
            <tr>
                <th><div style="width: 16px"></div></th>
                <th width="100%"><?= t('Name') ?></th>
                <?php
                if (!is_object($pae ?? null)) {
                    ?>
                    <th><div style="width: 16px"></div></th>
                    <?php
                }
                ?>
            </tr>
            <tr>
                <td colspan="3" id="ccm-permissions-access-entity-combination-groups-none"><?= t('No users or groups added.') ?></td>
            </tr>
        </table>
    </form>
    <input
        type="button"
        class="btn btn-secondary float-end dialog-launch"
        dialog-width="640" dialog-height="480"
        dialog-modal="false"
        dialog-title="<?= t('Add Group') ?>"
        id="ccm-permissions-access-entity-members-add-group"
        href="<?= h($resolverManager->resolve(['/ccm/system/dialogs/groups/search'])) ?>"
        value="<?= t('Add Group') ?>"
    />
</div>
<div class="dialog-buttons">
    <input type="button" onclick="$.fn.dialog.closeTop()" value="<?= t('Cancel') ?>" class="btn btn-default btn-hover-danger">
    <input type="submit" onclick="$('#ccm-permission-access-entity-combination-groups-form').submit()" value="<?= t('Save') ?>" class="btn pull-right btn-primary">
</div>
<script>
ConcreteEvent.unsubscribe('SelectGroup');
ConcreteEvent.subscribe('SelectGroup', function(e, data) {
    var gID = data.gID,
        gName = data.gName;
    if ($("input[class=combogID][value=" + gID + "]").length === 0) {
        $.fn.dialog.closeTop();
        $("#ccm-permissions-access-entity-combination-groups-none").hide();
        var tbl = $("#ccm-permissions-access-entity-combination-groups");
        tbl.append('<tr><td><input type="hidden" class="combogID" name="gID[]" value="' + gID + '"><i class="fas fa-users"></i></td><td>' + gName + '</td><?=
            is_object($pae ?? null) ? ''
            : '<td><a href="javascript:void(0)" onclick="ccm_removeCombinationGroup(this)"><i class="fas fa-trash"></i></a></td>'
        ?>');
    }
});
ccm_removeCombinationGroup = function(link) {
    $(link).parent().parent().remove();
    var tbl = $("#ccm-permissions-access-entity-combination-groups");
    if (tbl.find('tr').length == 2) {
        $("#ccm-permissions-access-entity-combination-groups-none").show();
    }
};
$(document).ready(function() {
    $('#ccm-permission-access-entity-combination-groups-form').ajaxForm({
        dataType: 'json',
        beforeSubmit: function() {
            $.fn.dialog.showLoader();
        },
        success: function(r) {
            $.fn.dialog.hideLoader();
            $.fn.dialog.closeTop();
            $('#ccm-permissions-access-entity-form .btn-group').removeClass('open');
            $('#ccm-permissions-access-entity-form input[name=peID]').val(r.peID);
            $('#ccm-permissions-access-entity-label').html('<div class="alert alert-info">' + r.label + '</div>');
        }
    });
});
</script>
