<?php
defined('C5_EXECUTE') or die('Access Denied.');
?>
<form method="POST" id="user-timezone-form" action="<?= $view->action('update') ?>">
    <?php $token->output('update_timezone') ?>

    <fieldset>
        <legend><?=t('Server Configuration'); ?></legend>

        <div class="form-group">
            <label class="control-label form-label">
                <?php echo t('PHP Setting'); ?>
            </label>

            <div><?= h($serverTimezonePHP); ?></div>
        </div>
        <div class="form-group">
            <label class="control-label form-label">
                <?php echo t('Database Setting'); ?>
            </label>

            <div><?= h($serverTimezoneDB); ?></div>
        </div>

        <div class="form-group">
            <label class="control-label launch-tooltip form-label" data-bs-placement="right" title="<?= t(
    'These two values must match, otherwise there will be date inconsistencies.'
); ?>">
                <?php echo t('Status'); ?>
            </label>
            <div>
                <?php if (!$dbTimezoneOk) {
                ?>
                    <p class="alert alert-warning"><i class="fa fa-exclamation-triangle"></i>
                        <?= $dbDeltaDescription; ?>
                    </p>
                    <p>
                        <a href="#" id="user-timezone-autofix" class="btn btn-warning btn-sm"><?=t('Fix PHP timezone'); ?></a>
                    </p>
                <?php
            } else {
                ?>
                    <div class="text-success"><i class="fa fa-check"></i>
                        <?=t('Success. These time zone values match.'); ?>
                    </div>
                <?php
            } ?>
            </div>
        </div>
    </fieldset>
    <fieldset>
        <legend><?=t('Settings'); ?></legend>
        <div class="form-group">
            <label class="control-label launch-tooltip form-label" for="timezone" data-bs-placement="right" title="<?= t(
                    'This will control the default timezone that will be used to display date/times.'
                ); ?>">
                <?php echo t('Default Timezone'); ?>
            </label>
            <div class="row ps-3">
                <select class="selectpicker" name="timezone" data-live-search="true" data-width="auto">
                    <?php
                    foreach ($timezones as $areaName => $namedTimezones) {
                        ?>
                        <optgroup label="<?= h($areaName); ?>">
                            <?php
                            foreach ($namedTimezones as $tzID => $tzName) {
                                $zone = new DateTimeZone($tzID);
                                $zoneName = Punic\Calendar::getTimezoneNameNoLocationSpecific($zone);
                                if ($zoneName) {
                                    $zoneName = '(' . $zoneName . ')';
                                } ?>
                                <option value="<?= h($tzID); ?>"<?= strcasecmp($tzID, $timezone) === 0 ? ' selected="selected"' : ''; ?>>
                                    <?= h($tzName); ?> <?=$zoneName; ?>
                                </option>
                                <?php
                            } ?>
                        </optgroup>
                        <?php
                    }
                    ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label form-label">
                <?php echo t('User-Specific Timezones'); ?>
            </label>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="user_timezones" value="1"<?= $user_timezones ? ' checked="checked"' : ''; ?> />
                <label class="form-check-label" for="user_timezones">
                    <span class="launch-tooltip control-label form-label" data-bs-placement="right" title="<?= t(
                        'With this setting enabled, users may specify their own time zone in their user profile, and content timestamps will be adjusted accordingly. Without this setting enabled, content timestamps appear in server time.'
                    ); ?>"><?php echo t('Enable user defined time zones.'); ?></span>
                </label>
            </div>
        </div>
    </fieldset>


    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <?php echo $interface->submit(t('Save'), 'user-timezone-form', 'right', 'btn-primary'); ?>
        </div>
    </div>

</form>
<?php
if (isset($compatibleTimezones) && !empty($compatibleTimezones)) {
                        ?>
    <div id="user-timezone-autofix-dialog" style="display: none" class="ccm-ui" title="<?=t('Select time zone'); ?>">
        <form method="POST" action="<?= $view->action('setSystemTimezone'); ?>" class="ccm-ui" id="user-timezone-autofix-form">
            <?php $token->output('set_system_timezone'); ?>
            <div class="form-group">
                <select class="form-select" size="15" name="new-timezone">
                    <?php
                    foreach ($compatibleTimezones as $timezoneID => $timezoneName) {
                        ?><option value="<?=h($timezoneID); ?>"><?=h($timezoneName); ?></option><?php
                    } ?>
                </select>
            </div>
        </form>
        <div class="dialog-buttons">
            <button type="button" onclick="jQuery.fn.dialog.closeTop()" class="btn btn-secondary float-start"><?=t('Cancel'); ?></button>
            <button type="button" onclick="$('#user-timezone-autofix-form').submit()" class="btn btn-primary float-end"><?=t('Save'); ?></button>
        </div>
    </div>
    <?php
                    }
?>
<script>
$(document).ready(function() {
    $('#user-timezone-autofix').on('click', function(e) {
        e.preventDefault();
        var $dlg = $('#user-timezone-autofix-dialog');
        if ($dlg.length === 0) {
            window.alert(<?=json_encode("No PHP compatible time zone is compatible with the database time zone.\nYou should change the database default timezone."); ?>);
            return;
        }
        jQuery.fn.dialog.open({
            element: $dlg,
            resizable: false,
            height: 370
        });
    });
});
</script>
