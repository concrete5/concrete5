<?php
namespace Concrete\Core\Multilingual\Service;

use Concrete\Core\Multilingual\Page\Section\Section;
use Concrete\Core\Package\Package;
use Gettext\Extractors\Mo as MoExtractor;
use Gettext\Extractors\Po as PoExtractor;
use Gettext\Translations;
use Gettext\Generators\Po as PoGenerator;
use Gettext\Generators\Mo as MoGenerator;
use Concrete\Core\Package\PackageList;
use Concrete\Core\Support\Facade\Application;
use C5TL\Options as C5TLOptions;
use C5TL\Parser\Php as C5TLParserPhp;
use C5TL\Parser\BlockTemplates as C5TLParserBlockTemplates;
use C5TL\Parser\ThemePresets as C5TLParserThemePresets;
use C5TL\Parser\ConfigFiles as C5TLParserConfigFiles;
use C5TL\Parser\Dynamic as C5TLParserDynamic;

defined('C5_EXECUTE') or die('Access Denied.');

class Extractor
{
    /**
     * return \GetText\Translations $translations;.
     */
    public function extractTranslatableSiteStrings()
    {
        $app = Application::getFacadeApplication();
        $translations = new Translations();
        $translations->insert('SiteName', $app->make('site')->getSite()->getSiteName());
        $fh = $app->make('helper/file');
        C5TLOptions::setTemporaryDirectory($fh->getTemporaryDirectory());
        $phpParser = new C5TLParserPhp();
        $blockTemplatesParser = new C5TLParserBlockTemplates();
        $themesPresetsParser = new C5TLParserThemePresets();
        $configFilesParser = new C5TLParserConfigFiles();

        $configFilesParser->parseDirectory(DIR_BASE, '', $translations);

        $processApplication = [
            DIRNAME_BLOCKS => [$phpParser, $blockTemplatesParser],
            DIRNAME_ELEMENTS => [$phpParser],
            DIRNAME_CONTROLLERS => [$phpParser],
            DIRNAME_MAIL_TEMPLATES => [$phpParser],
            DIRNAME_PAGE_TYPES => [$phpParser],
            DIRNAME_PAGES => [$phpParser],
            DIRNAME_THEMES => [$phpParser, $themesPresetsParser, $blockTemplatesParser],
            DIRNAME_VIEWS => [$phpParser],
            DIRNAME_CLASSES => [$phpParser]
        ];
        foreach ($processApplication as $dirname => $parsers) {
            if (is_dir(DIR_APPLICATION.'/'.$dirname)) {
                foreach ($parsers as $parser) {
                    /* @var $parser \C5TL\Parser */
                    $fullDirname = DIR_APPLICATION.'/'.$dirname;
                    if (is_dir($fullDirname)) {
                        $parser->parseDirectory(
                            $fullDirname,
                            DIRNAME_APPLICATION.'/'.$dirname,
                            $translations
                        );
                    }
                }
            }
        }

        if (is_dir(DIR_PACKAGES)) {
            $packages = Package::getInstalledList();
            foreach ($packages as $package) {
                $fullDirname = DIR_PACKAGES.'/'.$package->getPackageHandle();
                $phpParser->parseDirectory($fullDirname,
                    DIRNAME_PACKAGES.'/'.$package->getPackageHandle(),
                    $translations
                );
                $packageController = $package->getController();
                $packageController->getTranslatableStrings($translations);
            }
        }

        // Now, we grab dynamic content that's part of our site that we translate dynamically
        $dynamicTranslations = $this->getDynamicTranslations();
        $translations->mergeWith($dynamicTranslations);

        return $translations;
    }

    public function getDynamicTranslations()
    {
        $app = Application::getFacadeApplication();
        $fh = $app->make('helper/file');
        C5TLOptions::setTemporaryDirectory($fh->getTemporaryDirectory());
        $parser = new C5TLParserDynamic();

        return $parser->parseRunningConcrete5();
    }

    public function clearTranslationsFromDatabase()
    {
        $app = Application::getFacadeApplication();
        $db = $app->make('database')->connection();
        $db->executeQuery('truncate table MultilingualTranslations');
    }

    public function deleteSectionTranslationFile(Section $section)
    {
        $po = DIR_LANGUAGES_SITE_INTERFACE . '/' . $section->getLocale() . '.po';
        $mo = DIR_LANGUAGES_SITE_INTERFACE . '/' . $section->getLocale() . '.mo';
        if (is_file($po)) {
            unlink($po);
        }
        if (is_file($mo)) {
            unlink($mo);
        }
    }

    public function mergeTranslationsWithSectionFile(Section $section, Translations $translations)
    {
        $file = DIR_LANGUAGES_SITE_INTERFACE . '/' . $section->getLocale() . '.po';
        if (is_file($file)) {
            $sectionTranslations = PoExtractor::fromFile($file);
            $translations->mergeWith($sectionTranslations);
        }
    }

