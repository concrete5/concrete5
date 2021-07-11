<?php

namespace Concrete\Core\Page\Collection;

use Concrete\Core\Area\Area;
use Block;
use CacheLocal;
use CollectionVersion;
use Concrete\Core\Area\CustomStyle as AreaCustomStyle;
use Concrete\Core\Area\GlobalArea;
use Concrete\Core\Attribute\Key\CollectionKey;
use Concrete\Core\Block\CustomStyle as BlockCustomStyle;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Database\Driver\PDOStatement;
use Concrete\Core\Entity\Attribute\Value\PageValue;
use Concrete\Core\Foundation\ConcreteObject;
use Concrete\Core\Gathering\Item\Page as PageGatheringItem;
use Concrete\Core\Page\Cloner;
use Concrete\Core\Page\ClonerOptions;
use Concrete\Core\Page\Collection\Version\VersionList;
use Concrete\Core\Page\Search\IndexedSearch;
use Concrete\Core\Page\Summary\Template\Populator;
use Concrete\Core\Search\Index\IndexManagerInterface;
use Concrete\Core\Statistics\UsageTracker\TrackableInterface;
use Concrete\Core\StyleCustomizer\Inline\StyleSet;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Facade;
use Config;
use Doctrine\DBAL\FetchMode;
use Loader;
use Page;
use PageCache;
use Permissions;
use Stack;

class Collection extends ConcreteObject implements TrackableInterface
{
    /**
     * The collection ID.
     *
     * @deprecated Use getCollectionID (what's deprecated is the public part)
     *
     * @var int|null
     */
    public $cID;

    /**
     * The collection version object.
     *
     * @var \Concrete\Core\Page\Collection\Version\Version|null
     */
    protected $vObj;

    /**
     * The collection handle.
     *
     * @var string|null
     */
    protected $cHandle;

    /**
     * The date/time when the collection has been created.
     *
     * @var string|null
     *
     * @example 2017-12-31 23:59:59
     */
    protected $cDateAdded;

    /**
     * The date/time when the collection was last modified.
     *
     * @var string|null
     *
     * @example 2017-12-31 23:59:59
     */
    protected $cDateModified;

    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * Destruct the class instance.
     */
    public function __destruct()
    {
        unset($this->attributes);
        unset($this->vObj);
    }

    /**
     * Get a collection by ID.
     *
     * @param int $cID The collection ID
     * @param string|int|false $version the collection version ('RECENT' for the most recent version, 'ACTIVE' for the currently published version, 'SCHEDULED' for the currently scheduled version, a falsy value to not load the collection version, or an integer to retrieve a specific version ID)
     *
     * @return \Concrete\Core\Page\Collection\Collection If the collection is not found, you'll get an empty Collection instance
     */
    public static function getByID($cID, $version = 'RECENT')
    {
        $app = Application::getFacadeApplication();
        /** @var Connection $db */
        $db = $app->make(Connection::class);
        $qb = $db->createQueryBuilder();
        $qb->select('c.cDateAdded', 'c.cDateModified', 'c.cID')
            ->from('Collections', 'c')
            ->where('c.cID = :cID')
            ->setParameter('cID', $cID);
        $row = $qb->execute()->fetch();

        $c = new self();
        if ($row !== false) {
            $c->setPropertiesFromArray($row);

            if ($version != false) {
                // we don't do this on the front page
                $c->loadVersionObject($version);
            }
        }

        return $c;
    }

    /**
     * Get a Collection by handle (provided that it's not a Page handle).
     * If there's there's no collection with the specified handle, a new Collection will be created.
     *
     * @param string $handle the collection handle
     *
     * @return \Concrete\Core\Page\Collection\Collection|null return NULL if $handle is the handle of an existing page
     */
    public static function getByHandle($handle)
    {
        $app = Application::getFacadeApplication();
        /** @var Connection $db */
        $db = $app->make(Connection::class);
        $qb = $db->createQueryBuilder();
        $qb->select('c.cID', 'p.cID')
            ->from('Collections', 'c')
            ->leftJoin('c', 'Pages', 'p', 'c.cID = p.cID')
            ->where('c.cHandle = :cHandle')
            ->setParameter('cHandle', $handle);

        // first we ensure that this does NOT appear in the Pages table. This is not a page. It is more basic than that
        /** @var PDOStatement $r */
        $r = $qb->execute();
        if ($r->rowCount() === 0) {
            // there is nothing in the collections table for this page, so we create and grab

            $data = [
                'handle' => $handle,
            ];
            $cObj = self::createCollection($data);
        } else {
            $row = $r->fetch();
            if ($row['cID'] > 0 && $row['pcID'] === null) {
                // there is a collection, but it is not a page. so we grab it
                $cObj = self::getByID($row['cID']);
            }
        }

        return $cObj ?? null;
    }

    /**
     * (Re)Index all the collections that are marked as to be (re)indexed.
     *
     * @return int returns the number of reindexed pages
     */
    public static function reindexPendingPages()
    {
        $app = Application::getFacadeApplication();

        $indexStack = $app->make(IndexManagerInterface::class);

        $num = 0;
        /** @var Connection $db */
        $db = $app->make(Connection::class);
        $qb = $db->createQueryBuilder();
        $r = $qb->select('cID')
            ->from('PageSearchIndex')
            ->where('cRequiresReindex = 1')
            ->execute();
        while ($id = $r->fetchColumn()) {
            $indexStack->index(\Concrete\Core\Page\Page::class, $id);
            $num++;
        }
        Config::save('concrete.misc.do_page_reindex_check', false);

        return $num;
    }

