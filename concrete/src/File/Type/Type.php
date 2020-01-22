<?php
namespace Concrete\Core\File\Type;

use Concrete\Core\Support\Facade\Application;
use Concrete\Core\File\Image\Thumbnail\Type\Type as ThumbnailType;

class Type
{
    // File Type Constants

    /**
     * Image file type.
     *
     * @var int
     */
    const T_IMAGE = 1;          //!< @javascript-exported

    /**
     * Video file type.
     *
     * @var int
     */
    const T_VIDEO = 2;          //!< @javascript-exported

    /**
     * Text file type.
     *
     * @var int
     */
    const T_TEXT = 3;           //!< @javascript-exported

    /**
     * Audio file type.
     *
     * @var int
     */
    const T_AUDIO = 4;          //!< @javascript-exported

    /**
     * Document file type.
     *
     * @var int
     */
    const T_DOCUMENT = 5;       //!< @javascript-exported

    /**
     * Application file type.
     *
     * @var int
     */
    const T_APPLICATION = 6;    //!< @javascript-exported

    /**
     * Other/unknown file type.
     *
     * @var int
     */
    const T_UNKNOWN = 99;       //!< @javascript-exported

    // Properties

    /**
     * Handle of the owner package (empty string if not available).
     *
     * @deprecated Use the getPackageHandle()/setPackageHandle() methods
     *
     * @var string
     */
    public $pkgHandle = '';

    /**
     * Generic category type (one of the \Concrete\Core\File\Type\Type::T_... constants).
     *
     * @deprecated Use the getGenericType()/setGenericType()/getGenericDisplayType() methods
     *
     * @var int
     */
    public $type = null;

    /**
     * Name (empty string if generic type).
     *
     * @deprecated Use the getName()/setName()/getDisplayName() methods
     *
     * @var string
     */
    public $name = '';

    /**
     * Single file extension.
     *
     * @deprecated Use the getExtension()/setExtension() methods
     *
     * @var string
     */
    public $extension = '';

    /**
     * Handle of the custom importer (empty string if not available).
     *
     * @deprecated Use the getCustomImporter()/setCustomImporter() methods
     *
     * @var string
     */
    public $customImporter = '';

    /**
     * Handle of the editor (empty string if not available).
     *
     * @deprecated Use the getEditor()/setEditor() methods
     *
     * @var string
     */
    public $editor = '';

    /**
     * Handle of the inline viewer (empty string if not available).
     *
     * @deprecated Use the getView()/setView() methods
     *
     * @var string
     */
    public $view = '';

    // Getters/setters

    /**
     * Get the handle of the owner package (empty string if not available).
     *
     * @return string
     */
    public function getPackageHandle()
    {
        return $this->pkgHandle;
    }

    /**
     * Set the handle of the owner package (empty string if not available).
     *
     * @param string $value
     *
     * @return static Returns the class instance itself for method chaining
     */
    public function setPackageHandle($value)
    {
        $this->pkgHandle = (string) $value;

        return $this;
    }

    /**
     * Get the generic category type (one of the \Concrete\Core\File\Type\Type::T_... constants).
     *
     * @return int
     */
    public function getGenericType()
    {
        return $this->type ?: static::T_UNKNOWN;
    }

    /**
     * Set the generic category type (one of the \Concrete\Core\File\Type\Type::T_... constants).
     *
     * @param int $value
     *
     * @return static Returns the class instance itself for method chaining
     */
    public function setGenericType($value)
    {
        $this->type = (int) $value;

        return $this;
    }

    /**
     * Get the name of the generic category type.
     *
     * @return string
     */
    public function getGenericDisplayType()
    {
        return static::getGenericTypeText($this->getGenericType());
    }

    /**
     * Get the name (empty string if generic type).
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the name (empty string if generic type).
     *
     * @param string
     *
     * @return static Returns the class instance itself for method chaining
     */
    public function setName($value)
    {
        $this->name = (string) $value;

        return $this;
    }

    /**
     * Get the display name (localized).
     *
     * @return string
     */
    public function getDisplayName()
    {
        $name = $this->getName();

        return ($name === '') ? $this->getGenericDisplayType() : t($name);
    }

    /**
     * Get the single file extension.
     *
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * Set the single file extension.
     *
     * @param string $value
     *
     * @return static Returns the class instance itself for method chaining
     */
    public function setExtension($value)
    {
        $this->extension = (string) $value;

        return $this;
    }

    /**
     * Get the handle of the custom importer (empty string if not available).
     *
     * @return string
     */
    public function getCustomImporter()
    {
        return $this->customImporter;
    }

    /**
     * Set the handle of the custom importer (empty string if not available).
     *
     * @param string $value
     *
     * @return static Returns the class instance itself for method chaining
     */
    public function setCustomImporter($value)
    {
        $this->customImporter = (string) $value;

        return $this;
    }

    /**
     * Get the handle of the editor (empty string if not available).
     *
     * @return string
     */
    public function getEditor()
    {
        return $this->editor;
    }

    /**
     * Set the handle of the editor (empty string if not available).
     *
     * @param string $value
     *
     * @return static Returns the class instance itself for method chaining
     */
    public function setEditor($value)
    {
        $this->editor = (string) $value;

        return $this;
    }

