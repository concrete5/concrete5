<?php
namespace Concrete\Core\Page\Theme;

use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Cache\Level\RequestCache;
use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Http\ResponseAssetGroup;
use Concrete\Core\Support\Facade\Facade;
use Config;
use Loader;
use Page;
use Environment;
use Core;
use Concrete\Core\Page\Theme\File as PageThemeFile;
use Concrete\Core\Package\PackageList;
use Concrete\Core\Foundation\ConcreteObject;
use PageTemplate;
use Concrete\Core\Page\Theme\GridFramework\GridFramework;
use Concrete\Core\Page\Single as SinglePage;
use Concrete\Core\StyleCustomizer\Preset;
use Concrete\Core\Entity\StyleCustomizer\CustomCssRecord;
use Localization;

/**
 * A page's theme is a pointer to a directory containing templates, CSS files and optionally PHP includes, images and JavaScript files.
 * Themes inherit down the tree when a page is added, but can also be set at the site-wide level (thereby overriding any previous choices.).
 */
class Theme extends ConcreteObject
{
    const E_THEME_INSTALLED = 1;
    const THEME_EXTENSION = '.php';
    const THEME_CUSTOMIZABLE_STYLESHEET_EXTENSION = '.less';
    const FILENAME_TYPOGRAPHY_CSS = 'typography.css';

    protected $pThemeName;
    protected $pThemeID;
    protected $pThemeDescription;
    protected $pThemeDirectory;
    protected $pThemeThumbnail;
    protected $pThemeHandle;
    protected $pThemeURL;
    protected $pThemeIsPreview = false;
    protected $pkgID;
    protected $stylesheetCachePath;
    protected $stylesheetCacheRelativePath = REL_DIR_FILES_CACHE;

    public function __construct()
    {
        $this->setStylesheetCachePath(Config::get('concrete.cache.directory'));
    }

    /**
     * Get the installed themes provided by packages.
     *
     * @return \Concrete\Core\Page\Theme\Theme[]
     */
    public static function getGlobalList()
    {
        return static::getList('pkgID > 0');
    }

    /**
     * Get the installed themes provided by the core.
     *
     * @return \Concrete\Core\Page\Theme\Theme[]
     */
    public static function getLocalList()
    {
        return static::getList('pkgID = 0');
    }

    /**
     * Get the installed themes provided by a package.
     *
     * @var \Concrete\Core\Entity\Package|\Concrete\Core\Package\Package $pkg
     *
     * @return \Concrete\Core\Page\Theme\Theme[]
     */
    public static function getListByPackage($pkg)
    {
        return static::getList('pkgID = '.$pkg->getPackageID());
    }

    /**
     * Get the installed themes.
     *
     * @param string|null $where a custom SQL criteria to filter the installed themes.
     *
     * @return \Concrete\Core\Page\Theme\Theme[]
     */
    public static function getList($where = null)
    {
        if ($where != null) {
            $where = ' where '.$where;
        }

        $db = Loader::db();
        $r = $db->query('select pThemeID from PageThemes'.$where);
        $themes = [];
        while ($row = $r->fetchRow()) {
            $pl = static::getByID($row['pThemeID']);
            $themes[] = $pl;
        }

        return $themes;
    }

    /**
     * Get the handles of all the installed themes.
     *
     * @return string[]
     */
    public static function getInstalledHandles()
    {
        $db = Loader::db();

        return $db->GetCol('select pThemeHandle from PageThemes');
    }

    /**
     * Mark an asset as provided by this theme.
     *
     * @param string $assetType E.g. 'css' or 'javascript' (or an asset group identifier like 'jquery/ui')
     * @param string|false $assetHandle E.g. 'core/colorpicker'.
     */
    public function providesAsset($assetType, $assetHandle = null)
    {
        $r = ResponseAssetGroup::get();
        $r->markAssetAsIncluded($assetType, $assetHandle);
    }

    /**
     * Mark an asset as reuired by this theme.
     * Accepts the same arguments as \Concrete\Core\Http\ResponseAssetGroup::requireAsset().
     *
     * @see \Concrete\Core\Http\ResponseAssetGroup::requireAsset()
     */
    public function requireAsset()
    {
        $r = ResponseAssetGroup::get();
        $args = func_get_args();
        call_user_func_array([$r, 'requireAsset'], $args);
    }

    /**
     * Get the all the themes available in the /application/themes directory.
     *
     * @param bool $filterInstalled true (default) to exclude the already installed themes, false to include them.
     *
     * @return \Concrete\Core\Page\Theme\Theme[]
     */
    public static function getAvailableThemes($filterInstalled = true)
    {
        $db = Loader::db();
        $dh = Loader::helper('file');

        $themes = $dh->getDirectoryContents(DIR_FILES_THEMES);
        if ($filterInstalled) {
            // strip out themes we've already installed
            $handles = $db->GetCol('select pThemeHandle from PageThemes');
            $themesTemp = [];
            foreach ($themes as $t) {
                if (!in_array($t, $handles)) {
                    $themesTemp[] = $t;
                }
            }
            $themes = $themesTemp;
        }

        if (count($themes) > 0) {
            $themesTemp = [];
            // get theme objects from the file system
            foreach ($themes as $t) {
                $th = static::getByFileHandle($t);
                if (!empty($th)) {
                    $themesTemp[] = $th;
                }
            }
            $themes = $themesTemp;
        }

        return $themes;
    }