    /**
     * Create a new Collection instance.
     *
     * @param array $data {
     *
     *     @var int|null $cID The ID of the collection to create (if unspecified or NULL: database autoincrement value)
     *     @var string $handle The collection handle (default: NULL)
     *     @var string $name The collection name (default: empty string)
     *     @var string $cDescription The collection description (default: NULL)
     *     @var string $cDatePublic The collection publish date/time in format 'YYYY-MM-DD hh:mm:ss' (default: now)
     *     @var bool $cvIsApproved Is the collection version approved (default: true)
     *     @var bool $cvIsNew Is the collection to be considered "new"? (default: true if $cvIsApproved is false, false if $cvIsApproved is true)
     *     @var int|null $pThemeID The collection theme ID (default: NULL)
     *     @var int|null $pTemplateID The collection template ID (default: NULL)
     *     @var int|null $uID The ID of the collection author (default: NULL)
     * }
     *
     * @return \Concrete\Core\Page\Collection\Collection
     */
    public static function createCollection($data)
    {
        $app = Application::getFacadeApplication();
        /** @var Connection $db */
        $db = $app->make(Connection::class);
        $dh = Loader::helper('date');
        $cDate = $dh->getOverridableNow();

        $data = array_merge(
            [
                'name' => '',
                'pTemplateID' => 0,
                'handle' => null,
                'uID' => null,
                'cDatePublic' => $cDate,
                'cDescription' => null,
            ],
            $data
            );

        $cDatePublic = ($data['cDatePublic']) ? $data['cDatePublic'] : $cDate;

        if (isset($data['cID'])) {
            $res = $db->insert('Collections', [
                'cID' => $data['cID'],
                'cHandle' => $data['handle'],
                'cDateAdded' => $cDate,
                'cDateModified' => $cDate
            ]);
            $newCID = $data['cID'];
        } else {
            $res = $db->insert('Collections', [
                'cHandle' => $data['handle'],
                'cDateAdded' => $cDate,
                'cDateModified' => $cDate
            ]);
            $newCID = $db->lastInsertId();
        }

        $cvIsApproved = (isset($data['cvIsApproved']) && $data['cvIsApproved'] == 0) ? 0 : 1;
        $cvIsNew = 1;
        if ($cvIsApproved) {
            $cvIsNew = 0;
        }
        if (isset($data['cvIsNew'])) {
            $cvIsNew = $data['cvIsNew'];
        }
        $data['name'] = Loader::helper('text')->sanitize($data['name']);
        $pThemeID = 0;
        if (isset($data['pThemeID']) && $data['pThemeID']) {
            $pThemeID = $data['pThemeID'];
        }

        $pTemplateID = 0;
        if ($data['pTemplateID']) {
            $pTemplateID = $data['pTemplateID'];
        }

        if (!is_int($data['uID']) || $data['uID'] < 1) {
            $data['uID'] = USER_SUPER_ID;
        }

        if ($res) {
            // now we add a pending version to the collectionversions table
            $db->insert('CollectionVersions', [
                'cID' => $newCID,
                'cvID' => 1,
                'pTemplateID' => $pTemplateID,
                'cvName' => $data['name'],
                'cvHandle' => $data['handle'],
                'cvDescription' => $data['cDescription'],
                'cvDatePublic' => $cDatePublic,
                'cvDateCreated' => $cDate,
                'cvComments' => t(VERSION_INITIAL_COMMENT),
                'cvAuthorUID' => $data['uID'],
                'cvIsApproved' => $cvIsApproved,
                'cvIsNew' => $cvIsNew,
                'pThemeID' => $pThemeID,
            ]);
        }

        return self::getByID($newCID);
    }

    /**
     * Get the collection ID.
     *
     * @return int|null
     */
    public function getCollectionID()
    {
        return $this->cID ? (int) $this->cID : null;
    }

    /**
     * Get the collection handle.
     *
     * @return string|null
     */
    public function getCollectionHandle()
    {
        return $this->cHandle;
    }

    /**
     * Get the date/time when the collection was last modified.
     *
     * @return string|null
     *
     * @example 2017-12-31 23:59:59
     */
    public function getCollectionDateLastModified()
    {
        return $this->cDateModified;
    }

    /**
     * Get the date/time when the collection has been created.
     *
     * @return string|null
     *
     * @example 2017-12-31 23:59:59
     */
    public function getCollectionDateAdded()
    {
        return $this->cDateAdded;
    }

    /**
     * Get the ID of the currently loaded version.
     *
     * @return int
     */
    public function getVersionID()
    {
        return $this->vObj->cvID;
    }

    /**
     * Get the currently loaded version object.
     *
     * @return \Concrete\Core\Page\Collection\Version\Version|null
     */
    public function getVersionObject()
    {
        return $this->vObj;
    }

    /**
     * Create a new Collection instance, using the same theme as this instance (if it's a Page instance).
     *
     * @param array $data {
     *
     *     @var int|null $cID The ID of the collection to create (if unspecified or NULL: database autoincrement value)
     *     @var string $handle The collection handle (default: NULL)
     *     @var string $name The collection name (default: empty string)
     *     @var string $cDescription The collection description (default: NULL)
     *     @var string $cDatePublic The collection publish date/time in format 'YYYY-MM-DD hh:mm:ss' (default: now)
     *     @var bool $cvIsApproved Is the collection version approved (default: true)
     *     @var bool $cvIsNew Is the collection to be considered "new"? (default: true if $cvIsApproved is false, false if $cvIsApproved is true)
     *     @var int|null $pTemplateID The collection template ID (default: NULL)
     *     @var int|null $uID The ID of the collection author (default: NULL)
     * }
     *
     * @return \Concrete\Core\Page\Collection\Collection
     */
    public function addCollection($data)
    {
        $data['pThemeID'] = 0;
        if (isset($this) && $this instanceof Page) {
            $data['pThemeID'] = $this->getCollectionThemeID();
        }

        return static::createCollection($data);
    }

