<?php defined('C5_EXECUTE') or die('Access Denied.');?>
<form method="post" class="ccm-dashboard-content-form" action="<?=$view->action('save_association', $entity->getID())?>">
    <input type="hidden" name="association_id" value="<?=$association->getID()?>">

    <?=$token->output()?>

    <div class="form-group">
        <label for="name" class="control-label"><?=t('Target Property Name')?></label>
        <?=$form->text('target_property_name', $association->getTargetPropertyName())?>
    </div>

    <div class="form-group">
        <label for="name" class="control-label"><?=t('Inversed Property Name')?></label>
        <?=$form->text('inversed_property_name', $association->getInversedByPropertyName())?>
    </div>

    <div class="form-group">
        <label class="control-label"><?=t('Owning Association')?></label>
        <div class="radio">
            <label><?=$form->radio('is_owning_association', 1, $association->isOwningAssociation())?> <?=t('Yes')?></label>
        </div>
        <div class="radio">
            <label><?=$form->radio('is_owning_association', 0, $association->isOwningAssociation())?> <?=t('No')?></label>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label"><?=t('Owned By Association')?></label>
        <div class="radio">
            <label><?=$form->radio('is_owned_by_association', 1, $association->isOwnedByAssociation())?> <?=t('Yes')?></label>
        </div>
        <div class="radio">
            <label><?=$form->radio('is_owned_by_association', 0, $association->isOwnedByAssociation())?> <?=t('No')?></label>
        </div>
    </div>



    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <a href="<?=URL::to('/dashboard/system/express/entities/associations', 'view_association_details', $association->getID())?>" class="pull-left btn btn-default" type="button" ><?=t('Back to Association')?></a>
            <button class="pull-right btn btn-primary" type="submit" ><?=t('Save')?></button>
        </div>
    </div>
</form>