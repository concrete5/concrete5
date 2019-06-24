<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Multilingual;

use Concrete\Core\Application\EditResponse;
use Concrete\Core\Multilingual\Page\Section\Section;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Multilingual\Page\Section\Translation;
use Concrete\Core\Page\Controller\DashboardSitePageController;
use Core;
use Database;
use Config;
use Punic\Misc;

defined('C5_EXECUTE') or die('Access Denied.');

class TranslateInterface extends DashboardSitePageController
{
    public $helpers = array('form');

    public function view()
    {
        $extractor = Core::make('multilingual/extractor');
        $this->set('extractor', $extractor);
        $this->set('site', $this->getSite());
        $this->set('app', $this->app);
    }
    public function reloaded()
    {
        $this->set('message', t('Languages refreshed.'));
        $this->view();
    }

    public function exported()
    {
        $this->set('message', t('The translations have been exported to file and will be used by the website.'));
        $this->view();
    }

    public function reset_complete()
    {
        $this->set('message', t('Languages reset.'));
        $this->view();
    }

    public function reset_languages()
    {
        if (Core::make('token')->validate('reset_languages')) {
            $u = new \User();
            $extractor = Core::make('multilingual/extractor');
            if ($u->isSuperUser()) {
                $list = Section::getList();
                foreach ($list as $section) {
                    // now we load the translations that currently exist for each section
                    $extractor->deleteSectionTranslationFile($section);
                }
                $extractor->clearTranslationsFromDatabase();
                $this->redirect('/dashboard/system/multilingual/translate_interface', 'reset_complete');
            }
        } else {
            $this->error->add(t('Only the admin user may reset all strings.'));
        }
    }

    public function submit()
    {
        $extractor = Core::make('multilingual/extractor');
        if ($this->post('action') == 'reload') {
            if (Core::make('token')->validate()) {
                // First, we look in all the site sources for PHP code with GetText
                $baseTranslations = $extractor->extractTranslatableSiteStrings();

                // $translations contains all of our site translations.
                $list = Section::getList($this->getSite());
                $defaultSourceLocale = $this->getSite()
                    ->getConfigRepository()->get('multilingual.default_source_locale');
                foreach ($list as $section) {
                    /* @var $section \Concrete\Core\Multilingual\Page\Section\Section */
                    if ($section->getLocale() != $defaultSourceLocale) {
                        $localeTranslations = clone $baseTranslations;
                        /* @var $localeTranslations \Gettext\Translations */
                        $localeTranslations->setLanguage($section->getLocale());
                        $localeTranslations->setPluralForms($section->getNumberOfPluralForms(), $section->getPluralsRule());
                        // now we load the translations that currently exist for each section
                        $extractor->mergeTranslationsWithSectionFile($section, $localeTranslations);
                        $extractor->mergeTranslationsWithCore($section, $localeTranslations);
                        $extractor->mergeTranslationsWithPackages($section, $localeTranslations);
                        $extractor->saveSectionTranslationsToFile($section, $localeTranslations);

                        // now that we've updated the translation file, we take all the translations and
                        // we insert them into the database so we can update our counts, and give the users
                        // a web interface
                        $extractor->saveSectionTranslationsToDatabase($section, $localeTranslations);
                    }
                }
                $this->redirect('/dashboard/system/multilingual/translate_interface', 'reloaded');
            } else {
                $this->error->add(Core::make('token')->getErrorMessage());
            }
        }
        if ($this->post('action') == 'export') {
            if (Core::make('token')->validate()) {
                $defaultSourceLocale = $this->getSite()->getConfigRepository()->get('multilingual.default_source_locale');
                $list = Section::getList();
                foreach ($list as $section) {
                    if ($section->getLocale() != $defaultSourceLocale) {
                        $translations = $section->getSectionInterfaceTranslations();
                        $extractor->mergeTranslationsWithSectionFile($section, $translations);
                        $extractor->saveSectionTranslationsToFile($section, $translations);
                    }
                }
                \Localization::clearCache();
                $this->redirect('/dashboard/system/multilingual/translate_interface', 'exported');
            } else {
                $this->error->add(Core::make('token')->getErrorMessage());
            }
        }
        $this->view();
    }