    /**
     * Get the handle of the inline viewer (empty string if not available).
     *
     * @return string
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * Set the handle of the inline viewer (empty string if not available).
     *
     * @param string $value
     *
     * @return static Returns the class instance itself for method chaining
     */
    public function setView($value)
    {
        $this->view = (string) $value;

        return $this;
    }

    /**
     * Get the inspector for this file type (if available).
     *
     * @return \Concrete\Core\File\Type\Inspector\Inspector|null
     */
    public function getCustomInspector()
    {
        $importer = $this->getCustomImporter();
        if ($importer === '') {
            $result = null;
        } else {
            $name = camelcase($importer) . 'Inspector';
            $class = overrideable_core_class('Core\\File\\Type\\Inspector\\' . $name, 'File/Type/Inspector/' . $name . '.php', $this->getPackageHandle());
            $app = Application::getFacadeApplication();
            $result = $app->make($class);
        }

        return $result;
    }

    // Helper functions

    /**
     * Get the list of all the file extensions used by approved file versions.
     *
     * @return string[]
     */
    public static function getUsedExtensionList()
    {
        $app = Application::getFacadeApplication();
        $db = $app->make('database')->connection();
        $stm = $db->executeQuery("select distinct fvExtension from FileVersions where fvIsApproved = 1 and fvExtension is not null and fvExtension <> ''");
        $extensions = [];
        while ($row = $stm->fetch()) {
            $extensions[] = $row['fvExtension'];
        }

        return $extensions;
    }

    /**
     * Get the list of all the file types used by approved file versions.
     *
     * @return int[]
     */
    public static function getUsedTypeList()
    {
        $app = Application::getFacadeApplication();
        $db = $app->make('database')->connection();
        $stm = $db->query('select distinct fvType from FileVersions where fvIsApproved = 1 and fvType is not null and fvType <> 0');
        $types = [];
        while ($row = $stm->fetch()) {
            $types[] = (int) $row['fvType'];
        }

        return $types;
    }

    /**
     * Get the list of all the available type IDs.
     *
     * @return int[]
     */
    public static function getTypeList()
    {
        return [
            static::T_DOCUMENT,
            static::T_IMAGE,
            static::T_VIDEO,
            static::T_AUDIO,
            static::T_TEXT,
            static::T_APPLICATION,
            static::T_UNKNOWN,
        ];
    }

    /**
     * Returns a thumbnail for this type of file.
     *
     * @param bool $fullImageTag Set to true to retrieve the full HTML image tag, false to just retrieve the image URL
     *
     * @return string
     */
    public function getThumbnail($fullImageTag = true)
    {
        $app = Application::getFacadeApplication();
        $config = $app->make('config');
        $type = ThumbnailType::getByHandle($config->get('concrete.icons.file_manager_listing.handle'));
        if (file_exists(DIR_AL_ICONS . '/' . $this->getExtension() . '.svg')) {
            $url = REL_DIR_AL_ICONS . '/' . $this->getExtension() . '.svg';
        } else {
            $url = AL_ICON_DEFAULT;
        }
        if ($fullImageTag == true) {
            return sprintf(
                '<img src="%s" width="%s" height="%s" class="img-responsive ccm-generic-thumbnail" alt="%s">',
                $url,
                $type->getWidth(),
                $type->getHeight(),
                t(/* i18n: %s is a file type */ '%s file icon', mb_strtoupper($this->getExtension()))
            );
        } else {
            return $url;
        }
    }

    /**
     * Does the file type support thumbnails.
     *
     * @return bool|null Return true if the type supports thumbnails, null otherwise
     */
    public function supportsThumbnails()
    {
        $typeName = strtoupper($this->getName());
        if ($typeName == 'PNG' || $typeName == 'JPEG'|| $typeName == 'GIF'|| $typeName == 'WEBP') {
            return true;
        }
    }

    /**
     * Is the file type an SVG.
     *
     * @return bool|null Return true if the type is an SVG, null otherwise
     */
    public function isSVG()
    {
        $typeName = strtoupper($this->getName());
        if ($typeName == 'SVG') {
            return true;
        }
    }

    /**
     * Is the file type an SVG.
     *
     * @return bool|null Return true if the type is an SVG, null otherwise
     */
    public function isWEBP()
    {
        $typeName = strtoupper($this->getName());
        if ($typeName == 'WEBP') {
            return true;
        }
    }

    /**
     * Get the name of a generic file type.
     *
     * @param int $type Generic category type (one of the \Concrete\Core\File\Type\Type::T_... constants)
     *
     * @return string|null Returns null if the type is not recognized, its translated display name otherwise
     */
    public static function getGenericTypeText($type)
    {
        switch ($type) {
            case static::T_IMAGE:
                return t('Image');
                break;
            case static::T_VIDEO:
                return t('Video');
                break;
            case static::T_TEXT:
                return t('Text');
                break;
            case static::T_AUDIO:
                return t('Audio');
                break;
            case static::T_DOCUMENT:
                return t('Document');
                break;
            case static::T_APPLICATION:
                return t('Application');
                break;
            case static::T_UNKNOWN:
                return t('File');
                break;

        }
    }
}
