<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Utility\Service\Identifier;
use HtmlObject\Element;

$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);
/** @var Identifier $idHelper */
$idHelper = $app->make(Identifier::class);
?>

<?php if ($akSelectAllowMultipleValues && !$akSelectAllowOtherValues) { ?>
    <?php $options = $controller->getOptions(); ?>
    <?php foreach ($options as $opt) { ?>

        <div class="form-check">
            <?php $id = "ccm-chechkbox-" . $idHelper->getString(12); ?>
            <?php echo $form->checkbox($view->field('atSelectOptionValue') . '[]', $opt->getSelectAttributeOptionID(), in_array($opt->getSelectAttributeOptionID(), $selectedOptionIDs), ["class" => "form-check-input", "id" => $id]); ?>
            <?php echo $form->label($id, $opt->getSelectAttributeOptionDisplayValue(), ["class" => "form-check-label"]) ?>
        </div>

    <?php } ?>
<?php } ?>

<?php
// Select Menu.
if (!$akSelectAllowMultipleValues && !$akSelectAllowOtherValues && !$akDisplayMultipleValuesOnSelect) {
    if (!$akHideNoneOption) {
        $options = ['' => t('** None')];
    }
    foreach ($controller->getOptions() as $option) {
        $options[$option->getSelectAttributeOptionID()] = $option->getSelectAttributeOptionDisplayValue();
    }

    echo $form->select($view->field('atSelectOptionValue'), $options, empty($selectedOptionIDs) ? '' : $selectedOptionIDs[0]);
}
?>
<?php if (!$akSelectAllowMultipleValues && !$akSelectAllowOtherValues && $akDisplayMultipleValuesOnSelect) { ?>
    <?php if (!$akHideNoneOption) { ?>
        <div class="form-check">
            <?php $id = "ccm-chechkbox-" . $idHelper->getString(12); ?>
            <?php echo $form->radio($view->field('atSelectOptionValue'), '', empty($selectedOptionIDs) ? '' : $selectedOptionIDs[0], ["class" => "form-check-input", "id" => $id]) ?>
            <?php echo $form->label($id, t('None'), ["class" => "form-check-label"]) ?>
        </div>
    <?php } ?>

    <?php foreach ($controller->getOptions() as $opt) { ?>
        <div class="form-check">
            <?php $id = "ccm-chechkbox-" . $idHelper->getString(12); ?>
            <?php echo $form->radio($view->field('atSelectOptionValue'), $opt->getSelectAttributeOptionID(), in_array($opt->getSelectAttributeOptionID(), $selectedOptionIDs), ["class" => "form-check-input", "id" => $id]); ?>
            <?php echo $form->label($id, $opt->getSelectAttributeOptionDisplayValue(), ["class" => "form-check-label"]) ?>
        </div>
    <?php } ?>
<?php } ?>

<?php
// Select2
if ($akSelectAllowOtherValues) {
    $options = [];

    if (is_array($selectedOptionIDs) && count($selectedOptionIDs)) {
        $optionsList = $controller->getOptions();
        foreach ($optionsList as $opt) {
            if (in_array('SelectAttributeOption:' . $opt->getSelectAttributeOptionID(), $selectedOptionIDs)) {
                $options['SelectAttributeOption:' . $opt->getSelectAttributeOptionID()] = $opt->getSelectAttributeOptionDisplayValue(true);
            }
        }
    }

    echo (string)new Element(
        'span',
        $form->selectMultiple($view->field('atSelectOptionValue'), $options, count($selectedOptionIDs) ? $selectedOptionIDs : '', ['data-select-and-add' => $akID]),
        [
            'class' => 'ccm-select-values-selector',
            'id' => 'ccm-select-values-selector-' . $akID,
        ]
    );
    ?>

    <script type="text/javascript">
        $(function () {
            $('select[data-select-and-add="<?php echo $akID?>"]').selectpicker(
                {
                    liveSearch: true,
                    allowAdd: true,
                }
            ).ajaxSelectPicker(
                {
                    ajax: {
                        url: "<?php echo $view->action('load_autocomplete_values'); ?>",
                        data: {
                            term: "{{{q}}}"
                        },
                    },
                    locale: {
                        currentlySelected: "<?php echo t('Currently Selected'); ?>",
                        emptyTitle: "<?php echo t('Select and begin typing'); ?>",
                        errorText: "<?php echo t('Unable to retrieve results'); ?>",
                        searchPlaceholder: "<?php echo t('Search...'); ?>",
                        statusInitialized: "<?php echo t('Start typing a search query'); ?>",
                        statusNoResults: "<?php echo t('No Results'); ?>",
                        statusSearching: "<?php echo t('Searching...'); ?>",
                        statusTooShort: "<?php echo t('Please enter more characters'); ?>",
                    },
                    preserveSelected: true,
                    clearOnEmpty: false,
                    minLength: 2,
                },
            );
        });
    </script>
<?php }
