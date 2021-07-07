<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php
$options = $this->controller->getOptions();
if ($akSelectAllowMultipleValues) {
    $index = 1;
    ?>

	<?php foreach ($options as $opt) {
    ?>
		<div class="form-check"><input class="form-check-input" type="checkbox" id="<?=$this->field('atSelectOptionID')?>[]_<?=$index?>" name="<?=$this->field('atSelectOptionID')?>[]" value="<?=$opt->getSelectAttributeOptionID()?>" <?php if (in_array($opt->getSelectAttributeOptionID(), $selectedOptions)) {
    ?> checked <?php 
}
    ?> /><label class="form-check-label" for="<?=$this->field('atSelectOptionID')?>[]_<?=$index?>"><?=$opt->getSelectAttributeOptionDisplayValue()?></label></div>
	<?php
        $index++;
}
    ?>

<?php 
} else {
    ?>
	<select class="form-select" name="<?=$this->field('atSelectOptionID')?>[]">
		<option value=""><?=t('** All')?></option>
	<?php foreach ($options as $opt) {
    ?>
		<option value="<?=$opt->getSelectAttributeOptionID()?>" <?php if (in_array($opt->getSelectAttributeOptionID(), $selectedOptions)) {
    ?> selected <?php 
}
    ?>><?=$opt->getSelectAttributeOptionDisplayValue()?></option>	
	<?php 
}
    ?>
	</select>

<?php 
}
