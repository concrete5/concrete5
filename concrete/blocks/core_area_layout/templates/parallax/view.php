<?php defined('C5_EXECUTE') or die('Access Denied.');
$a = $b->getBlockAreaObject();
$container = $formatter->getLayoutContainerHtmlObject();
$background = '';
$style = $b->getCustomStyle();
if (is_object($style)) {
    $set = $style->getStyleSet();
    $image = $set->getBackgroundImageFileObject();
    if (is_object($image)) {
        $background = $image->getRelativePath();
        if (!$background) {
            $background = $image->getURL();
        }
    }
}

?>

<div data-stripe-wrapper="parallax" data-background-image="<?= $background ?>">
    <?php
    foreach ($columns as $column) {
        $html = $column->getColumnHtmlObject();
        $container->appendChild($html);
    }

    echo $container;
    ?>
</div>

