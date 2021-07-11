<?php

namespace Concrete\Core\Page;

use Concrete\Core\Area\Area;
use Block;
use CacheLocal;
use Concrete\Core\Cache\Level\RequestCache;
use Concrete\Core\Entity\Page\Summary\CustomPageTemplateCollection;
use Concrete\Core\Page\Collection\Collection;
use Concrete\Core\Attribute\Key\CollectionKey;
use Concrete\Core\Attribute\ObjectInterface as AttributeObjectInterface;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\Page\PagePath;
use Concrete\Core\Entity\Page\Template as TemplateEntity;
use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Entity\Site\SiteTree;
use Concrete\Core\Entity\StyleCustomizer\CustomCssRecord;
use Concrete\Core\Export\ExportableInterface;
use Concrete\Core\Localization\Locale\Service as LocaleService;
use Concrete\Core\Logging\Channels;
use Concrete\Core\Multilingual\Page\Section\Section;
use Concrete\Core\Package\PackageList;
use Concrete\Core\Page\Search\ColumnSet\DefaultSet;
use Concrete\Core\Page\Stack\Stack;
use Concrete\Core\Page\Statistics as PageStatistics;
use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\Page\Theme\ThemeRouteCollection;
use Concrete\Core\Page\Type\Composer\Control\BlockControl;
use Concrete\Core\Page\Type\Composer\FormLayoutSetControl;
use Concrete\Core\Page\Type\Type;
use Concrete\Core\Permission\Access\Entity\GroupCombinationEntity as GroupCombinationPermissionAccessEntity;
use Concrete\Core\Permission\Access\Entity\GroupEntity as GroupPermissionAccessEntity;
use Concrete\Core\Permission\Access\Entity\PageOwnerEntity;
use Concrete\Core\Permission\Access\Entity\UserEntity as UserPermissionAccessEntity;
use Concrete\Core\Permission\AssignableObjectInterface;
use Concrete\Core\Permission\AssignableObjectTrait;
use Concrete\Core\Permission\Key\PageKey as PagePermissionKey;
use Concrete\Core\Site\SiteAggregateInterface;
use Concrete\Core\Site\Tree\TreeInterface;
use Concrete\Core\Summary\Category\CategoryMemberInterface;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\User\User;
use Config;
use Core;
use Database;
use Doctrine\ORM\EntityManagerInterface;
use Environment;
use Events;
use Group;
use PageCache;
use PageTemplate;
use PageTheme;
use PageType;
use Queue;
use Request;
use Session;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use UserInfo;

/**
 * The page object in Concrete encapsulates all the functionality used by a typical page and their contents including blocks, page metadata, page permissions.
 */