    /**
     * Get a theme from the file system.
     *
     * @param string $handle the theme handle
     * @param string $dir the parent directory that contains the theme directory
     * @param string $pkgHandle the handle of the package providing the theme
     *
     * @return \Concrete\Core\Page\Theme\Theme|null
     */
    public static function getByFileHandle($handle, $dir = DIR_FILES_THEMES, $pkgHandle = '')
    {
        $dirt = $dir.'/'.$handle;
        if (is_dir($dirt)) {
            $res = static::getThemeNameAndDescription($dirt, $handle, $pkgHandle);

            $th = new static();
            $th->pThemeHandle = $handle;
            $th->pThemeDirectory = $dirt;
            $th->pThemeName = $res->pThemeName;
            $th->pThemeDescription = $res->pThemeDescription;
            if (strlen($res->pError) > 0) {
                $th->error = $res->pError;
            }
            switch ($dir) {
                case DIR_FILES_THEMES:
                    $th->pThemeURL = DIR_REL.'/'.DIRNAME_APPLICATION.'/'.DIRNAME_THEMES.'/'.$handle;
                    break;
            }

            return $th;
        }
    }

    /**
     * Checks the theme for a styles.xml file (which is how customizations happen).
     *
     * @return bool
     */
    public function isThemeCustomizable()
    {
        $env = Environment::get();
        $r = $env->getRecord(
            DIRNAME_THEMES.'/'.$this->getThemeHandle().'/'.DIRNAME_CSS.'/'.FILENAME_STYLE_CUSTOMIZER_STYLES,
            $this->getPackageHandle()
        );

        return $r->exists();
    }

    /**
     * Gets the style list object for this theme.
     *
     * @return \Concrete\Core\StyleCustomizer\StyleList
     */
    public function getThemeCustomizableStyleList()
    {
        if (!isset($this->styleList)) {
            $env = Environment::get();
            $r = $env->getRecord(
                DIRNAME_THEMES.'/'.$this->getThemeHandle(
                ).'/'.DIRNAME_CSS.'/'.FILENAME_STYLE_CUSTOMIZER_STYLES,
                $this->getPackageHandle()
            );
            $this->styleList = \Concrete\Core\StyleCustomizer\StyleList::loadFromXMLFile($r->file);
        }

        return $this->styleList;
    }

    /**
     * Get a preset for this theme by handle.
     *
     * @return \Concrete\Core\StyleCustomizer\Preset|null
     */
    public function getThemeCustomizablePreset($handle)
    {
        $env = Environment::get();
        if ($this->isThemeCustomizable()) {
            $file = $env->getRecord(
                DIRNAME_THEMES.'/'.$this->getThemeHandle(
                ).'/'.DIRNAME_CSS.'/'.DIRNAME_STYLE_CUSTOMIZER_PRESETS.'/'.$handle.static::THEME_CUSTOMIZABLE_STYLESHEET_EXTENSION,
                $this->getPackageHandle()
            );
            if ($file->exists()) {
                $urlroot = $env->getURL(
                    DIRNAME_THEMES.'/'.$this->getThemeHandle().'/'.DIRNAME_CSS,
                    $this->getPackageHandle()
                );
                $preset = Preset::getFromFile($file->file, $urlroot);

                return $preset;
            }
        }
    }

    /**
     * Get all presets available to this theme.
     *
     * @return \Concrete\Core\StyleCustomizer\Preset[]
     */
    public function getThemeCustomizableStylePresets()
    {
        $presets = [];
        $env = Environment::get();
        if ($this->isThemeCustomizable()) {
            $directory = $env->getPath(
                DIRNAME_THEMES.'/'.$this->getThemeHandle(
                ).'/'.DIRNAME_CSS.'/'.DIRNAME_STYLE_CUSTOMIZER_PRESETS,
                $this->getPackageHandle()
            );
            $urlroot = $env->getURL(
                DIRNAME_THEMES.'/'.$this->getThemeHandle().'/'.DIRNAME_CSS,
                $this->getPackageHandle()
            );
            $dh = Loader::helper('file');
            $files = $dh->getDirectoryContents($directory);
            foreach ($files as $f) {
                if (strrchr($f, '.') == static::THEME_CUSTOMIZABLE_STYLESHEET_EXTENSION) {
                    $preset = Preset::getFromFile($directory.'/'.$f, $urlroot);
                    if (is_object($preset)) {
                        $presets[] = $preset;
                    }
                }
            }
        }
        usort(
            $presets,
            function ($a, $b) {
                if ($a->isDefaultPreset()) {
                    return -1;
                } else {
                    return strcasecmp($a->getPresetDisplayName('text'), $b->getPresetDisplayName('text'));
                }
            }
        );

        return $presets;
    }

