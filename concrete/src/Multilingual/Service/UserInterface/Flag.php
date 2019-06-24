<?php
namespace Concrete\Core\Multilingual\Service\UserInterface;

use Concrete\Core\Entity\Site\Locale;
use Concrete\Core\Localization\Locale\LocaleInterface;
use Concrete\Core\Multilingual\Page\Section\Section;
use Database;
use HtmlObject\Image;

defined('C5_EXECUTE') or die('Access Denied.');

class Flag
{
    /**
     * Returns a flag for a passed country/region.
     */
    public static function getFlagIcon($region, $filePathOnly = false)
    {
        $val = \Core::make('helper/validation/strings');
        if ($val->alphanum($region, false, true)) {
            $region = h(strtolower($region));
        } else {
            $region = false;
        }

        if ($region) {
            $v = \View::getInstance();

            if ($v->getThemeDirectory() != '' && file_exists(
                $v->getThemeDirectory() . '/' . DIRNAME_IMAGES . '/' . DIRNAME_IMAGES_LANGUAGES . '/' . $region . '.png'
            )) {
                $icon = $v->getThemePath() . '/' . DIRNAME_IMAGES . '/' . DIRNAME_IMAGES_LANGUAGES . '/' . $region . '.png';
            } elseif (file_exists(
                DIR_APPLICATION . '/' . DIRNAME_IMAGES . '/' . DIRNAME_IMAGES_LANGUAGES . '/' . $region . '.png'
            )) {
                $icon = REL_DIR_APPLICATION . '/' . DIRNAME_IMAGES . '/' . DIRNAME_IMAGES_LANGUAGES . '/' . $region . '.png';
            } else {
                $icon = ASSETS_URL . '/' . DIRNAME_IMAGES . '/' . DIRNAME_IMAGES_LANGUAGES . '/' . $region . '.png';
            }

            if (isset($icon)) {
                if ($filePathOnly) {
                    return $icon;
                } else {
                    $img = new Image($icon, $region, ['id' => 'ccm-region-flag-' . $region, 'class' => 'ccm-region-flag']);
                    return $img;
                }
            }
        }
    }

    public static function getSectionFlagIcon($page, $filePathOnly = false)
    {
        $db = Database::get();
        $section = Section::getBySectionOfSite($page);
        $icon = $section->getCountry();
        return self::getFlagIcon($icon, $filePathOnly);
    }

    public static function getLocaleFlagIcon(LocaleInterface $locale, $filePathOnly = false)
    {
        $icon = $locale->getCountry();
        return self::getFlagIcon($icon, $filePathOnly);
    }


    public static function getDashboardSitemapIconSRC($page)
    {
        if ($page->getPageTypeHandle() == STACK_CATEGORY_PAGE_TYPE) {
            $section = Section::getByLocale($page->getCollectionName());
            if (is_object($section)) {
                return self::getSectionFlagIcon($section, true);
            }
        }
        $ids = Section::getIDList();
        if (in_array($page->getCollectionID(), $ids)) {
            return self::getSectionFlagIcon($page, true);
        }
    }
}
