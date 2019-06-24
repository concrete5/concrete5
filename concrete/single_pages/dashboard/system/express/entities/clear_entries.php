<?php defined('C5_EXECUTE') or die('Access Denied.');?>

<div class="ccm-dashboard-header-buttons">

    <?php
    $manage = new \Concrete\Controller\Element\Dashboard\Express\Menu($entity);
    $manage->render();
    ?>

</div>


<div class="row">
    <?php View::element('dashboard/express/detail_navigation', array('entity' => $entity))?>
    <div class="col-md-8">
        <form action="<?=$view->action('delete_entries')?>" method="post">
            <br>
            <h2><?= t('Are you sure you want to clear all entries?') ?></h2>
            <h4 class="text-danger"><?= t('This process cannot be undone.') ?></h4>
            <br>
            <div class="form-group text-center">
                <?= $token->output('clear_entries')?>
                <?= $form->hidden('entity_id', $entity->getId()) ?>
                <a href="<?=URL::to('/dashboard/system/express/entities', 'view_entity', $entity->getId())?>" class="btn btn-danger"><?= t('Cancel') ?></a>
                <button type="submit" class="btn btn-primary"><?= t('Clear Entity Entries') ?></button>
            </div>
        </form>
    </div>
</div>
