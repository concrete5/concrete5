<?php
defined('C5_EXECUTE') or die('Access Denied.');

?>
<div class="ccm-dashboard-header-buttons btn-group">
    <a href="<?= $view->action('update') ?>" class="btn btn-secondary"><?= t('Install/Update Languages') ?></a>
</div>
<?php

if (empty($interfacelocales)) {
    ?>
    <fieldset>
	   <?= t("You don't have any interface languages installed. You must run concrete5 in English.") ?>
    </fieldset>
    <?php
} else {
    ?>
    <form method="post" action="<?= $view->action('save_interface_language') ?>">
        <fieldset>
            <div class="form-group">
                <?= $form->label('', t('Login')) ?>
                <div class="form-check">
                    <?= $form->checkbox('LANGUAGE_CHOOSE_ON_LOGIN', 1, $LANGUAGE_CHOOSE_ON_LOGIN) ?>&nbsp;
                    <?= $form->label('LANGUAGE_CHOOSE_ON_LOGIN',t('Offer choice of language on login.'), ['class'=>'form-check-label']) ?>
                </div>
            </div>
            <div class="form-group">
                <?= $form->label('SITE_LOCALE', t('Default Language')) ?>

                    <?= $form->select('SITE_LOCALE', $interfacelocales, $SITE_LOCALE) ?>
            </div>
            <?php $token->output('save_interface_language') ?>
        </fieldset>
        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
                <?= Core::make('helper/concrete/ui')->submit(t('Save'), 'save', 'right', 'btn-primary') ?>
            </div>
        </div>
    </form>
    <?php
}

if (isset($mlLink)) {
    ?>
    <div class="ms-3 alert alert-info small">
        <?= t(
            'You can configure the site languages in the %s dashboard page.',
            sprintf('<a href="%s">%s</a>', h($mlLink[1]), h($mlLink[0]))
        ) ?>
    </div>
    <?php
}
