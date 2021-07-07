<?php
use Concrete\Core\Entity\Attribute\Key\Settings\UserGroupSettings;

?>
<fieldset>
    <legend><?php echo t('User/Group Options')?></legend>

    <div class="form-group">

        <?php echo $form->label('akDisplayAllGroups', t('Groups to Display'))?>

        <div class="form-check">
            <?php echo $form->radio('akDisplayGroupsBeneathSpecificParent', 0, $akDisplayGroupsBeneathSpecificParent)?>
            <label class="form-check-label" for="akDisplayGroupsBeneathSpecificParent1"><?=t('Display all groups in the list.')?></label>
        </div>

        <div class="form-check">
            <?php echo $form->radio('akDisplayGroupsBeneathSpecificParent', 1, $akDisplayGroupsBeneathSpecificParent)?>
            <label class="form-check-label" for="akDisplayGroupsBeneathSpecificParent2"><?=t('Display groups beneath a specific parent.')?></label>
        </div>
    </div>

    <div class="form-group" data-select="parent-group">
        <?php
        echo $groupSelector->selectGroupWithTree('akDisplayGroupsBeneathParentID', $akDisplayGroupsBeneathParentID);
        ?>
    </div>

    <div class="form-group">

        <?php echo $form->label('akMembersOnly', t('Group Selection'))?>

        <div class="form-check">
            <?php echo $form->radio('akGroupSelectionMethodType', 'all', $akGroupSelectionMethodType)?>
            <label class="form-check-label" for="akGroupSelectionMethodType3"><?=t('Allow user to select any group in the list.')?></label>
        </div>

        <div class="form-check">
            <?php echo $form->radio('akGroupSelectionMethodType', 'custom', $akGroupSelectionMethodType)?>
            <label class="form-check-label" for="akGroupSelectionMethodType4"><?=t('Custom selection.')?></label>
        </div>

    </div>

    <div class="form-group" data-select="select-groups-custom">
        <label class="control-label form-label"><?=t('Populate group list with:')?></label>

        <div class="form-check">
            <?php echo $form->checkbox('akGroupSelectionMethod[]', UserGroupSettings::GROUP_SELECTION_METHOD_IN_GROUP, $akGroupSelectionMethodInGroup)?>
            <label class="form-check-label" for="akGroupSelectionMethod_G"><?=t('A list of groups that the user is in.')?></label>
        </div>

        <div class="form-check">
            <?php echo $form->checkbox('akGroupSelectionMethod[]', UserGroupSettings::GROUP_SELECTION_METHOD_PERMISSIONS, $akGroupSelectionMethodPermissions)?>
            <label class="form-check-label" for="akGroupSelectionMethod_P"><?=t('A list of groups the user is allowed to assign.')?></label>
        </div>

    </div>

</fieldset>

<script type="text/javascript">
$(function() {
    $('input[name=akDisplayGroupsBeneathSpecificParent]').on('change', function() {
        var display = $('input[name=akDisplayGroupsBeneathSpecificParent]:checked').val();
        if (display > 0) {
            $('div[data-select=parent-group]').show();
        } else {
            $('div[data-select=parent-group]').hide();
        }
    }).trigger("change");

    $('input[name=akGroupSelectionMethodType]').on('change', function() {
        var methodType = $('input[name=akGroupSelectionMethodType]:checked').val();
        if (methodType == 'custom') {
            $('div[data-select=select-groups-custom]').show();
        } else {
            $('div[data-select=select-groups-custom]').hide();
        }
    }).trigger("change");

});
</script>
