<?php
    defined('C5_EXECUTE') or die('Access Denied.');
    $this->inc('form.php', array('b' => $b, 'a' => $a));

?>

<input type="hidden" name="arLayoutID" value="<?=$controller->arLayout->getAreaLayoutID()?>" />
<input type="hidden" name="arLayoutEdit" value="1" />

	<div id="ccm-layouts-edit-mode" class="ccm-layouts-edit-mode-edit">

	<?php
    $container = $formatter->getLayoutContainerHtmlObject();
    foreach ($columns as $column) {
        $html = $column->getColumnHtmlObjectEditMode();
        $container->appendChild($html);
    }
    echo $container;

    ?>

	</div>
