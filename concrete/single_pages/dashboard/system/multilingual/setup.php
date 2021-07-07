<?php
defined('C5_EXECUTE') or die('Access Denied.');

$app = Concrete\Core\Support\Facade\Application::getFacadeApplication();
$u = $app->make(Concrete\Core\User\User::class);
?>

<div class="ccm-dashboard-header-buttons">
    <button class="btn btn-primary" data-dialog-width="400" data-dialog-height="500" data-dialog="add-locale"><?= t('Add Locale') ?></button>
</div>

<h3><?= t('Locales') ?></h3>

<table class="table table-striped">
    <tr>
        <th style="width: 1px">&nbsp;</th>
        <th style="width: auto"><?= t('Home Page') ?></th>
        <th style="width: auto"><?= t('Language') ?></th>
        <th style="width: auto"><?= t('Locale') ?></th>
        <th style="width: 1px">&nbsp;</th>
    </tr>
    <?php
    foreach ($locales as $locale) {
        /* @var Concrete\Core\Entity\Site\Locale $locale */
        $localeSiteTree = $locale->getSiteTree();
        $home = $localeSiteTree === null ? null : $localeSiteTree->getSiteHomePageObject();
        ?>
        <tr>
            <td><?= $flag->getLocaleFlagIcon($locale) ?></td>
            <td>
                <?php
                if (is_object($home)) {
                    ?><a href="<?= $home->getCollectionLink() ?>"><?= $home->getCollectionName() ?></a><?php
                } else {
                    ?><span class="text-warning"><?= t('None Created.') ?></span><?php
                }
                ?>
            </td>
            <td><?= $locale->getLanguageText() ?></td>
            <td><?= $locale->getLocale() ?></td>
            <td style="white-space: nowrap">
                <a data-dialog-title="<?= t('Delete Locale') ?>" data-dialog="delete-section-<?= $locale->getLocaleID() ?>" href="#" class="icon-link"><i class="fa fa-trash"></i></a>
                <a data-dialog-title="<?= t('Change Locale') ?>" data-dialog="change-section-<?= $locale->getLocaleID() ?>" href="#" class="icon-link"><i class="fa fa-edit"></i></a>
            </td>
        </tr>
        <?php
    }
    ?>
</table>

<?php
foreach ($locales as $locale) {
    ?>
    <div style="display: none">
        <div data-dialog-wrapper="delete-section-<?= $locale->getLocaleID() ?>">
            <?php
            if ($locale->getIsDefault()) {
                ?>
                <p><?= t('The default multilingual section can\'t be removed.') ?></p>
                <?php
            } elseif (!$u->isSuperUser()) {
                ?>
                <p><?= t('Only the super user may remove a multilingual section.') ?></p>
                <?php
            } else {
                ?>
                <form data-form="delete-locale-<?= $locale->getLocaleID() ?>" method="post" action="<?= $view->action('remove_locale_section') ?>">
                    <?php $token->output('remove_locale_section') ?>
                    <input type="hidden" name="siteLocaleID" value="<?= $locale->getLocaleID() ?>">
                    <p><?= t('Delete this multilingual section? This will remove the entire site tree and its content from your website.') ?></p>
                    <div class="dialog-buttons">
                        <button class="btn btn-secondary" data-dialog-action="cancel"><?= t('Cancel') ?></button>
                        <button class="btn btn-danger float-end" onclick="$('form[data-form=delete-locale-<?= $locale->getLocaleID() ?>]').submit()" type="submit"><?= t('Delete') ?></button>
                    </div>
                </form>
                <?php
            }
            ?>
        </div>
        <div data-dialog-wrapper="change-section-<?= $locale->getLocaleID() ?>">
            <form data-form="change-locale-<?= $locale->getLocaleID() ?>" method="post" action="<?= $view->action('change_locale_section') ?>">
                <?php $token->output('change_locale_section') ?>
                <input type="hidden" name="siteLocaleID" value="<?= $locale->getLocaleID() ?>">
                <div class="form-group">
                    <?= $form->label('msLanguageChange' . $locale->getLocaleID(), t('Choose Language')) ?>
                    <?= $form->select('msLanguageChange' . $locale->getLocaleID(), $languages, $locale->getLanguage()) ?>
                </div>
                <div class="form-group">
                    <?= $form->label('msCountryChange' . $locale->getLocaleID(), t('Choose Country')) ?>
                    <?= $form->select('msCountryChange' . $locale->getLocaleID(), array_merge(['' => t('** None Selected')], $countries), $locale->getCountry()) ?>
                </div>
                <div class="form-group">
                    <label class="control-label form-label"><?= t('Icon') ?></label>
                    <div id="ccm-multilingual-language-icon-change<?= $locale->getLocaleID() ?>"><?= t('None') ?></div>
                </div>
                <div class="dialog-buttons">
                    <button class="btn btn-secondary" data-dialog-action="cancel"><?= t('Cancel') ?></button>
                    <button class="btn btn-primary float-end" onclick="$('form[data-form=change-locale-<?= $locale->getLocaleID() ?>]').submit()" type="submit"><?= t('Update') ?></button>
                </div>
            </form>
        </div>
    </div>
    <?php
}

