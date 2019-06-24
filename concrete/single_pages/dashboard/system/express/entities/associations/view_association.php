<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<div class="ccm-dashboard-header-buttons btn-group">
    <a href="<?=URL::to('/dashboard/system/express/entities/associations', $entity->getID())?>" class="btn btn-default"><?=t("Back to Object")?></a>
    <a href="<?=URL::to('/dashboard/system/express/entities/associations', 'edit', $association->getID())?>" class="btn btn-default"><?=t("Edit Details")?></a>
    <button type="button" class="btn btn-danger" data-action="delete-association"><?= t('Delete Association') ?></button>
</div>

<div style="display: none">
    <div id="ccm-dialog-delete-association" class="ccm-ui">
        <form method="post" action="<?=$view->action('delete_association', $entity->getID())?>">
            <?=Core::make("token")->output('delete_association')?>
            <input type="hidden" name="association_id" value="<?=$association->getID()?>">
            <p><?=t('Are you sure you want to delete this association? This cannot be undone.')?></p>
            <div class="dialog-buttons">
                <button class="btn btn-default pull-left" onclick="jQuery.fn.dialog.closeTop()"><?=t('Cancel')?></button>
                <button class="btn btn-danger pull-right" onclick="$('#ccm-dialog-delete-association form').submit()"><?=t('Delete Association')?></button>
            </div>
        </form>
    </div>
</div>


<script type="text/javascript">
    $(function() {
        $('button[data-action=delete-association]').on('click', function() {
            var $element = $('#ccm-dialog-delete-association');
            jQuery.fn.dialog.open({
                element: $element,
                modal: true,
                width: 320,
                title: '<?=t('Delete Association')?>',
                height: 'auto'
            });
        });
    });
</script>

<h3><?=$formatter->getDisplayName()?></h3>

<h4><?=t('Type')?></h4>
<p><?=$formatter->getTypeDisplayName()?></p>

<h4><?=t('Source Object')?></h4>
<p><a href="<?=URL::to('/dashboard/system/express/entities', 'view_entity', $entity->getID())?>"><?=$entity->getEntityDisplayName()?></a></p>

<h4><?=t('Inversed Property Name')?></h4>
<p><?=$association->getInversedByPropertyName()?></p>

<h4><?=t('Target Object')?></h4>
<p><a href="<?=URL::to('/dashboard/system/express/entities', 'view_entity', $association->getTargetEntity()->getID())?>"><?=$association->getTargetEntity()->getEntityDisplayName()?></a></p>

<h4><?=t('Target Property Name')?></h4>
<p><?=$association->getTargetPropertyName()?></p>

<?php if ($association->isOwningAssociation()) { ?>
<h4><?=t('Association Type')?></h4>
<p><?=t('Owning')?></p>
    <?php } else if ($association->isOwnedByAssociation()) { ?>
    <h4><?=t('Association Type')?></h4>
    <p><?=t('Owned By')?></p>

<?php } ?>