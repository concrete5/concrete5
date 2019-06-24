<?php
defined('C5_EXECUTE') or die('Access Denied.');
?>

<div data-search="pages" class="ccm-ui">

	<?php
	$header->render();
	?>

	<?php Loader::element('pages/search', array('result' => $result))?>

</div>

<script type="text/javascript">
	$(function() {
		$('div[data-search=pages]').concretePageAjaxSearch({
			result: <?=json_encode($result->getJSONObject())?>,
			mode: 'choose'
		});
	});
</script>