    /**
     * Set this instance to be a preview for the current request.
     */
    public function enablePreviewRequest()
    {
        $this->setStylesheetCacheRelativePath(REL_DIR_FILES_CACHE.'/preview');
        $this->setStylesheetCachePath(Config::get('concrete.cache.directory').'/preview');
        $this->pThemeIsPreview = true;
    }

    public function resetThemeCustomStyles()
    {
        $db = Loader::db();
        $db->delete('PageThemeCustomStyles', ['pThemeID' => $this->getThemeID()]);
        $sheets = $this->getThemeCustomizableStyleSheets();
        foreach ($sheets as $sheet) {
            $sheet->clearOutputFile();
        }
    }

    /**
     * Is this instance a preview for the current request?
     *
     * @return bool
     */
    public function isThemePreviewRequest()
    {
        return $this->pThemeIsPreview;
    }

    /**
     * Get all the customizable LESS stylesheets.
     *
     * @return \Concrete\Core\StyleCustomizer\Stylesheet[]
     */
    public function getThemeCustomizableStyleSheets()
    {
        $sheets = [];
        $env = Environment::get();
        if ($this->isThemeCustomizable()) {
            $directory = $env->getPath(
                DIRNAME_THEMES.'/'.$this->getThemeHandle().'/'.DIRNAME_CSS,
                $this->getPackageHandle()
            );
            $dh = Loader::helper('file');
            $files = $dh->getDirectoryContents($directory);
            foreach ($files as $f) {
                if (strrchr($f, '.') == static::THEME_CUSTOMIZABLE_STYLESHEET_EXTENSION) {
                    $sheets[] = $this->getStylesheetObject($f);
                }
            }
        }

        return $sheets;
    }

    /**
     * Get a customizable LESS stylesheet given the stylesheed base file name.
     *
     * @param string $stylesheet
     *
     * @return \Concrete\Core\StyleCustomizer\Stylesheet
     */
    public function getStylesheetObject($stylesheet)
    {
        $env = Environment::get();
        $output = $this->getStylesheetCachePath().'/'.DIRNAME_CSS.'/'.$this->getThemeHandle();
        $relative = $this->getStylesheetCacheRelativePath().'/'.DIRNAME_CSS.'/'.$this->getThemeHandle();
        $r = $env->getRecord(
            DIRNAME_THEMES.'/'.$this->getThemeHandle().'/'.DIRNAME_CSS.'/'.$stylesheet,
            $this->getPackageHandle()
        );

        $stylesheet = new \Concrete\Core\StyleCustomizer\Stylesheet($stylesheet, $r->file, $r->url, $output, $relative);

        return $stylesheet;
    }

    /**
     * Look into the current CSS directory and return a fully compiled stylesheet when passed a LESS stylesheet.
     * Also serves up custom value list values for the stylesheet if they exist.
     *
     * @param string $stylesheet The LESS stylesheet to compile
     *
     * @return string The path to the stylesheet
     */
    public function getStylesheet($stylesheet)
    {
        $stylesheet = $this->getStylesheetObject($stylesheet);
        $styleValues = $this->getThemeCustomStyleObjectValues();
        if (!is_null($styleValues)) {
            $stylesheet->setValueList($styleValues);
        }
        if (!$this->isThemePreviewRequest()) {
            if (!$stylesheet->outputFileExists() || !Config::get('concrete.cache.theme_css')) {
                $stylesheet->output();
            }
        }
        $path = $stylesheet->getOutputRelativePath();
        if ($this->isThemePreviewRequest()) {
            $path .= '?ts='.time();
        } else {
            $path .= '?ts='.filemtime($stylesheet->getOutputPath());
        }

        return $path;
    }

    /**
     * Get a CustomStyle object for the theme if one exists.
     *
     * @return \Concrete\Core\Page\CustomStyle|null
     */
    public function getThemeCustomStyleObject()
    {
        $db = Loader::db();
        $row = $db->FetchAssoc('select * from PageThemeCustomStyles where pThemeID = ?', [$this->getThemeID()]);
        if (isset($row['pThemeID'])) {
            $o = new \Concrete\Core\Page\CustomStyle();
            $o->setThemeID($this->getThemeID());
            $o->setValueListID($row['scvlID']);
            $o->setPresetHandle($row['preset']);
            $o->setCustomCssRecordID($row['sccRecordID']);

            return $o;
        }
    }

    /**
     * Get the value list of the custom style object if one exists.
     *
     * @return \Concrete\Core\StyleCustomizer\Style\ValueList|null
     */
    public function getThemeCustomStyleObjectValues()
    {
        $style = $this->getThemeCustomStyleObject();
        if (is_object($style)) {
            return $style->getValueList();
        }

        return null;
    }

