<?php defined('C5_EXECUTE') or die("Access Denied.");?>

<?php
if (isset($attributeHeader)) {
    $attributeHeader->render();
}
?>

<div class="row">
    <? View::element('dashboard/express/detail_navigation', array('entity' => $entity))?>
    <div class="col-md-8">
        <?php
        $attributeView->render();
        ?>
    </div>
</div>
