<?php
namespace Concrete\Core\Localization;

use Loader;
use Cache;
use Events;
use Concrete\Core\Asset\AssetList;
use Concrete\Core\Asset\AssetPointer;

class Localization
{
    private static $loc
    = null;

    public static function getInstance()
    {
        if (null === self::$loc) {
            self::$loc = new self;

        }

        return self::$loc;
    }

    public static function changeLocale($locale)
    {
        $loc = Localization::getInstance();
        $loc->setLocale($locale);
    }
    /** Returns the currently active locale
	* @return string
	* @example 'en_US'
	*/
    public static function activeLocale()
    {
        $loc = Localization::getInstance();

        return $loc->getLocale();
    }
    /** Returns the language for the currently active locale
	* @return string
	* @example 'en'
	*/
    public static function activeLanguage()
    {
        return current(explode('_', self::activeLocale()));
    }

    protected $translate;

    public function __construct()
    {
        \Zend_Date::setOptions(array('format_type' => 'php'));
        $cache = Cache::getLibrary();
        if (is_object($cache)) {
            \Zend_Translate::setCache($cache);
            \Zend_Date::setOptions(array('cache'=>$cache));
        }
    }

    public function setLocale($locale)
    {
        $assetList = AssetList::getInstance();
        $assetList->unregister('javascript', 'redactor_locale');
        $assetList->unregister('inline_javascript', 'redactor_locale');
        $localeNeededLoading = false;
        if (!ENABLE_TRANSLATE_LOCALE_EN_US && $locale == 'en_US' && isset($this->translate)) {
            unset($this->translate);

            return;
        }
        if (!(ENABLE_TRANSLATE_LOCALE_EN_US || $locale != 'en_US')) {
            return;
        }

        if (is_dir(DIR_LANGUAGES . '/' . $locale)) {
            $languageDir = DIR_LANGUAGES . '/' . $locale;
        } elseif (is_dir(DIR_LANGUAGES_CORE . '/' . $locale)) {
            $languageDir = DIR_LANGUAGES_CORE . '/' . $locale;
        }
        if (!$languageDir) {
            return;
        }

        $options = array(
            'adapter' => 'Zend_Translate_Adapter_Gettext',
            'content' => $languageDir,
            'locale'  => $locale,
            'disableNotices'  => true
        );
        if (defined('TRANSLATE_OPTIONS')) {
            $_options = unserialize(TRANSLATE_OPTIONS);
            if (is_array($_options)) {
                $options = array_merge($options, $_options);
            }
        }

        if (!isset($this->translate)) {
            $this->translate = new \Zend_Translate($options);
            $localeNeededLoading = true;
        } else {
            if (!in_array($locale, $this->translate->getList())) {
                $this->translate->addTranslation($options);
                $localeNeededLoading = true;
            }
            $this->translate->setLocale($locale);
        }

        $alternatives = array(Localization::activeLocale());
        if (Localization::activeLocale() !== Localization::activeLanguage()) {
            $alternatives[] = Localization::activeLanguage();
        }
        foreach ($alternatives as $alternative) {
            $relFilename = DIRNAME_JAVASCRIPT . '/i18n/redactor-' . $alternative . '.js';
            $filename = DIR_BASE_CORE . '/' . $relFilename;
            if (is_file($filename)) {
                $assetList->register('javascript', 'redactor_locale', $relFilename);
                $assetList->register('inline_javascript', 'redactor_locale', null)->setCode("$.Redactor.opts.lang = '$alternative';");
                $assetGroup = $assetList->getAssetGroup('redactor');
                if (is_object($assetGroup)) {
                    $assetGroup->add(new AssetPointer('javascript', 'redactor_locale'));
                    $assetGroup->add(new AssetPointer('inline_javascript', 'redactor_locale'));
                }
                break;
            }
        }

        if ($localeNeededLoading) {
            $event = new \Symfony\Component\EventDispatcher\GenericEvent();
            $event->setArgument('locale', $locale);
            $ret = Events::dispatch('on_locale_load', $event);
        }
    }

    public function getLocale()
    {
        return isset($this->translate) ? $this->translate->getLocale() : 'en_US';
    }

    public function getActiveTranslateObject()
    {
        return $this->translate;
    }

