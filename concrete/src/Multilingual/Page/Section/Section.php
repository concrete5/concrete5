<?php
namespace Concrete\Core\Multilingual\Page\Section;

use Concrete\Core\Entity\Site\Locale;
use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Entity\Site\SiteTree;
use Concrete\Core\Page\Page;
use Concrete\Core\Site\Tree\TreeInterface;
use Database;
use Concrete\Core\Multilingual\Page\Event;
use Gettext\Translations;
use Punic\Language;

defined('C5_EXECUTE') or die('Access Denied.');

class Section extends Page
{

    public static function isMultilingualSection($cID)
    {
        if (is_object($cID)) {
            $cID = $cID->getCollectionID();
        }

        if ($cID) {
            $entity = self::getLocaleFromHomePageID($cID);

            return is_object($entity);
        }
    }

    protected static function getLocaleFromHomePageID($cID)
    {
        $em = Database::get()->getEntityManager();
        $tree = $em->getRepository('Concrete\Core\Entity\Site\SiteTree')
            ->findOneBySiteHomePageID($cID);
        if (is_object($tree)) {
            return $em->getRepository('Concrete\Core\Entity\Site\Locale')
                ->findOneByTree($tree);
        }
    }


    protected function setLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * @var Locale
     */
    protected $locale;

    public function getPermissionResponseClassName()
    {
        return '\\Concrete\\Core\\Permission\\Response\\MultilingualSectionResponse';
    }

    public function getPermissionObjectKeyCategoryHandle()
    {
        return 'multilingual_section';
    }

    /**
     * Returns an instance of  MultilingualSection for the given page ID.
     *
     * @param int $cID
     * @param int|string $cvID
     * @param string $class
     *
     * @return MultilingualSection|false
     */
    public static function getByID($cID, $cvID = 'RECENT')
    {
        $entity = self::getLocaleFromHomePageID($cID);
        if ($entity) {
            $obj = parent::getByID($cID, $cvID);
            $obj->setLocale($entity);

            return $obj;
        }

        return false;
    }

    /**
     * @param Page $page
     *
     * @return Section
     */
    public static function getBySectionOfSite($page)
    {
        $identifier = sprintf('/multilingual/section/%s', $page->getCollectionID());
        $cache = \Core::make('cache/request');
        $item = $cache->getItem($identifier);
        if (!$item->isMiss()) {
            $returnID = $item->get();
        } else {

            $item->lock();

            $returnID = false;

            if ($page->isPageDraft()) {
                $tree = false;
                if ($page->getPageDraftTargetParentPageID()) {
                    $cParentID = $page->getPageDraftTargetParentPageID();
                    if ($cParentID) {
                        $parent = Page::getByID($cParentID);
                        $tree = $parent->getSiteTreeObject();
                    }
                }
            }

            if (!isset($tree)) {
                $tree = $page->getSiteTreeObject();
            }

            if ($tree instanceof SiteTree) {
                $returnID = $tree->getSiteHomePageID();
            }

            $cache->save($item->set($returnID));
        }

        if ($returnID) {
            return static::getByID($returnID);
        }
    }


    public function getLanguageText($locale = null)
    {
        return $this->locale->getLanguageText($locale);
    }

    public function getLanguage()
    {
        return $this->locale->getLanguage();
    }

    public function getIcon()
    {
        return $this->locale->getCountry();
    }

    public function getCountry()
    {
        return $this->locale->getCountry();
    }

    /**
     * Returns the number of plural forms.
     *
     * @return int
     *
     * @example For Japanese: returns 1
     * @example For English: returns 2
     * @example For French: returns 2
     * @example For Russian returns 3
     */
    public function getNumberOfPluralForms()
    {
        return (int) $this->locale->getNumPlurals();
    }

    /**
     * Returns the rule to determine which plural we should use (in gettext notation).
     *
     * @return string
     *
     * @example For Japanese: returns '0'
     * @example For English: returns 'n != 1'
     * @example For French: returns 'n > 1'
     * @example For Russian returns '(n % 10 == 1 && n % 100 != 11) ? 0 : ((n % 10 >= 2 && n % 10 <= 4 && (n % 100 < 12 || n % 100 > 14)) ? 1 : 2)'
     */
    public function getPluralsRule()
    {
        return (string) $this->locale->getPluralRule();
    }