$defaultLocales = [];
$defaultLocaleID = 0;
foreach ($locales as $locale) {
    $defaultLocales[$locale->getLocaleID()] = sprintf('%s (%s)', $locale->getLanguageText(), $locale->getLocale());
    if ($locale->getIsDefault()) {
        $defaultLocaleID = $locale->getLocaleID();
    }
}
?>
<h3><?= t('Settings') ?></h3>
<form method="post" action="<?= $this->action('set_default') ?>">
    <div class="form-group">
        <label class="control-label form-label"><?= t('Default Locale') ?></label>
        <?= $form->select('defaultLocale', $defaultLocales, $defaultLocaleID, ['required' => 'required']) ?>
    </div>
    <div class="form-group">
        <div class="form-check">
            <?= $form->checkbox('redirectHomeToDefaultLocale', 1, $redirectHomeToDefaultLocale) ?>
            <label>
                <span><?= t('Redirect home page to default locale.') ?></span>
            </label>
        </div>
        <div style="margin-left: 20px">
            <div class="form-check <?= $redirectHomeToDefaultLocale ? '' : ' disabled' ?>">
                <?= $form->checkbox('useBrowserDetectedLocale', 1, $useBrowserDetectedLocale, $redirectHomeToDefaultLocale ? [] : ['disabled' => 'disabled']) ?>
                <label>
                    <span><?= t('Attempt to use visitor\'s locale based on their browser information.') ?></span>
                </label>
            </div>
        </div>
        <div class="form-check">
            <?= $form->checkbox('alwaysTrackUserLocale', 1, $alwaysTrackUserLocale) ?>
            <label>
                <span><?= t('Always track user locale.') ?> <i class="launch-tooltip control-label fa fa-question-circle" title="<?= h(t('Tracking user locales requires the creation of session cookies. Disable this option to avoid tracking user locale in case the session cookie is not yet set.')) ?>"></i></span>
            </label>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $('#redirectHomeToDefaultLocale').on('change', function() {
                $('#useBrowserDetectedLocale')
                    .prop('disabled', !this.checked)
                    .closest('div.checkbox')[this.checked ? 'removeClass' : 'addClass']('disabled')
                ;
            });
        });
    </script>
    <div class="form-group">
        <label class="control-label form-label"><?= t('Site interface source locale') ?></label>
        <div class="row row-cols-auto g-0 align-items-center">
            <div class="col-auto">
                <?= $form->select('defaultSourceLanguage', array_merge(['' => t('*** Unknown or mixed language')], $languages), $defaultSourceLanguage, ['class' => 'me-2']) ?>
            </div>
            <div class="col-auto">
                <?= $form->select('defaultSourceCountry', array_merge(['' => t('*** Undetermined country')], $countries), $defaultSourceCountry) ?>
            </div>
        </div>
    </div>
    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <?php $token->output('set_default') ?>
            <button class="float-end btn btn-primary" type="submit" name="save"><?= t('Save Settings') ?></button>
        </div>
    </div>
</form>


<div style="display: none">
    <div data-dialog-wrapper="add-locale">
        <form data-dialog-form="add-locale" action="<?= $view->action('add_content_section') ?>">
            <fieldset>
                <legend><?= t('Locale') ?></legend>
                <div class="form-group">
                    <?= $form->label('msLanguage', t('Choose Language')) ?>
                    <?= $form->select('msLanguage', $languages) ?>
                </div>
                <div class="form-group">
                    <?= $form->label('msCountry', t('Choose Country')) ?>
                    <?= $form->select('msCountry', array_merge(['' => t('** None Selected')], $countries)) ?>
                </div>
                <div class="form-group">
                    <label class="control-label form-label"><?= t('Icon') ?></label>
                    <div id="ccm-multilingual-language-icon"><?= t('None') ?></div>
                </div>
            </fieldset>
            <fieldset>
                <legend><?= t('Home Page') ?></legend>
                <div class="form-group">
                    <?= $form->label('template', t('Template')) ?>
                    <?= $form->select('template', $templates) ?>
                </div>
                <div class="form-group">
                    <?= $form->label('homePageName', t('Page Name')) ?>
                    <?= $form->text('homePageName') ?>
                </div>
                <div class="form-group">
                    <?= $form->label('URL Slug', t('URL Slug')) ?>
                    <?= $form->text('urlSlug') ?>
                </div>
            </fieldset>
            <div class="dialog-buttons">
                <button class="btn btn-secondary float-start" data-dialog-action="cancel"><?= t('Cancel') ?></button>
                <button class="btn btn-primary float-end" data-dialog-action="submit"><?= t('Add Locale') ?></button>
            </div>
            <?php $token->output('add_content_section') ?>
        </form>
    </div>
</div>

