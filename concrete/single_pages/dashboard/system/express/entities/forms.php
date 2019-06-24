<?php defined('C5_EXECUTE') or die('Access Denied.');?>

<div class="ccm-dashboard-header-buttons btn-group">


    <?php
    $manage = new \Concrete\Controller\Element\Dashboard\Express\Menu($entity);
    $manage->render();
    ?>

    <a href="<?=URL::to('/dashboard/system/express/entities/forms', 'add', $entity->getId())?>" class="btn btn-primary">
        <?=t("Add Form")?>
    </a>

</div>


<div class="row">
    <?php View::element('dashboard/express/detail_navigation', array('entity' => $entity))?>
    <div class="col-md-8">

        <?php if (count($forms)) {
    ?>

            <ul class="item-select-list" id="ccm-stack-list">
                <?php foreach ($forms as $form) {
    ?>
                    <li>
                        <a href="<?=URL::to('/dashboard/system/express/entities/forms', 'view_form_details', $form->getID())?>">
                            <i class="fa fa-list-alt"></i> <?=h($form->getName())?>
                        </a>
                    </li>
                <?php 
}
    ?>
            </ul>

        <?php

} else {
    ?>
            <p><?=t('You have not created any forms.')?></p>
        <?php

} ?>


    </div>
</div>
