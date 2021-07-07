<fieldset class="ccm-attribute ccm-attribute-date-time">

    <legend><?=t('Date/Time Options')?></legend>

    <div class="form-group">
        <div class="form-check">
            <?=$form->checkbox('akUseNowIfEmpty', '1', isset($akUseNowIfEmpty) ? $akUseNowIfEmpty : false)?>
            <label class="form-check-label" for="akUseNowIfEmpty">
                <?=t('Suggest the current date/time if empty')?>
            </label>
        </div>
    </div>

    <div class="form-group">
        <?=$form->label('akDateDisplayMode', t('Ask User For'))?>
        <?php
        $akDateDisplayModeOptions = [
            'date_time' => t('Both Date and Time'),
            'date' => t('Date Only'),
            'date_text' => t('Text Input Field with Date'),
            'text' => t('Text Input Field with Date and Time'),
        ];
        if (!isset($akDateDisplayMode) || !isset($akDateDisplayModeOptions[$akDateDisplayMode])) {
            $akDateDisplayMode = key($akDateDisplayModeOptions);
        }
        ?>
        <?=$form->select('akDateDisplayMode', $akDateDisplayModeOptions, $akDateDisplayMode) ?>
    </div>

    <div class="form-group">
        <?= $form->label('akTextCustomFormat', '<a href="http://php.net/manual/function.date.php" target="_blank">' . t('Custom format') . ' ' . '<i class="fa fa-question-circle"></i></a>', ['class' => 'launch-tooltip form-label', 'data-html' => 'true', 'title' => h(t('Here you can specify an optional custom format for text inputs (click to see the PHP manual for the %s function)', '<code>date</code>'))]) ?>
        <?= $form->text('akTextCustomFormat', isset($akTextCustomFormat) ? $akTextCustomFormat : '') ?>
    </div>

    <div class="form-group">
        <?=$form->label('akTimeResolution', t('Time Resolution'))?>
        <?php
        $akTimeResolutionOptions = [];
        foreach ([
            // With seconds
            1, 5, 10, 15, 30, 60,
            // Minutes only
            60 * 1, 60 * 5, 60 * 10, 60 * 15, 60 * 30,
            // Hours only
            60 * 60, 60 * 60 * 3, 60 * 60 * 4, 60 * 60 * 6, 60 * 60 * 12
        ] as $totalSeconds) {
            $seconds = $totalSeconds;
            $hours = (int) ($totalSeconds / 3600);
            $seconds -= $hours * 3600;
            $minutes = (int) ($seconds / 60);
            $seconds -= $minutes * 60;
            $parts = [];
            if ($hours !== 0) {
                $parts[] = Punic\Unit::format($hours, 'duration/hour', 'long');
            }
            if ($minutes !== 0) {
                $parts[] = Punic\Unit::format($minutes, 'duration/minute', 'long');
            }
            if (empty($parts) || $seconds !== 0) {
                $parts[] = Punic\Unit::format($seconds, 'duration/second', 'long');
            }
            $akTimeResolutionOptions[$totalSeconds] = Punic\Misc::join($parts);
        }
        $akTimeResolutionExtra = [];
        if ($akDateDisplayMode !== 'date_time') {
            $akTimeResolutionExtra['disabled'] = 'disabled';
        }
        if (!isset($akTimeResolution) || !isset($akTimeResolutionOptions[$akTimeResolution])) {
            $akTimeResolution = '60';
        }
        ?>
        <?=$form->select('akTimeResolution', $akTimeResolutionOptions, $akTimeResolution, $akTimeResolutionExtra)?>
    </div>

</fieldset>

<script>
$(document).ready(function() {
    $('#akDateDisplayMode')
        .on('change', function() {
            if (this.value === 'date_time') {
                $('#akTimeResolution').removeAttr('disabled');
            } else {
                $('#akTimeResolution').attr('disabled', 'disabled');
            }
            switch (this.value) {
                case 'text':
                case 'date_text':
                    $('#akTextCustomFormat').removeAttr('disabled');
                    break;
                default:
                    $('#akTextCustomFormat').attr('disabled', 'disabled');
                    break;
            }
        })
        .trigger('change')
    ;
});
</script>
