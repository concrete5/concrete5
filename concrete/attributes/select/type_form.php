<?php
if (!isset($defaultNewOptionNm)) {
    $defaultNewOptionNm = null;
}
function getAttributeOptionHTML($v)
{
    if ($v == 'TEMPLATE') {
        $akSelectValueID = 'TEMPLATE_CLEAN';
        $akSelectValue = 'TEMPLATE';
    } else {
        if ($v->getSelectAttributeOptionID() != false) {
            $akSelectValueID = $v->getSelectAttributeOptionID();
        } else {
            $akSelectValueID = uniqid();
        }
        $akSelectValue = $v->getSelectAttributeOptionValue();
    }
    ?>
		<div id="akSelectValueDisplay_<?=$akSelectValueID?>" >
			<div class="rightCol">
				<input class="btn btn-primary" type="button" onClick="ccmAttributesHelper.editValue('<?=addslashes($akSelectValueID)?>')" value="<?=t('Edit')?>" />
				<input class="btn btn-danger" type="button" onClick="ccmAttributesHelper.deleteValue('<?=addslashes($akSelectValueID)?>')" value="<?=t('Delete')?>" />
			</div>			
			<span onClick="ccmAttributesHelper.editValue('<?=addslashes($akSelectValueID)?>')" id="akSelectValueStatic_<?=$akSelectValueID?>" class="leftCol"><?=$akSelectValue ?></span>
		</div>
		<div id="akSelectValueEdit_<?=$akSelectValueID?>" style="display:none">
			<span class="leftCol">
				<input name="akSelectValueOriginal_<?=$akSelectValueID?>" type="hidden" value="<?=$akSelectValue?>" />
				<?php if (is_object($v) && $v->getSelectAttributeOptionID()) {
    ?>
					<input id="akSelectValueExistingOption_<?=$akSelectValueID?>" name="akSelectValueExistingOption_<?=$akSelectValueID?>" type="hidden" value="<?=$akSelectValueID?>" />
				<?php
} else {
    ?>
					<input id="akSelectValueNewOption_<?=$akSelectValueID?>" name="akSelectValueNewOption_<?=$akSelectValueID?>" type="hidden" value="<?=$akSelectValueID?>" />
				<?php
}
    ?>
				<input id="akSelectValueField_<?php echo $akSelectValueID?>" onkeypress="ccmAttributesHelper.keydownHandler(event);" class="akSelectValueField form-control" data-select-value-id="<?php echo $akSelectValueID;
    ?>" name="akSelectValue_<?php echo $akSelectValueID?>" type="text" value="<?php echo h($akSelectValue)?>" size="40" />
			</span>		
			<div class="rightCol">
				<input class="btn btn-default" type="button" onClick="ccmAttributesHelper.editValue('<?=addslashes($akSelectValueID)?>')" value="<?=t('Cancel')?>" />
				<input class="btn btn-success" type="button" onClick="ccmAttributesHelper.changeValue('<?=addslashes($akSelectValueID)?>')" value="<?=t('Save')?>" />
			</div>		
		</div>	
		<div class="ccm-spacer">&nbsp;</div>
<?php
} ?>

<fieldset class="ccm-attribute ccm-attribute-select">
<legend><?=t('Select Options')?></legend>

<div class="form-group">
    <label><?=t("Multiple Values")?></label>
    <div class="form-check">
        <?=$form->checkbox('akSelectAllowMultipleValues', 1, $akSelectAllowMultipleValues)?>
        <label for="akSelectAllowMultipleValues" class="form-check-label"><?=t('Allow multiple options to be chosen.')?></label>
    </div>
</div>

<div class="form-group" data-group="single-value">
	<label><?=t("Single Value")?></label>
	<div class="form-check">
        <?=$form->checkbox('akDisplayMultipleValuesOnSelect', 1, $akDisplayMultipleValuesOnSelect)?>
        <label for="akDisplayMultipleValuesOnSelect" class="form-check-label">
			<?=t('Display full option list when selecting.')?>
		</label>
	</div>
	<div class="help-block"><?=t('Enabling this will typically display the list with radio buttons.')?></div>
</div>

