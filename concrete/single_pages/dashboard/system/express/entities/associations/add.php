<?php defined('C5_EXECUTE') or die('Access Denied.');?>
<form method="post" class="ccm-dashboard-content-form" action="<?=$view->action('add', $entity->getID())?>">
    <?=$token->output('add_association')?>

    <fieldset>
        <div class="form-group">
            <label class="control-label" for="name"><?=t('Source Object')?></label>
            <p><?=$entity->getEntityDisplayName()?></p>
        </div>
        <div class="form-group">
            <label class="control-label" for="name"><?=t('Type')?></label>
            <?=$form->select('type', $types)?>
        </div>
        <div class="form-group">
            <label class="control-label" for="name"><?=t('Target Object')?></label>
            <select name="target_entity" class="form-control">
                <?php foreach($entities as $targetEntity) { ?>
                    <option value="<?=$targetEntity->getID()?>" data-plural="<?=$targetEntity->getPluralHandle()?>" data-singular="<?=$targetEntity->getHandle()?>"><?=$targetEntity->getEntityDisplayName()?></option>
                <?php } ?>
            </select>
        </div>
        <div class="form-group">
            <label class="control-label" for="name"><?=t('Target Property Name')?></label>
            <div class="input-group">
                <span class="input-group-addon">
                    <?=$form->checkbox('overrideTarget', 1, false, ['data-toggle' => 'association-property'])?>
                </span>
                <input name="target_property_name" type="hidden" value="" />
                <?=$form->text('target_property_name', '', ['disabled' => 'disabled'])?>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label" for="name"><?=t('Inversed Property Name')?></label>
            <div class="input-group">
                <span class="input-group-addon">
                    <?=$form->checkbox('overrideInverse', 1, false, ['data-toggle' => 'association-property'])?>
                </span>
                <input name="inversed_property_name" type="hidden" value="<?=$entity->getHandle()?>" />
                <?=$form->text('inversed_property_name', $entity->getHandle(), ['disabled' => 'disabled'])?>
            </div>
        </div>
    </fieldset>
    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <a href="<?=URL::to('/dashboard/system/express/entities/associations', $entity->getId())?>"
               class="pull-left btn btn-default" type="button" ><?=t('Back to Associations')?></a>
            <button class="pull-right btn btn-primary" type="submit" ><?=t('Save')?></button>
        </div>
    </div>
</form>

<script type="text/javascript">
    $(function() {
        $('input[data-toggle=association-property]').on('change', function() {
            var disabled;
            if ($(this).is(':checked')) {
                disabled = false;
            } else {
                disabled = true;
                $('select[name=target_entity]').trigger('change');
            }
            $(this).closest('.form-group').find('.form-control').prop('disabled', disabled);
        }).trigger('change');
        $('select[name=target_entity],select[name=type]').on('change', function() {
            if ($('select[name=type]').val() == 'OneToMany' || $('select[name=type]').val() == 'ManyToMany') {
                var value = $('select[name=target_entity]').find('option:selected').attr('data-plural');
            } else {
                var value = $('select[name=target_entity]').find('option:selected').attr('data-singular');
            }
            $('input[name=target_property_name]').val(value);

            if ($('select[name=type]').val() == 'ManyToMany' || $('select[name=type]').val() == 'ManyToOne') {
                var value = '<?=$entity->getPluralHandle()?>';
            } else {
                var value = '<?=$entity->getHandle()?>';
            }
            $('input[name=inversed_property_name]').val(value);

        }).trigger('change');
    });
</script>