    /**
     * Load a specific collection version (you can retrieve it with the getVersionObject() method).
     *
     * @param string|int $cvID the collection version ('RECENT' for the most recent version, 'ACTIVE' for the currently published version, 'SCHEDULED' for the currently scheduled version, or an integer to retrieve a specific version ID)
     */
    public function loadVersionObject($cvID = 'ACTIVE')
    {
        $this->vObj = CollectionVersion::get($this, $cvID);
    }

    /**
     * Get the Collection instance to be modified (this instance if it's a new or master Collection, a clone otherwise).
     *
     * @return $this|\Concrete\Core\Page\Page
     */
    public function getVersionToModify()
    {
        $vObj = $this->getVersionObject();
        if ($this->isMasterCollection() || ($vObj->isNew())) {
            return $this;
        } else {
            $nc = $this->cloneVersion(null);

            return $nc;
        }
    }

    /**
     * Get the automatic comment for the next collection version.
     *
     * @return string Example: 'Version 2'
     */
    public function getNextVersionComments()
    {
        $c = Page::getByID($this->getCollectionID(), 'ACTIVE');
        $cvID = $c->getVersionID();

        return t('Version %d', $cvID + 1);
    }

    /**
     * (Re)Index the contents of this collection (or mark the collection as to be reindexed if $actuallyDoReindex is falsy and the concrete.page.search.always_reindex configuration key is falsy).
     *
     * @param \Concrete\Core\Page\Search\IndexedSearch|false $index the IndexedSearch instance that indexes the collection content (if falsy: we'll create a new instance of it)
     * @param bool $actuallyDoReindex Set to true to always reindex the collection immediately (otherwise we'll look at the concrete.page.search.always_reindex configuration key)
     *
     * @return bool|null Return false if the collection can't be indexed, NULL otherwise
     */
    public function reindex($index = false, $actuallyDoReindex = true)
    {
        if ($this->isAlias() && !$this->isExternalLink()) {
            return false;
        }

        $app = Application::getFacadeApplication();
        /** @var Connection $db */
        $db = $app->make(Connection::class);

        if ($actuallyDoReindex || Config::get('concrete.page.search.always_reindex') == true) {
            // Retrieve the attribute values for the current page
            $category = \Core::make('Concrete\Core\Attribute\Category\PageCategory');
            $indexer = $category->getSearchIndexer();
            $values = $category->getAttributeValues($this);
            foreach ($values as $value) {
                $indexer->indexEntry($category, $value, $this);
            }

            if ($index == false) {
                $index = new IndexedSearch();
            }

            $index->reindexPage($this);

            $qb = $db->createQueryBuilder();
            $r = $qb->select('cID')
                ->from('PageSearchIndex')
                ->where('cID = :cID')
                ->setParameter('cID', $this->getCollectionID())
                ->execute()->fetch();

            $qb2 = $db->createQueryBuilder();
            if ($r !== false) {
                $qb2->update('PageSearchIndex')
                    ->set('cRequiresReindex', 0)
                    ->where('cID = :cID');
            } else {
                $qb2->insert('PageSearchIndex')
                    ->setValue('cID', ':cID')
                    ->setValue('cRequiresReindex', 0);
            }
            $qb2->setParameter('cID', $this->getCollectionID())->execute();

            $cache = PageCache::getLibrary();
            $cache->purge($this);

            $app = Facade::getFacadeApplication();
            $populator = $app->make(Populator::class);
            $populator->updateAvailableSummaryTemplates($this);

        } else {
            Config::save('concrete.misc.do_page_reindex_check', true);

            $qb = $db->createQueryBuilder();
            $r = $qb->select('cID')
                ->from('PageSearchIndex')
                ->where('cID = :cID')
                ->setParameter('cID', $this->getCollectionID())
                ->execute()->fetch();

            $qb2 = $db->createQueryBuilder();
            if ($r !== false) {
                $qb2->update('PageSearchIndex')
                    ->set('cRequiresReindex', 1)
                    ->where('cID = :cID')
                    ->setParameter('cID', $this->getCollectionID());
            } else {
                $qb2->insert('PageSearchIndex')
                    ->setValue('cID', ':cID')
                    ->setValue('cRequiresReindex', 1)
                    ->setParameter('cID', $this->getCollectionID());
            }
            $qb2->execute();
        }
    }

    /**
     * Set the attribute value for the currently loaded collection version.
     *
     * @param string|\Concrete\Core\Attribute\Key\CollectionKey $ak the attribute key (or its handle)
     * @param \Concrete\Core\Entity\Attribute\Value\Value\AbstractValue|mixed $value an attribute value object, or the data needed by the attribute controller to create the attribute value object
     *
     * @return \Concrete\Core\Entity\Attribute\Value\PageValue
     */
    public function setAttribute($ak, $value)
    {
        return $this->vObj->setAttribute($ak, $value);
    }

    /**
     * Return the value of the attribute with the handle $akHandle of the currently loaded version (if it's loaded).
     *
     * @param string|\Concrete\Core\Attribute\Key\CollectionKey $akHandle the attribute key (or its handle)
     * @param string|false $displayMode makes The format of the attribute value
     *
     * @return mixed|null
     *
     * @example When you need the raw attribute value or object, use this:
     * <code>
     * $c = Page::getCurrentPage();
     * $attributeValue = $c->getAttribute('attribute_handle');
     * </code>
     * @example If you need the formatted output supported by some attribute, use this:
     * <code>
     * $c = Page::getCurrentPage();
     * $attributeValue = $c->getAttribute('attribute_handle', 'display');
     * </code>
     * @example An attribute type like "date" will then return the date in the correct format just like other attributes will show you a nicely formatted output and not just a simple value or object.
     */
    public function getAttribute($akHandle, $displayMode = false)
    {
        if (is_object($this->vObj)) {
            return $this->vObj->getAttribute($akHandle, $displayMode);
        }
    }

