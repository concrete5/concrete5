<?php defined('C5_EXECUTE') or die('Access Denied.');

$jh = Core::make('helper/json');
?>

<?php if (is_object($tree)) {
    ?>
	<div data-tree="<?=$tree->getTreeID()?>">
	</div>

	<script type="text/javascript">
	$(function() {

		$('[data-tree]').concreteTree({
			'treeID': '<?=$tree->getTreeID()?>'
		});

	});
	</script>

    <?php
} ?>