<div class="form-group" data-group="single-value">
    <label><?=t("Hide None Option")?></label>
    <div class="form-check">
        <?=$form->checkbox('akHideNoneOption', 1, $akHideNoneOption)?>
        <label for="akHideNoneOption" class="form-check-label">
            <?=t('Hide none option from the list.')?>
        </label>
    </div>
</div>

<div class="form-group">
    <label><?=t("User Submissions")?></label>
    <div class="form-check">
        <?=$form->checkbox('akSelectAllowOtherValues', 1, $akSelectAllowOtherValues)?>
        <label for="akSelectAllowOtherValues" class="form-check-label">
             <?=t('Allow users to add to this list.')?>
        </label>
    </div>
</div>

<div class="form-group">
<label for="akSelectOptionDisplayOrder"><?=t("Option Order")?></label>
	<?php
    $displayOrderOptions = array(
        'display_asc' => t('Display Order'),
        'alpha_asc' => t('Alphabetical'),
        'popularity_desc' => t('Most Popular First'),
    );
    ?>

	<?=$form->select('akSelectOptionDisplayOrder', $displayOrderOptions, $akSelectOptionDisplayOrder)?>
</div>

<div class="clearfix">
<label><?=t('Values')?></label>
<div class="input">
	<div id="attributeValuesInterface">
	<div id="attributeValuesWrap">
	<?php
    Loader::helper('text');
    foreach ($akSelectValues as $v) {
        if ($v->getSelectAttributeOptionID() != false) {
            $akSelectValueID = $v->getSelectAttributeOptionID();
        } else {
            $akSelectValueID = uniqid();
        }
        ?>
		<div id="akSelectValueWrap_<?=$akSelectValueID?>" class="akSelectValueWrap akSelectValueWrapSortable">
			<?=getAttributeOptionHTML($v)?>
		</div>
	<?php
    } ?>
	</div>
	
	<div id="akSelectValueWrapTemplate" class="akSelectValueWrap" style="display:none">
		<?=getAttributeOptionHTML('TEMPLATE') ?>
	</div>
	
	<div id="addAttributeValueWrap" class="row row-cols-auto g-0 align-items-center">
        <div class="col-auto">
            <input id="akSelectValueFieldNew" name="akSelectValueNew" type="text" value="<?=$defaultNewOptionNm ?>" size="40"  class="form-control"
                   onfocus="ccmAttributesHelper.clrInitTxt(this,'<?=$defaultNewOptionNm ?>','faint',0)"
                   onblur="ccmAttributesHelper.clrInitTxt(this,'<?=$defaultNewOptionNm ?>','faint',1)"
                   onkeypress="ccmAttributesHelper.keydownHandler(event);"
            />
        </div>
        <div class="col-auto">
            <input class="btn btn-primary" type="button" onClick="ccmAttributesHelper.saveNewOption(); $('#ccm-attribute-key-form').unbind()" value="<?=t('Add') ?>" />
        </div>
	</div>
	</div>

</div>
</div>