    public function addSiteInterfaceLanguage($language)
    {
        if (is_object($this->translate)) {
            $this->translate->addTranslation(DIR_LANGUAGES_SITE_INTERFACE . '/' . $language . '.mo', $language);
        } else {
            $cache = Cache::getLibrary();
            if (is_object($cache)) {
                \Zend_Translate::setCache($cache);
            }
            $this->translate = new \Zend_Translate(array('adapter' => 'gettext', 'content' => DIR_LANGUAGES_SITE_INTERFACE . '/' . $language . '.mo', 'locale' => $language, 'disableNotices' => true));
        }
    }

    public static function getTranslate()
    {
        $loc = Localization::getInstance();

        return $loc->getActiveTranslateObject();
    }

    public static function getAvailableInterfaceLanguages()
    {
        $languages = array();
        $fh = Loader::helper('file');

        if (file_exists(DIR_LANGUAGES)) {
            $contents = $fh->getDirectoryContents(DIR_LANGUAGES);
            foreach ($contents as $con) {
                if (is_dir(DIR_LANGUAGES . '/' . $con) && file_exists(DIR_LANGUAGES . '/' . $con . '/LC_MESSAGES/messages.mo')) {
                    $languages[] = $con;
                }
            }
        }
        if (file_exists(DIR_LANGUAGES_CORE)) {
            $contents = $fh->getDirectoryContents(DIR_LANGUAGES_CORE);
            foreach ($contents as $con) {
                if (is_dir(DIR_LANGUAGES_CORE . '/' . $con) && file_exists(DIR_LANGUAGES_CORE . '/' . $con . '/LC_MESSAGES/messages.mo') && (!in_array($con, $languages))) {
                    $languages[] = $con;
                }
            }
        }

        return $languages;
    }

    /**
	 * Generates a list of all available languages and returns an array like
	 * [ "de_DE" => "Deutsch (Deutschland)",
	 *   "en_US" => "English (United States)",
	 *   "fr_FR" => "Francais (France)"]
	 * The result will be sorted by the key.
	 * If the $displayLocale is set, the language- and region-names will be returned in that language
	 * @param string $displayLocale Language of the description
	 * @return Array An associative Array with locale as the key and description as content
	 */
    public static function getAvailableInterfaceLanguageDescriptions($displayLocale = null)
    {
        $languages = self::getAvailableInterfaceLanguages();
        if (count($languages) > 0) {
            array_unshift($languages, 'en_US');
        }
        $locales = array();
        foreach ($languages as $lang) {
            $locales[$lang] = self::getLanguageDescription($lang,$displayLocale);
        }
        natcasesort($locales);

        return $locales;
    }

    /**
	 * Get the description of a locale consisting of language and region description
	 * e.g. "French (France)"
	 * @param string $locale Locale that should be described
	 * @param string $displayLocale Language of the description
	 * @return string Description of a language
	 */
    public static function getLanguageDescription($locale, $displayLocale = null)
    {
        $localeList = \Zend_Locale::getLocaleList();
        if (! isset($localeList[$locale])) {
            return $locale;
        }

        if ($displayLocale !== NULL && (! isset($localeList[$displayLocale]))) {
            $displayLocale = null;
        }

        $cacheLibrary = Cache::getLibrary();
        if (is_object($cacheLibrary)) {
            \Zend_Locale_Data::setCache($cacheLibrary);
        }

        $displayLocale = $displayLocale?$displayLocale:$locale;

        $zendLocale = new \Zend_Locale($locale);
        $languageName = \Zend_Locale::getTranslation($zendLocale->getLanguage(), 'language', $displayLocale);
        $description = $languageName;
        $region = $zendLocale->getRegion();
        if ($region !== false) {
            $regionName = \Zend_Locale::getTranslation($region, 'country', $displayLocale);
            if ($regionName !== false) {
                $localeData = \Zend_Locale_Data::getList($displayLocale, 'layout');
                if ($localeData['characters'] == "right-to-left") {
                    $description = '(' . $languageName . ' (' . $regionName ;
                } else {
                    $description = $languageName . ' (' . $regionName . ")";
                }

            }
        }

        return $description;
    }

}
