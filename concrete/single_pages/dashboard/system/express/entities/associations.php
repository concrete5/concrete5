<?php defined('C5_EXECUTE') or die('Access Denied.');?>

<div class="ccm-dashboard-header-buttons btn-group">


    <?php
    $manage = new \Concrete\Controller\Element\Dashboard\Express\Menu($entity);
    $manage->render();
    ?>

    <a href="<?=URL::to('/dashboard/system/express/entities/associations', 'add', $entity->getId())?>" class="btn btn-primary">
        <?=t("Add Association")?>
    </a>

</div>

<div class="row">
    <?php View::element('dashboard/express/detail_navigation', array('entity' => $entity))?>
    <div class="col-md-8">

        <?php if (count($associations)) {
    ?>

            <ul class="item-select-list" id="ccm-stack-list">
                <?php foreach ($associations as $association) {
    $formatter = $association->getFormatter();
    ?>

                    <li>
                        <a href="<?=URL::to('/dashboard/system/express/entities/associations', 'view_association_details', $association->getID())?>">
                            <?=$formatter->getIcon()?> <?=$formatter->getDisplayName()?>
                        </a>
                    </li>
                <?php 
}
    ?>
            </ul>

        <?php

} else {
    ?>
            <p><?=t('You have not created any associations.')?></p>
        <?php

} ?>


    </div>
</div>