<?php
if (isset($mlLink)) {
    ?>
    <div class="alert alert-info small">
        <?= t(
            'You can configure the user languages in the %s dashboard page.',
            sprintf('<a href="%s">%s</a>', h($mlLink[1]), h($mlLink[0]))
        ) ?>
    </div>
    <?php
}
?>

<script>
$(document).ready(function() {
    'use strict';

    var ccmCountryForLanguageLister = (function () {
        var countryDictionary = <?= json_encode($countries) ?>,
            sortedCountries = (function () {
                var list = [];
                $.each(countryDictionary, function (id) {
                    list.push(id);
                });
                list.sort(function (aCode, bCode) {
                    var aName = countryDictionary[aCode].toLowerCase(), bName = countryDictionary[bCode].toLowerCase();
                    if (aName < bName) {
                        return -1;
                    }
                    if (aName > bName) {
                        return 1;
                    }
                    return 0;
                });
                return list;
            })(),
            cache = {'': []},
            iconCache = {};

        function appendCountries($parent, countryCodes) {
            $.each(countryCodes, function (_, countryCode) {
                $parent.append($('<option />').val(countryCode).text(countryDictionary[countryCode]));
            });
        }
        function updateCountrySelect($country, preferredCountryCodes) {
            var preferredCountries = [];
            $.each(preferredCountryCodes, function (_, countryCode) {
                if (countryCode in countryDictionary) {
                    preferredCountries.push(countryCode);
                }
            });
            var selectedCountry = $country.val();
            $country.empty().append($('<option value="" />').text(<?= json_encode(t('** None Selected')) ?>));
            if (preferredCountries.length) {
                var otherCountries = [];
                $.each(sortedCountries, function (_, countryCode) {
                    if ($.inArray(countryCode, preferredCountries) < 0) {
                        otherCountries.push(countryCode);
                    }
                });
                var $group;
                $country.append($group = $('<optgroup />').attr('label', <?= json_encode(t('Suggested countries')) ?>));
                appendCountries($group, preferredCountries);
                $country.append($group = $('<optgroup />').attr('label', <?= json_encode(t('Other countries')) ?>));
                appendCountries($group, otherCountries);
            }
            else {
                appendCountries($country, sortedCountries);
            }
            $country.val(selectedCountry);
        }

        function populateIcon($icon, country) {
            $icon.data('country', country || false);
            if (country) {
                if (iconCache.hasOwnProperty(country)) {
                    $icon.html(iconCache[country]);
                } else {
                    $icon.text('...');
                    $.post(
                        <?= json_encode($view->action('load_icon')) ?>,
                        {msCountry: country},
                        function (data) {
                            iconCache[country] = data ? data : '';
                            if ($icon.data('country') === country) {
                                $icon.html(iconCache[country]);
                            }
                        },
                        'html'
                    );
                }
            } else {
                $icon.text(<?= json_encode(t('None')) ?>);
            }
        }
        
        function CountryForLanguageLister($language, $country, $icon) {
            var me = this;
            this.$language = $language;
            this.$country = $country;
            this.$language.change(function () {
                me.updateCountries();
            });
            this.currentLanguage = null;
            this.$icon = $icon && $icon.length ? $icon : null;
            this.updateCountries();
            if (this.$icon !== null) {
                this.$country
                    .on('change', function() {
                        populateIcon(me.$icon, this.value);
                    })
                    .trigger('change')
                ;
            }
        }

        CountryForLanguageLister.prototype = {
            updateCountries: function () {
                var me = this;
                var language = this.$language.val();
                if (language === '') {
                    this.$country.attr('disabled', 'disabled');
                }
                else {
                    this.$country.removeAttr('disabled');
                }
                if (language === this.currentLanguage) {
                    return;
                }
                this.currentLanguage = language;
                if (language in cache) {
                    updateCountrySelect(this.$country, cache[language]);
                    return;
                }
                updateCountrySelect(this.$country, []);
                $.get(
                    <?= json_encode($view->action('get_countries_for_language')) ?>,
                    {language: language},
                    function (data) {
                        cache[language] = data ? data : [];
                        if (me.currentLanguage === language) {
                            updateCountrySelect(me.$country, cache[language]);
                        }
                    },
                    'json'
                );
            }
        };

        return CountryForLanguageLister;
    })();

    new ccmCountryForLanguageLister($('#msLanguage'), $('#msCountry'), $('#ccm-multilingual-language-icon'));
    <?php
    foreach ($locales as $locale) {
        ?>new ccmCountryForLanguageLister($('#msLanguageChange<?= $locale->getLocaleID() ?>'), $('#msCountryChange<?= $locale->getLocaleID() ?>'), $('#ccm-multilingual-language-icon-change<?= $locale->getLocaleID() ?>'));<?php
    }
    ?>
    new ccmCountryForLanguageLister($('#defaultSourceLanguage'), $('#defaultSourceCountry'));

    ConcreteEvent.subscribe('AjaxFormSubmitSuccess.addLocale', function (e, data) {
        if (data.form == 'add-locale') {
            window.location.reload();
        }
    });

});
</script>
