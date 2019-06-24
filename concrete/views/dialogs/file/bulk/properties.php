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
        'objects' => $files,
        'saveAction' => $controller->action('update_attribute'),
        'clearAction' => $controller->action('clear_attribute'),
        'permissionsCallback' => function ($ak, $permissionsArguments) {
            return true; // this is fine because you can't even access this interface without being able to edit every file.
        },
    ));?>
</section>

<script type="text/javascript">
	$('div[data-container=editable-fields]').concreteEditableFieldContainer({
		data: [
			<?php foreach ($files as $f) {
    ?>
				{'name': 'fID[]', 'value': '<?=$f->getFileID()?>'},
			<?php 
} ?>	
		]
	});
</script>

</div>
</div>