    /**
     * Create a CustomStyle (and optionally a preset and the custom CSS) for this theme.
     *
     * @param \Concrete\Core\StyleCustomizer\Preset|null|false $selectedPreset
     *
     * @return \Concrete\Core\Page\CustomStyle
     */
    public function setCustomStyleObject(\Concrete\Core\StyleCustomizer\Style\ValueList $valueList, $selectedPreset = false, CustomCssRecord $customCssRecord = null)
    {
        $db = Loader::db();
        $db->delete('PageThemeCustomStyles', ['pThemeID' => $this->getThemeID()]);
        $preset = false;
        if ($selectedPreset) {
            $preset = $selectedPreset->getPresetHandle();
        }
        $sccRecordID = 0;
        if ($customCssRecord !== null) {
            $sccRecordID = $customCssRecord->getRecordID();
        }
        $db->insert(
            'PageThemeCustomStyles',
            [
                'pThemeID' => $this->getThemeID(),
                'sccRecordID' => $sccRecordID,
                'preset' => $preset,
                'scvlID' => $valueList->getValueListID(),
            ]
        );

        // now we reset all cached css files in this theme
        $sheets = $this->getThemeCustomizableStyleSheets();
        foreach ($sheets as $s) {
            $s->clearOutputFile();
        }

        $scc = new \Concrete\Core\Page\CustomStyle();
        $scc->setThemeID($this->getThemeID());
        $scc->setValueListID($valueList->getValueListID());
        $scc->setPresetHandle($preset);
        $scc->setCustomCssRecordID($sccRecordID);

        return $scc;
    }

    /**
     * Get an installed theme given its handle.
     *
     * @param string $pThemeHandle
     *
     * @return \Concrete\Core\Page\Theme\Theme|null
     */
    public static function getByHandle($pThemeHandle)
    {
        $where = 'pThemeHandle = ?';
        $args = [$pThemeHandle];
        $pt = static::populateThemeQuery($where, $args);

        return $pt;
    }

    /**
     * Get an installed theme given its ID.
     *
     * @param int $pThemeID
     *
     * @return \Concrete\Core\Page\Theme\Theme|null
     */
    public static function getByID($pThemeID)
    {
        /** @var RequestCache $cache */
        $cache = Facade::getFacadeApplication()->make('cache/request');
        $key = '/PageTheme/' . $pThemeID;
        if ($cache->isEnabled()) {
            $item = $cache->getItem($key);
            if ($item->isHit()) {
                return $item->get();
            }
        }

        $where = 'pThemeID = ?';
        $args = [$pThemeID];
        $pt = static::populateThemeQuery($where, $args);

        if (isset($item) && $item->isMiss()) {
            $item->set($pt);
            $cache->save($item);
        }

        return $pt;
    }

    /**
     * Get the instance representing an installed theme.
     *
     * @param string $where the SQL part used in the SELECT query
     * @param array $args the parameters of the SQL query
     *
     * @return \Concrete\Core\Page\Theme\Theme|null
     */
    protected static function populateThemeQuery($where, $args)
    {
        $app = Application::getFacadeApplication();
        $cache = $app->make(RequestCache::class);
        $pl = null;

        $cacheSuffix =  $where;
        if (\is_array($args) === true) {
            $cacheSuffix .= json_encode($args);
        } else {
            $cacheSuffix .= $args;
        }

        $key = 'theme_populate_query/' . md5($cacheSuffix);

        $item = $cache->getItem($key);

        if ($item->isMiss() === true) {
            $db = Loader::db();
            $row = $db->GetRow(
                "select pThemeID, pThemeHandle, pThemeDescription, pkgID, pThemeName, pThemeHasCustomClass from PageThemes where {$where}",
                $args
            );
            $env = Environment::get();

            if (!empty($row)) {
                $item->lock();
                $standardClass = '\\Concrete\Core\\Page\\Theme\\Theme';
                if ($row['pThemeHasCustomClass']) {
                    $pkgHandle = PackageList::getHandle($row['pkgID']);
                    $r = $env->getRecord(DIRNAME_THEMES.'/'.$row['pThemeHandle'].'/'.FILENAME_THEMES_CLASS, $pkgHandle);
                    $prefix = $r->override ? true : $pkgHandle;
                    $customClass = core_class(
                        'Theme\\'.
                        Loader::helper('text')->camelcase($row['pThemeHandle']).
                        '\\PageTheme',
                    $prefix);
                    try {
                        $pl = Core::make($customClass);
                    } catch (\ReflectionException $e) {
                        $pl = Core::make($standardClass);
                    }
                } else {
                    $pl = Core::make($standardClass);
                }
                $pl->setPropertiesFromArray($row);
                $pkgHandle = $pl->getPackageHandle();
                $pl->pThemeDirectory = $env->getPath(DIRNAME_THEMES.'/'.$row['pThemeHandle'], $pkgHandle);
                $pl->pThemeURL = $env->getURL(DIRNAME_THEMES.'/'.$row['pThemeHandle'], $pkgHandle);
                
                $item->set($pl);
                $cache->save($item);
            }
        } else {
            $pl = $item->get();
        }

        return $pl;
    }

