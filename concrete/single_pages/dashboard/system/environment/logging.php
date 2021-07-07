<?php
defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Form\Service\Form $form
 * @var Concrete\Core\Page\View\PageView $view
 * @var Concrete\Core\Validation\CSRF\Token $token
 * @var array $handlers
 * @var array $levels
 * @var bool $enableDashboardReport
 * @var bool $intLogApi
 * @var bool $intLogEmails
 * @var bool $intLogErrors
 * @var string $coreLoggingLevel
 * @var string $handler
 * @var string $logFile
 * @var string $loggingMode
 */

?>

<form id="ccm-system-logging" method="post" action="<?= $view->action('update_logging'); ?>">
	<?php $token->output('update_logging'); ?>

    <fieldset>
        <legend><?=t('Mode'); ?></legend>
        <div class="form-group">
            <div class="form-check">
                <?= $form->radio('logging_mode', 'simple', $loggingMode, ['v-model' => 'loggingMode']); ?>
                <?= $form->label('logging_mode1', t('Simple'), ['class' => 'form-check-label']); ?>
            </div>

            <p class="help-block">
                <?= t('Logs to the database (accessible via the <a href="%s">Logs Dashboard Page</a>) or to a file. All custom logging that uses the "application" channel is saved, but any core logs below the threshold level below are discarded.', URL::to('/dashboard/reports/logs')); ?>
            </p>

            <div v-show="loggingMode == 'simple'" v-cloak>
                <div class="form-group">
                    <?= $form->label('logging_level', t('Core Logging Level')); ?>
                    <?= $form->select('logging_level', $levels, $coreLoggingLevel); ?>
                </div>

                <div class="form-group">
                    <?= $form->label('handler', t('Handler')); ?>
                    <?= $form->select('handler', $handlers, $handler, ['v-model' => 'handler']); ?>
                </div>

                <div class="form-group" v-show="handler == 'file'">
                    <?= $form->label('logFile', t('File')); ?>
                    <?= $form->text('logFile', $logFile); ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="form-check">
                <?= $form->radio('logging_mode', 'advanced', $loggingMode, ['v-model' => 'loggingMode']); ?>
                <?= $form->label('logging_mode2', t('Advanced'), ['class' => 'form-check-label']); ?>
            </div>

            <p class="help-block">
                <?= t(
                    // i18n: %1$s and %3$s are configuration keys, %2$s is an URL
                    'Loads a custom configuration array from %1$s. Define your PHP array in the <a href="%2$s" target="_blank">Monolog Cascade</a> format. <b>Note:</b> unless you specify %3$s within your advanced configuration array, simple configuration will be used.',
                    '<code>concrete.log.configuration.advanced.configuration</code>',
                    'https://github.com/theorchard/monolog-cascade/blob/master/README.md',
                    '<code>loggers</code>'
                ); ?>
            </p>
        </div>
    </fieldset>

    <fieldset>
        <legend><?= t('Channel Logging'); ?></legend>
        <div class="form-group">
            <div class="form-check">
                <?= $form->checkbox('ENABLE_LOG_ERRORS', 1, $intLogErrors); ?>
                <?= $form->label('ENABLE_LOG_ERRORS', t('Log Application Exceptions'), ['class' => 'form-check-label']); ?>
            </div>

            <div class="form-check">
                <?= $form->checkbox('ENABLE_LOG_EMAILS', 1, $intLogEmails); ?>
                <?= $form->label('ENABLE_LOG_EMAILS', t('Log Emails Sent'), ['class' => 'form-check-label']); ?>
            </div>

            <div class="form-check">
                <?= $form->checkbox('ENABLE_LOG_API', 1, $intLogApi); ?>
                <?= $form->label(
                    'ENABLE_LOG_API',
                    t('Log API request headers') .
                    ' <i class="fa fa-question-circle launch-tooltip" title="' .
                    t('The logging level needs to be set to the value: Debug') . '"></i>',
                    ['class' => 'form-check-label']
                ); ?>
            </div>
        </div>
    </fieldset>

    <fieldset>
        <legend><?=t('Reporting'); ?></legend>
        <div class="form-group">
            <div class="form-check">
                <?= $form->checkbox('enable_dashboard_report', 1, $enableDashboardReport); ?>
                <?= $form->label('enable_dashboard_report', t('Enable Dashboard Logs Report'), ['class' => 'form-check-label']); ?>
            </div>

            <p class="help-block">
                <?= t('Enables or disables the Dashboard Logs Page – useful if your logging configuration no longer uses database logging.'); ?>
            </p>
        </div>
    </fieldset>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <button class="btn btn-primary float-end" type="submit"><?= t('Save'); ?></button>
        </div>
	</div>
</form>

<script>
$(function () {
    Concrete.Vue.activateContext('cms', function (Vue, config) {
        new Vue({
            el: '#ccm-system-logging',
            data: {
                loggingMode: document.querySelector('input[name="logging_mode"]:checked').value,
                handler: document.getElementById('handler').value,
            },
        });
    });
});
</script>
