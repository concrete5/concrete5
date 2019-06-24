<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>
<?php
$options = $this->controller->getOptions();
if ($akSelectAllowMultipleValues) {
    ?>

	<?php foreach ($options as $opt) {
    ?>
		<div class="checkbox"><label><input type="checkbox" name="<?=$this->field('atSelectOptionID')?>[]" value="<?=$opt->getSelectAttributeOptionID()?>" <?php if (in_array($opt->getSelectAttributeOptionID(), $selectedOptions)) {
    ?> checked <?php 
}
    ?> /><?=$opt->getSelectAttributeOptionDisplayValue()?></label></div>
	<?php 
}
    ?>

<?php 
} else {
    ?>
	<select class="form-control" name="<?=$this->field('atSelectOptionID')?>[]">
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