    /**
     * Install a theme given its handle.
     *
     * @param string $pThemeHandle the handle of the theme to be installed.
     * @param \Concrete\Core\Entity\Package|\Concrete\Core\Package\Package|null $pkg
     *
     * @throws \Exception in case of errors.
     *
     * @return \Concrete\Core\Page\Theme\Theme|null returns NULL if the directory containing the theme could not be found
     */
    public static function add($pThemeHandle, $pkg = null)
    {
        if (is_object($pkg)) {
            if (is_dir(DIR_PACKAGES.'/'.$pkg->getPackageHandle())) {
                $dir = DIR_PACKAGES.'/'.$pkg->getPackageHandle().'/'.DIRNAME_THEMES.'/'.$pThemeHandle;
            } else {
                $dir = DIR_PACKAGES_CORE.'/'.$pkg->getPackageHandle().'/'.DIRNAME_THEMES.'/'.$pThemeHandle;
            }
            $pkgID = $pkg->getPackageID();
        } else {
            if (is_dir(DIR_FILES_THEMES.'/'.$pThemeHandle)) {
                $dir = DIR_FILES_THEMES.'/'.$pThemeHandle;
                $pkgID = 0;
            } else {
                $dir = DIR_FILES_THEMES_CORE.'/'.$pThemeHandle;
                $pkgID = 0;
            }
        }
        $l = static::install($dir, $pThemeHandle, $pkgID);

        return $l;
    }

    /**
     * Grab all files in theme that are PHP based (or html if we go that route)
     * and then lists them out, by type, allowing people to install them as page type, etc...
     *
     * @return \Concrete\Core\Page\Theme\File[]
     */
    public function getFilesInTheme()
    {
        $dh = Loader::helper('file');
        $templateList = PageTemplate::getList();
        $pts = [];
        foreach ($templateList as $pt) {
            $pts[] = $pt->getPageTemplateHandle();
        }
        $files = [];
        $filesTmp = $dh->getDirectoryContents($this->pThemeDirectory);
        foreach ($filesTmp as $f) {
            if (strrchr($f, '.') == static::THEME_EXTENSION) {
                $fHandle = substr($f, 0, strpos($f, '.'));

                if ($f == FILENAME_THEMES_VIEW) {
                    $type = PageThemeFile::TFTYPE_VIEW;
                } elseif ($f == FILENAME_THEMES_CLASS) {
                    $type = PageThemeFile::TFTYPE_PAGE_CLASS;
                } else {
                    if ($f == FILENAME_THEMES_DEFAULT) {
                        $type = PageThemeFile::TFTYPE_DEFAULT;
                    } else {
                        if (in_array($f, SinglePage::getThemeableCorePages())) {
                            $type = PageThemeFile::TFTYPE_SINGLE_PAGE;
                        } else {
                            if (in_array($fHandle, $pts)) {
                                $type = PageThemeFile::TFTYPE_PAGE_TEMPLATE_EXISTING;
                            } else {
                                $type = PageThemeFile::TFTYPE_PAGE_TEMPLATE_NEW;
                            }
                        }
                    }
                }

                $pf = new PageThemeFile();
                $pf->setFilename($f);
                $pf->setType($type);
                $files[] = $pf;
            }
        }

        return $files;
    }

    /**
     * Get the theme details by reading a directory containing the theme.
     *
     * @param string $dir
     * @param string $pThemeHandle
     * @param string $pkgHandle
     *
     * @return \stdClass
     */
    private static function getThemeNameAndDescription($dir, $pThemeHandle, $pkgHandle = '')
    {
        $res = new \stdClass();
        $res->pThemeName = '';
        $res->pThemeDescription = '';
        $res->pError = '';
        if (file_exists($dir.'/'.FILENAME_THEMES_DESCRIPTION)) {
            $con = file($dir.'/'.FILENAME_THEMES_DESCRIPTION);
            $res->pThemeName = trim($con[0]);
            $res->pThemeDescription = trim($con[1]);
        }
        $pageThemeFile = $dir.'/'.FILENAME_THEMES_CLASS;
        if (is_file($pageThemeFile)) {
            try {
                $cn = '\\Theme\\'.camelcase($pThemeHandle).'\\PageTheme';
                $classNames = [];
                if (strlen($pkgHandle)) {
                    $classNames[] = '\\Concrete\\Package\\'.camelcase($pkgHandle).$cn;
                } else {
                    $classNames[] = '\\Application'.$cn;
                    $classNames[] = '\\Concrete'.$cn;
                }
                $className = null;
                foreach ($classNames as $cn) {
                    if (class_exists($cn, false)) {
                        $className = $cn;
                        break;
                    }
                }
                if (is_null($className)) {
                    include_once $pageThemeFile;
                    foreach ($classNames as $cn) {
                        if (class_exists($cn, false)) {
                            $className = $cn;
                            break;
                        }
                    }
                }
                if (is_null($className)) {
                    $res->pError = t(/*i18n: %1$s is a filename, %2$s is a PHP class name */'The theme file %1$s does not define the class %2$s', FILENAME_THEMES_CLASS, ltrim($classNames[0], '\\'));
                } else {
                    $instance = new $className();
                    $extensionOf = '\\Concrete\\Core\\Page\\Theme\\Theme';
                    if (!is_a($instance, $extensionOf)) {
                        $res->pError = t(/*i18n: %1$s is a filename, %2$s and %3$s are PHP class names */'The theme file %1$s should define a %2$s class that extends the class %3$s', FILENAME_THEMES_CLASS, ltrim($className, '\\'), ltrim($extensionOf, '\\'));
                    } else {
                        if (method_exists($instance, 'getThemeName')) {
                            $s = $instance->getThemeName();
                            if (strlen($s) > 0) {
                                $res->pThemeName = $s;
                            }
                        }
                        if (method_exists($instance, 'getThemeDescription')) {
                            $s = $instance->getThemeDescription();
                            if (strlen($s) > 0) {
                                $res->pThemeDescription = $s;
                            }
                        }
                    }
                }
            } catch (\Exception $x) {
                $res->pError = $x->getMessage();
            }
        }

        return $res;
    }

