<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>
<form method="post" class="ccm-dashboard-content-form" action="<?= $view->action('add') ?>">
    <?= $token->output('add_entity') ?>

    <div class="form-group <?php if ($error->containsField('name')) { ?>has-error<?php } ?>">
        <label for="name" class="control-label"><?= t('Name') ?></label>
        <div class="input-group">
            <?= $form->text('name', '', ['autofocus' => 'autofocus']) ?>
            <span class="input-group-addon"><i class="fa fa-asterisk"></i></span>
        </div>
        <p class="help-block"><?= t('The name is how your entity will appear in the Dashboard.') ?></p>
    </div>
    <div class="form-group <?php if ($error->containsField('handle')) { ?>has-error<?php } ?>">
        <label for="name" class="control-label"><?= t('Handle') ?></label>
        <div class="input-group">
            <?= $form->text('handle') ?>
            <span class="input-group-addon"><i class="fa fa-asterisk"></i></span>
        </div>
        <p class="help-block"><?= t('A unique string consisting of lowercase letters and underscores only.') ?></p>
    </div>
    <div class="form-group">
        <label for="name" class="control-label"><?= t('Plural Handle') ?></label>
        <?= $form->text('plural_handle') ?>
        <p class="help-block"><?= t('The plural representation of the handle above. Used to retrieve this entity if it is used in associations.') ?></p>
    </div>
    <div class="form-group">
        <label for="name" class="control-label"><?= t('Name Mask') ?></label>
        <?= $form->text('label_mask') ?>
        <p class="help-block"><?= t('Example <code>Entry %name%</code> or <code>Complaint %date% at %hotel%</code>') ?></p>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="headingThree">
            <h4 class="panel-title">
                <a class="collapsed" role="button" data-toggle="collapse" href="#advanced"><?= t('Advanced Options') ?>
                </a>
            </h4>
        </div>
        <div id="advanced"
             class="panel-collapse collapse <?php if ($controller->getRequest()->request->get('description') || $controller->getRequest()->request('supports_custom_display_order') || $controller->getRequest()->request->get('owned_by')) { ?>in <?php } ?>">
            <div class="panel-body">
                <div class="form-group">
                    <label for="name" class="control-label"><?=t('Description')?></label>
                    <?=$form->textarea('description', array('rows' => 5))?>
                    <p class="help-block"><?=t('An internal description. This is not publicly displayed.')?></p>
                </div>
                <div class="form-group">
                    <label for="name" class="control-label"><?= t('Custom Display Order') ?></label>
                    <div class="checkbox"><label>
                            <?= $form->checkbox('supports_custom_display_order', 1) ?>
                            <?= t('This entity supports custom display ordering via Dashboard interfaces.') ?>
                        </label></div>
                </div>
                <div class="form-group">
                    <label for="name" class="control-label"><?= t('Owned By') ?></label>
                    <?= $form->select('owned_by', $entities) ?>
                </div>
                <div class="form-group" style="display: none" data-group="owned_by_type">
                    <label for="name" class="control-label"><?= t('Owning Type') ?></label>
                    <?= $form->select('owning_type', array(
                       'many' => t('Many'),
                    'one' => t('One')
                    )); ?>
                </div>
            </div>
        </div>
    </div>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <a href="<?= URL::to('/dashboard/system/express/entities') ?>" class="pull-left btn btn-default"
               type="button"><?= t('Back to List') ?></a>
            <button class="pull-right btn btn-primary" type="submit"><?= t('Save') ?></button>
        </div>
    </div>
</form>

<script type="text/javascript">
    $(function() {
        $('select[name=owned_by]').on('change', function() {
            if ($(this).val()) {
                $('div[data-group=owned_by_type]').show();
            } else {
                $('div[data-group=owned_by_type]').hide();
            }
        }).trigger('change');
    });
</script>