    /**
     * Returns the plural cases for the language; array keys are the case name, array values are some examples for that case.
     *
     * @return array
     *
     * @example For Japanese: returns
     *     'other' => '0~15, 100, 1000, 10000, 100000, 1000000, …'
     * @example For English: returns
     *     'one' => '1',
     *     'other' => '0, 2~16, 100, 1000, 10000, 100000, 1000000, …'
     * @example For French: returns
     *     'one' => '0, 1',
     *     'other' => '2~17, 100, 1000, 10000, 100000, 1000000, …'
     * @example For Russian returns
     *     'one' => '1, 21, 31, 41, 51, 61, 71, 81, 101, 1001, …',
     *     'few' => '2~4, 22~24, 32~34, 42~44, 52~54, 62, 102, 1002, …',
     *     'other' => '0, 5~19, 100, 1000, 10000, 100000, 1000000, …',
     */
    public function getPluralsCases()
    {
        return (array) $this->locale->getPluralCases();
    }

    public static function getIDList(Site $site = null)
    {
        if (!$site) {
            $site = \Site::getSite();
        }

        $cache = \Core::make('cache/request');
        $item = $cache->getItem(sprintf('multilingual/section/ids/%s', $site->getSiteID()));
        if ($item->isMiss()) {
            $ids = [];
            foreach($site->getLocales() as $locale) {
                $tree = $locale->getSiteTree();
                if (is_object($tree)) {
                    $ids[] = $tree->getSiteHomePageID();
                }
            }
            $cache->save($item->set($ids));
        } else {
            $ids = $item->get();
        }

        return $ids;
    }

    public static function getList(Site $site = null)
    {
        $ids = self::getIDList($site);
        $pages = [];
        if ($ids && is_array($ids)) {
            foreach ($ids as $cID) {
                $obj = self::getByID($cID);
                if (is_object($obj)) {
                    $pages[] = $obj;
                }
            }
        }

        return $pages;
    }

    public static function getRelatedCollectionIDForLocale($cID, $locale)
    {
        $mpRelationID = self::getMultilingualPageRelationID($cID);

        if (!$mpRelationID) {
            return null;
        }

        $relatedCID = self::getCollectionIDForLocale($mpRelationID, $locale);

        return $relatedCID;
    }

    public static function getMultilingualPageRelationID($cID)
    {
        $db = Database::get();

        $mpRelationID = $db->getOne(
            'select mpRelationID from MultilingualPageRelations where cID = ?',
            [$cID]
        );

        return $mpRelationID;
    }

    public static function isAssigned($page)
    {
        $mpRelationID = self::getMultilingualPageRelationID($page->getCollectionID());

        return $mpRelationID > 0;
    }


    public static function getCollectionIDForLocale($mpRelationID, $locale)
    {
        $db = Database::get();

        $cID = $db->GetOne(
            'select cID from MultilingualPageRelations where mpRelationID = ? and mpLocale = ?',
            [$mpRelationID, $locale]
        );

        return $cID;
    }

    public static function registerDuplicate($newPage, $oldPage)
    {
        $db = Database::get();

        $mpRelationID = self::getMultilingualPageRelationID($oldPage->getCollectionID());

        if (static::isMultilingualSection($newPage)) {
            $ms = static::getByID($newPage->getCollectionID());
        } else {
            $ms = static::getBySectionOfSite($newPage);
        }
        if (static::isMultilingualSection($oldPage)) {
            $msx = static::getByID($oldPage->getCollectionID());
        } else {
            $msx = static::getBySectionOfSite($oldPage);
        }
        $isNew = false;
        if (is_object($ms)) {
            if (!$mpRelationID) {
                $isNew = true;
                $mpRelationID = $db->GetOne('select max(mpRelationID) as mpRelationID from MultilingualPageRelations');
                if (!$mpRelationID) {
                    $mpRelationID = 1;
                } else {
                    ++$mpRelationID;
                }

                // adding in a check to see if old page was part of a language section or neutral.
                if (is_object($msx)) {
                    $db->Execute(
                        'insert into MultilingualPageRelations (mpRelationID, cID, mpLanguage, mpLocale) values (?, ?, ?, ?)',
                        [
                            $mpRelationID,
                            $oldPage->getCollectionID(),
                            $msx->getLanguage(),
                            $msx->getLocale(),
                        ]
                    );
                }
            }

            if ($isNew) {
                $cID = self::getCollectionIDForLocale($mpRelationID, $ms->getLocale());

                if ($cID > 0) {
                    $db->Execute(
                        'delete from MultilingualPageRelations where mpRelationID = ? and mpLocale = ?',
                        [$mpRelationID, $ms->getLocale()]
                    );
                }
            }

            $v = [
                $mpRelationID,
                $newPage->getCollectionID(),
                $ms->getLocale(),
                $ms->getLanguage()
            ];

            $db->Execute('insert into MultilingualPageRelations (mpRelationID, cID, mpLocale, mpLanguage) values (?, ?, ?, ?)', $v);

            $pde = new Event($newPage);
            $pde->setLocale($ms->getLocale());
            \Events::dispatch('on_multilingual_page_relate', $pde);
        }
    }

