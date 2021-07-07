<?php
if (is_object($key)) {
    $uakProfileDisplay = $key->isAttributeKeyDisplayedOnProfile();
    $uakProfileEdit = $key->isAttributeKeyEditableOnProfile();
    $uakProfileEditRequired = $key->isAttributeKeyRequiredOnProfile();
    $uakRegisterEdit = $key->isAttributeKeyEditableOnRegister();
    $uakRegisterEditRequired = $key->isAttributeKeyRequiredOnRegister();
    $uakMemberListDisplay = $key->isAttributeKeyDisplayedOnMemberList();
}
?>
<?php $form = Loader::helper('form'); ?>
<fieldset>
    <legend><?=t('User Attribute Options')?></legend>
    <div class="form-group">
        <label class="control-label form-label"><?=t('Public Display')?></label>
        <div class="form-check">
            <?=$form->checkbox('uakProfileDisplay', 1, !empty($uakProfileDisplay))?>
            <?=$form->label('uakProfileDisplay',t('Displayed in Public Profile.'), ['class'=>'form-check-label']);?>
        </div>
        <div class="form-check">
            <?=$form->checkbox('uakMemberListDisplay', 1, !empty($uakMemberListDisplay))?>
            <?=$form->label('uakMemberListDisplay',t('Displayed on Member List.'), ['class'=>'form-check-label']);?>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label form-label"><?=t('Edit Mode')?></label>
        <div class="form-check">
            <?=$form->checkbox('uakProfileEdit', 1, !empty($uakProfileEdit))?>
            <?=$form->label('uakProfileEdit',t('Editable in Profile.'), ['class'=>'form-check-label']);?>
        </div>
        <div class="form-check">
            <?=$form->checkbox('uakProfileEditRequired', 1, !empty($uakProfileEditRequired))?>
            <?=$form->label('uakProfileEditRequired',t('Editable and Required in Profile.'), ['class'=>'form-check-label']);?>
        </div>
    </div>


    <div class="form-group">
        <label class="control-label form-label"><?=t('Registration')?></label>
        <div class="form-check">
            <?=$form->checkbox('uakRegisterEdit', 1, !empty($uakRegisterEdit))?>
            <?=$form->label('uakRegisterEdit',t('Show on Registration Form.'), ['class'=>'form-check-label']);?>
        </div>
        <div class="form-check">
            <?=$form->checkbox('uakRegisterEditRequired', 1, !empty($uakRegisterEditRequired))?>
            <?=$form->label('uakRegisterEditRequired', t('Require on Registration Form.'), ['class'=>'form-check-label']);?>
        </div>
    </div>
</fieldset>

<script type="text/javascript">
$(function() {
	$('input[name=uakProfileEdit]').click(function() {
		if ($(this).prop('checked')) {
			$('input[name=uakProfileEditRequired]').attr('disabled', false);
		} else {
			$('input[name=uakProfileEditRequired]').attr('checked', false);
			$('input[name=uakProfileEditRequired]').attr('disabled', true);		
		}
	});

	$('input[name=uakRegisterEdit]').click(function() {
		if ($(this).prop('checked')) {
			$('input[name=uakRegisterEditRequired]').attr('disabled', false);
		} else {
			$('input[name=uakRegisterEditRequired]').attr('checked', false);
			$('input[name=uakRegisterEditRequired]').attr('disabled', true);		
		}
	});
	

	if ($('input[name=uakProfileEdit]').prop('checked')) {
		$('input[name=uakProfileEditRequired]').attr('disabled', false);
	} else {
		$('input[name=uakProfileEditRequired]').attr('checked', false);
		$('input[name=uakProfileEditRequired]').attr('disabled', true);		
	}	

	if ($('input[name=uakRegisterEdit]').prop('checked')) {
		$('input[name=uakRegisterEditRequired]').attr('disabled', false);
	} else {
		$('input[name=uakRegisterEditRequired]').attr('checked', false);
		$('input[name=uakRegisterEditRequired]').attr('disabled', true);		
	}	

});
</script>
