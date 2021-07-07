<?php
/* @var Concrete\Attribute\Address\Controller $controller */
/* @var Concrete\Core\Form\Service\Form $form */
/* @var array $scopeItems */
/* @var Concrete\Core\Attribute\View $view */

$app = Concrete\Core\Support\Facade\Application::getFacadeApplication();
$co = $app->make('helper/lists/countries');
$countries = $co->getCountries();
foreach (array_keys($countries) as $countryID) {
    if (empty($countryID) || empty($countries[$countryID])) {
        unset($countries[$countryID]);
    }
}
if (isset($_POST['akHasCustomCountries'])) {
    $akHasCustomCountries = $_POST['akHasCustomCountries'];
} elseif (!isset($akHasCustomCountries)) {
    $akHasCustomCountries = 0;
}
if (!isset($akDefaultCountry)) {
    $akDefaultCountry = '';
}
if ($akHasCustomCountries) {
    if (isset($_POST['akCustomCountries'])) {
        $akCustomCountries = $_POST['akCustomCountries'];
    }
    if (empty($akCustomCountries) || !is_array($akCustomCountries)) {
        $akCustomCountries = array();
    }
} else {
    $akCustomCountries = array_keys($countries);
}
?>
<fieldset class="ccm-attribute ccm-attribute-address">
    <legend><?=t('Address Options')?></legend>
    <div class="form-group">
        <?= $form->label('', t('Available Countries')) ?>
        <div class="form-check">
            <?= $form->radio('akHasCustomCountries', 0, $akHasCustomCountries) ?>
            <label class="form-check-label" for="akHasCustomCountries1"><?= t('All Available Countries') ?></label>
        </div>
        <div class="form-check">
            <?= $form->radio('akHasCustomCountries', 1, $akHasCustomCountries) ?>
            <label class="form-check-label" for="akHasCustomCountries2"><?= t('Selected Countries') ?></label>
        </div>
        <?= $form->selectMultiple('akCustomCountries', $countries, $akCustomCountries, ['size' => 7]) ?>
    </div>
    <div class="form-group">
        <?= $form->label('akDefaultCountry', t('Default Country')) ?>
        <?= $form->select('akDefaultCountry', array_merge(['' => t('Choose Country')], $countries), $akDefaultCountry) ?>
    </div>
    <div class="form-group">
        <?= $form->label('', t('Geolocation')) ?>
        <div class="form-check">
            <?= $form->checkbox('akGeolocateCountry', 1, isset($akGeolocateCountry) ? $akGeolocateCountry : false) ?>
            <label class="form-check-label" for="akGeolocateCountry"><?= t('Suggest the Country from the user IP address') ?></label>
        </div>
    </div>
</fieldset>
<script>
$(function() {
    function updateCustomCountries() {
        if ($('input[name="akHasCustomCountries"][value="1"]').is(':checked')) {
            $('#akCustomCountries').attr('disabled' , false);
        } else {
            $('#akCustomCountries').attr('disabled' , true);
            $('#akCustomCountries option').attr('selected', true);
        }
    }        
    $('input[name=akHasCustomCountries]').on('click', function() {
        updateCustomCountries();
    });
    updateCustomCountries();
});
</script>