    /**
     * Export this theme by creating the XML nodes under the provided XML node.
     *
     * @param \SimpleXMLElement $node
     */
    public function export($node)
    {
        $pst = static::getSiteTheme();
        $activated = 0;
        if ($pst->getThemeID() == $this->getThemeID()) {
            $activated = 1;
        }
        $type = $node->addChild('theme');
        $type->addAttribute('handle', $this->getThemeHandle());
        $type->addAttribute('package', $this->getPackageHandle());
        $type->addAttribute('activated', $activated);
    }

    /**
     * Export all the installed themes by creating the XML nodes under the provided XML node.
     *
     * @param \SimpleXMLElement $xml
     */
    public static function exportList($xml)
    {
        $nxml = $xml->addChild('themes');
        $list = static::getList();
        foreach ($list as $pt) {
            $pt->export($nxml);
        }
    }

    /**
     * Install a theme.
     *
     * @param string $dir
     * @param string $pThemeHandle
     * @param int|null $pkgID
     *
     * @throws \Exception in case of errors.

     * @return \Concrete\Core\Page\Theme\Theme|null returns NULL if $dir does not exist
     */
    protected static function install($dir, $pThemeHandle, $pkgID)
    {
        $result = null;
        if (is_dir($dir)) {
            $pkg = null;
            if ($pkgID) {
                $pkg = \Concrete\Core\Package\Package::getByID($pkgID);
            }
            $db = Loader::db();
            $cnt = $db->getOne('select count(pThemeID) from PageThemes where pThemeHandle = ?', [$pThemeHandle]);
            if ($cnt > 0) {
                throw new \Exception(static::E_THEME_INSTALLED);
            }
            $loc = Localization::getInstance();
            $loc->pushActiveContext(Localization::CONTEXT_SYSTEM);
            try {
                $res = static::getThemeNameAndDescription($dir, $pThemeHandle, is_object($pkg) ? $pkg->getPackageHandle() : '');
            } catch (\Exception $x) {
                $loc->popActiveContext();
                throw $x;
            }
            $loc->popActiveContext();
            if (strlen($res->pError) === 0) {
                $pThemeName = $res->pThemeName;
                $pThemeDescription = $res->pThemeDescription;
                $db->query(
                    'insert into PageThemes (pThemeHandle, pThemeName, pThemeDescription, pkgID) values (?, ?, ?, ?)',
                    [$pThemeHandle, $pThemeName, $pThemeDescription, $pkgID]
                );

                $env = Environment::get();
                $env->clearOverrideCache();

                $pt = static::getByID($db->Insert_ID());
                $pt->updateThemeCustomClass();

                $result = $pt;
            }
        }

        return $result;
    }

    /**
     * (Re)Scan the theme folder to check if it contains the page_theme.php file: if so, marks the theme as having a controller.
     */
    public function updateThemeCustomClass()
    {
        $env = Environment::get();
        $db = Loader::db();
        $r = $env->getRecord(
            DIRNAME_THEMES.'/'.$this->pThemeHandle.'/'.FILENAME_THEMES_CLASS,
            $this->getPackageHandle()
        );
        if ($r->exists()) {
            $db->Execute('update PageThemes set pThemeHasCustomClass = 1 where pThemeID = ?', [$this->pThemeID]);
            $this->pThemeHasCustomClass = true;
        } else {
            $db->Execute('update PageThemes set pThemeHasCustomClass = 0 where pThemeID = ?', [$this->pThemeID]);
            $this->pThemeHasCustomClass = false;
        }
    }

    /**
     * Get the ID of the installed theme (if available).
     *
     * @return int|null
     */
    public function getThemeID()
    {
        return $this->pThemeID;
    }

    /**
     * Get the (English) name of the theme.
     *
     * @return string
     */
    public function getThemeName()
    {
        return $this->pThemeName;
    }

