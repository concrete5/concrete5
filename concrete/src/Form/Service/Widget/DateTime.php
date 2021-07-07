<?php
namespace Concrete\Core\Form\Service\Widget;

use Concrete\Core\Http\ResponseAssetGroup;
use Concrete\Core\Support\Facade\Application;
use DateTime as PHPDateTime;
use Exception;

class DateTime
{
    /**
     * Takes a "field" and grabs all the corresponding disparate fields from $_POST and translates into a timestamp.
     * If $field has both date and time, the resulting value will be converted from the user timezone to the system timezone.
     * If $field has only date and not time, no timezone conversion will occur.
     *
     * @param string $field The name of the field to translate
     * @param array $arr The array containing the value. If null (default) we'll use $_POST
     * @param bool $asDateTime Set to true to get a DateTime object, false (default) for a string representation
     *
     * @return \DateTime|string|null In case of success returns the timestamp (in the format 'Y-m-d H:i:s' or 'Y-m-d' if $asDateTime is false) or the DateTime instance (if $asDateTime is true); if the date/time was not received we'll return null (if $field value is empty)
     */
    public function translate($field, $arr = null, $asDateTime = false)
    {
        $app = Application::getFacadeApplication();
        $dh = $app->make('helper/date');
        /* @var \Concrete\Core\Localization\Service\Date $dh */
        if ($arr === null) {
            $arr = $_POST;
        }
        // Example of $field: akID[5][value]
        if (preg_match('/^([^\[\]]+)\[([^\[\]]+)(?:\]\[([^\[\]]+))*\]$/', $field, $matches)) {
            // Example: $matches === ['akID[5][test]', 'akID', '5', 'value']
            array_shift($matches);
            while (isset($matches[1]) && is_array($arr)) {
                $key = array_shift($matches);
                $arr = isset($arr[$key]) ? $arr[$key] : null;
            }
            $field = $matches[0];
        }

        $datetime = null;
        if (is_array($arr)) {
            $systemTimezone = $dh->getTimezone('system');
            if (isset($arr[$field . '_dt'])) {
                $value = $arr[$field . '_dt'];
                if (is_string($value) && trim($value) !== '') {
                    $h = isset($arr[$field . '_h']) ? (int) $arr[$field . '_h'] : 0;
                    $m = isset($arr[$field . '_m']) ? (int) $arr[$field . '_m'] : 0;
                    $s = isset($arr[$field . '_s']) ? (int) $arr[$field . '_s'] : 0;
                    if (isset($arr[$field . '_a'])) {
                        if ($arr[$field . '_a'] === 'AM' && $h === 12) {
                            $h = 0;
                        } elseif ($arr[$field . '_a'] === 'PM' && $h < 12) {
                            $h += 12;
                        }
                    }
                    $value .= ' ' . substr("0$h", -2) . ':' . substr("0$m", -2) . ':' . substr("0$s", -2);
                    try {
                        $datetime = new PHPDateTime($value, $dh->getTimezone('user'));
                    } catch (Exception $foo) {
                    }
                    $datetime->setTimezone($systemTimezone);
                }
            } elseif (isset($arr[$field])) {
                $value = $arr[$field];
                if (is_string($value) && trim($value) !== '') {
                    try {
                        $datetime = new PHPDateTime($value, $systemTimezone);
                    } catch (Exception $foo) {
                    }
                }
            }
        }

        if ($datetime === null || $asDateTime) {
            $result = $datetime;
        } else {
            $result = $datetime->format('Y-m-d H:i:s');
        }

        return $result;
    }