    public function mergeTranslationsWithCore(Section $section, Translations $translations)
    {
        // Now we're going to load the core translations.
        $poFile = DIR_LANGUAGES . '/' . $section->getLocale() . '/LC_MESSAGES/messages.po';
        $moFile = DIR_LANGUAGES . '/' . $section->getLocale() . '/LC_MESSAGES/messages.mo';
        $coreTranslations = null;
        if (is_file($poFile)) {
            $coreTranslations = PoExtractor::fromFile($poFile);
        } elseif (is_file($moFile)) {
            $coreTranslations = MoExtractor::fromFile($moFile);
        }

        if (isset($coreTranslations)) {
            // Now that we have the core translations, we loop through all the translations from above, and check
            // to see if the core has a translation for this string. If the core does not, we include it in the translations
            // object to return.

            // This is actually much faster than unsetting the matching translation from the existing translations object

            foreach ($translations as $translation) {
                /* @var $translation \Gettext\Translation */
                if (!$translation->hasTranslation()) {
                    $coreTranslation = $coreTranslations->find($translation);
                    if ($coreTranslation && $coreTranslation->hasTranslation()) {
                        $translation->mergeWith($coreTranslation);
                    }
                }
            }
        }
    }

    public function mergeTranslationsWithPackages(Section $section, Translations $translations)
    {
        foreach (PackageList::get()->getPackages() as $package) {
            /* @var $package \Concrete\Core\Package\Package */
            $baseDir = $package->getPackagePath() . '/' . DIRNAME_LANGUAGES . '/' . $section->getLocale() . '/LC_MESSAGES';
            $poFile = $baseDir . '/messages.po';
            $moFile = $baseDir . '/messages.mo';
            $packageTranslations = null;
            if (is_file($poFile)) {
                $packageTranslations = PoExtractor::fromFile($poFile);
            } elseif (is_file($moFile)) {
                $packageTranslations = MoExtractor::fromFile($moFile);
            }
            if (isset($packageTranslations)) {
                foreach ($translations as $translation) {
                    /* @var $translation \Gettext\Translation */
                    if (!$translation->hasTranslation()) {
                        $packageTranslation = $packageTranslations->find($translation);
                        if ($packageTranslation && $packageTranslation->hasTranslation()) {
                            $translation->mergeWith($packageTranslation);
                        }
                    }
                }
            }
        }
    }

    public function saveSectionTranslationsToFile(Section $section, Translations $translations)
    {
        $po = DIR_LANGUAGES_SITE_INTERFACE . '/' . $section->getLocale() . '.po';
        $mo = DIR_LANGUAGES_SITE_INTERFACE . '/' . $section->getLocale() . '.mo';

        PoGenerator::toFile($translations, $po);

        /* Do not generate mo for empty catalog, it crashes Zend\I18n gettext loader */
        $empty = true;
        foreach ($translations as $entry) {
            if ($entry->hasTranslation()) {
                $empty = false;
                break;
            }
        }

        if (!$empty) {
            MoGenerator::$includeEmptyTranslations = true;
            MoGenerator::toFile($translations, $mo);
        } else {
            if (is_file($mo)) {
                unlink($mo);
            }
        }
    }

    public function saveSectionTranslationsToDatabase(Section $section, Translations $translations)
    {
        $app = Application::getFacadeApplication();
        $db = $app->make('database')->connection();
        $db->delete('MultilingualTranslations', ['mtSectionID' => $section->getCollectionID()]);
        foreach ($translations as $translation) {
            /* @var $translation \Gettext\Translation */
            $data = [
                'mtSectionID' => $section->getCollectionID(),
                'msgid' => $translation->getOriginal(),
                'msgidPlural' => $translation->getPlural(),
                'msgstr' => $translation->getTranslation(),
                'context' => $translation->getContext(),
            ];
            $plurals = $translation->getPluralTranslation();
            if (!empty($plurals)) {
                $data['msgstrPlurals'] = implode("\x00", $plurals);
            }
            $comments = $translation->getExtractedComments();
            if (!empty($comments)) {
                $data['comments'] = implode("\n", $comments);
            }
            $references = $translation->getReferences();
            if (!empty($references)) {
                $data['reference'] = '';
                foreach ($translation->getReferences() as $reference) {
                    $data['reference'] .= (isset($reference[1]) ? implode(':', $reference) : $reference[0]) . "\n";
                }
            }
            $flags = $translation->getFlags();
            if (!empty($flags)) {
                $data['flags'] = implode("\n", $flags);
            }
            $db->insert('MultilingualTranslations', $data);
        }
    }

    public function getSectionSiteInterfaceCompletionData(Section $section)
    {
        $app = Application::getFacadeApplication();
        $db = $app->make('database')->connection();
        $data = [];
        $data['messageCount'] = $db->fetchColumn('select count(mtID) from MultilingualTranslations where mtSectionID = ?',
            [$section->getCollectionID()]
        );
        $data['translatedCount'] = $db->fetchColumn(
            'select count(mtID) from MultilingualTranslations where mtSectionID = ? and msgstr != ""',
            [$section->getCollectionID()]
        );
        $data['completionPercentage'] = 0;
        if ($data['messageCount'] > 0) {
            $data['completionPercentage'] = round(($data['translatedCount'] / $data['messageCount']) * 100);
        }

        return $data;
    }
}
