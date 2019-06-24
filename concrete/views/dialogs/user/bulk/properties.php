<?php
defined('C5_EXECUTE') or die('Access Denied.');
?>

<div class="ccm-ui">
<div data-container="editable-fields">

<section>
	<h4><?=t('Other Attributes')?></h4>
	<?php

    Loader::element('attribute/editable_list', array(
        'attributes' => $attributes,
        'objects' => $users,
        'saveAction' => $controller->action('update_attribute'),
        'clearAction' => $controller->action('clear_attribute'),
        'permissionsArguments' => array('attributes' => $allowedEditAttributes),
        'permissionsCallback' => function ($ak, $permissionsArguments) {
            return is_array($permissionsArguments['attributes']) && in_array($ak->getAttributeKeyID(), $permissionsArguments['attributes']);
        },
    ));?>
</section>

<script type="text/javascript">
	$('div[data-container=editable-fields]').concreteEditableFieldContainer({
		data: [
			<?php foreach ($users as $ui) {
    ?>
				{'name': 'item[]', 'value': '<?=$ui->getUserID()?>'},
			<?php 
} ?>	
		]
	});
</script>

</div>
</div>