class Page extends Collection implements CategoryMemberInterface,
    \Concrete\Core\Permission\ObjectInterface,
    AttributeObjectInterface,
    AssignableObjectInterface,
    TreeInterface,
    SiteAggregateInterface,
    ExportableInterface
{
    use AssignableObjectTrait;

    /**
     * @deprecated what's deprecated is the public part: use the getSiteTreeObject() method to access this property
     *
     * @var \Concrete\Core\Entity\Site\Tree|null
     */
    public $siteTree;
    /**
     * The page controller.
     *
     * @var \Concrete\Core\Page\Controller\PageController|null
     */
    protected $controller;

    /**
     * The list of block IDs that are alias.
     *
     * @var int[]|null
     */
    protected $blocksAliasedFromMasterCollection = null;

    /**
     * The original cID of a page (if it's a page alias).
     *
     * @var int|null
     */
    protected $cPointerOriginalID = null;

    /**
     * The original siteTreeID of a page (if it's a page alias).
     *
     * @var int|null
     */
    protected $cPointerOriginalSiteTreeID = null;

    /**
     * The link for the aliased page.
     *
     * @var string|null
     */
    protected $cPointerExternalLink = null;

    /**
     * Should the alias link to be opened in a new window?
     *
     * @var bool|int|null
     */
    protected $cPointerExternalLinkNewWindow = null;

    /**
     * Is this page a page default?
     *
     * @var bool|int|null
     */
    protected $isMasterCollection = null;

    /**
     * The ID of the page from which this page inherits permissions from.
     *
     * @var int|null
     */
    protected $cInheritPermissionsFromCID = null;

    /**
     * Is this a system page?
     *
     * @var bool
     */
    protected $cIsSystemPage = false;

    /**
     * The site tree ID.
     *
     * @var int|null
     */
    protected $siteTreeID;

    /**
     * The custom name of the alias page.
     *
     * @var string|null NULL if the page is not an alias, empty string if we should use the name of the aliased page, the custom alias name otherwise.
     */
    private $customAliasName;

    /**
     * The handle of the alias page
     *
     * @var string|null NULL if the page is not an alias, the alias handle otherwise.
     */
    private $aliasHandle;

    /**
     * Whether this page has a custom summary template collection assigned to it. If null/false,
     * then we just use any that are found to fit the criteria.
     *
     * @var bool|null
     */
    protected $hasCustomSummaryTemplateCollection;

    /**
     * Initialize collection until we populate it.
     */
    public function __construct()
    {
        $this->loadError(COLLECTION_INIT); // init collection until we populate.
    }

    /**
     * Get a page given its path.
     *
     * @param string $path the page path (example: /path/to/page)
     * @param string $version the page version ('RECENT' for the most recent version, 'ACTIVE' for the currently published version, 'SCHEDULED' for the currently scheduled version, or an integer to retrieve a specific version ID)
     * @param \Concrete\Core\Site\Tree\TreeInterface|null $tree
     *
     * @return \Concrete\Core\Page\Page
     */
    public static function getByPath($path, $version = 'RECENT', TreeInterface $tree = null)
    {
        $path = rtrim($path, '/'); // if the path ends in a / remove it.
        $cache = \Core::make('cache/request');

        if ($tree) {
            $item = $cache->getItem(sprintf('site/page/path/%s/%s', $tree->getSiteTreeID(), trim($path, '/')));
            $cID = $item->get();
            if ($item->isMiss()) {
                $db = Database::connection();
                $cID = $db->fetchColumn('select Pages.cID from PagePaths inner join Pages on Pages.cID = PagePaths.cID where cPath = ? and siteTreeID = ?', [$path, $tree->getSiteTreeID()]);
                $cache->save($item->set($cID));
            }
        } else {
            $item = $cache->getItem(sprintf('page/path/%s', trim($path, '/')));
            $cID = $item->get();
            if ($item->isMiss()) {
                $db = Database::connection();
                $cID = $db->fetchColumn('select cID from PagePaths where cPath = ?', [$path]);
                $cache->save($item->set($cID));
            }
        }

        return self::getByID($cID, $version);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\ObjectInterface::getObjectAttributeCategory()
     *
     * @return \Concrete\Core\Attribute\Category\PageCategory
     */
    public function getObjectAttributeCategory()
    {
        $app = Facade::getFacadeApplication();

        return $app->make('\Concrete\Core\Attribute\Category\PageCategory');
    }

    /**
     * * Get a page given its ID.
     *
     * @param int $cID the ID of the page
     * @param string $version the page version ('RECENT' for the most recent version, 'ACTIVE' for the currently published version, 'SCHEDULED' for the currently scheduled version, or an integer to retrieve a specific version ID)
     *
     * @return \Concrete\Core\Page\Page
     */
    public static function getByID($cID, $version = 'RECENT')
    {
        /** @var RequestCache $cache */
        $cache = Facade::getFacadeApplication()->make('cache/request');
        $class = get_called_class();
        $key = '/' . substr($class, strrpos($class, '\\') + 1) . '/' . $cID . '/' . $version;
        if ($cID && $version && $cache->isEnabled()) {
            $item = $cache->getItem($key);
            if ($item->isHit()) {
                $c = $item->get();
                if ($c instanceof $class) {
                    return $c;
                }
            }
        }

        $where = 'where Pages.cID = ?';
        $c = new $class();
        $c->populatePage($cID, $where, $version);

        // must use cID instead of c->getCollectionID() because cID may be the pointer to another page
        if ($cID && $version && isset($item) && $item->isMiss()) {
            $item->set($c);
            $cache->save($item);
        }

        return $c;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Export\ExportableInterface::getExporter()
     *
     * @return \Concrete\Core\Page\Exporter
     */
    public function getExporter()
    {
        return new Exporter();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Permission\ObjectInterface::getPermissionResponseClassName()
     */
    public function getPermissionResponseClassName()
    {
        return '\\Concrete\\Core\\Permission\\Response\\PageResponse';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Permission\ObjectInterface::getPermissionAssignmentClassName()
     */
    public function getPermissionAssignmentClassName()
    {
        return '\\Concrete\\Core\\Permission\\Assignment\\PageAssignment';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Permission\ObjectInterface::getPermissionObjectKeyCategoryHandle()
     */
    public function getPermissionObjectKeyCategoryHandle()
    {
        return 'page';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Summary\Template\Category\CategoryMemberInterface::getSummaryCategoryHandle()
     */
    public function getSummaryCategoryHandle() : string
    {
        return 'page';
    }

    /**
     * @return int|null
     */
    public function getSummaryIdentifier()
    {
        return $this->getCollectionID();
    }

    /**
     * Return a representation of the Page object as something easily serializable.
     *
     * @return \stdClass
     */
    public function getJSONObject()
    {
        $r = new \stdClass();
        $r->type = $this->getPageTypeName();
        $r->name = $this->getCollectionName() !== '' ? $this->getCollectionName() : t('(No Title)');
        $r->datePublic = DefaultSet::getCollectionDatePublic($this);
        $r->dateModified = DefaultSet::getCollectionDateModified($this);
        $r->author = DefaultSet::getCollectionAuthor($this);
        if ($this->isAliasPage()) {
            $r->cID = $this->getCollectionPointerOriginalID();
        } else {
            $r->cID = $this->getCollectionID();
        }
        if ($this->isExternalLink()) {
            $r->url = $this->getCollectionPointerExternalLink();
        } else {
            $r->url = (string) $this->getCollectionLink();
        }

        return $r;
    }

    /**
     * Get the page controller.
     *
     * @return \Concrete\Core\Page\Controller\PageController
     */
    public function getPageController()
    {
        if (!isset($this->controller)) {
            $env = Environment::get();
            if ($this->getPageTypeID() > 0) {
                $pt = $this->getPageTypeObject();
                // return null if page type doesn't exist anymore
                if (!$pt) {
                    return;
                }
                $ptHandle = $pt->getPageTypeHandle();
                $r = $env->getRecord(DIRNAME_CONTROLLERS . '/' . DIRNAME_PAGE_TYPES . '/' . $ptHandle . '.php', $pt->getPackageHandle());
                $prefix = $r->override ? true : $pt->getPackageHandle();
                $class = core_class('Controller\\PageType\\' . camelcase($ptHandle), $prefix);
            } elseif ($this->isGeneratedCollection()) {
                $file = $this->getCollectionFilename();
                if (strpos($file, '/' . FILENAME_COLLECTION_VIEW) !== false) {
                    $path = substr($file, 0, strpos($file, '/' . FILENAME_COLLECTION_VIEW));
                } else {
                    $path = substr($file, 0, strpos($file, '.php'));
                }
                $r = $env->getRecord(DIRNAME_CONTROLLERS . '/' . DIRNAME_PAGE_CONTROLLERS . $path . '.php', $this->getPackageHandle());
                $prefix = $r->override ? true : $this->getPackageHandle();
                $class = core_class('Controller\\SinglePage\\' . str_replace('/', '\\', camelcase($path, true)), $prefix);
            }

            if (isset($class) && class_exists($class)) {
                $this->controller = Core::make($class, [$this]);
            } else {
                $this->controller = Core::make('\PageController', [$this]);
            }
        }

        return $this->controller;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Permission\ObjectInterface::getPermissionObjectIdentifier()
     */
    public function getPermissionObjectIdentifier()
    {
        // this is a hack but it's a really good one for performance
        // if the permission access entity for page owner exists in the database, then we return the collection ID. Otherwise, we just return the permission collection id
        // this is because page owner is the ONLY thing that makes it so we can't use getPermissionsCollectionID, and for most sites that will DRAMATICALLY reduce the number of queries.
        // Drafts are exceptions to this rule because some permission keys of these pages are inherited from "Edit Page Type Draft" permission.
        if (\Concrete\Core\Permission\Access\PageAccess::usePermissionCollectionIDForIdentifier() && !$this->isPageDraft()) {
            return $this->getPermissionsCollectionID();
        }

        return $this->getCollectionID();
    }

    /**
     * Is the page in edit mode?
     *
     * @return bool
     */
    public function isEditMode()
    {
        if ($this->getCollectionPath() == STACKS_LISTING_PAGE_PATH) {
            return true;
        }
        if ($this->getPageTypeHandle() == STACKS_PAGE_TYPE) {
            return true;
        }

        return $this->isCheckedOutByMe();
    }

    /**
     * Get the package ID for a page (page thats added by a package) (returns 0 if its not in a package).
     *
     * @return int
     */
    public function getPackageID()
    {
        return $this->pkgID;
    }

    /**
     * Get the handle the the package that added this page.
     *
     * @return string|null
     */
    public function getPackageHandle()
    {
        if (!isset($this->pkgHandle)) {
            $this->pkgHandle = PackageList::getHandle($this->pkgID);
        }

        return $this->pkgHandle;
    }

    /**
     * @deprecated There's no more an "Arrange Mode"
     *
     * @return false
     */
    public function isArrangeMode()
    {
        return $this->isCheckedOutByMe() && isset($_REQUEST['btask']) && $_REQUEST['btask'] === 'arrange';
    }

    /**
     * Forces the page to be checked in if its checked out.
     */
    public function forceCheckIn()
    {
        $db = Database::connection();
        $q = 'update Pages set cIsCheckedOut = 0, cCheckedOutUID = null, cCheckedOutDatetime = null, cCheckedOutDatetimeLastEdit = null where cID = ?';
        $db->executeQuery($q, [$this->cID]);
    }

    /**
     * @private
     * Forces all pages to be checked in and edit mode to be reset.
     * @TODO – move this into a command in version 9.
     */
    public static function forceCheckInForAllPages()
    {
        $db = Database::connection();
        $q = 'update Pages set cIsCheckedOut = 0, cCheckedOutUID = null, cCheckedOutDatetime = null, cCheckedOutDatetimeLastEdit = null';
        $db->executeQuery($q);
    }

    /**
     * Is this a dashboard page?
     *
     * @return bool
     */
    public function isAdminArea()
    {
        if ($this->isGeneratedCollection()) {
            $pos = strpos($this->getCollectionFilename(), '/' . DIRNAME_DASHBOARD);

            return $pos > -1;
        }

        return false;
    }

    /**
     * Uses a Request object to determine which page to load. Queries by path and then by cID.
     *
     * @param \Concrete\Core\Http\Request $request
     *
     * @return \Concrete\Core\Page\Page
     */
    public static function getFromRequest(Request $request)
    {
        // if something has already set a page object, we return it
        $c = $request->getCurrentPage();
        if (is_object($c)) {
            return $c;
        }
        if ($request->getPath() != '') {
            $path = $request->getPath();
            $db = Database::connection();
            $cID = false;
            $ppIsCanonical = false;
            $site = \Core::make('site')->getSite();
            $treeIDs = [0];
            foreach ($site->getLocales() as $locale) {
                $tree = $locale->getSiteTree();
                if (is_object($tree)) {
                    $treeIDs[] = $tree->getSiteTreeID();
                }
            }

            $treeIDs = implode(',', $treeIDs);

            while ((!$cID) && $path) {
                $row = $db->fetchAssoc('select pp.cID, ppIsCanonical from PagePaths pp inner join Pages p on pp.cID = p.cID where cPath = ? and siteTreeID in (' . $treeIDs . ')', [$path]);
                if (!empty($row)) {
                    $cID = $row['cID'];
                    if ($cID) {
                        $cPath = $path;
                        $ppIsCanonical = (bool) $row['ppIsCanonical'];
                        break;
                    }
                }
                $path = substr($path, 0, strrpos($path, '/'));
            }
            if ($cID && $cPath) {
                $c = self::getByID($cID, 'ACTIVE');
                $c->cPathFetchIsCanonical = $ppIsCanonical;
            } else {
                $c = new self();
                $c->loadError(COLLECTION_NOT_FOUND);
            }

            return $c;
        }
        $cID = $request->query->get('cID');
        if (!$cID) {
            $cID = $request->request->get('cID');
        }
        $cID = Core::make('helper/security')->sanitizeInt($cID);
        if ($cID) {
            $c = self::getByID($cID, 'ACTIVE');
        } else {
            $site = \Core::make('site')->getSite();
            $c = $site->getSiteHomePageObject('ACTIVE');
        }
        $c->cPathFetchIsCanonical = true;


        return $c;
    }

    /**
     * Persist the data associated to a block when it has been moved around in the page.
     *
     * @param int $area_id The ID of the area where the block resides after the arrangment
     * @param int $moved_block_id The ID of the moved block
     * @param int[] $block_order The IDs of all the blocks in the area, ordered by their display order
     */
    public function processArrangement($area_id, $moved_block_id, $block_order)
    {
        $area_handle = Area::getAreaHandleFromID($area_id);
        $db = Database::connection();

        // Remove the moved block from its old area, and all blocks from the destination area.
        $db->executeQuery('UPDATE CollectionVersionBlockStyles SET arHandle = ?  WHERE cID = ? and cvID = ? and bID = ?',
                     [$area_handle, $this->getCollectionID(), $this->getVersionID(), $moved_block_id]);
        $db->executeQuery('UPDATE CollectionVersionBlocks SET arHandle = ?  WHERE cID = ? and cvID = ? and bID = ?',
                     [$area_handle, $this->getCollectionID(), $this->getVersionID(), $moved_block_id]);

        $update_query = 'UPDATE CollectionVersionBlocks SET cbDisplayOrder = CASE bID';
        $when_statements = [];
        $update_values = [];
        foreach ($block_order as $key => $block_id) {
            $when_statements[] = 'WHEN ? THEN ?';
            $update_values[] = $block_id;
            $update_values[] = $key;
        }

        $update_query .= ' ' . implode(' ', $when_statements) . ' END WHERE bID in (' .
            implode(',', array_pad([], count($block_order), '?')) . ') AND cID = ? AND cvID = ?';
        $values = array_merge($update_values, $block_order);
        $values = array_merge($values, [$this->getCollectionID(), $this->getVersionID()]);

        $db->executeQuery($update_query, $values);
    }

    /**
     * Is the page checked out?
     *
     * @return bool|null returns NULL if the page does not exist, a boolean otherwise
     */
    public function isCheckedOut()
    {
        // function to inform us as to whether the current collection is checked out
        $db = Database::connection();
        if (isset($this->isCheckedOutCache)) {
            return $this->isCheckedOutCache;
        }

        $q = "select cIsCheckedOut, cCheckedOutDatetimeLastEdit from Pages where cID = '{$this->cID}'";
        $r = $db->executeQuery($q);

        if ($r) {
            $row = $r->fetchRow();
            // If cCheckedOutDatetimeLastEdit is present, get the time span in seconds since it's last edit.
            if (!empty($row['cCheckedOutDatetimeLastEdit'])) {
                $dh = Core::make('helper/date');
                $timeSinceCheckout = ($dh->getOverridableNow(true) - strtotime($row['cCheckedOutDatetimeLastEdit']));
            }

            if ($row['cIsCheckedOut'] == 0) {
                return false;
            }
            if (isset($timeSinceCheckout) && $timeSinceCheckout > CHECKOUT_TIMEOUT) {
                $this->forceCheckIn();
                $this->isCheckedOutCache = false;

                return false;
            }
            $this->isCheckedOutCache = true;

            return true;
        }
    }

    /**
     * Gets the user that is editing the current page.
     *
     * @return string
     */
    public function getCollectionCheckedOutUserName()
    {
        $db = Database::connection();
        $query = 'select cCheckedOutUID from Pages where cID = ?';
        $vals = [$this->cID];
        $checkedOutId = $db->fetchColumn($query, $vals);
        if (is_object(UserInfo::getByID($checkedOutId))) {
            $ui = UserInfo::getByID($checkedOutId);
            $name = $ui->getUserName();
        } else {
            $name = t('Unknown User');
        }

        return $name;
    }

    /**
     * Checks if the page is checked out by the current user.
     *
     * @return bool
     */
    public function isCheckedOutByMe()
    {
        $app = Application::getFacadeApplication();
        $u = $app->make(User::class);

        return $this->getCollectionCheckedOutUserID() > 0 && $this->getCollectionCheckedOutUserID() == $u->getUserID();
    }

    /**
     * Checks if the page is a single page.
     *
     * Generated collections are collections without templates, that have special cFilename attributes
     *.
     *
     * @return bool
     */
    public function isGeneratedCollection()
    {
        return $this->getCollectionFilename() && !$this->getPageTemplateID();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Permission\AssignableObjectInterface::setPermissionsToOverride()
     */
    public function setPermissionsToOverride()
    {
        if ($this->cInheritPermissionsFrom != 'OVERRIDE') {
            $this->setPermissionsToManualOverride();
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Permission\AssignableObjectInterface::setChildPermissionsToOverride()
     */
    public function setChildPermissionsToOverride()
    {
        foreach ($this->getCollectionChildren() as $child) {
            $child->setPermissionsToManualOverride();
        }
    }

    /**
     * Remove specific permission keys for a specific access entity (user, group, group combination).
     *
     * @param \Concrete\Core\User\Group\Group|\Concrete\Core\User\Group\Group[]|\Concrete\Core\User\User|\Concrete\Core\User\UserInfo|\Concrete\Core\Entity\User\User $userOrGroup A list of groups for a group combination, or a group or a user
     * @param string[] $permissions the handles of page permission keys to be removed
     */
    public function removePermissions($userOrGroup, $permissions = [])
    {
        if ($this->cInheritPermissionsFrom != 'OVERRIDE') {
            return;
        }

        if (is_array($userOrGroup)) {
            $pe = GroupCombinationPermissionAccessEntity::getOrCreate($userOrGroup);
        // group combination
        } elseif ($userOrGroup instanceof User || $userOrGroup instanceof \Concrete\Core\User\UserInfo) {
            $pe = UserPermissionAccessEntity::getOrCreate($userOrGroup);
        } else {
            // group;
            $pe = GroupPermissionAccessEntity::getOrCreate($userOrGroup);
        }

        foreach ($permissions as $pkHandle) {
            $pk = PagePermissionKey::getByHandle($pkHandle);
            $pk->setPermissionObject($this);
            $pa = $pk->getPermissionAccessObject();
            if (is_object($pa)) {
                if ($pa->isPermissionAccessInUse()) {
                    $pa = $pa->duplicate();
                }
                $pa->removeListItem($pe);
                $pt = $pk->getPermissionAssignmentObject();
                $pt->assignPermissionAccess($pa);
            }
        }
    }

    /**
     * Get the drafts parent page for a specific site.
     *
     * @param \Concrete\Core\Entity\Site\Site|null $site If not specified, we'll use the default site
     *
     * @return \Concrete\Core\Page\Page
     */
    public static function getDraftsParentPage(Site $site = null)
    {
        $db = Database::connection();
        $site = $site ? $site : \Core::make('site')->getSite();
        $cParentID = $db->fetchColumn('select p.cID from PagePaths pp inner join Pages p on pp.cID = p.cID inner join SiteLocales sl on p.siteTreeID = sl.siteTreeID where cPath = ? and sl.siteID = ?', [Config::get('concrete.paths.drafts'), $site->getSiteID()]);

        return self::getByID($cParentID);
    }

    /**
     * Get the list of draft pages in a specific site.
     *
     * @param \Concrete\Core\Entity\Site\Site $site
     *
     * @return \Concrete\Core\Page\Page[]
     */
    public static function getDrafts(Site $site)
    {
        $db = Database::connection();
        $nc = self::getDraftsParentPage($site);
        $r = $db->executeQuery('select Pages.cID from Pages inner join Collections c on Pages.cID = c.cID where cParentID = ? order by cDateAdded desc', [$nc->getCollectionID()]);
        $pages = [];
        while ($row = $r->FetchRow()) {
            $entry = self::getByID($row['cID']);
            if (is_object($entry)) {
                $pages[] = $entry;
            }
        }

        return $pages;
    }

    /**
     * Is this a draft page?
     *
     * @return bool
     */
    public function isPageDraft()
    {
        if (isset($this->cIsDraft) && $this->cIsDraft) {
            return true;
        }

        return false;
    }

    /**
     * Set the page controller.
     *
     * @param \Concrete\Core\Page\Controller\PageController|null $controller
     */
    public function setController($controller)
    {
        $this->controller = $controller;
    }

    /**
     * @deprecated use the getPageController() method
     *
     * @return \Concrete\Core\Page\Controller\PageController
     */
    public function getController()
    {
        return $this->getPageController();
    }

    /**
     * This is the legacy function that is called just by xml. We pass these values in as though they were the old ones.
     *
     * @private
     *
     * @param \SimpleXMLElement $px
     */
    public function assignPermissionSet($px)
    {
        if (isset($px->guests)) {
            $pkHandles = self::translatePermissionsXMLToKeys($px->guests);
            $this->assignPermissions(Group::getByID(GUEST_GROUP_ID), $pkHandles);
        }
        if (isset($px->registered)) {
            $pkHandles = self::translatePermissionsXMLToKeys($px->registered);
            $this->assignPermissions(Group::getByID(REGISTERED_GROUP_ID), $pkHandles);
        }
        if (isset($px->administrators)) {
            $pkHandles = self::translatePermissionsXMLToKeys($px->administrators);
            $this->assignPermissions(Group::getByID(ADMIN_GROUP_ID), $pkHandles);
        }
        if (isset($px->group)) {
            foreach ($px->group as $g) {
                $pkHandles = self::translatePermissionsXMLToKeys($px->administrators);
                $this->assignPermissions(Group::getByID($g['gID']), $pkHandles);
            }
        }
        if (isset($px->user)) {
            foreach ($px->user as $u) {
                $pkHandles = self::translatePermissionsXMLToKeys($px->administrators);
                $this->assignPermissions(UserInfo::getByID($u['uID']), $pkHandles);
            }
        }
    }

    public function getSummaryTemplates(): array
    {
        $app = Application::getFacadeApplication();
        $em = $app->make(EntityManagerInterface::class);
        return $em->getRepository(\Concrete\Core\Entity\Page\Summary\PageTemplate::class)
            ->findByCID($this->getCollectionID());
    }

    /**
     * @return CustomPageTemplateCollection
     */
    protected function getCustomPageSummaryTemplateCollection()
    {
        $app = Application::getFacadeApplication();
        $em = $app->make(EntityManagerInterface::class);
        return $em->getRepository(CustomPageTemplateCollection::class)
            ->findOneByCID($this->getCollectionID());
    }

    public function hasCustomSummaryTemplates(): bool
    {
        if ($this->getCustomPageSummaryTemplateCollection()) {
            return true;
        }
        return false;
    }

    public function getCustomSelectedSummaryTemplates(): array
    {
        $collection = $this->getCustomPageSummaryTemplateCollection();
        if ($collection) {
            $templates = $collection->getTemplates();
            if ($templates) {
                return $templates->toArray();
            }
        }
        return [];
    }


    /**
     * Make an alias to a page.
     *
     * @param \Concrete\Core\Page\Page $parentPage The parent page
     * @param mixed $c
     *
     * @return int The ID of the new collection
     */
    public function addCollectionAlias($c)
    {
        $app = Application::getFacadeApplication();
        $db = Database::connection();
        // the passed collection is the parent collection
        $cParentID = $c->getCollectionID();

        $u = $app->make(User::class);
        $uID = $u->getUserID();

        $handle = (string) $this->getCollectionHandle();
        if ($handle === '') {
            $handle = Core::make('helper/text')->handle($this->getCollectionName());
        }
        $cDisplayOrder = $c->getNextSubPageDisplayOrder();

        $_cParentID = $c->getCollectionID();
        $q = 'select PagePaths.cPath from PagePaths where cID = ?';
        $v = [$_cParentID];
        if ($_cParentID != static::getHomePageID()) {
            $q .= ' and ppIsCanonical = ?';
            $v[] = 1;
        }
        $cPath = $db->fetchColumn($q, $v);

        $data = [
            'handle' => $handle,
            'name' => '',
        ];
        $cobj = parent::addCollection($data);
        $newCID = $cobj->getCollectionID();
        $siteTreeID = $c->getSiteTreeID();

        $v = [$newCID, $siteTreeID, $cParentID, $uID, $this->getCollectionID(), $cDisplayOrder];
        $q = 'insert into Pages (cID, siteTreeID, cParentID, uID, cPointerID, cDisplayOrder) values (?, ?, ?, ?, ?, ?)';
        $r = $db->prepare($q);

        $r->execute($v);

        PageStatistics::incrementParents($newCID);

        $q2 = 'insert into PagePaths (cID, cPath, ppIsCanonical, ppGeneratedFromURLSlugs) values (?, ?, ?, ?)';
        $v2 = [$newCID, $cPath . '/' . $handle, 1, 1];
        $db->executeQuery($q2, $v2);
        $pe = new Event(\Page::getByID($newCID));
        Events::dispatch('on_page_alias_add', $pe);
        return $newCID;
    }

    /**
     * Update the alias details.
     *
     * @param array $data Supported keys:
     * <ul>
     *     <li>string <code>customAliasName</code> empty string to use the name of the aliased page</li>
     *     <li>string <code>aliasHandle</code> the URL slug of the alias</li>
     * </ul>
     */
    public function updateCollectionAlias(array $data): void
    {
        if (!$this->isAliasPage()) {
            return;
        }
        $app = Application::getFacadeApplication();
        $data += [
            'customAliasName' => null,
            'aliasHandle' => '',
        ];
        $cSet = [];
        $cvSet = [];
        if ((string) $data['aliasHandle'] !== '') {
            $cSet['cHandle'] = $data['aliasHandle'];
            $cvSet['cvHandle'] = $data['aliasHandle'];
        }
        if ($data['customAliasName'] !== null) {
            $cvSet['cvName'] = $data['customAliasName'];
        }
        $db = $app->make(Connection::class);
        if ($cSet !== []) {
            $db->update(
                'Collections',
                $cSet,
                ['cID' => $this->getCollectionPointerOriginalID()]
            );
        }
        if ($cvSet !== []) {
            $db->update(
                'CollectionVersions',
                $cvSet,
                ['cID' => $this->getCollectionPointerOriginalID()]
            );
        }
        if ($data['customAliasName'] !== null) {
            $this->customAliasName = $data['customAliasName'];
        }
        if ((string) $data['aliasHandle'] !== '') {
            $this->aliasHandle = $data['aliasHandle'];
            $this->rescanCollectionPath();
        }
        $pe = new Event($this);
        $eventDispatcher = $app->make(EventDispatcherInterface::class);
        $eventDispatcher->dispatch('on_page_alias_edit', $pe);
    }

    /**
     * Update the name, link, and to open in a new window for an external link.
     *
     * @param string $cName
     * @param string $cLink
     * @param bool $newWindow
     */
    public function updateCollectionAliasExternal($cName, $cLink, $newWindow = 0)
    {
        if ($this->isExternalLink()) {
            $db = Database::connection();
            $this->markModified();
            if ($newWindow) {
                $newWindow = 1;
            } else {
                $newWindow = 0;
            }
            $db->executeQuery('update CollectionVersions set cvName = ? where cID = ?', [$cName, $this->cID]);
            $db->executeQuery('update Pages set cPointerExternalLink = ?, cPointerExternalLinkNewWindow = ? where cID = ?', [$cLink, $newWindow, $this->cID]);
        }
    }

    /**
     * Add a new external link as a child of this page.
     *
     * @param string $cName
     * @param string $cLink
     * @param bool $newWindow
     *
     * @return int The ID of the new collection
     */
    public function addCollectionAliasExternal($cName, $cLink, $newWindow = 0)
    {
        $app = Application::getFacadeApplication();
        $db = Database::connection();
        $dt = Core::make('helper/text');
        $ds = Core::make('helper/security');
        $u = $app->make(User::class);

        $cParentID = $this->getCollectionID();
        $uID = $u->getUserID();

        // make the handle out of the title
        $cLink = $ds->sanitizeURL($cLink);
        $handle = $dt->urlify($cName);
        $data = [
            'handle' => $handle,
            'name' => $cName,
        ];
        $cobj = parent::addCollection($data);
        $newCID = $cobj->getCollectionID();

        if ($newWindow) {
            $newWindow = 1;
        } else {
            $newWindow = 0;
        }

        $cInheritPermissionsFromCID = $this->getPermissionsCollectionID();
        $cInheritPermissionsFrom = 'PARENT';

        $siteTreeID = \Core::make('site')->getSite()->getSiteTreeID();

        $v = [$newCID, $siteTreeID, $cParentID, $uID, $cInheritPermissionsFrom, (int) $cInheritPermissionsFromCID, $cLink, $newWindow];
        $q = 'insert into Pages (cID, siteTreeID, cParentID, uID, cInheritPermissionsFrom, cInheritPermissionsFromCID, cPointerExternalLink, cPointerExternalLinkNewWindow) values (?, ?, ?, ?, ?, ?, ?, ?)';
        $r = $db->prepare($q);

        $r->execute($v);

        PageStatistics::incrementParents($newCID);

        self::getByID($newCID)->movePageDisplayOrderToBottom();

        return $newCID;
    }

    /**
     * Returns true if a page is a system page. A system page is either a page that is outside the site tree (has a site tree ID of 0)
     * or a page that is in the site tree, but whose parent starts at 0. That means its a root level page. Why do we need this
     * separate boolean then? Because we need to easily be able to filter all pages by whether they're a system page even
     * if we don't necessarily know where their starting page is.
     *
     * @return bool
     */
    public function isSystemPage()
    {
        return (bool) $this->cIsSystemPage;
    }

    /**
     * Gets the icon for a page (also fires the on_page_get_icon event).
     *
     * @return string The path to the icon
     */
    public function getCollectionIcon()
    {
        // returns a fully qualified image link for this page's icon, either based on its collection type or if icon.png appears in its view directory
        $pe = new Event($this);
        $pe->setArgument('icon', '');
        Events::dispatch('on_page_get_icon', $pe);
        $icon = $pe->getArgument('icon');

        if ($icon) {
            return $icon;
        }

        if (\Core::make('multilingual/detector')->isEnabled()) {
            $icon = \Concrete\Core\Multilingual\Service\UserInterface\Flag::getDashboardSitemapIconSRC($this);
        }

        if ($this->isGeneratedCollection()) {
            if ($this->getPackageID() > 0) {
                if (is_dir(DIR_PACKAGES . '/' . $this->getPackageHandle())) {
                    $dirp = DIR_PACKAGES;
                    $url = \Core::getApplicationURL();
                } else {
                    $dirp = DIR_PACKAGES_CORE;
                    $url = ASSETS_URL;
                }
                $file = $dirp . '/' . $this->getPackageHandle() . '/' . DIRNAME_PAGES . $this->getCollectionPath() . '/' . FILENAME_PAGE_ICON;
                if (file_exists($file)) {
                    $icon = $url . '/' . DIRNAME_PACKAGES . '/' . $this->getPackageHandle() . '/' . DIRNAME_PAGES . $this->getCollectionPath() . '/' . FILENAME_PAGE_ICON;
                }
            } elseif (file_exists(DIR_FILES_CONTENT . $this->getCollectionPath() . '/' . FILENAME_PAGE_ICON)) {
                $icon = \Core::getApplicationURL() . '/' . DIRNAME_PAGES . $this->getCollectionPath() . '/' . FILENAME_PAGE_ICON;
            } elseif (file_exists(DIR_FILES_CONTENT_REQUIRED . $this->getCollectionPath() . '/' . FILENAME_PAGE_ICON)) {
                $icon = ASSETS_URL . '/' . DIRNAME_PAGES . $this->getCollectionPath() . '/' . FILENAME_PAGE_ICON;
            }
        }


        return $icon;
    }

    /**
     * Remove an external link/alias.
     *
     * @return int|null cID of the original page if the page was an alias
     */
    public function removeThisAlias()
    {
        $pe = new DeletePageEvent($this);
        Events::dispatch('on_page_alias_delete', $pe);

        if ($this->isExternalLink()) {
            $this->delete();
        } elseif ($this->isAliasPage()) {
            $cIDRedir = $this->getCollectionPointerID();
            $db = Database::connection();

            PageStatistics::decrementParents($this->getCollectionPointerOriginalID());

            $args = [$this->getCollectionPointerOriginalID()];
            $q = 'delete from Pages where cID = ?';
            $db->executeQuery($q, $args);

            $q = 'delete from Collections where cID = ?';
            $db->executeQuery($q, $args);

            $q = 'delete from CollectionVersions where cID = ?';
            $db->executeQuery($q, $args);

            $q = 'delete from PagePaths where cID = ?';
            $db->executeQuery($q, $args);

            return $cIDRedir;
        }
    }

    /**
     * Create an array containing data about child pages.
     *
     * @param array $pages the previously loaded data
     * @param array $pageRow The data of current parent page (it must contain cID and optionally cDisplayOrder)
     * @param int $cParentID The parent page ID
     * @param int $level The current depth level
     * @param bool $includeThisPage Should $pageRow itself be added to the resulting array?
     *
     * @return array Every array item contains the following keys: {
     *
     *    @var int $cID
     *    @var int $cDisplayOrder
     *    @var int $cParentID
     *    @var int $level
     *    @var int $total
     * }
     */
    public function populateRecursivePages($pages, $pageRow, $cParentID, $level, $includeThisPage = true)
    {
        $db = Database::connection();
        $children = $db->GetAll('select cID, cDisplayOrder from Pages where cParentID = ? order by cDisplayOrder asc', [$pageRow['cID']]);
        if ($includeThisPage) {
            $pages[] = [
                'cID' => $pageRow['cID'],
                'cDisplayOrder' => isset($pageRow['cDisplayOrder']) ? $pageRow['cDisplayOrder'] : null,
                'cParentID' => $cParentID,
                'level' => $level,
                'total' => count($children),
            ];
        }
        ++$level;
        $cParentID = $pageRow['cID'];
        if (count($children) > 0) {
            foreach ($children as $pageRow) {
                $pages = $this->populateRecursivePages($pages, $pageRow, $cParentID, $level);
            }
        }

        return $pages;
    }

    /**
     * Sort a list of pages, so that the order is correct for the deletion.
     *
     * @param array $a
     * @param array $b
     *
     * @return int
     */
    public static function queueForDeletionSort($a, $b)
    {
        if ($a['level'] > $b['level']) {
            return -1;
        }
        if ($a['level'] < $b['level']) {
            return 1;
        }

        return 0;
    }

    /**
     * Sort a list of pages, so that the order is correct for the duplication.
     *
     * @param array $a
     * @param array $b
     *
     * @return int
     */
    public static function queueForDuplicationSort($a, $b)
    {
        if ($a['level'] > $b['level']) {
            return 1;
        }
        if ($a['level'] < $b['level']) {
            return -1;
        }
        if ($a['cDisplayOrder'] > $b['cDisplayOrder']) {
            return 1;
        }
        if ($a['cDisplayOrder'] < $b['cDisplayOrder']) {
            return -1;
        }
        if ($a['cID'] > $b['cID']) {
            return 1;
        }
        if ($a['cID'] < $b['cID']) {
            return -1;
        }

        return 0;
    }

    /**
     * @deprecated use the \Concrete\Core\Page\Exporter class
     *
     * @param \SimpleXMLElement $pageNode
     *
     * @see \Concrete\Core\Page\Page::getExporter()
     */
    public function export($pageNode)
    {
        $exporter = new Exporter();
        $exporter->export($this, $pageNode);
    }

    /**
     * Get the uID for a page that is checked out (if any).
     *
     * @return int|null
     */
    public function getCollectionCheckedOutUserID()
    {
        return $this->cCheckedOutUID;
    }

    /**
     * Get the path of this page.
     *
     * @return string
     */
    public function getCollectionPath()
    {
        return isset($this->cPath) ? $this->cPath : null;
    }

    /**
     * Returns the PagePath object for the current page.
     *
     * @return \Concrete\Core\Entity\Page\PagePath|null
     */
    public function getCollectionPathObject()
    {
        $em = \ORM::entityManager();
        $cID = ($this->getCollectionPointerOriginalID() > 0) ? $this->getCollectionPointerOriginalID() : $this->cID;
        $path = $em->getRepository('\Concrete\Core\Entity\Page\PagePath')->findOneBy(
            ['cID' => $cID, 'ppIsCanonical' => true,
        ]);

        return $path;
    }

    /**
     * Add a non-canonical page path to the current page.
     *
     * @param string $cPath
     * @param bool $commit Should the new PagePath instance be persisted?
     *
     * @return \Concrete\Core\Entity\Page\PagePath
     */
    public function addAdditionalPagePath($cPath, $commit = true)
    {
        $em = \ORM::entityManager();
        $path = new \Concrete\Core\Entity\Page\PagePath();
        $path->setPagePath('/' . trim($cPath, '/'));
        $path->setPageObject($this);
        $em->persist($path);
        if ($commit) {
            $em->flush();
        }

        return $path;
    }

    /**
     * Set the canonical page path for a page.
     *
     * @param string $cPath
     * @param bool $isAutoGenerated is the page path generated from URL slugs?
     */
    public function setCanonicalPagePath($cPath, $isAutoGenerated = false)
    {
        $em = \ORM::entityManager();
        $path = $this->getCollectionPathObject();
        if (is_object($path)) {
            $path->setPagePath($cPath);
        } else {
            $path = new \Concrete\Core\Entity\Page\PagePath();
            $path->setPagePath($cPath);
            $path->setPageObject($this);
        }
        $path->setPagePathIsAutoGenerated($isAutoGenerated);
        $path->setPagePathIsCanonical(true);
        $em->persist($path);
        $em->flush();
    }

    /**
     * Get all the page paths of this page.
     *
     * @return \Concrete\Core\Entity\Page\PagePath[]
     */
    public function getPagePaths()
    {
        $em = \ORM::entityManager();

        return $em->getRepository('\Concrete\Core\Entity\Page\PagePath')->findBy(
            ['cID' => $this->getCollectionID()], ['ppID' => 'asc']
        );
    }

    /**
     * Get all the non-canonical page paths of this page.
     *
     * @return \Concrete\Core\Entity\Page\PagePath[]
     */
    public function getAdditionalPagePaths()
    {
        $em = \ORM::entityManager();

        return $em->getRepository('\Concrete\Core\Entity\Page\PagePath')->findBy(
            ['cID' => $this->getCollectionID(), 'ppIsCanonical' => false,
        ]);
    }

    /**
     * Clears all page paths for a page.
     */
    public function clearPagePaths()
    {
        $em = \ORM::entityManager();
        $paths = $this->getPagePaths();
        foreach ($paths as $path) {
            $em->remove($path);
        }
        $em->flush();
    }

    /**
     * Returns full url for the current page.
     *
     * @param bool $appendBaseURL UNUSED
     *
     * @return string
     */
    public function getCollectionLink($appendBaseURL = false)
    {
        return Core::make('helper/navigation')->getLinkToCollection($this, $appendBaseURL);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Site\Tree\TreeInterface::getSiteTreeID()
     */
    public function getSiteTreeID()
    {
        return $this->cPointerOriginalSiteTreeID ?: $this->siteTreeID;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Site\SiteAggregateInterface::getSite()
     */
    public function getSite()
    {
        $tree = $this->getSiteTreeObject();
        if ($tree instanceof SiteTree) {
            return $tree->getSite();
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Site\Tree\TreeInterface::getSiteTreeObject()
     */
    public function getSiteTreeObject()
    {
        if (!isset($this->siteTree) && $this->getSiteTreeID()) {
            $em = \ORM::entityManager();
            $this->siteTree = $em->find('\Concrete\Core\Entity\Site\Tree', $this->getSiteTreeID());
        }

        return $this->siteTree;
    }

    /**
     * Returns the path for a page from its cID.
     *
     * @param int cID
     * @param mixed $cID
     *
     * @return @return string|false
     */
    public static function getCollectionPathFromID($cID)
    {
        $db = Database::connection();
        $path = $db->fetchColumn('select cPath from PagePaths inner join CollectionVersions on (PagePaths.cID = CollectionVersions.cID and CollectionVersions.cvIsApproved = 1) where PagePaths.cID = ? order by PagePaths.ppIsCanonical desc', [$cID]);

        return $path;
    }

    /**
     * Get the uID of the page author (if any).
     *
     * @return int|null
     */
    public function getCollectionUserID()
    {
        return $this->uID;
    }

    /**
     * Get the page handle.
     *
     * @return string
     */
    public function getCollectionHandle()
    {
        return $this->vObj->cvHandle;
    }

    /**
     * @deprecated use the getPageTypeName() method
     *
     * @return string|null
     */
    public function getCollectionTypeName()
    {
        return $this->getPageTypeName();
    }

    /**
     * Get the display name of the page type (if available).
     *
     * @return string|null
     */
    public function getPageTypeName()
    {
        if (!isset($this->pageType)) {
            $this->pageType = $this->getPageTypeObject();
        }
        if (is_object($this->pageType)) {
            return $this->pageType->getPageTypeDisplayName();
        }
    }

    /**
     * @deprecated use the getPageTypeID() method
     */
    public function getCollectionTypeID()
    {
        return $this->getPageTypeID();
    }

    /**
     * Get the Collection Type ID.
     *
     * @return int|null
     */
    public function getPageTypeID()
    {
        return isset($this->ptID) ? $this->ptID : null;
    }

    /**
     * Get the page type object.
     *
     * @return \Concrete\Core\Page\Type\Type|null
     */
    public function getPageTypeObject()
    {
        return PageType::getByID($this->getPageTypeID());
    }

    /**
     * Get the Page Template ID.
     *
     * @return int
     */
    public function getPageTemplateID()
    {
        return $this->vObj->pTemplateID;
    }

    /**
     * Get the Page Template Object (if available).
     *
     * @return \Concrete\Core\Entity\Page\Template|null
     */
    public function getPageTemplateObject()
    {
        return PageTemplate::getByID($this->getPageTemplateID());
    }

    /**
     * Get the handle of the Page Template (if available).
     *
     * @return string|false
     */
    public function getPageTemplateHandle()
    {
        $pt = $this->getPageTemplateObject();
        if ($pt instanceof TemplateEntity) {
            return $pt->getPageTemplateHandle();
        }

        return false;
    }

    /**
     * Get the handle of the Page Type (if available).
     *
     * @return string|false
     */
    public function getPageTypeHandle()
    {
        if (!isset($this->ptHandle)) {
            $this->ptHandle = false;
            $ptID = $this->getPageTypeID();
            if ($ptID) {
                $pt = Type::getByID($ptID);
                if (is_object($pt)) {
                    $this->ptHandle = $pt->getPageTypeHandle();
                }
            }
        }

        return $this->ptHandle;
    }

    /**
     * @deprecated use the getPageTypeHandle() method
     *
     * @return string|false
     */
    public function getCollectionTypeHandle()
    {
        return $this->getPageTypeHandle();
    }

    /**
     * Get the theme ID for the collection (if available).
     *
     * @return int|null
     */
    public function getCollectionThemeID()
    {
        $theme = $this->getCollectionThemeObject();
        if (is_object($theme)) {
            return $theme->getThemeID();
        }
    }

    /**
     * Check if a block is an alias from a page default.
     *
     * @param \Concrete\Core\Block\Block $b
     *
     * @return bool
     */
    public function isBlockAliasedFromMasterCollection($b)
    {
        if (!$b->isAlias()) {
            return false;
        }
        //Retrieve info for all of this page's blocks at once (and "cache" it)
        // so we don't have to query the database separately for every block on the page.
        if ($this->blocksAliasedFromMasterCollection === null) {
            $db = Database::connection();
            $q = 'SELECT cvb.bID FROM CollectionVersionBlocks AS cvb
                    INNER JOIN CollectionVersionBlocks AS cvb2
                        ON cvb.bID = cvb2.bID
                            AND cvb2.cID = ?
                    WHERE cvb.cID = ?
                        AND cvb.isOriginal = 0
                        AND cvb.cvID = ?
                    GROUP BY cvb.bID
                    ;';
            $v = [$this->getMasterCollectionID(), $this->getCollectionID(), $this->getVersionObject()->getVersionID()];
            $this->blocksAliasedFromMasterCollection = $db->GetCol($q, $v);
        }

        return in_array($b->getBlockID(), $this->blocksAliasedFromMasterCollection);
    }

    /**
     * Get the collection's theme object.
     *
     * @return \Concrete\Core\Page\Theme\Theme
     */
    public function getCollectionThemeObject()
    {
        if (!isset($this->themeObject)) {
            $app = Facade::getFacadeApplication();
            $tmpTheme = $app->make(ThemeRouteCollection::class)
                ->getThemeByRoute($this->getCollectionPath());
            if (isset($tmpTheme[0])) {
                switch ($tmpTheme[0]) {
                    case VIEW_CORE_THEME:
                        $this->themeObject = new \Concrete\Theme\Concrete\PageTheme();
                        break;
                    case 'dashboard':
                        $this->themeObject = new \Concrete\Theme\Dashboard\PageTheme();
                        break;
                    default:
                        $this->themeObject = PageTheme::getByHandle($tmpTheme[0]);
                        break;
                }
            } elseif ($this->vObj->pThemeID < 1) {
                $this->themeObject = PageTheme::getSiteTheme();
            } else {
                $this->themeObject = PageTheme::getByID($this->vObj->pThemeID);
            }
        }
        if (!$this->themeObject) {
            $this->themeObject = PageTheme::getSiteTheme();
        }

        return $this->themeObject;
    }

    /**
     * Get the page name.
     *
     * @return string
     */
    public function getCollectionName()
    {
        $customAliasName = (string) $this->getCustomAliasName();
        if ($customAliasName !== '') {
            return $customAliasName;
        }

        if (isset($this->vObj)) {
            return isset($this->vObj->cvName) ? $this->vObj->cvName : null;
        }

        return isset($this->cvName) ? $this->cvName : null;
    }

    /**
     * Get the custom name of the alias page.
     *
     * @return string|null NULL if the page is not an alias, empty string if we should use the name of the aliased page, the custom alias name otherwise.
     */
    public function getCustomAliasName(): ?string
    {
        return $this->customAliasName;
    }

    /**
     * Get the handle of the alias page
     *
     * @return string|null NULL if the page is not an alias, the alias handle otherwise
     */
    public function getAliasHandle(): ?string
    {
        return $this->aliasHandle;
    }

    /**
     * Get the collection ID for the aliased page (returns 0 unless used on an actual alias).
     *
     * @return int
     */
    public function getCollectionPointerID()
    {
        return isset($this->cPointerID) ? (int) $this->cPointerID : 0;
    }

    /**
     * Get the link for the aliased page.
     *
     * @return string|null
     */
    public function getCollectionPointerExternalLink()
    {
        return $this->cPointerExternalLink;
    }

    /**
     * Should the alias link to be opened in a new window?
     *
     * @return bool|int|null
     */
    public function openCollectionPointerExternalLinkInNewWindow()
    {
        return $this->cPointerExternalLinkNewWindow;
    }

    /**
     * Is this page an alias page of another page?
     *
     * @return bool
     *
     * @since concrete5 8.5.0a2
     */
    public function isAliasPage()
    {
        return $this->getCollectionPointerID() > 0;
    }

    /**
     * Is this page an alias page or an external link?
     *
     * @return bool
     *
     * @since concrete5 8.5.0a2
     */
    public function isAliasPageOrExternalLink()
    {
        return $this->isAliasPage() || $this->isExternalLink();
    }

    /**
     * @deprecated This method has been replaced with isAliasPageOrExternalLink() in concrete5 8.5.0a2 (same syntax and same result)
     *
     * @return bool
     */
    public function isAlias()
    {
        return $this->isAliasPageOrExternalLink();
    }

    /**
     * Is this page an external link?
     *
     * @return bool
     */
    public function isExternalLink()
    {
        return $this->cPointerExternalLink != null;
    }

    /**
     * Get the original cID of a page (if it's a page alias).
     *
     * @return int
     */
    public function getCollectionPointerOriginalID()
    {
        return $this->cPointerOriginalID;
    }

    /**
     * Get the file name of a page (single pages).
     *
     * @return string
     */
    public function getCollectionFilename()
    {
        return $this->cFilename;
    }

    /**
     * Get the date/time when the current version was made public (or a falsy value if the current version doesn't have public date).
     *
     * @return string
     *
     * @example 2009-01-01 00:00:00
     */
    public function getCollectionDatePublic()
    {
        return $this->vObj->cvDatePublic;
    }

    /**
     * Get the date/time when the current version was made public (or NULL value if the current version doesn't have public date).
     *
     * @return \DateTime|null
     */
    public function getCollectionDatePublicObject()
    {
        return Core::make('date')->toDateTime($this->getCollectionDatePublic());
    }

    /**
     * Get the description of a page.
     *
     * @return string
     */
    public function getCollectionDescription()
    {
        return $this->vObj->cvDescription;
    }

    /**
     * Ges the cID of the parent page.
     *
     * @return int|null
     */
    public function getCollectionParentID()
    {
        return isset($this->cParentID) ? (int) $this->cParentID : null;
    }

    /**
     * Get the parent cID of a page given its cID.
     *
     * @param int $cID
     *
     * @return int|null
     */
    public static function getCollectionParentIDFromChildID($cID)
    {
        $db = Database::connection();
        $q = 'select cParentID from Pages where cID = ?';
        $cParentID = $db->fetchColumn($q, [$cID]);

        return $cParentID;
    }

    /**
     * Get an array containint this cParentID and aliased parentIDs.
     *
     * @return int[]
     */
    public function getCollectionParentIDs()
    {
        $cIDs = [$this->cParentID];
        $db = Database::connection();
        $aliasedParents = $db->fetchAll('SELECT cParentID FROM Pages WHERE cPointerID = ?', [$this->cID]);
        foreach ($aliasedParents as $aliasedParent) {
            $cIDs[] = $aliasedParent['cParentID'];
        }

        return $cIDs;
    }

    /**
     *  Is this page a page default?
     *
     * @return bool|int|null
     */
    public function isMasterCollection()
    {
        return $this->isMasterCollection;
    }

    /**
     * Are template permissions overriden?
     *
     * @return bool|int|null
     */
    public function overrideTemplatePermissions()
    {
        return $this->cOverrideTemplatePermissions;
    }

    /**
     * Get the position of the page in the sitemap, relative to its parent page.
     *
     * @return int|null
     */
    public function getCollectionDisplayOrder()
    {
        return $this->cDisplayOrder;
    }

    /**
     * Set the theme of this page.
     *
     * @param \Concrete\Core\Page\Theme\Theme $pl
     */
    public function setTheme($pl)
    {
        $db = Database::connection();
        $db->executeQuery('update CollectionVersions set pThemeID = ? where cID = ? and cvID = ?', [$pl->getThemeID(), $this->cID, $this->vObj->getVersionID()]);
        $this->themeObject = $pl;
    }

    /**
     * Set the theme for a page using the page object.
     *
     * @param \Concrete\Core\Page\Type\Type|null $type
     */
    public function setPageType(\Concrete\Core\Page\Type\Type $type = null)
    {
        $ptID = 0;
        if (is_object($type)) {
            $ptID = $type->getPageTypeID();
        }
        $db = Database::connection();
        $db->executeQuery('update Pages set ptID = ? where cID = ?', [$ptID, $this->cID]);
        $this->ptID = $ptID;
    }

    /**
     * Set the permissions of sub-collections added beneath this permissions to inherit from the template.
     */
    public function setPermissionsInheritanceToTemplate()
    {
        $db = Database::connection();
        if ($this->cID) {
            $db->executeQuery('update Pages set cOverrideTemplatePermissions = 0 where cID = ?', [$this->cID]);
        }
    }

    /**
     * Set the permissions of sub-collections added beneath this permissions to inherit from the parent.
     */
    public function setPermissionsInheritanceToOverride()
    {
        $db = Database::connection();
        if ($this->cID) {
            $db->executeQuery('update Pages set cOverrideTemplatePermissions = 1 where cID = ?', [$this->cID]);
        }
    }

    /**
     * Get the ID of the page from which this page inherits permissions from.
     *
     * @return int|null
     */
    public function getPermissionsCollectionID()
    {
        return $this->cInheritPermissionsFromCID;
    }

    /**
     * Where permissions should be inherited from? 'PARENT' or 'TEMPLATE' or 'OVERRIDE'.
     *
     * @return string|null
     */
    public function getCollectionInheritance()
    {
        return $this->cInheritPermissionsFrom;
    }

    /**
     * Get the ID of the page from which the parent page page inherits permissions from.
     *
     * @return int|null
     */
    public function getParentPermissionsCollectionID()
    {
        $db = Database::connection();
        $cParentID = $this->cParentID;
        if (!$cParentID) {
            $cParentID = $this->getSiteHomePageID();
        }

        $v = [$cParentID];
        $q = 'select cInheritPermissionsFromCID from Pages where cID = ?';
        $ppID = $db->fetchColumn($q, $v);

        return $ppID;
    }

    /**
     * Get the page from which this page inherits permissions from.
     *
     * @return \Concrete\Core\Page\Page
     */
    public function getPermissionsCollectionObject()
    {
        return self::getByID($this->cInheritPermissionsFromCID, 'RECENT');
    }

    /**
     * Get the master page of this page, given its page template and page type.
     *
     * @return int returns 0 if not found
     */
    public function getMasterCollectionID()
    {
        $pt = PageType::getByID($this->getPageTypeID());
        if (!is_object($pt)) {
            return 0;
        }
        $template = PageTemplate::getByID($this->getPageTemplateID());
        if (!is_object($template)) {
            return 0;
        }
        $c = $pt->getPageTypePageTemplateDefaultPageObject($template);

        return $c->getCollectionID();
    }

    /**
     * Get the ID of the original collection.
     *
     * @return int|null
     */
    public function getOriginalCollectionID()
    {
        // this is a bit weird...basically, when editing a master collection, we store the
        // master collection ID in session, along with the collection ID we were looking at before
        // moving to the master collection. This allows us to get back to that original collection
        return Session::get('ocID');
    }

    /**
     * Get the number of child pages.
     *
     * @return int|null
     */
    public function getNumChildren()
    {
        return $this->cChildren;
    }

    /**
     * Get the number of child pages (direct children only).
     *
     * @return int
     */
    public function getNumChildrenDirect()
    {
        // direct children only
        $db = Database::connection();
        $v = [$this->cID];
        $num = $db->fetchColumn('select count(cID) as total from Pages where cParentID = ?', $v);
        if ($num) {
            return $num;
        }

        return 0;
    }

    /**
     * Get the first child of the current page, or null if there is no child.
     *
     * @param string $sortColumn the ORDER BY clause
     *
     * @return \Concrete\Core\Page\Page|false
     */
    public function getFirstChild($sortColumn = 'cDisplayOrder asc')
    {
        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);
        $now = $app->make('date')->getOverridableNow();
        $cID = $db->fetchColumn(
            <<<EOT
select
    Pages.cID
from
    Pages
    inner join CollectionVersions
        on Pages.cID = CollectionVersions.cID
where
    cParentID = ?
    and cvIsApproved = 1 and (cvPublishDate is null or cvPublishDate <= ?) and (cvPublishEndDate is null or cvPublishEndDate >= ?)
order by
    {$sortColumn}
EOT
            ,
            [$this->cID, $now, $now]
        );
        if ($cID && $cID != $this->getSiteHomePageID()) {
            return self::getByID($cID, 'ACTIVE');
        }

        return false;
    }

    /**
     * Get the list of child page IDs, sorted by their display order.
     *
     * @param bool $oneLevelOnly set to true to return only the direct children, false for all the child pages
     *
     * @return int[]
     */
    public function getCollectionChildrenArray($oneLevelOnly = 0)
    {
        $this->childrenCIDArray = [];
        $this->_getNumChildren($this->cID, $oneLevelOnly);

        return $this->childrenCIDArray;
    }

    /**
     * Get the immediate children of the this page.
     *
     * @return \Concrete\Core\Page\Page[]
     */
    public function getCollectionChildren()
    {
        $children = [];
        $db = Database::connection();
        $q = 'select cID from Pages where cParentID = ? and cIsTemplate = 0 order by cDisplayOrder asc';
        $r = $db->executeQuery($q, [$this->getCollectionID()]);
        if ($r) {
            while ($row = $r->fetchRow()) {
                if ($row['cID'] > 0) {
                    $c = self::getByID($row['cID']);
                    $children[] = $c;
                }
            }
        }

        return $children;
    }

    /**
     * Check if a collection is this page itself or one of its sub-pages.
     *
     * @param \Concrete\Core\Page\Collection\Collection $cobj
     *
     * @return bool
     */
    public function canMoveCopyTo($cobj)
    {
        $children = $this->getCollectionChildrenArray();
        $children[] = $this->getCollectionID();

        return !in_array($cobj->getCollectionID(), $children);
    }

    /**
     * Update the collection name.
     *
     * @param string $name
     */
    public function updateCollectionName($name)
    {
        $db = Database::connection();
        $vo = $this->getVersionObject();
        $cvID = $vo->getVersionID();
        $this->markModified();
        if (is_object($this->vObj)) {
            $this->vObj->cvName = $name;

            $app = Application::getFacadeApplication();
            $cHandle = $app->make(HandleGenerator::class)->generate($this, ['cName' => $name]);

            $db->executeQuery('update CollectionVersions set cvName = ?, cvHandle = ? where cID = ? and cvID = ?', [$name, $cHandle, $this->getCollectionID(), $cvID]);

            $cache = PageCache::getLibrary();
            $cache->purge($this);

            $pe = new Event($this);
            Events::dispatch('on_page_update', $pe);
        }
    }

    /**
     * Does this page have theme customizations?
     *
     * @return bool
     */
    public function hasPageThemeCustomizations()
    {
        $db = Database::connection();

        return $db->fetchColumn('select count(cID) from CollectionVersionThemeCustomStyles where cID = ? and cvID = ?', [
            $this->cID, $this->getVersionID(),
        ]) > 0;
    }

    /**
     * Clears the custom theme styles for this page.
     */
    public function resetCustomThemeStyles()
    {
        $db = Database::connection();
        $db->executeQuery('delete from CollectionVersionThemeCustomStyles where cID = ? and cvID = ?', [$this->getCollectionID(), $this->getVersionID()]);
        $this->writePageThemeCustomizations();
    }

    /**
     * Set the custom style for this page for a specific theme.
     *
     * @param \Concrete\Core\Page\Theme\Theme $theme
     * @param \Concrete\Core\StyleCustomizer\Style\ValueList $valueList
     * @param \Concrete\Core\StyleCustomizer\Preset|null|false $selectedPreset
     * @param \Concrete\Core\Entity\StyleCustomizer\CustomCssRecord $customCssRecord
     * @param \Concrete\Core\Page\Theme\Theme $pt
     *
     * @return \Concrete\Core\Page\CustomStyle
     */
    public function setCustomStyleObject(\Concrete\Core\Page\Theme\Theme $pt, \Concrete\Core\StyleCustomizer\Style\ValueList $valueList, $selectedPreset = false, CustomCssRecord $customCssRecord = null)
    {
        $db = Database::connection();
        $db->delete('CollectionVersionThemeCustomStyles', ['cID' => $this->getCollectionID(), 'cvID' => $this->getVersionID()]);
        $preset = false;
        if ($selectedPreset) {
            $preset = $selectedPreset->getPresetHandle();
        }
        $sccRecordID = 0;
        if ($customCssRecord !== null) {
            $sccRecordID = $customCssRecord->getRecordID();
        }
        $db->insert(
            'CollectionVersionThemeCustomStyles',
            [
                'cID' => $this->getCollectionID(),
                'cvID' => $this->getVersionID(),
                'pThemeID' => $pt->getThemeID(),
                'sccRecordID' => $sccRecordID,
                'preset' => $preset,
                'scvlID' => $valueList->getValueListID(),
            ]
        );

        $scc = new \Concrete\Core\Page\CustomStyle();
        $scc->setThemeID($pt->getThemeID());
        $scc->setValueListID($valueList->getValueListID());
        $scc->setPresetHandle($preset);
        $scc->setCustomCssRecordID($sccRecordID);

        return $scc;
    }

    /**
     * Get the CSS class to be used to wrap the whole page contents.
     *
     * @return string
     */
    public function getPageWrapperClass()
    {
        $pt = $this->getPageTypeObject();

        $view = $this->getPageController()->getViewObject();
        if ($view) {
            $ptm = $view->getPageTemplate();
        } else {
            $ptm = $this->getPageTemplateObject();
        }

        $classes = ['ccm-page', 'ccm-page-id-' . $this->getCollectionID()];
        if (is_object($pt)) {
            $classes[] = 'page-type-' . str_replace('_', '-', $pt->getPageTypeHandle());
        }
        if (is_object($ptm)) {
            $classes[] = 'page-template-' . str_replace('_', '-', $ptm->getPageTemplateHandle());
        }

        return implode(' ', $classes);
    }

    /**
     * Write the page theme customization CSS files to the cache directory.
     */
    public function writePageThemeCustomizations()
    {
        $theme = $this->getCollectionThemeObject();
        if (is_object($theme) && $theme->isThemeCustomizable()) {
            $style = $this->getCustomStyleObject();
            $scl = is_object($style) ? $style->getValueList() : null;

            $theme->setStylesheetCachePath(Config::get('concrete.cache.directory') . '/pages/' . $this->getCollectionID());
            $theme->setStylesheetCacheRelativePath(REL_DIR_FILES_CACHE . '/pages/' . $this->getCollectionID());
            $sheets = $theme->getThemeCustomizableStyleSheets();
            foreach ($sheets as $sheet) {
                if (is_object($scl)) {
                    $sheet->setValueList($scl);
                    $sheet->output();
                } else {
                    $sheet->clearOutputFile();
                }
            }
        }
    }

    /**
     * Clears the custom theme styles for every page.
     */
    public static function resetAllCustomStyles()
    {
        $db = Database::connection();
        $db->delete('CollectionVersionThemeCustomStyles', ['1' => 1]);
        Core::make('app')->clearCaches();
    }

    /**
     * Update the data of this page.
     *
     * @param array $data Recognized keys are {
     *
     *     @var string $cHandle
     *     @var string $cName
     *     @var string $cDescription
     *     @var string $cDatePublic
     *     @var int $ptID
     *     @var int $pTemplateID
     *     @var int $uID
     *     @var string $$cFilename
     *     @var int $cCacheFullPageContent -1: use the default settings; 0: no; 1: yes
     *     @var int $cCacheFullPageContentLifetimeCustom
     *     @var string $cCacheFullPageContentOverrideLifetime
     * }
     */
    public function update($data)
    {
        $db = Database::connection();

        $vo = $this->getVersionObject();
        $cvID = $vo->getVersionID();
        $this->markModified();

        $cName = $this->getCollectionName();
        $cDescription = $this->getCollectionDescription();
        $cDatePublic = $this->getCollectionDatePublic();
        $uID = $this->getCollectionUserID();
        $pkgID = $this->getPackageID();
        $cFilename = $this->getCollectionFilename();
        $pTemplateID = $this->getPageTemplateID();
        $ptID = $this->getPageTypeID();
        $existingPageTemplateID = $pTemplateID;

        $cCacheFullPageContent = $this->cCacheFullPageContent;
        $cCacheFullPageContentLifetimeCustom = $this->cCacheFullPageContentLifetimeCustom;
        $cCacheFullPageContentOverrideLifetime = $this->cCacheFullPageContentOverrideLifetime;

        if (isset($data['cName'])) {
            $cName = $data['cName'];
        }
        if (isset($data['cCacheFullPageContent'])) {
            $cCacheFullPageContent = $data['cCacheFullPageContent'];
        }
        if (isset($data['cCacheFullPageContentLifetimeCustom'])) {
            $cCacheFullPageContentLifetimeCustom = (int) ($data['cCacheFullPageContentLifetimeCustom']);
        }
        if (isset($data['cCacheFullPageContentOverrideLifetime'])) {
            $cCacheFullPageContentOverrideLifetime = $data['cCacheFullPageContentOverrideLifetime'];
        }
        if (isset($data['cDescription'])) {
            $cDescription = $data['cDescription'];
        }
        if (isset($data['cDatePublic'])) {
            $cDatePublic = $data['cDatePublic'];
        }
        if (isset($data['uID'])) {
            $uID = $data['uID'];
        }
        if (isset($data['pTemplateID'])) {
            $pTemplateID = $data['pTemplateID'];
        }
        if (isset($data['ptID'])) {
            $ptID = $data['ptID'];
        }

        if (!$cDatePublic) {
            $cDatePublic = Core::make('helper/date')->getOverridableNow();
        }
        $app = Application::getFacadeApplication();
        $txt = $app->make('helper/text');
        $cName = $txt->sanitize($cName);
        $cHandle = $app->make(HandleGenerator::class)->generate($this, $data);

        if ($this->isGeneratedCollection()) {
            if (isset($data['cFilename'])) {
                $cFilename = $data['cFilename'];
            }
            // we only update a subset
            $v = [$cName, $cHandle, $cDescription, $cDatePublic, $cvID, $this->cID];
            $q = 'update CollectionVersions set cvName = ?, cvHandle = ?, cvDescription = ?, cvDatePublic = ? where cvID = ? and cID = ?';
            $r = $db->prepare($q);
            $r->execute($v);
        } else {
            if ($existingPageTemplateID && $pTemplateID && ($existingPageTemplateID != $pTemplateID) && $this->getPageTypeID() > 0 && $this->isPageDraft()) {
                // we are changing a page template in this operation.
                // when that happens, we need to get the new defaults for this page, remove the other blocks
                // on this page that were set by the old defaults master page
                $pt = $this->getPageTypeObject();
                if (is_object($pt)) {
                    $template = PageTemplate::getbyID($pTemplateID);
                    $existingPageTemplate = PageTemplate::getByID($existingPageTemplateID);

                    $oldMC = $pt->getPageTypePageTemplateDefaultPageObject($existingPageTemplate);
                    $newMC = $pt->getPageTypePageTemplateDefaultPageObject($template);

                    $currentPageBlocks = $this->getBlocks();
                    $newMCBlocks = $newMC->getBlocks();
                    $oldMCBlocks = $oldMC->getBlocks();
                    $oldMCBlockIDs = [];
                    foreach ($oldMCBlocks as $ob) {
                        $oldMCBlockIDs[] = $ob->getBlockID();
                    }

                    // now, we default all blocks on the current version of the page.
                    $db->executeQuery('delete from CollectionVersionBlocks where cID = ? and cvID = ?', [$this->getCollectionID(), $cvID]);

                    // now, we go back and we alias blocks from the new master collection onto the page.
                    foreach ($newMCBlocks as $b) {
                        $bt = $b->getBlockTypeObject();
                        if ($bt->getBlockTypeHandle() == BLOCK_HANDLE_PAGE_TYPE_OUTPUT_PROXY) {
                            continue;
                        }
                        if ($bt->isCopiedWhenPropagated()) {
                            $b->duplicate($this, true);
                        } else {
                            $b->alias($this);
                        }
                    }

                    // now, we go back and re-add the blocks we originally had on the page
                    // but only if they're not present in the oldMCBlocks array
                    foreach ($currentPageBlocks as $b) {
                        if (!in_array($b->getBlockID(), $oldMCBlockIDs)) {
                            $newBlockDisplayOrder = $this->getCollectionAreaDisplayOrder($b->getAreaHandle());
                            $db->executeQuery('insert into CollectionVersionBlocks (cID, cvID, bID, arHandle, cbDisplayOrder, isOriginal, cbOverrideAreaPermissions, cbIncludeAll) values (?, ?, ?, ?, ?, ?, ?, ?)', [
                                $this->getCollectionID(), $cvID, $b->getBlockID(), $b->getAreaHandle(), $newBlockDisplayOrder, (int) ($b->isAlias()), $b->overrideAreaPermissions(), $b->disableBlockVersioning(),
                            ]);
                        }
                    }

                    // Now, we need to change the default styles on the page, in case we are inheriting any from the
                    // defaults (for areas)
                    if ($template) {
                        $this->acquireAreaStylesFromDefaults($template);
                    }
                }
            }

            $v = [$cName, $cHandle, $pTemplateID, $cDescription, $cDatePublic, $cvID, $this->cID];
            $q = 'update CollectionVersions set cvName = ?, cvHandle = ?, pTemplateID = ?, cvDescription = ?, cvDatePublic = ? where cvID = ? and cID = ?';
            $r = $db->prepare($q);
            $r->execute($v);
        }

        // load new version object
        $this->loadVersionObject($cvID);

        $db->executeQuery('update Pages set ptID = ?, uID = ?, pkgID = ?, cFilename = ?, cCacheFullPageContent = ?, cCacheFullPageContentLifetimeCustom = ?, cCacheFullPageContentOverrideLifetime = ? where cID = ?', [$ptID, $uID, $pkgID, $cFilename, $cCacheFullPageContent, $cCacheFullPageContentLifetimeCustom, $cCacheFullPageContentOverrideLifetime, $this->cID]);

        $cache = PageCache::getLibrary();
        $cache->purge($this);

        $this->refreshCache();

        $pe = new Event($this);
        Events::dispatch('on_page_update', $pe);
    }

    /**
     * Clear all the page permissions.
     */
    public function clearPagePermissions()
    {
        $db = Database::connection();
        $db->executeQuery('delete from PagePermissionAssignments where cID = ?', [$this->cID]);
        $this->permissionAssignments = [];
    }

    /**
     * Set this page permissions to be inherited from its parent page.
     */
    public function inheritPermissionsFromParent()
    {
        $db = Database::connection();
        $cpID = $this->getParentPermissionsCollectionID();
        $this->updatePermissionsCollectionID($this->cID, $cpID);
        $v = ['PARENT', (int) $cpID, $this->cID];
        $q = 'update Pages set cInheritPermissionsFrom = ?, cInheritPermissionsFromCID = ? where cID = ?';
        $db->executeQuery($q, $v);
        $this->cInheritPermissionsFrom = 'PARENT';
        $this->cInheritPermissionsFromCID = $cpID;
        $this->clearPagePermissions();
        $this->rescanAreaPermissions();
    }

    /**
     * Set this page permissions to be inherited from its parent type defaults.
     */
    public function inheritPermissionsFromDefaults()
    {
        $db = Database::connection();
        $type = $this->getPageTypeObject();
        if (is_object($type)) {
            $master = $type->getPageTypePageTemplateDefaultPageObject();
            if (is_object($master)) {
                $cpID = $master->getCollectionID();
                $this->updatePermissionsCollectionID($this->cID, $cpID);
                $v = ['TEMPLATE', (int) $cpID, $this->cID];
                $q = 'update Pages set cInheritPermissionsFrom = ?, cInheritPermissionsFromCID = ? where cID = ?';
                $db->executeQuery($q, $v);
                $this->cInheritPermissionsFrom = 'TEMPLATE';
                $this->cInheritPermissionsFromCID = $cpID;
                $this->clearPagePermissions();
                $this->rescanAreaPermissions();
            }
        }
    }

    /**
     * Set this page permissions to be manually specified.
     */
    public function setPermissionsToManualOverride()
    {
        if ($this->cInheritPermissionsFrom != 'OVERRIDE') {
            $db = Database::connection();
            $this->acquirePagePermissions($this->getPermissionsCollectionID());
            $this->acquireAreaPermissions($this->getPermissionsCollectionID());

            $cpID = $this->cID;
            $this->updatePermissionsCollectionID($this->cID, $cpID);
            $v = ['OVERRIDE', (int) $cpID, $this->cID];
            $q = 'update Pages set cInheritPermissionsFrom = ?, cInheritPermissionsFromCID = ? where cID = ?';
            $db->executeQuery($q, $v);
            $this->cInheritPermissionsFrom = 'OVERRIDE';
            $this->cInheritPermissionsFromCID = $cpID;
            $this->rescanAreaPermissions();
        }
    }

    /**
     * Rescan the page areas ensuring that they are inheriting permissions properly.
     */
    public function rescanAreaPermissions()
    {
        $db = Database::connection();
        $r = $db->executeQuery('select arHandle, arIsGlobal from Areas where cID = ?', [$this->getCollectionID()]);
        while ($row = $r->FetchRow()) {
            $a = Area::getOrCreate($this, $row['arHandle'], $row['arIsGlobal']);
            $a->rescanAreaPermissionsChain();
        }
    }

    /**
     * Are template permissions overriden?
     *
     * @param bool|int $cOverrideTemplatePermissions
     */
    public function setOverrideTemplatePermissions($cOverrideTemplatePermissions)
    {
        $db = Database::connection();
        $v = [$cOverrideTemplatePermissions, $this->cID];
        $q = 'update Pages set cOverrideTemplatePermissions = ? where cID = ?';
        $db->executeQuery($q, $v);
        $this->cOverrideTemplatePermissions = $cOverrideTemplatePermissions;
    }

    /**
     * Set the child pages of a list of parent pages to inherit permissions from the specified page (provided that they previouly had the same inheritance page as this page).
     *
     * @param int|string $cParentIDString A comma-separeted list of parent page IDs
     * @param int $newInheritPermissionsFromCID the ID of the new page the child pages should inherit permissions from
     * @param mixed $npID
     */
    public function updatePermissionsCollectionID($cParentIDString, $npID)
    {
        // now we iterate through
        $db = Database::connection();
        $pcID = $this->getPermissionsCollectionID();
        $q = "select cID from Pages where cParentID in ({$cParentIDString}) and cInheritPermissionsFromCID = {$pcID}";
        $r = $db->query($q);
        $cList = [];
        while ($row = $r->fetchRow()) {
            $cList[] = $row['cID'];
        }
        if (count($cList) > 0) {
            $cParentIDString = implode(',', $cList);
            $q2 = "update Pages set cInheritPermissionsFromCID = {$npID} where cID in ({$cParentIDString})";
            $db->query($q2);
            $this->updatePermissionsCollectionID($cParentIDString, $npID);
        }
    }

    /**
     * Acquire the area permissions, copying them from the inherited ones.
     *
     * @param int $permissionsCollectionID the ID of the collection from which the page previously inherited permissions from
     */
    public function acquireAreaPermissions($permissionsCollectionID)
    {
        $v = [$this->cID];
        $db = Database::connection();
        $q = 'delete from AreaPermissionAssignments where cID = ?';
        $db->executeQuery($q, $v);

        // ack - we need to copy area permissions from that page as well
        $v = [$permissionsCollectionID];
        $q = 'select cID, arHandle, paID, pkID from AreaPermissionAssignments where cID = ?';
        $r = $db->executeQuery($q, $v);
        while ($row = $r->fetchRow()) {
            $v = [$this->cID, $row['arHandle'], $row['paID'], $row['pkID']];
            $q = 'insert into AreaPermissionAssignments (cID, arHandle, paID, pkID) values (?, ?, ?, ?)';
            $db->executeQuery($q, $v);
        }

        // any areas that were overriding permissions on the current page need to be overriding permissions
        // on the NEW page as well.
        $v = [$permissionsCollectionID];
        $q = 'select * from Areas where cID = ? and arOverrideCollectionPermissions';
        $r = $db->executeQuery($q, $v);
        while ($row = $r->fetchRow()) {
            $v = [$this->cID, $row['arHandle'], $row['arOverrideCollectionPermissions'], $row['arInheritPermissionsFromAreaOnCID'], $row['arIsGlobal']];
            $q = 'insert into Areas (cID, arHandle, arOverrideCollectionPermissions, arInheritPermissionsFromAreaOnCID, arIsGlobal) values (?, ?, ?, ?, ?)';
            $db->executeQuery($q, $v);
        }
    }

    /**
     * Acquire the page permissions, copying them from the inherited ones.
     *
     * @param int $permissionsCollectionID the ID of the collection from which the page previously inherited permissions from
     */
    public function acquirePagePermissions($permissionsCollectionID)
    {
        $v = [$this->cID];
        $db = Database::connection();
        $q = 'delete from PagePermissionAssignments where cID = ?';
        $db->executeQuery($q, $v);

        $v = [$permissionsCollectionID];
        $q = 'select cID, paID, pkID from PagePermissionAssignments where cID = ?';
        $r = $db->executeQuery($q, $v);
        while ($row = $r->fetchRow()) {
            $v = [$this->cID, $row['paID'], $row['pkID']];
            $q = 'insert into PagePermissionAssignments (cID, paID, pkID) values (?, ?, ?)';
            $db->executeQuery($q, $v);
        }
    }

    /**
     * @deprecated is this function still useful? There's no reference to it in the core
     *
     * @param int|string $cParentIDString
     */
    public function updateGroupsSubCollection($cParentIDString)
    {
        // now we iterate through
        $db = Database::connection();
        $this->getPermissionsCollectionID();
        $q = "select cID from Pages where cParentID in ({$cParentIDString}) and cInheritPermissionsFrom = 'PARENT'";
        $r = $db->query($q);
        $cList = [];
        while ($row = $r->fetchRow()) {
            $cList[] = $row['cID'];
        }
        if (count($cList) > 0) {
            $cParentIDString = implode(',', $cList);
            $q2 = "update Pages set cInheritPermissionsFromCID = {$this->cID} where cID in ({$cParentIDString})";
            $db->query($q2);
            $this->updateGroupsSubCollection($cParentIDString);
        }
    }

    /**
     * Add a new block to a specific area of the page.
     *
     * @param \Concrete\Core\Entity\Block\BlockType\BlockType $bt the type of block to be added
     * @param \Concrete\Core\Area\Area $a the area instance (or its handle) to which the block should be added to
     * @param array $data The data of the block. This data depends on the specific block type
     *
     * @return \Concrete\Core\Block\Block
     */
    public function addBlock($bt, $a, $data)
    {
        $b = parent::addBlock($bt, $a, $data);
        $btHandle = $bt->getBlockTypeHandle();
        if ($b->getBlockTypeHandle() == BLOCK_HANDLE_PAGE_TYPE_OUTPUT_PROXY) {
            $bi = $b->getInstance();
            $output = $bi->getComposerOutputControlObject();
            $control = FormLayoutSetControl::getByID($output->getPageTypeComposerFormLayoutSetControlID());
            $object = $control->getPageTypeComposerControlObject();
            if ($object instanceof BlockControl) {
                $_bt = $object->getBlockTypeObject();
                $btHandle = $_bt->getBlockTypeHandle();
            }
        }
        $theme = $this->getCollectionThemeObject();
        if ($btHandle && $theme) {
            $areaTemplates = [];
            $pageTypeTemplates = [];
            if (is_object($a)) {
                $areaTemplates = $a->getAreaCustomTemplates();
            }
            $themeTemplates = $theme->getThemeDefaultBlockTemplates();
            if (!is_array($themeTemplates)) {
                $themeTemplates = [];
            } else {
                foreach ($themeTemplates as $key => $template) {
                    $pt = ($this->getPageTemplateHandle()) ? $this->getPageTemplateHandle() : 'default';
                    if (is_array($template) && $key == $pt) {
                        $pageTypeTemplates = $template;
                        unset($themeTemplates[$key]);
                    }
                }
            }
            $templates = array_merge($pageTypeTemplates, $themeTemplates, $areaTemplates);
            if (count($templates) && isset($templates[$btHandle])) {
                $template = $templates[$btHandle];
                $b->updateBlockInformation(['bFilename' => $template]);
            }
        }

        return $b;
    }

    /**
     * Get the relations of this page.
     *
     * @return \Concrete\Core\Entity\Page\Relation\SiblingRelation[]
     */
    public function getPageRelations()
    {
        $em = \Database::connection()->getEntityManager();
        $r = $em->getRepository('Concrete\Core\Entity\Page\Relation\SiblingRelation');
        $relation = $r->findOneBy(['cID' => $this->getCollectionID()]);
        $relations = [];
        if (is_object($relation)) {
            $allRelations = $r->findBy(['mpRelationID' => $relation->getPageRelationID()]);
            foreach ($allRelations as $relation) {
                if ($relation->getPageID() != $this->getCollectionID() && $relation->getPageObject()->getSiteTreeObject() instanceof SiteTree) {
                    $relations[] = $relation;
                }
            }
        }

        return $relations;
    }

    /**
     * Move this page under a new parent page.
     *
     * @param \Concrete\Core\Page\Page $newParentPage
     * @param mixed $nc
     */
    public function move($nc)
    {
        $db = Database::connection();
        $newCParentID = $nc->getCollectionID();
        $dh = Core::make('helper/date');

        $cID = ($this->getCollectionPointerOriginalID() > 0) ? $this->getCollectionPointerOriginalID() : $this->cID;

        PageStatistics::decrementParents($cID);

        $cDateModified = $dh->getOverridableNow();
//      if ($this->getPermissionsCollectionID() != $this->getCollectionID() && $this->getPermissionsCollectionID() != $this->getMasterCollectionID()) {
        if ($this->getPermissionsCollectionID() != $cID) {
            // implicitly, we're set to inherit the permissions of wherever we are in the site.
            // as such, we'll change to inherit whatever permissions our new parent has
            $npID = $nc->getPermissionsCollectionID();
            if ($npID != $this->getPermissionsCollectionID()) {
                //we have to update the existing collection with the info for the new
                //as well as all collections beneath it that are set to inherit from this parent
                // first we do this one
                $q = 'update Pages set cInheritPermissionsFromCID = ? where cID = ?';
                $r = $db->executeQuery($q, [(int) $npID, $cID]);
                $this->updatePermissionsCollectionID($cID, $npID);
            }
        }

        $oldParent = self::getByID($this->getCollectionParentID(), 'RECENT');

        $db->executeQuery('update Collections set cDateModified = ? where cID = ?', [$cDateModified, $cID]);
        $v = [$newCParentID, $cID];
        $q = 'update Pages set cParentID = ? where cID = ?';
        $r = $db->prepare($q);
        $r->execute($v);

        PageStatistics::incrementParents($cID);
        if (!$this->isActive()) {
            $this->activate();
            // if we're moving from the trash, we have to activate recursively
            if ($this->isInTrash()) {
                $childPages = $this->populateRecursivePages([], ['cID' => $cID], $this->getCollectionParentID(), 0, false);
                foreach ($childPages as $page) {
                    $db->executeQuery('update Pages set cIsActive = 1 where cID = ?', [$page['cID']]);
                }
            }
        }

        if ($nc->getSiteTreeID() != $this->getSiteTreeID()) {
            $db->executeQuery('update Pages set siteTreeID = ? where cID = ?', [$nc->getSiteTreeID(), $cID]);
            if (!isset($childPages)) {
                $childPages = $this->populateRecursivePages([], ['cID' => $cID], $this->getCollectionParentID(), 0, false);
            }
            foreach ($childPages as $page) {
                $db->executeQuery('update Pages set siteTreeID = ? where cID = ?', [$nc->getSiteTreeID(), $page['cID']]);
            }
        }

        $this->siteTreeID = $nc->getSiteTreeID();
        $this->siteTree = null; // in case we need to get the updated one
        $this->cParentID = $newCParentID;
        $this->movePageDisplayOrderToBottom();
        // run any event we have for page move. Arguments are
        // 1. current page being moved
        // 2. former parent
        // 3. new parent

        $newParent = self::getByID($newCParentID, 'RECENT');

        $pe = new MovePageEvent($this);
        $pe->setOldParentPageObject($oldParent);
        $pe->setNewParentPageObject($newParent);
        Events::dispatch('on_page_move', $pe);

        $multilingual = \Core::make('multilingual/detector');
        if ($multilingual->isEnabled()) {
            Section::registerMove($this, $oldParent, $newParent);
        }

        // now that we've moved the collection, we rescan its path
        $this->rescanCollectionPath();
    }

    /**
     * Duplicate this page and all its child pages and return the new Page created.
     *
     * @param \Concrete\Core\Page\Page|null $toParentPage The page under which this page should be copied to
     * @param bool $preserveUserID Set to true to preserve the original page author IDs
     * @param \Concrete\Core\Entity\Site\Site|null $site the destination site (used if $toParentPage is NULL)
     * @param null|mixed $nc
     *
     * @return \Concrete\Core\Page\Page
     */
    public function duplicateAll($nc = null, $preserveUserID = false, Site $site = null)
    {
        $nc2 = $this->duplicate($nc, $preserveUserID, $site);
        self::_duplicateAll($this, $nc2, $preserveUserID, $site);

        return $nc2;
    }

    /**
     * Duplicate this page and return the new Page created.
     *
     * @param \Concrete\Core\Page\Page|null $toParentPage The page under which this page should be copied to
     * @param bool $preserveUserID Set to true to preserve the original page author IDs
     * @param \Concrete\Core\Site\Tree\TreeInterface|null $site the destination site (used if $toParentPage is NULL)
     * @param null|mixed $nc
     *
     * @return \Concrete\Core\Page\Page
     */
    public function duplicate($nc = null, $preserveUserID = false, TreeInterface $site = null)
    {
        $app = Application::getFacadeApplication();
        $cloner = $app->make(Cloner::class);
        $clonerOptions = $app->build(ClonerOptions::class)
            ->setKeepOriginalAuthor($preserveUserID)
        ;

        return $cloner->clonePage($this, $clonerOptions, $nc ? $nc : null, $site);
    }

    /**
     * Delete this page and all its child pages.
     *
     * @return null|false return false if it's not possible to delete this page (for instance because it's the main homepage)
     */
    public function delete()
    {
        $cID = $this->getCollectionID();

        if ($this->isAliasPage()) {
            $this->removeThisAlias();

            return;
        }

        if ($cID < 1 || $cID == static::getHomePageID()) {
            return false;
        }

        $db = Database::connection();

        // run any internal event we have for page deletion
        $pe = new DeletePageEvent($this);
        Events::dispatch('on_page_delete', $pe);

        if (!$pe->proceed()) {
            return false;
        }

        $app = Facade::getFacadeApplication();
        $logger = $app->make('log/factory')->createLogger(Channels::CHANNEL_SITE_ORGANIZATION);
        $logger->notice(t('Page "%s" at path "%s" deleted',
            $this->getCollectionName(),
            $this->getCollectionPath()
        ));

        parent::delete();

        $cID = $this->getCollectionID();

        // Now that all versions are gone, we can delete the collection information
        $q = "delete from PagePaths where cID = '{$cID}'";
        $r = $db->query($q);

        // remove all pages where the pointer is this cID
        $r = $db->executeQuery('select cID from Pages where cPointerID = ?', [$cID]);
        while ($row = $r->fetchRow()) {
            PageStatistics::decrementParents($row['cID']);
            $db->executeQuery('DELETE FROM PagePaths WHERE cID=?', [$row['cID']]);
        }

        // Update cChildren for cParentID
        PageStatistics::decrementParents($cID);

        $db->executeQuery('delete from PagePermissionAssignments where cID = ?', [$cID]);

        $db->executeQuery('delete from Pages where cID = ?', [$cID]);

        $db->executeQuery('delete from MultilingualPageRelations where cID = ?', [$cID]);

        $db->executeQuery('delete from SiblingPageRelations where cID = ?', [$cID]);

        $db->executeQuery('delete from Pages where cPointerID = ?', [$cID]);

        $db->executeQuery('delete from Areas WHERE cID = ?', [$cID]);

        $db->executeQuery('delete from PageSearchIndex where cID = ?', [$cID]);

        $r = $db->executeQuery('select cID from Pages where cParentID = ?', [$cID]);
        if ($r) {
            while ($row = $r->fetchRow()) {
                if ($row['cID'] > 0) {
                    $nc = self::getByID($row['cID']);
                    $nc->delete();
                }
            }
        }

        if (\Core::make('multilingual/detector')->isEnabled()) {
            Section::unregisterPage($this);
        }

        $cache = PageCache::getLibrary();
        $cache->purge($this);
    }

    /**
     * Move this page and all its child pages to the trash.
     */
    public function moveToTrash()
    {

        // run any internal event we have for page trashing
        $pe = new Event($this);
        Events::dispatch('on_page_move_to_trash', $pe);

        $trash = self::getByPath(Config::get('concrete.paths.trash'));
        $app = Facade::getFacadeApplication();
        $logger = $app->make('log/factory')->createLogger(Channels::CHANNEL_SITE_ORGANIZATION);
        $logger->notice(t('Page "%s" at path "%s" Moved to trash',
            $this->getCollectionName(),
            $this->getCollectionPath()
        ));

        $this->move($trash);
        $this->deactivate();

        // if this page has a custom canonical path we need to clear it
        $path = $this->getCollectionPathObject();
        if (!$path->isPagePathAutoGenerated()) {
            $path = $this->getAutoGeneratedPagePathObject();
            $this->setCanonicalPagePath($path->getPagePath(), true);
            $this->rescanCollectionPath();
        }
        $cID = ($this->getCollectionPointerOriginalID() > 0) ? $this->getCollectionPointerOriginalID() : $this->cID;
        $pages = [];
        $pages = $this->populateRecursivePages($pages, ['cID' => $cID], $this->getCollectionParentID(), 0, false);
        $db = Database::connection();
        foreach ($pages as $page) {
            $db->executeQuery('update Pages set cIsActive = 0 where cID = ?', [$page['cID']]);
        }
    }

    /**
     * Regenerate the display order of the child pages.
     */
    public function rescanChildrenDisplayOrder()
    {
        $db = Database::connection();
        // this should be re-run every time a new page is added, but i don't think it is yet - AE
        //$oneLevelOnly=1;
        //$children_array = $this->getCollectionChildrenArray( $oneLevelOnly );
        $q = 'SELECT cID FROM Pages WHERE cParentID = ? ORDER BY cDisplayOrder';
        $children_array = $db->getCol($q, [$this->getCollectionID()]);
        $current_count = 0;
        foreach ($children_array as $newcID) {
            $q = 'update Pages set cDisplayOrder = ? where cID = ?';
            $db->executeQuery($q, [$current_count, $newcID]);
            ++$current_count;
        }
    }

    /**
     * Is this the homepage for the site tree this page belongs to?
     *
     * @return bool
     */
    public function isHomePage()
    {
        return $this->getSiteHomePageID() == $this->getCollectionID();
    }

    /**
     * Get the ID of the homepage for the site tree this page belongs to.
     *
     * @return int|null Returns NULL if there's no default locale
     */
    public function getSiteHomePageID()
    {
        return static::getHomePageID($this);
    }

    /**
     * @deprecated use the isHomePage() method
     *
     * @return bool
     */
    public function isLocaleHomePage()
    {
        return $this->getCollectionID() > 0 && $this->getSiteHomePageID() == $this->getCollectionID();
    }

    /**
     * Get the ID of the home page.
     *
     * @param Page|int $page the page (or its ID) for which you want the home (if not specified, we'll use the default locale site tree)
     *
     * @return int|null returns NULL if $page is null (or it doesn't have a SiteTree associated) and if there's no default locale
     */
    public static function getHomePageID($page = null)
    {
        if ($page) {
            if (!$page instanceof self) {
                $page = self::getByID($page);
            }
            if ($page instanceof self) {
                $siteTree = $page->getSiteTreeObject();
                if ($siteTree !== null) {
                    return $siteTree->getSiteHomePageID();
                }
            }
        }
        $locale = Application::getFacadeApplication()->make(LocaleService::class)->getDefaultLocale();
        if ($locale !== null) {
            $siteTree = $locale->getSiteTreeObject();
            if ($siteTree != null) {
                return $siteTree->getSiteHomePageID();
            }
        }

        $entityManager = Application::getFacadeApplication()->make(EntityManagerInterface::class);

        try {
            $site = $entityManager->getRepository('Concrete\Core\Entity\Site\Site')
                ->findOneBy(['siteIsDefault' => true]);
            if ($site !== null) {
                return $site->getSiteHomePageID();
            }
        } catch (\Exception $e) {
            return null;
        }


        return null;
    }

    /**
     * Get a new PagePath object with the computed canonical page path.
     *
     * @return \Concrete\Core\Entity\Page\PagePath
     */
    public function getAutoGeneratedPagePathObject()
    {
        $path = new PagePath();
        $path->setPagePathIsAutoGenerated(true);
        //if (!$this->isHomePage()) {
        $path->setPagePath($this->computeCanonicalPagePath());
        //}

        return $path;
    }

    /**
     * Get the next available display order of child pages.
     *
     * @return int
     */
    public function getNextSubPageDisplayOrder()
    {
        $db = Database::connection();
        $max = $db->fetchColumn('select max(cDisplayOrder) from Pages where cParentID = ?', [$this->getCollectionID()]);

        return is_numeric($max) ? ($max + 1) : 0;
    }

    /**
     * Get the URL-slug-based path to the current page (including any suffixes) in a string format. Does so in real time.
     *
     * @return string
     */
    public function generatePagePath()
    {
        $newPath = '';
        //if ($this->cParentID > 0) {
        /**
         * @var Connection
         */
        $db = \Database::connection();
        /* @var $em \Doctrine\ORM\EntityManager */
        $pathObject = $this->getCollectionPathObject();
        if (is_object($pathObject) && !$pathObject->isPagePathAutoGenerated()) {
            $pathString = $pathObject->getPagePath();
        } else {
            $pathString = $this->computeCanonicalPagePath();
        }
        if (!$pathString) {
            return ''; // We are allowed to pass in a blank path in the event of the home page being scanned.
        }
        // ensure that the path is unique
        $suffix = 0;
        $cID = ($this->getCollectionPointerOriginalID() > 0) ? $this->getCollectionPointerOriginalID() : $this->cID;
        $pagePathSeparator = Config::get('concrete.seo.page_path_separator');
        while (true) {
            $newPath = ($suffix === 0) ? $pathString : $pathString . $pagePathSeparator . $suffix;
            $result = $db->fetchColumn('select p.cID from PagePaths pp inner join Pages p on pp.cID = p.cID where pp.cPath = ? and pp.cID <> ? and p.siteTreeID = ?',
                    [
                        $newPath,
                        $cID,
                        $this->getSiteTreeID(),
                    ]
                );
            if (empty($result)) {
                break;
            }
            ++$suffix;
        }
        //}

        return $newPath;
    }

    /**
     * Recalculate the canonical page path for the current page and its sub-pages, based on its current version, URL slug, etc.
     */
    public function rescanCollectionPath()
    {
        //if ($this->cParentID > 0) {
        $newPath = $this->generatePagePath();

        $pathObject = $this->getCollectionPathObject();
        $ppIsAutoGenerated = true;
        if (is_object($pathObject) && !$pathObject->isPagePathAutoGenerated()) {
            $ppIsAutoGenerated = false;
        }
        $this->setCanonicalPagePath($newPath, $ppIsAutoGenerated);
        $this->rescanSystemPageStatus();
        $this->cPath = $newPath;
        $this->refreshCache();

        $children = $this->getCollectionChildren();
        if (count($children) > 0) {
            $myCollectionID = $this->getCollectionID();
            foreach ($children as $child) {
                // Let's avoid recursion caused by potentially malformed data
                if ($child->getCollectionID() !== $myCollectionID) {
                    $child->rescanCollectionPath();
                }
            }
        }
        //}
    }

    /**
     * Set a new display order for this page (or for another page given its ID).
     *
     * @param int $displayOrder
     * @param int|null $cID The page ID to set the display order for (if empty, we'll use this page)
     */
    public function updateDisplayOrder($displayOrder, $cID = 0)
    {
        $displayOrder = (int) $displayOrder;

        //this line was added to allow changing the display order of aliases
        if (!(int) $cID) {
            $cID = ($this->getCollectionPointerOriginalID() > 0) ? $this->getCollectionPointerOriginalID() : $this->cID;
        }

        $app = Application::getFacadeApplication();
        $db = $app->make(Connection::class);

        $oldDisplayOrder = $db->fetchColumn('SELECT cDisplayOrder FROM Pages WHERE cID = ?', [$cID]);

        // Exit out if the display order for this page doesn't change.
        if ($oldDisplayOrder === null || $displayOrder === (int) $oldDisplayOrder) {
            return;
        }

        // Store the new display order.
        $db->executeQuery('update Pages set cDisplayOrder = ? where cID = ?', [$displayOrder, $cID]);

        // Because the display order of another page can be changed,
        // the page object is retrieved first in order to pass it to the event.
        $page = $this;
        if ($cID && (int) $cID !== (int) $this->getCollectionID()) {
            $page = static::getByID($cID);
        }

        if ($page->isError()) {
            return;
        }

        // Fire an event that the page display order has changed.
        $event = new DisplayOrderUpdateEvent($page);
        $event->setOldDisplayOrder($oldDisplayOrder);
        $event->setNewDisplayOrder($displayOrder);
        Events::dispatch('on_page_display_order_update', $event);
    }

    /**
     * Make this page the first child of its parent.
     */
    public function movePageDisplayOrderToTop()
    {
        // first, we take the current collection, stick it at the beginning of an array, then get all other items from the current level that aren't that cID, order by display order, and then update
        $db = Database::connection();
        $nodes = [];
        $nodes[] = $this->getCollectionID();
        $r = $db->GetCol('select cID from Pages where cParentID = ? and cID <> ? order by cDisplayOrder asc', [$this->getCollectionParentID(), $this->getCollectionID()]);
        $nodes = array_merge($nodes, $r);
        $displayOrder = 0;
        foreach ($nodes as $do) {
            $co = self::getByID($do);
            $co->updateDisplayOrder($displayOrder);
            ++$displayOrder;
        }
    }

    /**
     * Make this page the first child of its parent.
     */
    public function movePageDisplayOrderToBottom()
    {
        // find the highest cDisplayOrder and increment by 1
        $db = Database::connection();
        $mx = $db->fetchAssoc('select max(cDisplayOrder) as m from Pages where cParentID = ?', [$this->getCollectionParentID()]);
        $max = $mx ? $mx['m'] : 0;
        ++$max;
        $this->updateDisplayOrder($max);
    }

    /**
     * Move this page before of after another page.
     *
     * @param \Concrete\Core\Page\Page $referencePage The reference page
     * @param string $position 'before' or 'after'
     * @param Page $c
     */
    public function movePageDisplayOrderToSibling(Page $c, $position = 'before')
    {
        $myCID = $this->getCollectionPointerOriginalID() ?: $this->getCollectionID();
        $relatedCID = $c->getCollectionPointerOriginalID() ?: $c->getCollectionID();
        $pageIDs = [];
        $db = Database::connection();
        $r = $db->executeQuery('select cID from Pages where cParentID = ? and cID <> ? order by cDisplayOrder asc', [$this->getCollectionParentID(), $myCID]);
        while (($cID = $r->fetchColumn()) !== false) {
            if ($cID == $relatedCID && $position == 'before') {
                $pageIDs[] = $myCID;
            }
            $pageIDs[] = $cID;
            if ($cID == $relatedCID && $position == 'after') {
                $pageIDs[] = $myCID;
            }
        }
        $displayOrder = 0;
        foreach ($pageIDs as $cID) {
            $co = self::getByID($cID);
            $co->updateDisplayOrder($displayOrder);
            ++$displayOrder;
        }
    }

    /**
     * Recalculate the "is a system page" state.
     * Looks at the current page. If the site tree ID is 0, sets system page to true.
     * If the site tree is not user, looks at where the page falls in the hierarchy. If it's inside a page
     * at the top level that has 0 as its parent, then it is considered a system page.
     */
    public function rescanSystemPageStatus()
    {
        $systemPage = false;
        $db = Database::connection();
        $cID = $this->getCollectionID();
        if (!$this->isHomePage()) {
            if ($this->getSiteTreeID() == 0) {
                $systemPage = true;
            } else {
                $cID = ($this->getCollectionPointerOriginalID() > 0) ? $this->getCollectionPointerOriginalID() : $this->getCollectionID();
                $db = Database::connection();
                $path = $db->fetchColumn('select cPath from PagePaths where cID = ? and ppIsCanonical = 1', [$cID]);
                if ($path) {
                    // Grab the top level parent
                    $fragments = explode('/', $path);
                    $topPath = '/' . $fragments[1];
                    $c = \Page::getByPath($topPath);
                    if (is_object($c) && !$c->isError()) {
                        if ($c->getCollectionParentID() == 0 && !$c->isHomePage()) {
                            $systemPage = true;
                        }
                    }
                }
            }
        }

        if ($systemPage) {
            $db->executeQuery('update Pages set cIsSystemPage = 1 where cID = ?', [$cID]);
            $this->cIsSystemPage = true;
        } else {
            $db->executeQuery('update Pages set cIsSystemPage = 0 where cID = ?', [$cID]);
            $this->cIsSystemPage = false;
        }
    }

    /**
     * Is this page in the trash?
     *
     * @return bool
     */
    public function isInTrash()
    {
        return $this->getCollectionPath() != Config::get('concrete.paths.trash') && strpos($this->getCollectionPath(), Config::get('concrete.paths.trash')) === 0;
    }

    /**
     * Make this page child of nothing, thus moving it to the root level.
     */
    public function moveToRoot()
    {
        $db = Database::connection();
        $db->executeQuery('update Pages set cParentID = 0 where cID = ?', [$this->getCollectionID()]);
        $this->cParentID = 0;
        $this->rescanSystemPageStatus();
    }

    /**
     * Mark this page as non active.
     */
    public function deactivate()
    {
        $db = Database::connection();
        $db->executeQuery('update Pages set cIsActive = 0 where cID = ?', [$this->getCollectionID()]);
    }

    /**
     * Mark this page as non draft.
     */
    public function setPageToDraft()
    {
        $db = Database::connection();
        $db->executeQuery('update Pages set cIsDraft = 1 where cID = ?', [$this->getCollectionID()]);
        $this->cIsDraft = true;
    }

    /**
     * Mark this page as active.
     */
    public function activate()
    {
        $db = Database::connection();
        $db->executeQuery('update Pages set cIsActive = 1 where cID = ?', [$this->getCollectionID()]);
    }

    /**
     * Is this page marked as active?
     *
     * @return bool
     */
    public function isActive()
    {
        return (bool) $this->cIsActive;
    }

    /**
     * Set the page index score (used by a PageList for instance).
     *
     * @param float $score
     */
    public function setPageIndexScore($score)
    {
        $this->cIndexScore = $score;
    }

    /**
     * Get the page index score (as set by a PageList for instance).
     *
     * @return float
     */
    public function getPageIndexScore()
    {
        return round($this->cIndexScore, 2);
    }

    /**
     * Get the indexed content of this page.
     *
     * @return string
     */
    public function getPageIndexContent()
    {
        $db = Database::connection();

        return $db->fetchColumn('select content from PageSearchIndex where cID = ?', [$this->cID]);
    }

    /**
     * Add the home page to the system. Typically used only by the installation program.
     *
     * @param \Concrete\Core\Site\Tree\TreeInterface|null $siteTree
     *
     * @return \Concrete\Core\Page\Page
     **/
    public static function addHomePage(TreeInterface $siteTree = null)
    {
        $app = Application::getFacadeApplication();
        // creates the home page of the site
        $db = $app->make(Connection::class);

        $cParentID = 0;
        $uID = HOME_UID;

        $data = [
            'name' => HOME_NAME,
            'uID' => $uID,
        ];
        $cobj = parent::createCollection($data);
        $cID = $cobj->getCollectionID();

        if (!is_object($siteTree)) {
            $site = \Core::make('site')->getSite();
            $siteTree = $site->getSiteTreeObject();
        }
        $siteTreeID = $siteTree->getSiteTreeID();

        $v = [$cID, $siteTreeID, $cParentID, $uID, 'OVERRIDE', 1, (int) $cID, 0];
        $q = 'insert into Pages (cID, siteTreeID, cParentID, uID, cInheritPermissionsFrom, cOverrideTemplatePermissions, cInheritPermissionsFromCID, cDisplayOrder) values (?, ?, ?, ?, ?, ?, ?, ?)';
        $r = $db->prepare($q);
        $r->execute($v);
        if (!$siteTree->getSiteHomePageID()) {
            $siteTree->setSiteHomePageID($cID);
            $em = $app->make(EntityManagerInterface::class);
            $em->flush($siteTree);
        }
        $pc = self::getByID($cID, 'RECENT');

        return $pc;
    }

    /**
     * Add a new page, child of this page.
     *
     * @param \Concrete\Core\Page\Type\Type|null $pageType
     * @param array $data Supported keys: {
     *
     *     @var int|null $uID The ID of the page author (if unspecified or NULL: current user)
     *     @var int|null $pkgID the ID of the package that creates this page
     *     @var string $cName The page name
     *     @var string $name (used if cName is not specified)
     *     @var int|null $cID The ID of the page to create (if unspecified or NULL: database autoincrement value)
     *     @var int|bool $cIsActive Is the page to be considered as active?
     *     @var int|bool $cIsDraft Is the page to be considered as draft?
     *     @var string $cHandle The page handle
     *     @var string $cDescription The page description (default: NULL)
     *     @var string $cDatePublic The page publish date/time in format 'YYYY-MM-DD hh:mm:ss' (default: now)
     *     @var bool $cvIsApproved Is the page version approved (default: true)
     *     @var bool $cvIsNew Is the page to be considered "new"? (default: true if $cvIsApproved is false, false if $cvIsApproved is true)
     *     @var bool $cAcquireComposerOutputControls
     * }
     *
     * @param \Concrete\Core\Entity\Page\Template|null $pageTemplate
     * @param mixed $pt
     * @param mixed $template
     *
     * @return \Concrete\Core\Page\Page
     **/
    public function add($pt, $data, $template = false)
    {
        $data += [
            'cHandle' => null,
        ];
        $app = Application::getFacadeApplication();
        $db = Database::connection();
        $txt = Core::make('helper/text');

        // the passed collection is the parent collection
        $cParentID = $this->getCollectionID();

        $u = $app->make(User::class);
        if (isset($data['uID'])) {
            $uID = $data['uID'];
        } else {
            $uID = $u->getUserID();
            $data['uID'] = $uID;
        }

        if (isset($data['pkgID'])) {
            $pkgID = $data['pkgID'];
        } else {
            $pkgID = 0;
        }

        $cIsActive = 1;
        if (isset($data['cIsActive']) && !$data['cIsActive']) {
            $cIsActive = 0;
        }

        $cIsDraft = 0;
        if (isset($data['cIsDraft']) && $data['cIsDraft']) {
            $cIsDraft = 1;
        }

        if (isset($data['cName'])) {
            $data['name'] = $data['cName'];
        } elseif (!isset($data['name'])) {
            $data['name'] = '';
        }

        if (!$data['cHandle']) {
            // make the handle out of the title
            $handle = $txt->urlify($data['name']);
        } else {
            $handle = $txt->slugSafeString($data['cHandle']); // we take it as it comes.
        }

        $handle = str_replace('-', Config::get('concrete.seo.page_path_separator'), $handle);
        $data['handle'] = $handle;

        $ptID = 0;
        $masterCIDBlocks = null;
        $masterCID = null;
        if ($pt instanceof \Concrete\Core\Page\Type\Type) {
            if ($pt->getPageTypeHandle() == STACKS_PAGE_TYPE) {
                $data['cvIsNew'] = 0;
            }
            if ($pt->getPackageID() > 0) {
                $pkgID = $pt->getPackageID();
            }

            // if we have a page type and we don't have a template,
            // then we use the page type's default template
            if ($pt->getPageTypeDefaultPageTemplateID() > 0 && !$template) {
                $template = $pt->getPageTypeDefaultPageTemplateObject();
            }

            $ptID = $pt->getPageTypeID();
            if ($template) {
                $mc1 = $pt->getPageTypePageTemplateDefaultPageObject($template);
                $mc2 = $pt->getPageTypePageTemplateDefaultPageObject();
                $masterCIDBlocks = $mc1->getCollectionID();
                $masterCID = $mc2->getCollectionID();
            }
        }

        if ($template instanceof TemplateEntity) {
            $data['pTemplateID'] = $template->getPageTemplateID();
        }

        $cobj = parent::addCollection($data);
        $cID = $cobj->getCollectionID();

        //$this->rescanChildrenDisplayOrder();
        $cDisplayOrder = $this->getNextSubPageDisplayOrder();

        $siteTreeID = $this->getSiteTreeID();

        $cInheritPermissionsFromCID = ($this->overrideTemplatePermissions()) ? $this->getPermissionsCollectionID() : $masterCID;
        $cInheritPermissionsFrom = ($this->overrideTemplatePermissions()) ? 'PARENT' : 'TEMPLATE';
        $v = [$cID, $siteTreeID, $ptID, $cParentID, $uID, $cInheritPermissionsFrom, $this->overrideTemplatePermissions(), (int) $cInheritPermissionsFromCID, $cDisplayOrder, $pkgID, $cIsActive, $cIsDraft];
        $q = 'insert into Pages (cID, siteTreeID, ptID, cParentID, uID, cInheritPermissionsFrom, cOverrideTemplatePermissions, cInheritPermissionsFromCID, cDisplayOrder, pkgID, cIsActive, cIsDraft) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
        $r = $db->prepare($q);
        $res = $r->execute($v);

        $newCID = $cID;

        if ($res) {
            // Collection added with no problem -- update cChildren on parrent
            PageStatistics::incrementParents($newCID);

            if ($r) {
                $cAcquireComposerOutputControls = false;
                if (isset($data['cAcquireComposerOutputControls']) && $data['cAcquireComposerOutputControls']) {
                    $cAcquireComposerOutputControls = true;
                }
                // now that we know the insert operation was a success, we need to see if the collection type we're adding has a master collection associated with it
                if ($masterCIDBlocks) {
                    $this->_associateMasterCollectionBlocks($newCID, $masterCIDBlocks, $cAcquireComposerOutputControls);
                }
                if ($masterCID) {
                    $this->_associateMasterCollectionAttributes($newCID, $masterCID);
                }
            }

            $pc = self::getByID($newCID, 'RECENT');
            // if we are in the drafts area of the site, then we don't check multilingual status. Otherwise
            // we do
            if ($this->getCollectionPath() != Config::get('concrete.paths.drafts')) {
                Section::registerPage($pc);
            }

            if ($template) {
                $pc->acquireAreaStylesFromDefaults($template);
            }

            // run any internal event we have for page addition
            $pe = new Event($pc);
            Events::dispatch('on_page_add', $pe);

            $pc->rescanCollectionPath();
        }

        $entities = $u->getUserAccessEntityObjects();
        $hasAuthor = false;
        foreach ($entities as $obj) {
            if ($obj instanceof PageOwnerEntity) {
                $hasAuthor = true;
            }
        }
        if (!$hasAuthor) {
            $u->refreshUserGroups();
        }

        return $pc;
    }

    /**
     * Get the custom style for the currently loaded page version (if any).
     *
     * @return \Concrete\Core\Page\CustomStyle|null
     */
    public function getCustomStyleObject()
    {
        $db = Database::connection();
        $row = $db->FetchAssoc('select * from CollectionVersionThemeCustomStyles where cID = ? and cvID = ?', [$this->getCollectionID(), $this->getVersionID()]);
        if (isset($row['cID'])) {
            $o = new \Concrete\Core\Page\CustomStyle();
            $o->setThemeID($row['pThemeID']);
            $o->setValueListID($row['scvlID']);
            $o->setPresetHandle($row['preset']);
            $o->setCustomCssRecordID($row['sccRecordID']);

            return $o;
        }
    }

    /**
     * Get the full-page cache flag (-1: use global setting; 0: no; 1: yes - NULL if page is not loaded).
     *
     * @return int|null
     */
    public function getCollectionFullPageCaching()
    {
        return $this->cCacheFullPageContent;
    }

    /**
     * Get the full-page cache lifetime criteria ('default': use default lifetime; 'forever': no expiration; 'custom': custom lifetime value - see getCollectionFullPageCachingLifetimeCustomValue(); other: use the default lifetime - NULL if page is not loaded).
     *
     * @return string|null
     */
    public function getCollectionFullPageCachingLifetime()
    {
        return $this->cCacheFullPageContentOverrideLifetime;
    }

    /**
     * Get the full-page cache custom lifetime in minutes (to be used if getCollectionFullPageCachingLifetime() is 'custom').
     *
     * @return int|null returns NULL if page is not loaded
     */
    public function getCollectionFullPageCachingLifetimeCustomValue()
    {
        return $this->cCacheFullPageContentLifetimeCustom;
    }

    /**
     * Get the actual full-page cache lifespan (in seconds).
     *
     * @return int
     */
    public function getCollectionFullPageCachingLifetimeValue()
    {
        if ($this->cCacheFullPageContentOverrideLifetime == 'default') {
            $lifetime = Config::get('concrete.cache.lifetime');
        } elseif ($this->cCacheFullPageContentOverrideLifetime == 'custom') {
            $lifetime = $this->cCacheFullPageContentLifetimeCustom * 60;
        } elseif ($this->cCacheFullPageContentOverrideLifetime == 'forever') {
            $lifetime = 31536000; // 1 year
        } else {
            if (Config::get('concrete.cache.full_page_lifetime') == 'custom') {
                $lifetime = Config::get('concrete.cache.full_page_lifetime_value') * 60;
            } elseif (Config::get('concrete.cache.full_page_lifetime') == 'forever') {
                $lifetime = 31536000; // 1 year
            } else {
                $lifetime = Config::get('concrete.cache.lifetime');
            }
        }

        if (!$lifetime) {
            // we have no value, which means forever, but we need a numerical value for page caching
            $lifetime = 31536000;
        }

        return $lifetime;
    }

    /**
     * Create a new page.
     *
     * @param array $data The data to be used to create the page. See Collection::createCollection() for the supported keys, plus 'pkgID' and 'filename'.
     * @param \Concrete\Core\Site\Tree\TreeInterface|null $parent the parent page (or the site) that will contain the new page
     *
     * @return \Concrete\Core\Page\Page
     *
     * @see \Concrete\Core\Page\Collection\Collection::createCollection()
     */
    public static function addStatic($data, TreeInterface $parent = null)
    {
        $db = Database::connection();
        if ($parent instanceof self) {
            $cParentID = $parent->getCollectionID();
            $parent->rescanChildrenDisplayOrder();
            $cDisplayOrder = $parent->getNextSubPageDisplayOrder();
            $cInheritPermissionsFromCID = $parent->getPermissionsCollectionID();
            $cOverrideTemplatePermissions = $parent->overrideTemplatePermissions();
        } else {
            $cParentID = static::getHomePageID();
            $cDisplayOrder = 0;
            $cInheritPermissionsFromCID = $cParentID;
            $cOverrideTemplatePermissions = 1;
        }

        if (isset($data['pkgID'])) {
            $pkgID = $data['pkgID'];
        } else {
            $pkgID = 0;
        }

        $cFilename = $data['filename'];

        $uID = USER_SUPER_ID;
        $data['uID'] = $uID;
        $cobj = parent::createCollection($data);
        $cID = $cobj->getCollectionID();

        // These get set to parent by default here, but they can be overridden later
        $cInheritPermissionsFrom = 'PARENT';

        $siteTreeID = 0;
        if (is_object($parent)) {
            $siteTreeID = $parent->getSiteTreeID();
        }

        $v = [$cID, $siteTreeID, $cFilename, $cParentID, $cInheritPermissionsFrom, $cOverrideTemplatePermissions, (int) $cInheritPermissionsFromCID, $cDisplayOrder, $uID, $pkgID];
        $q = 'insert into Pages (cID, siteTreeID, cFilename, cParentID, cInheritPermissionsFrom, cOverrideTemplatePermissions, cInheritPermissionsFromCID, cDisplayOrder, uID, pkgID) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
        $r = $db->prepare($q);
        $res = $r->execute($v);

        if ($res) {
            // Collection added with no problem -- update cChildren on parrent
            PageStatistics::incrementParents($cID);
        }

        $pc = self::getByID($cID);
        $pc->rescanCollectionPath();

        return $pc;
    }

    /**
     * Get the currently requested page.
     *
     * @return \Concrete\Core\Page\Page|null
     */
    public static function getCurrentPage()
    {
        $req = Request::getInstance();
        $current = $req->getCurrentPage();

        return $current;
    }

    /**
     * Get the ID of the draft parent page ID.
     *
     * @return int
     */
    public function getPageDraftTargetParentPageID()
    {
        $db = Database::connection();

        return $db->fetchColumn('select cDraftTargetParentPageID from Pages where cID = ?', [$this->cID]);
    }

    /**
     * Set the ID of the draft parent page ID.
     *
     * @param int $cParentID
     */
    public function setPageDraftTargetParentPageID($cParentID)
    {
        if ($cParentID != $this->getPageDraftTargetParentPageID()) {
            Section::unregisterPage($this);
        }
        $db = Database::connection();
        $cParentID = (int) $cParentID;
        $db->executeQuery('update Pages set cDraftTargetParentPageID = ? where cID = ?', [$cParentID, $this->cID]);
        $this->cDraftTargetParentPageID = $cParentID;

        Section::registerPage($this);
    }

    /**
     * Read the data from the database.
     *
     * @param mixed $cInfo The argument of the $where condition
     * @param string $where The SQL 'WHERE' part
     * @param string|int $cvID
     */
    protected function populatePage($cInfo, $where, $cvID)
    {
        $app = Facade::getFacadeApplication();
        /** @var RequestCache $cache */
        $cache = $app->make('cache/request');
        $key = '/page/row/' . $cInfo;
        $item = $cache->getItem($key);
        $row = false;
        $originalRow = null;

        if ($item->isHit()) {
            $row = $item->get();
        } elseif ($item->isMiss()) {
            $db = $app->make(Connection::class);

            $fields = 'Pages.cID, Pages.pkgID, Pages.siteTreeID, Pages.cPointerID, Pages.cPointerExternalLink, Pages.cIsDraft, Pages.cIsActive, Pages.cIsSystemPage, Pages.cPointerExternalLinkNewWindow, Pages.cFilename, Pages.ptID, Collections.cDateAdded, Pages.cDisplayOrder, Collections.cDateModified, cInheritPermissionsFromCID, cInheritPermissionsFrom, cOverrideTemplatePermissions, cCheckedOutUID, cIsTemplate, uID, cPath, cParentID, cChildren, cCacheFullPageContent, cCacheFullPageContentOverrideLifetime, cCacheFullPageContentLifetimeCustom, Collections.cHandle';
            $from = 'Pages INNER JOIN Collections ON Pages.cID = Collections.cID LEFT JOIN PagePaths ON (Pages.cID = PagePaths.cID AND PagePaths.ppIsCanonical = 1)';

            $row = $db->fetchAssoc("SELECT {$fields} FROM {$from} {$where}", [$cInfo]);
            if ($row !== false && $row['cPointerID'] > 0) {
                $originalRow = $row;
                $row = $db->fetchAssoc("SELECT {$fields}, CollectionVersions.cvName FROM {$from} LEFT JOIN CollectionVersions ON CollectionVersions.cID = ? WHERE Pages.cID = ? LIMIT 1", [$row['cID'], $row['cPointerID']]);
            }

            $item->set($row);
            $cache->save($item);
        }

        if ($row !== false) {
            if ($originalRow !== null) {
                $this->customAliasName = (string) $row['cvName'];
                unset($row['cvName']);
            }
            foreach ($row as $key => $value) {
                $this->{$key} = $value;
            }
            if ($originalRow !== null) {
                $this->cPointerID = $originalRow['cPointerID'];
                $this->cIsActive = $originalRow['cIsActive'];
                $this->cPointerOriginalID = $originalRow['cID'];
                $this->cPointerOriginalSiteTreeID = $originalRow['siteTreeID'];
                $this->cPath = $originalRow['cPath'];
                $this->cParentID = $originalRow['cParentID'];
                $this->cDisplayOrder = $originalRow['cDisplayOrder'];
                $this->aliasHandle = (string) $originalRow['cHandle'];
            }
            $this->isMasterCollection = $row['cIsTemplate'];
            $this->loadError(false);
            if ($cvID != false) {
                $this->loadVersionObject($cvID);
            }
        } else {
            // there was no record of this particular collection in the database
            $this->loadError(COLLECTION_NOT_FOUND);
        }
    }

    /**
     * Populate the childrenCIDArray property (called by the getCollectionChildrenArray() method).
     *
     * @param int $cID
     * @param bool $oneLevelOnly
     * @param string $sortColumn
     */
    protected function _getNumChildren($cID, $oneLevelOnly = 0, $sortColumn = 'cDisplayOrder asc')
    {
        $db = Database::connection();
        $q = "select cID from Pages where cParentID = {$cID} and cIsTemplate = 0 order by {$sortColumn}";
        $r = $db->query($q);
        if ($r) {
            while ($row = $r->fetchRow()) {
                if ($row['cID'] > 0) {
                    $this->childrenCIDArray[] = $row['cID'];
                    if (!$oneLevelOnly) {
                        $this->_getNumChildren($row['cID']);
                    }
                }
            }
        }
    }

    /**
     * Duplicate all the child pages of a specific page which has already have been duplicated.
     *
     * @param \Concrete\Core\Page\Page $originalParentPage The original parent page
     * @param \Concrete\Core\Page\Page $newParentPage The duplicated parent page
     * @param bool $preserveUserID Set to true to preserve the original page author IDs
     * @param \Concrete\Core\Entity\Site\Site|null $site the destination site
     * @param mixed $cParent
     * @param mixed $cNewParent
     */
    protected function _duplicateAll($cParent, $cNewParent, $preserveUserID = false, Site $site = null)
    {
        $db = Database::connection();
        $cID = $cParent->getCollectionID();
        $q = 'select cID, ptHandle from Pages p left join PageTypes pt on p.ptID = pt.ptID where cParentID = ? order by cDisplayOrder asc';
        $r = $db->executeQuery($q, [$cID]);
        if ($r) {
            while ($row = $r->fetchRow()) {
                // This is a terrible hack.
                if ($row['ptHandle'] === STACKS_PAGE_TYPE) {
                    $tc = Stack::getByID($row['cID']);
                } else {
                    $tc = self::getByID($row['cID']);
                }
                $nc = $tc->duplicate($cNewParent, $preserveUserID, $site);
                $tc->_duplicateAll($tc, $nc, $preserveUserID, $site);
            }
        }
    }

    /**
     * Get the canonical path string of this page .
     * This happens before any uniqueness checks get run.
     *
     * @return string
     */
    protected function computeCanonicalPagePath()
    {
        $parent = self::getByID($this->cParentID);
        $parentPath = $parent->getCollectionPathObject();
        $path = '';
        if ($parentPath instanceof PagePath) {
            $path = $parentPath->getPagePath();
        }
        $path .= '/';
        $cID = ($this->getCollectionPointerOriginalID() > 0) ? $this->getCollectionPointerOriginalID() : $this->cID;
        /** @var \Concrete\Core\Utility\Service\Validation\Strings $stringValidator */
        $stringValidator = Core::make('helper/validation/strings');
        if ($stringValidator->notempty($this->getAliasHandle())) {
            $path .= $this->getAliasHandle();
        } elseif ($stringValidator->notempty($this->getCollectionHandle())) {
            $path .= $this->getCollectionHandle();
        } elseif (!$this->isHomePage()) {
            $path .= $cID;
        } else {
            $path = ''; // This is computing the path for the home page, which has no handle, and so shouldn't have a segment.
        }

        $event = new PagePathEvent($this);
        $event->setPagePath($path);
        $event = Events::dispatch('on_compute_canonical_page_path', $event);

        return $event->getPagePath();
    }

    /**
     * Duplicate the master collection blocks/permissions to a newly created page.
     *
     * @param int $newCID the ID of the newly created page
     * @param int $mcID the ID of the master collection
     * @param bool $cAcquireComposerOutputControls
     * @param mixed $masterCID
     */
    protected function _associateMasterCollectionBlocks($newCID, $masterCID, $cAcquireComposerOutputControls)
    {
        $mc = self::getByID($masterCID, 'ACTIVE');
        $nc = self::getByID($newCID, 'RECENT');
        $db = Database::connection();

        $mcID = $mc->getCollectionID();
        $mcvID = $mc->getVersionID();

        $q = "select CollectionVersionBlocks.arHandle, BlockTypes.btCopyWhenPropagate, CollectionVersionBlocks.cbOverrideAreaPermissions, CollectionVersionBlocks.bID from CollectionVersionBlocks inner join Blocks on Blocks.bID = CollectionVersionBlocks.bID inner join BlockTypes on Blocks.btID = BlockTypes.btID where CollectionVersionBlocks.cID = '$mcID' and CollectionVersionBlocks.cvID = '{$mcvID}' order by CollectionVersionBlocks.cbDisplayOrder asc";

        // ok. This function takes two IDs, the ID of the newly created virgin collection, and the ID of the crusty master collection
        // who will impart his wisdom to the his young learner, by duplicating his various blocks, as well as their permissions, for the
        // new collection

        //$q = "select CollectionBlocks.cbAreaName, Blocks.bID, Blocks.bName, Blocks.bFilename, Blocks.btID, Blocks.uID, BlockTypes.btClassname, BlockTypes.btTablename from CollectionBlocks left join BlockTypes on (Blocks.btID = BlockTypes.btID) inner join Blocks on (CollectionBlocks.bID = Blocks.bID) where CollectionBlocks.cID = '$masterCID' order by CollectionBlocks.cbDisplayOrder asc";
        //$q = "select CollectionVersionBlocks.cbAreaName, Blocks.bID, Blocks.bName, Blocks.bFilename, Blocks.btID, Blocks.uID, BlockTypes.btClassname, BlockTypes.btTablename from CollectionBlocks left join BlockTypes on (Blocks.btID = BlockTypes.btID) inner join Blocks on (CollectionBlocks.bID = Blocks.bID) where CollectionBlocks.cID = '$masterCID' order by CollectionBlocks.cbDisplayOrder asc";

        $r = $db->query($q);

        if ($r) {
            while ($row = $r->fetchRow()) {
                $b = Block::getByID($row['bID'], $mc, $row['arHandle']);
                if ($cAcquireComposerOutputControls || !in_array($b->getBlockTypeHandle(), ['core_page_type_composer_control_output'])) {
                    if ($row['btCopyWhenPropagate']) {
                        $b->duplicate($nc, true);
                    } else {
                        $b->alias($nc);
                    }
                }
            }
        }
    }

    /**
     * Duplicate the master collection attributes to a newly created page.
     *
     * @param int $newCID the ID of the newly created page
     * @param int $mcID the ID of the master collection
     * @param mixed $masterCID
     */
    protected function _associateMasterCollectionAttributes($newCID, $masterCID)
    {
        $mc = self::getByID($masterCID, 'ACTIVE');
        $nc = self::getByID($newCID, 'RECENT');
        $attributes = CollectionKey::getAttributeValues($mc);
        foreach ($attributes as $attribute) {
            $value = $attribute->getValueObject();
            if ($value) {
                $value = clone $value;
                $nc->setAttribute($attribute->getAttributeKey(), $value);
            }
        }
    }

    /**
     * Copy the area styles from a page template.
     *
     * @param \Concrete\Core\Entity\Page\Template $pageTemplate
     * @param \Concrete\Core\Entity\Page\Template $template
     */
    protected function acquireAreaStylesFromDefaults(\Concrete\Core\Entity\Page\Template $template)
    {
        $pt = $this->getPageTypeObject();
        if (is_object($pt)) {
            $mc = $pt->getPageTypePageTemplateDefaultPageObject($template);
            $db = Database::connection();

            // first, we delete any styles we currently have
            $db->delete('CollectionVersionAreaStyles', ['cID' => $this->getCollectionID(), 'cvID' => $this->getVersionID()]);

            // now we acquire
            $q = 'select issID, arHandle from CollectionVersionAreaStyles where cID = ?';
            $r = $db->executeQuery($q, [$mc->getCollectionID()]);
            while ($row = $r->FetchRow()) {
                $db->executeQuery(
                    'insert into CollectionVersionAreaStyles (cID, cvID, arHandle, issID) values (?, ?, ?, ?)',
                    [
                        $this->getCollectionID(),
                        $this->getVersionID(),
                        $row['arHandle'],
                        $row['issID'],
                    ]
                );
            }
        }
    }

    /**
     * @param \SimpleXMLElement $node
     *
     * @return string[]
     */
    private static function translatePermissionsXMLToKeys($node)
    {
        $pkHandles = [];
        if ($node['canRead'] == '1') {
            $pkHandles[] = 'view_page';
            $pkHandles[] = 'view_page_in_sitemap';
        }
        if ($node['canWrite'] == '1') {
            $pkHandles[] = 'view_page_versions';
            $pkHandles[] = 'edit_page_properties';
            $pkHandles[] = 'edit_page_contents';
            $pkHandles[] = 'edit_page_multilingual_settings';
            $pkHandles[] = 'approve_page_versions';
            $pkHandles[] = 'move_or_copy_page';
            $pkHandles[] = 'preview_page_as_user';
            $pkHandles[] = 'add_subpage';
        }
        if ($node['canAdmin'] == '1') {
            $pkHandles[] = 'edit_page_speed_settings';
            $pkHandles[] = 'edit_page_permissions';
            $pkHandles[] = 'edit_page_theme';
            $pkHandles[] = 'schedule_page_contents_guest_access';
            $pkHandles[] = 'edit_page_page_type';
            $pkHandles[] = 'edit_page_template';
            $pkHandles[] = 'delete_page';
            $pkHandles[] = 'delete_page_versions';
        }

        return $pkHandles;
    }
}