    /**
     * Creates form fields and JavaScript calendar includes for a particular item (date/time string representations will be converted from the user system-zone to the time-zone).
     *
     * @param string $field The field prefix (will be used as $field parameter in the translate method)
     * @param \DateTime|string $value The initial value
     * @param bool $includeActivation Set to true to include a checkbox to enable/disable the date/time fields
     * @param bool $calendarAutoStart Set to false to avoid initializing the Javascript calendar
     * @param string $classes A list of space-separated classes to add to the ui-datepicker-div container
     * @param int $timeResolution The time resolution in seconds (60 means we won't ask seconds)
     * @param array $datePickerOptions datepicker properties, see jquery-ui datepicker docs
     *
     * @return string
     */
    public function datetime($field, $value = null, $includeActivation = false, $calendarAutoStart = true, $classes = null, $timeResolution = 60, array $datePickerOptions = [])
    {
        $app = Application::getFacadeApplication();
        $dh = $app->make('helper/date');
        /* @var \Concrete\Core\Localization\Service\Date $dh */

        $timeResolution = (int) $timeResolution;
        if ($timeResolution < 1) {
            $timeResolution = 60;
        }

        // Calculate the field names
        if (substr($field, -1) === ']') {
            $prefix = substr($field, 0, -1);
            $fieldActivate = $prefix . '_activate]';
            $fieldDate = $prefix . '_dt]';
            $fieldHours = $prefix . '_h]';
            $fieldMinutes = $prefix . '_m]';
            $fieldSeconds = $prefix . '_s]';
            $fieldAMPM = $prefix . '_a]';
        } else {
            $checkPostField = $field;
            $checkPostData = $_POST;
            $fieldActivate = $field . '_activate';
            $fieldDate = $field . '_dt';
            $fieldHours = $field . '_h';
            $fieldMinutes = $field . '_m';
            $fieldSeconds = $field . '_s';
            $fieldAMPM = $field . '_a';
        }
        $id = trim(preg_replace('/[^0-9A-Za-z-]+/', '_', $field), '_');

        // Set the initial date/time value
        $dateTime = null;
        $requestValue = $this->translate($field, null, true);
        if ($requestValue !== null) {
            $dateTime = $requestValue;
            $dateTime->setTimezone($dh->getTimezone('user'));
        } else {
            if ($value) {
                if ($value instanceof PHPDateTime) {
                    $dateTime = clone $value;
                    $dateTime->setTimezone($dh->getTimezone('user'));
                } else {
                    try {
                        $dateTime = $dh->toDateTime($value, 'user', 'system');
                    } catch (Exception $x) {
                    }
                }
            }
        }

        // Determine the date/time parts
        $timeFormat = $dh->getTimeFormat();
        if ($dateTime === null) {
            $now = new PHPDateTime('now', $dh->getTimezone('user'));
            $timeHour = (int) $now->format('G');
            $timeMinute = (int) $now->format('i');
            $timeSecond = (int) $now->format('s');
        } else {
            $timeHour = (int) $dateTime->format('G');
            $timeMinute = (int) $dateTime->format('i');
            $timeSecond = (int) $dateTime->format('s');
        }
        if ($timeFormat === 12) {
            $timeAMPM = ($timeHour < 12) ? 'AM' : 'PM';
            $timeHour = ($timeHour % 12);
            if ($timeHour === 0) {
                $timeHour = 12;
            }
        }

        // Split the time resolution
        $tr = $timeResolution;
        $stepSeconds = $tr % 60;
        $tr = (int) (($tr - $stepSeconds) / 60);
        $stepMinutes = $tr % 60;
        $tr = (int) (($tr - $stepMinutes) / 60);
        $stepHours = $tr;

        if ($stepSeconds !== 0 && $stepMinutes === 0) {
            $stepMinutes = 1;
        }
        if ($stepHours === 0) {
            $stepHours = 1;
        }

        // Build HTML
        $datePickerOptionsAsJSON = json_encode($datePickerOptions, JSON_FORCE_OBJECT);
        $shownDateFormat = $dh->getPHPDatePattern();
        $disabled = '';
        $html = '<div class="row row-cols-auto gx-1 align-items-center">';
        if ($includeActivation) {
            $html .= '<div class="col-auto"><input type="checkbox" id="' . $id . '_activate" class="ccm-activate-date-time" ccm-date-time-id="' . $id . '" name="' . $fieldActivate . '"';
            if ($dateTime === null) {
                $disabled = ' disabled="disabled"';
            } else {
                $html .= ' checked="checked"';
            }
            $html .= ' /></div>';
        }
        $html .= '<div class="col-auto"><span class="ccm-input-date-wrapper" id="' . $id . '_dw">';
        $html .= '<input type="text" id="' . $id . '_dt_pub" class="form-control ccm-input-date"' . $disabled;
        if (!$calendarAutoStart && $dateTime !== null) {
            $html .= ' value="' . h($dateTime->format($shownDateFormat)) . '"';
        }
        $html .= ' />';
        $html .= '<input type="hidden" id="' . $id . '_dt" name="' . $fieldDate . '"' . $disabled;
        if (!$calendarAutoStart && $dateTime !== null) {
            $html .= ' value="' . h($dateTime->format('Y-m-d')) . '"';
        }
        $html .= ' />';
        $html .= '</span></div>';
        $html .= '<div class="col-auto"><span class="ccm-input-time-wrapper row row-cols-auto gx-1 align-items-center" id="' . $id . '_tw">';
        $html .= '<div class="col-auto"><select class="form-select" id="' . $id . '_h" name="' . $fieldHours . '"' . $disabled . '>';
        $hourStart = ($timeFormat === 12) ? 1 : 0;
        $hourEnd = ($timeFormat === 12) ? 12 : 23;
        $hourList = [];
        for ($i = $hourStart; $i <= $hourEnd; $i += $stepHours) {
            $hoursList[] = $i;
        }
        $timeHour = $this->selectNearestValue($hoursList, $timeHour);
        foreach ($hoursList as $i) {
            $html .= '<option value="' . $i . '"';
            if ($i === $timeHour) {
                $html .= ' selected="selected"';
            }
            $html .= '>' . $i . '</option>';
        }
        $html .= '</select></div>';
        if ($stepMinutes !== 0) {
            $html .= '<div class="col-auto"><span class="separator me-0">:</span></div>';
            $html .= '<div class="col-auto"><select class="form-select"  id="' . $id . '_m" name="' . $fieldMinutes . '"' . $disabled . '>';
            $minutesList = [];
            for ($i = 0; $i < 60; $i += $stepMinutes) {
                $minutesList[] = $i;
            }
            $timeMinute = $this->selectNearestValue($minutesList, $timeMinute);
            foreach ($minutesList as $i) {
                $html .= '<option value="' . sprintf('%02d', $i) . '"';
                if ($i === $timeMinute) {
                    $html .= ' selected="selected"';
                }
                $html .= '>' . sprintf('%02d', $i) . '</option>';
            }
            $html .= '</select></div>';
        }
        if ($timeFormat === 12) {
            $html .= '<div class="col-auto"><select class="form-select" id="' . $id . '_a" name="' . $fieldAMPM . '"' . $disabled . '>';
            $html .= '<option value="AM"';
            if ($timeAMPM === 'AM') {
                $html .= ' selected="selected"';
            }
            $html .= '>';
            // This prints out the translation of "AM" in the current language
            $html .= $dh->date('A', mktime(1), 'system');
            $html .= '</option>';
            $html .= '<option value="PM"';
            if ($timeAMPM === 'PM') {
                $html .= ' selected="selected"';
            }
            $html .= '>';
            // This prints out the translation of "PM" in the current language
            $html .= $dh->date('A', mktime(13), 'system');
            $html .= '</option>';
            $html .= '</select></div>';
        }
        if ($stepSeconds !== 0) {
            $html .= '<div class="col-auto"><span class="separator me-0">:</span></div>';
            $html .= '<div class="col-auto"><select class="form-select"  id="' . $id . '_s" name="' . $fieldSeconds . '"' . $disabled . '>';
            $secondsList = [];
            for ($i = 0; $i < 60; $i += $stepSeconds) {
                $secondsList[] = $i;
            }
            $timeSecond = $this->selectNearestValue($secondsList, $timeSecond);
            foreach ($secondsList as $i) {
                $html .= '<option value="' . sprintf('%02d', $i) . '"';
                if ($i === $timeSecond) {
                    $html .= ' selected="selected"';
                }
                $html .= '>' . sprintf('%02d', $i) . '</option>';
            }
            $html .= '</select></div>';
        }
        $html .= '</span></div>';
        $html .= '</div>';

        // Create the Javascript for the calendar
        if ($calendarAutoStart) {
            $dateFormat = json_encode($dh->getJQueryUIDatePickerFormat($shownDateFormat));
            if ($classes) {
                $beforeShow = 'beforeShow: function() { $(\'#ui-datepicker-div\').addClass(' . json_encode((string) $classes) . '); },';
            } else {
                $beforeShow = '';
            }
            if ($dateTime === null) {
                $defaultDateJs = "''";
            } else {
                $defaultDateJs = 'new Date(' . implode(', ', [$dateTime->format('Y'), $dateTime->format('n') - 1, (int) $dateTime->format('j')]) . ')';
            }
            $html .= <<<EOT
<script type="text/javascript">
$(function() {
  $('#{$id}_dt_pub').datepicker($.extend({
    dateFormat: $dateFormat,
    altFormat: 'yy-mm-dd',
    altField: '#{$id}_dt',
    changeYear: true,
    showAnim: 'fadeIn',
    yearRange: 'c-100:c+10',
    $beforeShow
    onClose: function(dateText, inst) {
      if(!dateText) {
        $(inst.settings.altField).val('');
      }
    }
  },{$datePickerOptionsAsJSON})).datepicker('setDate', $defaultDateJs).attr('autocomplete', 'off');
});
</script>
EOT;
        }

        // Add the Javascript to handle the activation
        if ($includeActivation) {
            $html .= <<<EOT
<script type="text/javascript">
$(function() {
  $('#{$id}_activate').click(function() {
    if ($(this).is(':checked')) {
      $('#{$id}_dw input,#{$id}_tw select').removeAttr('disabled');
    } else {
      $('#{$id}_dw input,#{$id}_tw select').attr('disabled', 'disabled');
    }
  });
});
</script>
EOT;
        }

        return $html;
    }