    /**
     * Get the localized name for this theme (escaped accordingly to $format).
     *
     * @param string $format Escape the result in html format (if $format is 'html'); if $format is 'text' or any other value, the display name won't be escaped
     *
     * @return string
     */
    public function getThemeDisplayName($format = 'html')
    {
        $value = $this->getThemeName();
        if (strlen($value)) {
            $value = t($value);
        } else {
            $value = t('(No Name)');
        }
        switch ($format) {
            case 'html':
                return h($value);
            case 'text':
            default:
                return $value;
        }
    }

    /**
     * Get the ID of the package providing this theme (if available).
     *
     * @return int|null
     */
    public function getPackageID()
    {
        return $this->pkgID;
    }

    /**
     * Get the handle of the package providing this theme (if available).
     *
     * @return string|false|null
     */
    public function getPackageHandle()
    {
        return \Concrete\Core\Package\PackageList::getHandle($this->pkgID);
    }

    /**
     * Returns whether a theme has a custom class.
     */
    public function hasCustomClass()
    {
        return $this->pThemeHasCustomClass;
    }

    /**
     * Get the handle of this theme.
     *
     * @return string
     */
    public function getThemeHandle()
    {
        return $this->pThemeHandle;
    }

    /**
     * Get the (English) description of this theme.
     *
     * @return string
     */
    public function getThemeDescription()
    {
        return $this->pThemeDescription;
    }

    /**
     * Get the localized description for this theme (escaped accordingly to $format).
     *
     * @param string $format Escape the result in html format (if $format is 'html'); if $format is 'text' or any other value, the display name won't be escaped
     *
     * @return string
     */
    public function getThemeDisplayDescription($format = 'html')
    {
        $value = $this->getThemeDescription();
        if (strlen($value)) {
            $value = t($value);
        } else {
            $value = t('(No Description)');
        }
        switch ($format) {
            case 'html':
                return h($value);
            case 'text':
            default:
                return $value;
        }
    }

    /**
     * Get the directory containing this theme.
     *
     * @return string
     */
    public function getThemeDirectory()
    {
        return $this->pThemeDirectory;
    }

    /**
     * Get the URL prefix of the assets provided by this theme.
     *
     * @return string
     */
    public function getThemeURL()
    {
        return $this->pThemeURL;
    }

    /**
     * Get the URL of the theme typography.css file.
     *
     * @return string
     */
    public function getThemeEditorCSS()
    {
        return $this->pThemeURL.'/'.static::FILENAME_TYPOGRAPHY_CSS;
    }

    /**
     * Set the URL prefix of the assets provided by this theme.
     *
     * @param string $pThemeURL
     */
    public function setThemeURL($pThemeURL)
    {
        $this->pThemeURL = $pThemeURL;
    }

    /**
     * Set the absolute path of the theme folder.
     *
     * @param string $pThemeDirectory
     */
    public function setThemeDirectory($pThemeDirectory)
    {
        $this->pThemeDirectory = $pThemeDirectory;
    }

    /**
     * Set the handle of this theme.
     *
     * @param string $pThemeHandle
     */
    public function setThemeHandle($pThemeHandle)
    {
        $this->pThemeHandle = $pThemeHandle;
    }

    /**
     * Set the absolute path of a directory where the CSS stylesheet files should be stored.
     *
     * @param string $path
     */
    public function setStylesheetCachePath($path)
    {
        $this->stylesheetCachePath = $path;
    }

    /**
     * Set the path of a directory where the CSS stylesheet files should be stored, relative to the website root directory.
     *
     * @param string $path
     */
    public function setStylesheetCacheRelativePath($path)
    {
        $this->stylesheetCacheRelativePath = $path;
    }

    /**
     * Get the absolute path of a directory where the CSS stylesheet files should be stored.
     *
     * @return string
     */
    public function getStylesheetCachePath()
    {
        return $this->stylesheetCachePath;
    }

    /**
     * Get the path of a directory where the CSS stylesheet files should be stored, relative to the website root directory.
     *
     * @return string
     */
    public function getStylesheetCacheRelativePath()
    {
        return $this->stylesheetCacheRelativePath;
    }

    /**
     * Is this theme uninstallable?
     *
     * @return bool
     */
    public function isUninstallable()
    {
        return $this->pThemeDirectory != DIR_FILES_THEMES_CORE.'/'.$this->getThemeHandle();
    }

    /**
     * Get the theme thumbnail image.
     *
     * @return \HtmlObject\Image
     */
    public function getThemeThumbnail()
    {
        if (file_exists($this->pThemeDirectory.'/'.FILENAME_THEMES_THUMBNAIL)) {
            $src = $this->pThemeURL.'/'.FILENAME_THEMES_THUMBNAIL;
        } else {
            $src = ASSETS_URL_THEMES_NO_THUMBNAIL;
        }
        $html = new \HtmlObject\Image();
        $img = $html->src($src)
            ->width(Config::get('concrete.icons.theme_thumbnail.width'))
            ->height(Config::get('concrete.icons.theme_thumbnail.height'))
            ->class('ccm-icon-theme');

        return $img;
    }

