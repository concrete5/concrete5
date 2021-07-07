<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Form\Service\Form;
use Concrete\Core\Http\Request;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Validation\SanitizeService;
use Concrete\Core\Area\Layout\Preset\UserPreset;

/** @var UserPreset[] $presets */

$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);
/** @var SanitizeService $sanitizeService */
$sanitizeService = $app->make(SanitizeService::class);
/** @var Request $request */
$request = $app->make(Request::class);
?>

<div class="ccm-ui">
    <p>
        <?php echo t('You can add saved layouts to other areas in your site. Note: these are different than any layout presets that might be included with your theme.') ?>
    </p>

    <form method="post" action="<?php echo $controller->action('submit') ?>"
          data-dialog-form="save-area-layout-presets">

        <?php echo $form->hidden('arLayoutID', $sanitizeService->sanitizeInt($request->request->get('arLayoutID'))); ?>

        <div class="form-group">
            <?php echo $form->label('arLayoutPresetID', t('Preset')); ?>
            <?php echo $form->select('arLayoutPresetID', $presets, ['class' => 'span3']) ?>
        </div>

        <div class="form-group" id="ccm-layout-save-preset-name">
            <?php echo $form->label('arLayoutPresetName', t('Name')); ?>
            <?php echo $form->text('arLayoutPresetName') ?>
        </div>

        <div class="alert alert-warning" id="ccm-layout-save-preset-override">
            <?php echo t('Note: this will override the selected preset with the new preset. It will not update any layouts already in use.') ?>
        </div>

        <div class="dialog-buttons">
            <button class="btn btn-secondary float-start" data-dialog-action="cancel">
                <?php echo t('Cancel') ?>
            </button>

            <button type="button" data-dialog-action="submit" class="btn btn-success float-end">
                <?php echo t('Save Preset') ?>
            </button>
        </div>
    </form>
</div>

<!--suppress ES6ConvertVarToLetConst, EqualityComparisonWithCoercionJS -->
<script type="text/javascript">
    $(function () {
        var $input = $('input[name=arLayoutPresetName]', 'form[data-dialog-form=save-area-layout-presets]');
        $('select[name=arLayoutPresetID]', 'form[data-dialog-form=save-area-layout-presets]').on('change', function () {
            if ($(this).val() == '-1') {
                $('#ccm-layout-save-preset-override').hide();
                $input.val('');
            } else {
                $('#ccm-layout-save-preset-override').show();
                $input.val($(this).find('option:selected').text());
            }
        }).trigger('change');
    });
</script>