    public function save_translation()
    {
        $result = new EditResponse();
        try {
            $token = $this->app->make('token');
            if (!$token->validate('translate/save')) {
                throw new \Exception(t('Invalid token, please refresh and try again.'));
            }

            $translation = null;
            $mtID = @intval($this->post('id'));
            if ($mtID > 0) {
                $translation = Translation::getByRecordID($mtID);
            }
            if (!isset($translation)) {
                throw new \Exception(t('Invalid parameter received: %s', 'id'));
            }
            $singular = '';
            $plurals = array();
            if ($this->post('clear') !== '1') {
                $translated = $this->post('translated');
                if ((!is_array($translated)) || (count($translated) < 1)) {
                    throw new \Exception(t('Invalid parameter received: %s', 'translated'));
                }
                if ($translation->hasPlural()) {
                    $singular = array_shift($translated);
                    $plurals = $translated;
                } else {
                    if (count($translated) !== 1) {
                        throw new \Exception(t('Invalid parameter received: %s', 'translated'));
                    }
                    $singular = $translated[0];
                }
            }
            $translation->updateTranslation($singular, $plurals);
        } catch (\Exception $x) {
            $result->setError($x);
        }
        $result->outputJSON();
    }

    public function export_translations($localeCode)
    {
        $result = new EditResponse();
        try {
            if (!Core::make('token')->validate('export_translations')) {
                throw new \Exception(Core::make('token')->getErrorMessage());
            }
            $section = Section::getByLocale($localeCode);
            if (is_object($section) && (!$section->isError())) {
                if ($section->getLocale() == $this->site->getConfigRepository()->get('multilingual.default_source_locale')) {
                    $section = null;
                }
            } else {
                $section = null;
            }
            if (!isset($section)) {
                throw new \Exception(t('Invalid language identifier'));
            }
            $translations = $section->getSectionInterfaceTranslations();
            $extractor = Core::make('multilingual/extractor');
            $extractor->mergeTranslationsWithSectionFile($section, $translations);
            $extractor->saveSectionTranslationsToFile($section, $translations);
            \Localization::clearCache();
            $result->setAdditionalDataAttribute('newToken', Core::make('token')->generate('export_translations'));
            $result->message = t('The translations have been exported to file and will be used by the website.');
        } catch (\Exception $x) {
            $result->setError($x);
        }
        $result->outputJSON();
    }

    public function translate_po($mtSectionID = false)
    {
        $this->view();

        $mtSectionID = intval($mtSectionID);
        $section = Section::getByID(intval($mtSectionID));
        if ($section) {
            $translations = $section->getSectionInterfaceTranslations();
            $this->requireAsset('core/translator');
            $this->set('section', $section);
            $jsonTranslations = array();
            $numPlurals = $section->getNumberOfPluralForms();
            foreach ($translations as $translation) {
                /* @var $translation \Concrete\Core\Multilingual\Page\Section\Translation */
                $jsonTranslation = array();
                $jsonTranslation['id'] = $translation->getRecordID();
                if ($translation->hasContext()) {
                    $jsonTranslation['context'] = $translation->getContext();
                }
                $jsonTranslation['original'] = $translation->getOriginal();
                if ($translation->hasPlural()) {
                    $jsonTranslation['originalPlural'] = $translation->getPlural();
                }
                if ($translation->hasTranslation()) {
                    $t = array($translation->getTranslation());
                    if (($numPlurals > 1) && $translation->hasPlural()) {
                        $t = array_merge($t, $translation->getPluralTranslation());
                    }
                    $jsonTranslation['translations'] = $t;
                }
                $extractedComments = '';
                if ($translation->hasExtractedComments()) {
                    $extractedComments = trim(implode("\n", $translation->getExtractedComments()));
                }
                if ($extractedComments !== '') {
                    $jsonTranslation['comments'] = $extractedComments;
                }
                if ($translation->hasReferences()) {
                    $jsonTranslation['references'] = $translation->getReferences();
                }
                $jsonTranslations[] = $jsonTranslation;
            }

            $this->set('translations', $jsonTranslations);
            $this->set('textDirection', Misc::getCharacterOrder($section->getLocale()) === 'right-to-left' ? 'rtl' : 'ltr');
        } else {
            $this->redirect('/dashboard/system/multilingual/translate_interface');
        }
    }
}