    /**
     * @param string $language
     *
     * @return Section|false
     */
    public static function getByLanguage($language, Site $site = null)
    {
        if (!$site) {
            $site = \Core::make('site')->getSite();
        }

        $em = Database::get()->getEntityManager();
        /**
         * @var $section Locale
         */
        $section = $em->getRepository('Concrete\Core\Entity\Site\Locale')
            ->findOneBy(['site' => $site, 'msLanguage' => $language]);

        if (is_object($section)) {
            $obj = parent::getByID($section->getSiteTree()->getSiteHomePageID(), 'RECENT');
            $obj->setLocale($section);

            return $obj;
        }

        return false;
    }

    /**
     * @param string $language
     *
     * @return Section|false
     */
    public static function getByLocale($locale, Site $site = null)
    {
        if (!$site) {
            $site = \Core::make('site')->getSite();
        }
        if ($locale) {
            if (!is_object($locale)) {
                $locale = explode('_', $locale);
                if (!isset($locale[1])) {
                    $locale[1] = '';
                }
                $em = Database::get()->getEntityManager();
                $locale = $em->getRepository('Concrete\Core\Entity\Site\Locale')
                    ->findOneBy(['site' => $site, 'msLanguage' => $locale[0], 'msCountry' => $locale[1]]);
            }

            if (is_object($locale)) {
                $obj = parent::getByID($locale->getSiteTree()->getSiteHomePageID(), 'RECENT');
                $obj->setLocale($locale);

                return $obj;
            }
        }

        return false;
    }

    /**
     * Gets the MultilingualSection object for the current section of the site.
     *
     * @return Section
     */
    public static function getCurrentSection()
    {
        static $lang;
        if (!isset($lang)) {
            $c = Page::getCurrentPage();
            if ($c instanceof Page) {
                $lang = self::getBySectionOfSite($c);
            }
        }

        return $lang;
    }

    public static function getByLocaleOrLanguage($locale)
    {
        $explode = preg_split('/-|_/', $locale);
        if (count($explode) == 2) {
            // we have a language first, and a country second
            $section = static::getByLocale($explode[0] . '_' . $explode[1]);
            if ($section) {
                return $section;
            }
        }
        $section = static::getByLanguage($explode[0]);

        return $section;
    }

    public function getLocaleObject()
    {
        return $this->locale;
    }

    public function getLocale()
    {
        $locale = $this->locale->getLocale();
        return $locale;
    }

    public static function getDefaultSection(Site $site = null)
    {
        if (!is_object($site)) {
            $site = \Site::getSite();
        }

        $default_locale = $site->getDefaultLocale();

        return static::getByLocale($default_locale);
    }

    public static function unregisterPage($page)
    {
        if ($page->isAlias()) {
            return;
        }
        $db = Database::get();
        $db->Execute('delete from MultilingualPageRelations where cID = ?', [$page->getCollectionID()]);
    }

    public static function registerPage($page)
    {
        if ($page->isAlias()) {
            return;
        }
        if (\Core::make('multilingual/detector')->isEnabled()) {
            $db = Database::get();
            $ms = static::getBySectionOfSite($page);
            if (is_object($ms)) {
                $mpRelationID = self::getMultilingualPageRelationID($page->getCollectionID());

                if ($mpRelationID) {
                    // already exists. We quit and return
                    return $mpRelationID;
                }

                // otherwise, we create a new one.

                $mpRelationID = $db->GetOne('select max(mpRelationID) as mpRelationID from MultilingualPageRelations');
                if (!$mpRelationID) {
                    $mpRelationID = 1;
                } else {
                    ++$mpRelationID;
                }
                $v = [$mpRelationID, $page->getCollectionID(), $ms->getLanguage(), $ms->getLocale()];
                $db->Execute(
                    'insert into MultilingualPageRelations (mpRelationID, cID, mpLanguage, mpLocale) values (?, ?, ?, ?)',
                    $v
                );
                $pde = new Event($page);
                $pde->setLocale($ms->getLocale());
                \Events::dispatch('on_multilingual_page_relate', $pde);

                return $mpRelationID;
            }
        }
    }