    /**
     * Creates form fields and JavaScript calendar includes for a particular item but includes only calendar controls (no time, so no time-zone conversions will be applied).
     *
     * @param string $field The field name (will be used as $field parameter in the translate method)
     * @param \DateTime|string $value The initial value
     * @param bool $calendarAutoStart Set to false to avoid initializing the Javascript calendar
     * @param array $datePickerOptions datepicker properties, see jquery-ui datepicker docs
     *
     * @return string
     */
    public function date($field, $value = null, $calendarAutoStart = true, array $datePickerOptions = [])
    {
        $app = Application::getFacadeApplication();
        $dh = $app->make('helper/date');
        /* @var \Concrete\Core\Localization\Service\Date $dh */
        $fh = $app->make('helper/form');
        /* @var \Concrete\Core\Form\Service\Form $fh */

        // Calculate the field names
        $id = trim(preg_replace('/[^0-9A-Za-z-]+/', '_', $field), '_');

        // Set the initial date/time value
        $dateTime = null;
        $requestValue = $this->translate($field, null, true);
        if ($requestValue !== null) {
            $dateTime = $requestValue;
        } elseif ($value) {
            if ($value instanceof PHPDateTime) {
                $dateTime = $value;
            } else {
                try {
                    $dateTime = $dh->toDateTime($value);
                } catch (Exception $x) {
                }
            }
        }

        // Build HTML
        $datePickerOptionsAsJSON = json_encode($datePickerOptions, JSON_FORCE_OBJECT);
        $shownDateFormat = $dh->getPHPDatePattern();
        $html = '<div>';
        $html .= '<span class="ccm-input-date-wrapper" id="' . $id . '_dw">';
        $html .= '<input type="text" id="' . $id . '_pub" class="form-control ccm-input-date"';
        if (!$calendarAutoStart && $dateTime !== null) {
            $html .= ' value="' . h($dateTime->format($shownDateFormat)) . '"';
        }
        $html .= '/>';
        $html .= '<input type="hidden" id="' . $id . '" name="' . $field . '" />';
        $html .= '</span>';
        $html .= '</div>';

        // Create the Javascript for the calendar
        if ($calendarAutoStart) {
            $dateFormat = json_encode($dh->getJQueryUIDatePickerFormat($shownDateFormat));
            if ($dateTime === null) {
                $defaultDateJs = "''";
            } else {
                $defaultDateJs = 'new Date(' . implode(', ', [$dateTime->format('Y'), $dateTime->format('n') - 1, (int) $dateTime->format('j')]) . ')';
            }
            $html .= <<<EOT
<script type="text/javascript">
$(function() {
  $('#{$id}_pub').datepicker($.extend({
    dateFormat: $dateFormat,
    altFormat: 'yy-mm-dd',
    altField: '#{$id}',
    changeYear: true,
    showAnim: 'fadeIn',
    yearRange: 'c-100:c+10',
    onClose: function(dateText, inst) {
      if(!dateText) {
        $(inst.settings.altField).val('');
      }
    }
  },{$datePickerOptionsAsJSON})).datepicker('setDate', $defaultDateJs).attr('autocomplete', 'off');
});
</script>
EOT;
        }

        return $html;
    }

    /**
     * Choose an array value nearest to a specified value.
     * Useful when we work with time resolutions.
     *
     * @param int $values
     * @param int $wantedValue
     *
     * @return int
     *
     * @example If the current time is 10 and the time resolution is 15, we have an array of values of [0, 15, 30, 45]: the closest value is 15.
     */
    protected function selectNearestValue(array $values, $wantedValue)
    {
        if (in_array($wantedValue, $values)) {
            $result = $wantedValue;
        } else {
            $result = null;
            $minDelta = PHP_INT_MAX;
            foreach ($values as $value) {
                $delta = abs($value - $wantedValue);
                if ($delta < $minDelta) {
                    $minDelta = $delta;
                    $result = $value;
                }
            }
        }

        return $result;
    }
}
