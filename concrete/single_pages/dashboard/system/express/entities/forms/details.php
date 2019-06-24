<?php defined('C5_EXECUTE') or die('Access Denied.');?>
<form method="post" class="ccm-dashboard-content-form" action="<?=$view->action('save')?>">
    <input type="hidden" name="entity_id" value="<?=$entity->getID()?>">

    <?php if (isset($expressForm)) {
    ?>
        <input type="hidden" name="form_id" value="<?=$expressForm->getID()?>">
    <?php 
} ?>

    <?=$token->output()?>

    <fieldset>
        <div class="form-group">
            <label for="name"><?=t('Name')?></label>
            <?=$form->text('name', $name)?>
        </div>
    </fieldset>
    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <a href="<?=URL::to('/dashboard/system/express/entities/forms', $entity->getID())?>" class="pull-left btn btn-default" type="button" ><?=t('Back to Forms')?></a>
            <button class="pull-right btn btn-primary" type="submit" ><?=t('Save')?></button>
        </div>
    </div>
</form>