    public static function registerMove($page, $oldParent, $newParent)
    {
        if ($page->isAlias()) {
            return;
        }

        if (static::isMultilingualSection($newParent)) {
            $ms = static::getByID($newParent->getCollectionID());
        } else {
            $ms = static::getBySectionOfSite($newParent);
        }
        if (self::isMultilingualSection($oldParent)) {
            $msx = static::getByID($oldParent->getCollectionID());
        } else {
            $msx = static::getBySectionOfSite($oldParent);
        }
        $db = Database::get();
        if (is_object($ms)) {
            $cID = $db->GetOne(
                'select cID from MultilingualPageRelations where cID = ?',
                [$page->getCollectionID()]
            );
            if (!$cID) {
                $mpRelationID = $db->GetOne('select max(mpRelationID) as mpRelationID from MultilingualPageRelations');
                if (!$mpRelationID) {
                    $mpRelationID = 1;
                } else {
                    ++$mpRelationID;
                }
                $v = [$mpRelationID, $page->getCollectionID(), $ms->getLanguage(), $ms->getLocale()];
                $db->Execute(
                    'insert into MultilingualPageRelations (mpRelationID, cID, mpLanguage, mpLocale) values (?, ?, ?, ?)',
                    $v
                );
            } else {
                $db->Execute(
                    'update MultilingualPageRelations set mpLanguage = ? where cID = ?',
                    [$ms->getLanguage(), $page->getCollectionID()]
                );
            }
        } else {
            self::unregisterPage($page);
        }
    }

    public static function relatePage($oldPage, $newPage, $locale)
    {
        $db = Database::get();
        $mpRelationID = self::getMultilingualPageRelationID($oldPage->getCollectionID());

        $section = self::getByLocale($locale);

        if ($mpRelationID && $section) {
            $v = [$mpRelationID, $newPage->getCollectionID(), $section->getLocale(), $section->getLanguage()];
            $db->Execute(
                'delete from MultilingualPageRelations where mpRelationID = ? and mpLocale = ?',
                [$mpRelationID, $section->getLocale()]
            );
            $db->Execute('delete from MultilingualPageRelations where cID = ?', [$newPage->getCollectionID()]);
            $db->Execute('insert into MultilingualPageRelations (mpRelationID, cID, mpLocale, mpLanguage) values (?, ?, ?, ?)', $v);
            $pde = new Event($newPage);
            $pde->setLocale($locale);
            \Events::dispatch('on_multilingual_page_relate', $pde);
        }
    }

    public function isDefaultMultilingualSection(Site $site = null)
    {
        if (!is_object($site)) {
            $site = \Site::getSite();
        }

        $default_locale = $site->getDefaultLocale();
        return $this->getLocale() == $default_locale->getLocale();
    }

    public static function ignorePageRelation($page, $locale)
    {
        $db = Database::get();

        // first, we retrieve the relation for the page in the default locale.
        $mpRelationID = static::registerPage($page);

        $v = [$mpRelationID, 0, $locale];
        $db->Execute('insert into MultilingualPageRelations (mpRelationID, cID, mpLocale) values (?, ?, ?)', $v);
        $pde = new Event($page);
        $pde->setLocale($locale);
        \Events::dispatch('on_multilingual_page_ignore', $pde);
    }


    /**
     * Receives a page in a different language tree, and tries to return the corresponding page in the current language tree.
     *
     * @return int|null
     */
    public function getTranslatedPageID($page)
    {
        $ids = static::getIDList();
        if (in_array($page->getCollectionID(), $ids)) {
            return $this->locale->getSiteTree()->getSiteHomePageID();
        }

        $mpRelationID = self::getMultilingualPageRelationID($page->getCollectionID());

        if ($mpRelationID) {
            $cID = self::getCollectionIDForLocale($mpRelationID, $this->getLocale());

            return $cID;
        }
    }

    /**
     * Loads the translations of this multilingual section.
     *
     * @param bool $untranslatedFirst Set to true to have untranslated strings first
     *
     * @return \Gettext\Translations
     */
    public function getSectionInterfaceTranslations($untranslatedFirst = false)
    {
        $translations = new Translations();
        $translations->setLanguage($this->getLocale());
        $translations->setPluralForms($this->getNumberOfPluralForms(), $this->getPluralsRule());
        $db = \Database::get();
        $r = $db->query(
            "select *
            from MultilingualTranslations
            where mtSectionID = ?
            order by ".($untranslatedFirst ? "if(ifnull(msgstr, '') = '', 0, 1), " : "")."mtID",
            [$this->getCollectionID()]
        );
        while ($row = $r->fetch()) {
            $t = Translation::getByRow($row);
            if (isset($t)) {
                $translations[] = $t;
            }
        }

        return $translations;
    }



}
