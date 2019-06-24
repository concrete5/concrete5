<?php defined('C5_EXECUTE') or die('Access Denied.');?>

<?php
if (!isset($headerMenu) || !is_object($headerMenu)) { ?>

<div class="ccm-dashboard-header-buttons">


    <?php
    $manage = new \Concrete\Controller\Element\Dashboard\Express\Menu($entity);
    $manage->render();
    ?>

</div>

<?php
} ?>


<div class="row">
    <?php View::element('dashboard/express/detail_navigation', array('entity' => $entity))?>
    <div class="col-md-8">
        <?php
        $attributeView->render();
        ?>
    </div>
</div>