    /**
     * Apply this theme to all the pages of a site.
     *
     * @param \Concrete\Core\Entity\Site\Site|null $site if null, we'll use the current site.
     */
    public function applyToSite(Site $site = null)
    {
        if (!is_object($site)) {
            $site = \Core::make('site')->getSite();
        }
        $entityManager = \Database::connection()->getEntityManager();
        $site->setThemeID($this->getThemeID());
        $entityManager->persist($site);
        $entityManager->flush();

        $treeIDs = [0];
        foreach($site->getLocales() as $locale) {
            $tree = $locale->getSiteTree();
            if (is_object($tree)) {
                $treeIDs[] = $tree->getSiteTreeID();
            }
        }

        $treeIDs = implode(',', $treeIDs);

        $db = Loader::db();
        $r = $db->query(
            "update CollectionVersions inner join Pages on CollectionVersions.cID = Pages.cID left join Packages on Pages.pkgID = Packages.pkgID set CollectionVersions.pThemeID = ? where cIsTemplate = 0 and siteTreeID in ({$treeIDs}) and (Pages.ptID > 0 or CollectionVersions.pTemplateID > 0)",
            array($this->pThemeID)
        );
    }

    /**
     * Get the theme for the current site.
     *
     * @return \Concrete\Core\Page\Theme\Theme|null
     */
    public static function getSiteTheme()
    {
        $site = \Core::make('site')->getSite();

        return static::getByID($site->getThemeID());
    }

    /**
     * Uninstall this theme.
     */
    public function uninstall()
    {
        $db = Loader::db();

        $db->query('delete from PageThemes where pThemeID = ?', [$this->pThemeID]);
        $env = Environment::get();
        $env->clearOverrideCache();
    }

    /**
     * The handle of the grid framework supported by this theme.
     *
     * @var string|null|false
     */
    protected $pThemeGridFrameworkHandle = false;

    /**
     * Register the assets provided by this theme.
     */
    public function registerAssets()
    {
    }

    /**
     * Does this theme support a grid framework?
     *
     * @return bool
     */
    public function supportsGridFramework()
    {
        $handle = $this->getThemeGridFrameworkHandle();
        return $handle != false;
    }

    /**
     * Get the grid framework supported by this theme.
     *
     * @return \Concrete\Core\Page\Theme\GridFramework\GridFramework|null
     */
    public function getThemeGridFrameworkObject()
    {
        $handle = $this->getThemeGridFrameworkHandle();
        if ($handle) {
            $framework = Core::make('manager/grid_framework')->driver($handle);
            return $framework;
        }
    }

    /**
     * Get the handle of the grid framework supported by this theme.
     *
     * @return string|NULL|false
     */
    public function getThemeGridFrameworkHandle()
    {
        return $this->pThemeGridFrameworkHandle;
    }

    /**
     * Get the handles of the features supported by this theme.
     *
     * @return string[]
     *
     * @see \Concrete\Core\Feature\Feature for a list of features
     * @see \Concrete\Theme\Elemental\PageTheme::getThemeSupportedFeatures() for an example
     */
    public function getThemeSupportedFeatures()
    {
        return [];
    }

    /**
     * Get the theme-specific CSS classes for every block.
     *
     * @return array array keys are the block type handles, array values are a list of the CSS classes.
     *
     * @see \Concrete\Theme\Elemental\PageTheme::getThemeBlockClasses() for an example
     */
    public function getThemeBlockClasses()
    {
        return [];
    }

    /**
     * Get the theme-specific CSS classes for every area.
     *
     * @return array array keys are the area names, array values are a list of the CSS classes.
     *
     * @see \Concrete\Theme\Elemental\PageTheme::getThemeAreaClasses() for an example
     */
    public function getThemeAreaClasses()
    {
        return [];
    }

    /**
     * Get the theme-specific style names to be used in the rich text editor.
     *
     * @return array Every array item is an array with the keys 'title', 'menuClass', 'spanClass', 'forceBlock'.
     *
     * @see \Concrete\Theme\Elemental\PageTheme::getThemeEditorClasses() for an example
     */
    public function getThemeEditorClasses()
    {
        return [];
    }

    /**
     * Get the theme-specific templates for every block.
     *
     * @return array array keys are the block type handles, array values are a list of the block templates.
     *
     * @see \Concrete\Theme\Elemental\PageTheme::getThemeDefaultBlockTemplates() for an example
     */
    public function getThemeDefaultBlockTemplates()
    {
        return [];
    }

    /**
     * Get the handles of the thumbnail types and related resolution breakpoint.
     *
     * @return array
     *
     * @see \Concrete\Theme\Elemental\PageTheme::getThemeResponsiveImageMap() for an example
     */
    public function getThemeResponsiveImageMap()
    {
        return [];
    }

    /**
     * @deprecated
     */
    public function getThemeGatheringGridItemMargin()
    {
        return 20;
    }

    /**
     * @deprecated
     */
    public function getThemeGatheringGridItemWidth()
    {
        return 150;
    }

    /**
     * @deprecated
     */
    public function getThemeGatheringGridItemHeight()
    {
        return 150;
    }
}