    /**
     * Return the attribute value object with the handle $akHandle of the currently loaded version (if it's loaded).
     *
     * @param string|\Concrete\Core\Attribute\Key\CollectionKey $akHandle the attribute key (or its handle)
     * @param bool $createIfNotExists
     *
     * @return \Concrete\Core\Entity\Attribute\Value\PageValue|null
     */
    public function getAttributeValueObject($akHandle, $createIfNotExists = false)
    {
        if (is_object($this->vObj)) {
            return $this->vObj->getAttributeValueObject($akHandle, $createIfNotExists);
        }
    }

    /**
     * Delete the values of the attributes associated to the currently loaded collection version.
     *
     * @param int[] $retainAKIDs a list of attribute key IDs to keep (their values won't be deleted)
     */
    public function clearCollectionAttributes($retainAKIDs = [])
    {
        $app = Application::getFacadeApplication();
        /** @var Connection $db */
        $db = $app->make(Connection::class);
        $qb = $db->createQueryBuilder();
        if (count($retainAKIDs) > 0) {
            $cleanAKIDs = [];
            foreach ($retainAKIDs as $akID) {
                $cleanAKIDs[] = (int) $akID;
            }
            $qb->delete('CollectionAttributeValues')
                ->where('cID = :cID')
                ->andWhere('cvID = :cvID')
                ->andWhere($qb->expr()->notIn('akID', $cleanAKIDs))
                ->setParameter('cID', $this->getCollectionID())
                ->setParameter('cvID', $this->getVersionID())
                ->execute();
        } else {
            $qb->delete('CollectionAttributeValues')
                ->where('cID = :cID')
                ->andWhere('cvID = :cvID')
                ->setParameter('cID', $this->getCollectionID())
                ->setParameter('cvID', $this->getVersionID())
                ->execute();
        }
        $this->reindex();
    }

    /**
     * Delete the value of a specific attribute key associated to the currently loaded collection version.
     *
     * @param string|\Concrete\Core\Attribute\Key\CollectionKey $ak the attribute key (or its handle)
     */
    public function clearAttribute($ak)
    {
        $this->vObj->clearAttribute($ak);
    }

    /**
     * Get the list of attribute keys for which the currently loaded collection version has values.
     *
     * @return \Concrete\Core\Entity\Attribute\Key\PageKey[]
     */
    public function getSetCollectionAttributes()
    {
        $category = $this->vObj->getObjectAttributeCategory();
        $values = $category->getAttributeValues($this->vObj);
        $attribs = [];
        foreach ($values as $value) {
            $attribs[] = $value->getAttributeKey();
        }

        return $attribs;
    }

    /**
     * Get an existing area associated to this collection.
     *
     * @param string $arHandle the handle of the area
     *
     * @return \Concrete\Core\Area\Area|null
     */
    public function getArea($arHandle)
    {
        return Area::get($this, $arHandle);
    }