</fieldset>
<script type="text/javascript">

	var ccmAttributesHelper={
		valuesBoxDisabled:function(typeSelect){
			var attrValsInterface=document.getElementById('attributeValuesInterface')
			var requiredVals=document.getElementById('reqValues');
			var allowOther=document.getElementById('allowOtherValuesWrap');
			var offMsg=document.getElementById('attributeValuesOffMsg');
			if (typeSelect.value == 'SELECT' || typeSelect.value == 'SELECT_MULTIPLE') {
				attrValsInterface.style.display='block';
				requiredVals.style.display='inline';
				if(allowOther) allowOther.style.display='block';
				offMsg.style.display='none';
			} else {
				requiredVals.style.display='none';
				attrValsInterface.style.display='none';
				if(allowOther) allowOther.style.display='none';
				offMsg.style.display='block';
			}
		},

		deleteValue:function(val){
			if(confirm(ccmi18n.deleteAttributeValue)) {
				$('#akSelectValueWrap_'+val).remove();
			}
		},

		editValue:function(val){
			if($('#akSelectValueDisplay_'+val).css('display')!='none'){
				$('#akSelectValueDisplay_'+val).css('display','none');
				$('#akSelectValueEdit_'+val).css('display','block').find('input[type="text"]').focus();
			}else{
				$('#akSelectValueDisplay_'+val).css('display','block');
				$('#akSelectValueEdit_'+val).css('display','none');
				var txtValue =  $('#akSelectValueStatic_'+val).html();
				$('#akSelectValueField_'+val).val( $('<div/>').html(txtValue).text());
			}
		},

		changeValue:function(val){
			var txtValue = $('<div/>').text($('#akSelectValueField_'+val).val()).html();
			$('#akSelectValueStatic_'+val).html( txtValue );
			this.editValue(val)
		},

		makeSortable: function() {
			$("div#attributeValuesWrap").sortable({
				cursor: 'move',
				opacity: 0.5
			});
		},

		saveNewOption:function(){
			var newValF=$('#akSelectValueFieldNew');
			var val = $('<div/>').text(newValF.val()).html();
			if(val=='') {
				return;
			}
			var ts = 't' + new Date().getTime();
			var template=document.getElementById('akSelectValueWrapTemplate');
			var newRowEl=document.createElement('div');
			newRowEl.innerHTML=template.innerHTML.replace(/template_clean/ig,ts).replace(/template/ig,val);
			newRowEl.id="akSelectValueWrap_"+ts;
			newRowEl.className='akSelectValueWrap akSelectValueWrapSortable';
			$('#attributeValuesWrap').append(newRowEl);
			newValF.val('');
		},

		clrInitTxt:function(field,initText,removeClass,blurred){
			if(blurred && field.value==''){
				field.value=initText;
				$(field).addClass(removeClass);
				return;
			}
			if(field.value==initText) field.value='';
			if($(field).hasClass(removeClass)) $(field).removeClass(removeClass);
		},

		keydownHandler:function(event){
			var form = $("#ccm-attribute-key-form");
			switch (event.keyCode) {
				case 13: // enter
					event.preventDefault();
					if (event.currentTarget.id === 'akSelectValueFieldNew') { // if the event originates from the "add" input field, create the option
						ccmAttributesHelper.saveNewOption();
					} else { // otherwise just fire the existing option save
						ccmAttributesHelper.changeValue(event.currentTarget.getAttribute('data-select-value-id'));
					}
					break;
				case 38: // arrow up
				case 40: // arrow down
					ccmAttributesHelper.changeValue(event.currentTarget.getAttribute('data-select-value-id'));
					var find = (event.keyCode === 38) ? 'prev' : 'next';
					var $target = $(event.currentTarget).closest('.akSelectValueWrap')[find]();
					if ($target.length) {
						$target.find('.leftCol').click();
					} else if (find === 'next') {
						$('#akSelectValueFieldNew').focus();
					}
					break;
			}
		},

		// legacy stub method
		addEnterClick:function(){
			ccmAttributesHelper.keydownHandler.apply(this, arguments);
		}

	}

	$(function() {
		$('input[name=akSelectAllowMultipleValues]').on('change', function() {
			if ($(this).is(':checked')) {
				$('div[data-group=single-value]').hide();
			} else {
				$('div[data-group=single-value]').show();
			}
		}).trigger('change');
		ccmAttributesHelper.makeSortable();
        <?php
        $max_input_vars = (int) @ini_get('max_input_vars');
        if ($max_input_vars > 0) {
            ?>
            var $form = $("#ccm-attribute-key-form");
            $form.on('submit', function(e) {
                var numFields = $form.find(':input').length;
                if (numFields > <?=$max_input_vars?>) {
                    alert(
                        <?=json_encode(t(
<<<'EOT'
Your current PHP configuration does not allow to save so many tags.

You should increase the value of the %1$s option in your php.ini.

The current value of this option is %2$s, but this form requires at least a value of %3$s.
EOT
                        , 'max_input_vars', $max_input_vars, '[[CURRENT_NUMBER_OF_FIELDS]]'))?>.replace('[[CURRENT_NUMBER_OF_FIELDS]]', numFields.toString())
                    );
                    e.preventDefault();
                }
            });
            <?php
        }
        ?> 
	});

</script>
