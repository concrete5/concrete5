<?php
defined('C5_EXECUTE') or die('Access Denied.');
?>

<div class="ccm-ui">
<div data-container="editable-fields">

<section>
	<h4><?=t('Page Attributes')?></h4>
	<?php

    Loader::element('attribute/editable_list', array(
        'attributes' => $attributes,
        'objects' => $pages,
        'saveAction' => $controller->action('update_attribute'),
        'clearAction' => $controller->action('clear_attribute'),
        'permissionsCallback' => function ($ak, $permissionsArguments) {
            return true;
        },
    ));?>
</section>

<script type="text/javascript">
	$('div[data-container=editable-fields]').concreteEditableFieldContainer({
		data: [
			<?php foreach ($pages as $c) {
    ?>
				{'name': 'item[]', 'value': '<?=$c->getCollectionID()?>'},
			<?php 
} ?>	
		]
	});
</script>

</div>
</div>