    /**
     * Does this collection contain blocks that are aliased in other pages?
     *
     * @return bool
     */
    public function hasAliasedContent()
    {
        $app = Application::getFacadeApplication();
        /** @var Connection $db */
        $db = $app->make(Connection::class);
        $qb = $db->createQueryBuilder();
        // aliased content is content on the particular page that is being
        // used elsewhere - but the content on the PAGE is the original version
        $r = $qb->select('bID')
            ->from('CollectionVersionBlocks')
            ->where('cID = :cID')
            ->andWhere('isOriginal = 1')
            ->setParameter('cID', $this->getCollectionID())
            ->execute();
        $bIDArray = [];
        if ($r) {
            while ($row = $r->fetch()) {
                $bIDArray[] = $row['bID'];
            }
            if (count($bIDArray) > 0) {
                $qb2 = $db->createQueryBuilder();
                $qb2->select('cID')
                    ->from('CollectionVersionBlocks')
                    ->where($qb2->expr()->in('bID', $bIDArray))
                    ->andWhere($qb2->expr()->neq('cID', ':cID'))
                    ->setParameter('cID', $this->getCollectionID())
                    ->setMaxResults(1);
                $aliasedCID = $qb2->execute()->fetchColumn();
                if ($aliasedCID > 0) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Get the custom style of an area in the currently loaded collection version.
     *
     * @param \Concrete\Core\Area\Area $area the area for which you want the custom styles
     * @param bool $force Set to true to retrieve a CustomStyle even if the area does not define any custom style
     *
     * @return \Concrete\Core\Area\CustomStyle|null return NULL if the area does not have any custom style and $force is false, a CustomStyle instance otherwise
     */
    public function getAreaCustomStyle($area, $force = false)
    {
        $areac = $area->getAreaCollectionObject();
        if ($areac instanceof Stack) {
            // this fixes the problem of users applying design to the main area on the page, and then that trickling into any
            // stacks that have been added to other areas of the page.
            return null;
        }
        $result = null;
        $styles = $this->vObj->getCustomAreaStyles();
        $areaHandle = $area->getAreaHandle();
        if ($force || isset($styles[$areaHandle])) {
            $pss = isset($styles[$areaHandle]) ? StyleSet::getByID($styles[$areaHandle]) : null;
            $result = new AreaCustomStyle($pss, $area, $this->getCollectionThemeObject());
        }

        return $result;
    }

    /**
     * Set the custom style of an area in the currently loaded collection version.
     *
     * @param \Concrete\Core\Area\Area $area
     * @param \Concrete\Core\Entity\StyleCustomizer\Inline\StyleSet $set
     */
    public function setCustomStyleSet($area, $set)
    {
        $app = Application::getFacadeApplication();
        /** @var Connection $db */
        $db = $app->make(Connection::class);
        $qb = $db->createQueryBuilder();
        $r = $qb->select('cID')
            ->from('CollectionVersionAreaStyles')
            ->where('cID = :cID')
            ->andWhere('cvID = :cvID')
            ->andWhere('arHandle = :arHandle')
            ->setParameter('cID', $this->getCollectionID())
            ->setParameter('cvID', $this->getVersionID())
            ->setParameter('arHandle', $area->getAreaHandle())
            ->execute()->fetch();

        $qb2 = $db->createQueryBuilder();
        if ($r !== false) {
            $qb2->update('CollectionVersionAreaStyles')
                ->set('issID', ':issID')
                ->where('cID = :cID')
                ->andWhere('cvID = :cvID')
                ->andWhere('arHandle = :arHandle');
        } else {
            $qb2->insert('CollectionVersionAreaStyles')
                ->setValue('cID', ':cID')
                ->setValue('cvID', ':cvID')
                ->setValue('arHandle', ':arHandle')
                ->setValue('issID', ':issID');
        }
        $qb2->setParameter('cID', $this->getCollectionID())
            ->setParameter('cvID', $this->getVersionID())
            ->setParameter('arHandle', $area->getAreaHandle())
            ->setParameter('issID', $set->getID())
            ->execute();
    }

    /**
     * Delete all the custom styles of an area of the currently loaded collection version.
     *
     * @param \Concrete\Core\Area\Area $area
     */
    public function resetAreaCustomStyle($area)
    {
        $app = Application::getFacadeApplication();
        /** @var Connection $db */
        $db = $app->make(Connection::class);
        $qb = $db->createQueryBuilder();
        $qb->delete('CollectionVersionAreaStyles')
            ->where('cID = :cID')
            ->andWhere('cvID = :cvID')
            ->andWhere('arHandle = :arHandle')
            ->setParameter('cID', $this->getCollectionID())
            ->setParameter('cvID', $this->getVersionID())
            ->setParameter('arHandle', $area->getAreaHandle())
            ->execute();
    }

    /**
     * Retrieve all custom style rules that should be inserted into the header on a page, whether they are defined in areas or blocks.
     *
     * @param bool $return set to true to return the HTML that defines the styles, false to add it to the current View instance
     *
     * @return string|null
     */
    public function outputCustomStyleHeaderItems($return = false)
    {
        if (!Config::get('concrete.design.enable_custom')) {
            return '';
        }

        $psss = [];
        $txt = Loader::helper('text');
        CacheLocal::set('pssCheck', $this->getCollectionID() . ':' . $this->getVersionID(), true);

        $app = Application::getFacadeApplication();
        /** @var Connection $db */
        $db = $app->make(Connection::class);
        $qb1 = $db->createQueryBuilder();
        $r1 = $qb1->select('bID', 'arHandle', 'issID')
            ->from('CollectionVersionBlockStyles')
            ->where('cID = :cID')
            ->andWhere('cvID = :cvID')
            ->andWhere('issID > 0')
            ->setParameter(':cID', $this->getCollectionID())
            ->setParameter(':cvID', $this->getVersionID())
            ->execute()->fetchAll();
        $qb2 = $db->createQueryBuilder();
        $r2 = $qb2->select('arHandle', 'issID')
            ->from('CollectionVersionAreaStyles')
            ->where('cID = :cID')
            ->andWhere('cvID = :cvID')
            ->andWhere('issID > 0')
            ->setParameter(':cID', $this->getCollectionID())
            ->setParameter(':cvID', $this->getVersionID())
            ->execute()->fetchAll();
        foreach ($r1 as $r) {
            $issID = $r['issID'];
            $arHandle = $txt->filterNonAlphaNum($r['arHandle']);
            $bID = $r['bID'];
            $obj = StyleSet::getByID($issID);
            if (is_object($obj)) {
                $b = new Block();
                $b->bID = $bID;
                $a = new Area($arHandle);
                $b->setBlockAreaObject($a);
                $obj = new BlockCustomStyle($obj, $b, $this->getCollectionThemeObject());
                $psss[] = $obj;
                CacheLocal::set(
                          'pssObject',
                          $this->getCollectionID() . ':' . $this->getVersionID() . ':' . $r['arHandle'] . ':' . $r['bID'],
                          $obj
                );
            }
        }

        foreach ($r2 as $r) {
            $issID = $r['issID'];
            $obj = StyleSet::getByID($issID);
            if (is_object($obj)) {
                $a = new Area($r['arHandle']);
                $obj = new AreaCustomStyle($obj, $a, $this->getCollectionThemeObject());
                $psss[] = $obj;
                CacheLocal::set(
                          'pssObject',
                          $this->getCollectionID() . ':' . $this->getVersionID() . ':' . $r['arHandle'],
                          $obj
                );
            }
        }

        // grab all the header block style rules for items in global areas on this page
        $qb3 = $db->createQueryBuilder();
        $rs = $qb3->select('arHandle')
            ->from('Areas')
            ->where('arIsGlobal = 1')
            ->andWhere('cID = :cID')
            ->setParameter('cID', $this->getCollectionID())
            ->execute()->fetchAll(FetchMode::COLUMN);
        if (count($rs) > 0) {
            $pcp = new Permissions($this);
            foreach ($rs as $garHandle) {
                if ($pcp->canViewPageVersions()) {
                    $s = Stack::getByName($garHandle, 'RECENT');
                } else {
                    $s = Stack::getByName($garHandle, 'ACTIVE');
                }
                if (is_object($s)) {
                    CacheLocal::set('pssCheck', $s->getCollectionID() . ':' . $s->getVersionID(), true);
                    $qb4 = $db->createQueryBuilder();
                    $rs1 = $qb4->select('bID', 'issID', 'arHandle')
                        ->from('CollectionVersionBlockStyles')
                        ->where('cID = :cID')
                        ->andWhere('cvID = :cvID')
                        ->andWhere('issID > 0')
                        ->setParameter('cID', $s->getCollectionID())
                        ->setParameter('cvID', $s->getVersionID())
                        ->execute()->fetchAll();
                    foreach ($rs1 as $r) {
                        $issID = $r['issID'];
                        $obj = StyleSet::getByID($issID);
                        if (is_object($obj)) {
                            $b = new Block();
                            $b->bID = $r['bID'];
                            $a = new GlobalArea($garHandle);
                            $b->setBlockAreaObject($a);
                            $obj = new BlockCustomStyle($obj, $b, $this->getCollectionThemeObject());
                            $psss[] = $obj;
                            CacheLocal::set(
                                      'pssObject',
                                      $s->getCollectionID() . ':' . $s->getVersionID() . ':' . $r['arHandle'] . ':' . $r['bID'],
                                      $obj
                            );
                        }
                    }
                }
            }
        }

        $styleHeader = '';
        foreach ($psss as $st) {
            $css = $st->getCSS();
            if ($css !== '') {
                $styleHeader .= $st->getStyleWrapper($css);
            }
        }

        if (strlen(trim($styleHeader))) {
            if ($return == true) {
                return $styleHeader;
            } else {
                $v = \View::getInstance();
                $v->addHeaderItem($styleHeader);
            }
        }
    }

    /**
     * Associate the edits of another collection to this collection.
     *
     * @param \Concrete\Core\Page\Collection\Collection $oc the collection that has been modified
     *
     * @return null|false return false if the other collection is already associated to this collection, NULL otherwise
     *
     * @example If a global area is modified inside this collection, you need to call $page->relateVersionEdits($globalArea)
     */
    public function relateVersionEdits($oc)
    {
        $app = Application::getFacadeApplication();
        /** @var Connection $db */
        $db = $app->make(Connection::class);
        $qb = $db->createQueryBuilder();
        $r = (int) $qb = $qb->select('count(*)')
            ->from('CollectionVersionRelatedEdits')
            ->where('cID = :cID')
            ->andWhere('cvID = :cvID')
            ->andWhere('cRelationID = :cRelationID')
            ->andWhere('cvRelationID = :cvRelationID')
            ->setParameter('cID', $this->getCollectionID())
            ->setParameter('cvID', $this->getVersionID())
            ->setParameter('cRelationID', $oc->getCollectionID())
            ->setParameter('cvRelationID', $oc->getVersionID())
            ->execute()->fetchColumn();
        if ($r > 0) {
            return false;
        } else {
            $qb2 = $db->createQueryBuilder();
            $qb2->insert('CollectionVersionRelatedEdits')
                ->setValue('cID', $this->getCollectionID())
                ->setValue('cvID', $this->getVersionID())
                ->setValue('cRelationID', $oc->getCollectionID())
                ->setValue('cvRelationID', $oc->getVersionID())
                ->execute();
        }
    }

    /**
     * Returns the ID of the page/collection type.
     *
     * @return int|false
     */
    public function getPageTypeID()
    {
        return false;
    }

    /**
     * Empty the collection-related cache.
     */
    public function refreshCache()
    {
        CacheLocal::flush();
    }

    /**
     * Get the blocks contained in the all the global areas.
     *
     * @return \Concrete\Core\Block\Block[]
     */
    public function getGlobalBlocks()
    {
        $app = Application::getFacadeApplication();
        /** @var Connection $db */
        $db = $app->make(Connection::class);
        $qb = $db->createQueryBuilder();
        $rs = $qb->select('stName')
            ->from('Stacks')
            ->where('stType = :stType')
            ->setParameter('stType', Stack::ST_TYPE_GLOBAL_AREA)
            ->execute()->fetchAll(FetchMode::COLUMN);
        $blocks = [];
        if (count($rs) > 0) {
            $pcp = new Permissions($this);
            foreach ($rs as $garHandle) {
                if ($pcp->canViewPageVersions()) {
                    $s = Stack::getByName($garHandle, 'RECENT');
                } else {
                    $s = Stack::getByName($garHandle, 'ACTIVE');
                }
                if (is_object($s)) {
                    $blocksTmp = $s->getBlocks(STACKS_AREA_NAME);
                    $blocks = array_merge($blocks, $blocksTmp);
                }
            }
        }

        return $blocks;
    }

    /**
     * List the blocks in the currently loaded collection version (or in a specific area within it).
     *
     * @param string|false $arHandle The handle if the area (or falsy to get all the blocks in the collection)
     *
     * @return \Concrete\Core\Block\Block[]
     */
    public function getBlocks($arHandle = false)
    {
        $blockIDs = $this->getBlockIDs($arHandle);

        $blocks = [];
        if (is_array($blockIDs)) {
            foreach ($blockIDs as $row) {
                $ab = Block::getByID($row['bID'], $this, $row['arHandle']);
                if (is_object($ab)) {
                    $blocks[] = $ab;
                }
            }
        }

        return $blocks;
    }

    /**
     * List the block IDs and the associated area handles in the currently loaded collection version (or in a specific area within it).
     *
     * @param string|false $arHandle The handle if the area (or falsy to get all the blocks in the collection version)
     *
     * @return array Return a list of arrays, each one is a dictionary like ['bID' => <block ID>, 'arHandle' => <area handle>]
     */
    public function getBlockIDs($arHandle = false)
    {
        $blockIDs = CacheLocal::getEntry(
                              'collection_block_ids',
                              $this->getCollectionID() . ':' . $this->getVersionID()
        );

        if (!is_array($blockIDs)) {
            $app = Application::getFacadeApplication();
            /** @var Connection $db */
            $db = $app->make(Connection::class);
            $qb = $db->createQueryBuilder();
            $r = $qb->select('b.bID', 'cvb.arHandle')
                ->from('CollectionVersionBlocks', 'cvb')
                ->innerJoin('cvb', 'Blocks', 'b', 'cvb.bID = b.bID')
                ->where('cvb.cID = :cID')
                ->andWhere($qb->expr()->orX(
                    'cvb.cvID = :cvID',
                    'cvb.cbIncludeAll = 1'
                ))
                ->orderBy('cvb.cbDisplayOrder', 'asc')
                ->setParameter('cID', $this->getCollectionID())
                ->setParameter('cvID', $this->getVersionID())
                ->execute()->fetchAll();
            $blockIDs = [];
            if (is_array($r)) {
                foreach ($r as $bl) {
                    $blockIDs[strtolower($bl['arHandle'])][] = $bl;
                }
            }
            CacheLocal::set('collection_block_ids', $this->getCollectionID() . ':' . $this->getVersionID(), $blockIDs);
        }

        $result = [];
        if ($arHandle != false) {
            $key = strtolower($arHandle);
            if (isset($blockIDs[$key])) {
                $result = $blockIDs[$key];
            }
        } else {
            foreach ($blockIDs as $arHandle => $row) {
                foreach ($row as $brow) {
                    if (!in_array($brow, $blockIDs)) {
                        $result[] = $brow;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Add a new block to a specific area of the currently loaded collection version.
     *
     * @param \Concrete\Core\Entity\Block\BlockType\BlockType $bt the type of block to be added
     * @param string|\Concrete\Core\Area\Area $a the area instance (or its handle) to which the block should be added to
     * @param array $data The data of the block. This data depends on the specific block type. Common values are: 'uID' to specify the ID of the author (if not specified: we'll use the current user), 'bName' to specify the block name.
     *
     * @return \Concrete\Core\Block\Block
     */
    public function addBlock($bt, $a, $data)
    {
        $app = Application::getFacadeApplication();
        /** @var Connection $db */
        $db = $app->make(Connection::class);

        // first we add the block to the system
        $nb = $bt->add($data, $this, $a);

        // now that we have a block, we add it to the collectionversions table

        $arHandle = (is_object($a)) ? $a->getAreaHandle() : $a;
        $cID = $this->getCollectionID();
        $vObj = $this->getVersionObject();

        if ($bt->includeAll()) {
            // normally, display order is dependant on a per area, per version basis. However, since this block
            // is not aliased across versions, then we want to get display order simply based on area, NOT based
            // on area + version
            $newBlockDisplayOrder = $this->getCollectionAreaDisplayOrder(
                                         $arHandle,
                                         true
            ); // second argument is "ignoreVersions"
        } else {
            $newBlockDisplayOrder = $this->getCollectionAreaDisplayOrder($arHandle);
        }

        $qb = $db->createQueryBuilder();
        $cbRelationID = (int) $qb->select('max(cbRelationID)')->from('CollectionVersionBlocks')
            ->execute()->fetchColumn();
        if (!$cbRelationID) {
            $cbRelationID = 1;
        } else {
            ++$cbRelationID;
        }

        $qb2 = $db->createQueryBuilder();
        $res = $qb2->insert('CollectionVersionBlocks')
            ->setValue('cID', ':cID')
            ->setValue('cvID', ':cvID')
            ->setValue('bID', ':bID')
            ->setValue('arHandle', ':arHandle')
            ->setValue('cbRelationID', ':cbRelationID')
            ->setValue('cbDisplayOrder', ':cbDisplayOrder')
            ->setValue('isOriginal', 1)
            ->setValue('cbIncludeAll', ':cbIncludeAll')
            ->setParameter('cID', $cID)
            ->setParameter('cvID', $vObj->getVersionID())
            ->setParameter('bID', $nb->getBlockID())
            ->setParameter('arHandle', $arHandle)
            ->setParameter('cbRelationID', $cbRelationID)
            ->setParameter('cbDisplayOrder', $newBlockDisplayOrder)
            ->setParameter('cbIncludeAll', (int) ($bt->includeAll()))
            ->execute();

        return Block::getByID($nb->getBlockID(), $this, $a);
    }

    /**
     * Get the next value of the display order (to be used when adding new blocks to an area).
     *
     * @param string $arHandle The handle of the area
     * @param bool $ignoreVersions Set to true to ignore the collection version
     *
     * @return int
     */
    public function getCollectionAreaDisplayOrder($arHandle, $ignoreVersions = false)
    {
        $app = Application::getFacadeApplication();
        /** @var Connection $db */
        $db = $app->make(Connection::class);
        $qb = $db->createQueryBuilder();
        $cID = $this->cID;
        $cvID = $this->vObj->cvID;
        if ($ignoreVersions) {
            $qb->select('max(cbDisplayOrder) as cbdis')
                ->from('CollectionVersionBlocks')
                ->where('cID = :cID')
                ->andWhere('arHandle = :arHandle')
                ->setParameter('cID', $cID)
                ->setParameter('arHandle', $arHandle);
        } else {
            $qb->select('max(cbDisplayOrder) as cbdis')
                ->from('CollectionVersionBlocks')
                ->where('cID = :cID')
                ->andWhere('cvID = :cvID')
                ->andWhere('arHandle = :arHandle')
                ->setParameter('cID', $cID)
                ->setParameter('cvID', $cvID)
                ->setParameter('arHandle', $arHandle);
        }
        /** @var PDOStatement $r */
        $r = $qb->execute();
        if ($r) {
            if ($r->rowCount() > 0) {
                // then we know we got a value; we increment it and return
                $res = $r->fetchRow();
                $displayOrder = $res['cbdis'];
                if ($displayOrder === null) {
                    return 0;
                }
                ++$displayOrder;

                return $displayOrder;
            } else {
                // we didn't get anything, so we return a zero
                return 0;
            }
        }
    }

    /**
     * Fix the display order properties for all the blocks within the collection/area.
     *
     * @param string $arHandle the handle of the area to be processed
     */
    public function rescanDisplayOrder($arHandle)
    {
        // this collection function f

        $cID = $this->cID;
        $cvID = $this->vObj->cvID;
        $app = Application::getFacadeApplication();
        /** @var Connection $db */
        $db = $app->make(Connection::class);
        $qb = $db->createQueryBuilder();
        $r = $qb->select('bID')
            ->from('CollectionVersionBlocks')
            ->where('cID = :cID')
            ->andWhere('cvID = :cvID')
            ->andWhere('arHandle = :arHandle')
            ->orderBy('cbDisplayOrder', 'asc')
            ->setParameter('cID', $cID)
            ->setParameter('cvID', $cvID)
            ->setParameter('arHandle', $arHandle)
            ->execute();

        if ($r) {
            $displayOrder = 0;
            while ($row = $r->fetch()) {
                $qb2 = $db->createQueryBuilder();
                $qb2->update('CollectionVersionBlocks')
                    ->set('cbDisplayOrder', ':cbDisplayOrder')
                    ->where('cID = :cID')
                    ->andWhere('cvID = :cvID')
                    ->andWhere('arHandle = :arHandle')
                    ->andWhere('bID = :bID')
                    ->setParameter('cbDisplayOrder', $displayOrder)
                    ->setParameter('cID', $cID)
                    ->setParameter('cvID', $cvID)
                    ->setParameter('arHandle', $arHandle)
                    ->setParameter('bID', $row['bID'])
                    ->execute();
                ++$displayOrder;
            }
        }
    }
    
    /**
     * Update the last edit date/time.
     */
    public function markModified()
    {
        // marks this collection as newly modified
        $app = Application::getFacadeApplication();
        /** @var Connection $db */
        $db = $app->make(Connection::class);
        $dh = Loader::helper('date');
        $cDateModified = $dh->getOverridableNow();

        $qb = $db->createQueryBuilder();
        $res = $qb->update('Collections')
            ->set('cDateModified', ':cDateModified')
            ->where('cID = :cID')
            ->setParameter('cDateModified', $cDateModified)
            ->setParameter('cID', $this->getCollectionID())
            ->execute();
    }

    /**
     * Delete this collection, and all its versions, contents and attributes.
     */
    public function delete()
    {
        if ($this->cID > 0) {
            $app = Application::getFacadeApplication();
            /** @var Connection $db */
            $db = $app->make(Connection::class);

            // First we delete all versions
            $vl = new VersionList($this);
            $vl->setItemsPerPage(-1);
            $vlArray = $vl->getPage();

            foreach ($vlArray as $v) {
                $v->delete();
            }

            $cID = $this->getCollectionID();

            $qb = $db->createQueryBuilder();
            $qb->delete('CollectionAttributeValues')
                ->where('cID = :cID')
                ->setParameter('cID', $cID)
                ->execute();

            $qb = $db->createQueryBuilder();
            $qb->delete('Collections')
                ->where('cID = :cID')
                ->setParameter('cID', $cID)
                ->execute();

            try {
                $qb = $db->createQueryBuilder();
                $qb->delete('CollectionSearchIndexAttributes')
                    ->where('cID = :cID')
                    ->setParameter('cID', $cID)
                    ->execute();
            } catch (\Exception $e) {
            }
        }
    }

    /**
     * Create a clone of this collection, and all its versions, contents and attributes.
     *
     * @return \Concrete\Core\Page\Collection\Collection
     */
    public function duplicateCollection()
    {
        $app = Application::getFacadeApplication();
        $cloner = $app->make(Cloner::class);
        $clonerOptions = $app->build(ClonerOptions::class)->setKeepOriginalAuthor(true);
        $newCollection = $cloner->cloneCollection($this, $clonerOptions);

        return $newCollection;
    }

    /**
     * Clone the currently loaded version and returns a Page instance containing the new version.
     *
     * @param string|null $versionComments the comments to be associated to the new Version
     * @param bool $createEmpty set to true to create a Version without any blocks/area styles, false to clone them too
     *
     * @return \Concrete\Core\Page\Page
     */
    public function cloneVersion($versionComments, $createEmpty = false)
    {
        $app = Application::getFacadeApplication();
        $cloner = $app->make(Cloner::class);
        $clonerOptions = $app->make(ClonerOptions::class)
            ->setVersionComments($versionComments)
            ->setCopyContents($createEmpty ? false : true)
        ;
        $newVersion = $cloner->cloneCollectionVersion($this->getVersionObject(), $this, $clonerOptions);

        return Page::getByID($newVersion->getCollectionID(), $newVersion->getVersionID());
    }

    /**
     * @deprecated Use of getAttributeValueObject()
     *
     * @param string|\Concrete\Core\Attribute\Key\CollectionKey $ak
     *
     * @return \Concrete\Core\Entity\Attribute\Value\PageValue|null
     */
    public function getAttributeValue($ak)
    {
        return $this->getAttributeValueObject($ak);
    }

    /**
     * @deprecated use the getAttribute() method
     *
     * @param string|\Concrete\Core\Attribute\Key\CollectionKey $akHandle
     *
     * @return mixed|null
     */
    public function getCollectionAttributeValue($akHandle)
    {
        return $this->getAttribute($akHandle);
    }

    /**
     * @deprecated use the getPageTypeID() method
     */
    public function getCollectionTypeID()
    {
        return $this->getPageTypeID();
    }
}
