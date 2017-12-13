<?php

namespace Concrete\Core\Entity\File;

use Carbon\Carbon;
use Concrete\Core\Attribute\Category\FileCategory;
use Concrete\Core\Attribute\Key\FileKey;
use Concrete\Core\Attribute\ObjectInterface;
use Concrete\Core\Attribute\ObjectTrait;
use Concrete\Core\Entity\Attribute\Value\FileValue;
use Concrete\Core\Entity\File\StorageLocation\StorageLocation;
use Concrete\Core\File\Exception\InvalidDimensionException;
use Concrete\Core\File\Image\Thumbnail\Path\Resolver;
use Concrete\Core\File\Image\Thumbnail\Thumbnail;
use Concrete\Core\File\Image\Thumbnail\ThumbnailFormatService;
use Concrete\Core\File\Image\Thumbnail\Type\Type;
use Concrete\Core\File\Image\Thumbnail\Type\Version as ThumbnailTypeVersion;
use Concrete\Core\File\Importer;
use Concrete\Core\File\Menu;
use Concrete\Core\File\Type\TypeList as FileTypeList;
use Concrete\Core\Http\FlysystemFileResponse;
use Concrete\Core\Support\Facade\Application;
use Core;
use Database;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Events;
use Imagine\Exception\NotSupportedException;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\Metadata\ExifMetadataReader;
use League\Flysystem\AdapterInterface;
use League\Flysystem\FileNotFoundException;
use Page;
use Permissions;
use stdClass;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use User;
use View;

/**
 * Represents a version of a file.
 *
 * @ORM\Entity
 * @ORM\Table(
 *     name="FileVersions",
 *     indexes={
 *         @ORM\Index(name="fvFilename", columns={"fvFilename"}),
 *         @ORM\Index(name="fvExtension", columns={"fvExtension"}),
 *         @ORM\Index(name="fvType", columns={"fvType"})
 *     }
 * )
 */
class Version implements ObjectInterface
{
    use ObjectTrait;

    /**
     * Update type: file replaced.
     *
     * @var int
     */
    const UT_REPLACE_FILE = 1;

    /**
     * Update type: title updated.
     *
     * @var int
     */
    const UT_TITLE = 2;

    /**
     * Update type: description updated.
     *
     * @var int
     */
    const UT_DESCRIPTION = 3;

    /**
     * Update type: tags modified.
     *
     * @var int
     */
    const UT_TAGS = 4;

    /**
     * Update type: extended attributes changed.
     *
     * @var int
     */
    const UT_EXTENDED_ATTRIBUTE = 5;

    /**
     * Update type: contents changed.
     *
     * @var int
     */
    const UT_CONTENTS = 6;

    /**
     * Update type: file version renamed.
     *
     * @var int
     */
    const UT_RENAME = 7;

    /**
     * The associated File instance.
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="File", inversedBy="versions")
     * @ORM\JoinColumn(name="fID", referencedColumnName="fID")
     *
     * @var \Concrete\Core\Entity\File\File
     */
    protected $file;

    /**
     * The progressive file version identifier.
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    protected $fvID = 0;

    /**
     * The name of the file.
     *
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $fvFilename = null;

    /**
     * The path prefix used to store the file in the file system.
     *
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string
     */
    protected $fvPrefix;

    /**
     * The date/time when the file version has been added.
     *
     * @ORM\Column(type="datetime")
     *
     * @var DateTime
     */
    protected $fvDateAdded;

    /**
     * The date/time when the file version has been approved.
     *
     * @ORM\Column(type="datetime")
     *
     * @var DateTime
     */
    protected $fvActivateDateTime;

    /**
     * Is this version the approved one for the associated file?
     *
     * @ORM\Column(type="boolean")
     *
     * @var bool
     */
    protected $fvIsApproved = false;

    /**
     * The ID of the user that created the file version.
     *
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    protected $fvAuthorUID = 0;

    /**
     * The size (in bytes) of the file version.
     *
     * @ORM\Column(type="bigint")
     *
     * @var int
     */
    protected $fvSize = 0;

    /**
     * The ID of the user that approved the file version.
     *
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    protected $fvApproverUID = 0;

    /**
     * The title of the file version.
     *
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string|null
     */
    protected $fvTitle = null;

    /**
     * The description of the file version.
     *
     * @ORM\Column(type="text", nullable=true)
     *
     * @var string|null
     */
    protected $fvDescription = null;

    /**
     * The extension of the file version.
     *
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string|null
     */
    protected $fvExtension = null;

    /**
     * The type of the file version.
     *
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    protected $fvType = 0;

    /**
     * The tags assigned to the file version (separated by a newline character - '\n').
     *
     * @ORM\Column(type="text", nullable=true)
     *
     * @var string|null
     */
    protected $fvTags = null;

    /**
     * Does this file version has a thumbnail to be used for file listing?
     *
     * @ORM\Column(type="boolean")
     *
     * @var bool
     */
    protected $fvHasListingThumbnail = false;

    /**
     * Does this file version has a thumbnail to be used used for details?
     *
     * @ORM\Column(type="boolean")
     *
     * @var bool
     */
    protected $fvHasDetailThumbnail = false;

    /**
     * The currently loaded Image instance.
     *
     * @var \Imagine\Image\ImageInterface|false|null null: still not loaded; false: load failed; ImageInterface otherwise
     */
    private $imagineImage = null;

    /**
     * Initialize the instance.
     */
    public function __construct()
    {
        $this->fvDateAdded = new DateTime();
        $this->fvActivateDateTime = new DateTime();
    }

    /**
     * Add a new file version.
     *
     * @param File $file the File instance associated to this version
     * @param string $filename The name of the file
     * @param string $prefix the path prefix used to store the file in the file system
     * @param array $data Valid array keys are {
     *
     *     @var int|null $uID the ID of the user that creates the file version (if not specified or empty: we'll assume the currently user logged in user)
     *     @var string $fvTitle the title of the file version
     *     @var string $fvDescription the description of the file version
     *     @var string $fvTags the tags to be assigned to the file version (separated by newlines and/or commas)
     *     @var bool $fvIsApproved Is this version the approved one for the associated file? (default: true)
     * }
     *
     * @return static
     */
    public static function add(\Concrete\Core\Entity\File\File $file, $filename, $prefix, $data = [])
    {
        $u = new User();
        $uID = (isset($data['uID']) && $data['uID'] > 0) ? $data['uID'] : $u->getUserID();

        if ($uID < 1) {
            $uID = 0;
        }

        $fvTitle = (isset($data['fvTitle'])) ? $data['fvTitle'] : '';
        $fvDescription = (isset($data['fvDescription'])) ? $data['fvDescription'] : '';
        $fvTags = (isset($data['fvTags'])) ? self::cleanTags($data['fvTags']) : '';
        $fvIsApproved = (isset($data['fvIsApproved'])) ? $data['fvIsApproved'] : '1';

        $dh = Core::make('helper/date');
        $date = new Carbon($dh->getOverridableNow());

        $fv = new static();
        $fv->fvFilename = $filename;
        $fv->fvPrefix = $prefix;
        $fv->fvDateAdded = $date;
        $fv->fvIsApproved = (bool) $fvIsApproved;
        $fv->fvApproverUID = $uID;
        $fv->fvAuthorUID = $uID;
        $fv->fvActivateDateTime = $date;
        $fv->fvTitle = $fvTitle;
        $fv->fvDescription = $fvDescription;
        $fv->fvTags = $fvTags;
        $fv->file = $file;
        $fv->fvID = 1;

        $em = \ORM::entityManager();
        $em->persist($fv);
        $em->flush();

        $fve = new \Concrete\Core\File\Event\FileVersion($fv);
        Events::dispatch('on_file_version_add', $fve);

        return $fv;
    }

    /**
     * Normalize the tags separator, remove empty tags.
     *
     * @param string $tagsStr The list of tags, delimited by '\n', '\r' or ','
     *
     * @return string
     */
    public static function cleanTags($tagsStr)
    {
        $tagsArray = explode("\n", str_replace(["\r", ','], "\n", $tagsStr));
        $cleanTags = [];
        foreach ($tagsArray as $tag) {
            if (!strlen(trim($tag))) {
                continue;
            }
            $cleanTags[] = trim($tag);
        }
        //the leading and trailing line break char is for searching: fvTag like %\ntag\n%
        return "\n" . implode("\n", $cleanTags) . "\n";
    }

    /**
     * Set the name of the file.
     *
     * @param string $filename
     */
    public function setFilename($filename)
    {
        $this->fvFilename = $filename;
    }

    /**
     * Get the path prefix used to store the file in the file system.
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->fvPrefix;
    }

    /**
     * Is this version the approved one for the associated file?
     *
     * @return bool
     */
    public function isApproved()
    {
        return $this->fvIsApproved;
    }

    /**
     * Get the the tags assigned to the file version (as a list of strings).
     *
     * @return string[]
     */
    public function getTagsList()
    {
        $tags = explode("\n", str_replace("\r", "\n", trim($this->getTags())));
        $clean_tags = [];
        foreach ($tags as $tag) {
            if (strlen(trim($tag))) {
                $clean_tags[] = trim($tag);
            }
        }

        return $clean_tags;
    }

    /**
     * Get the the tags assigned to the file version (one tag per line - lines are separated by '\n').
     *
     * @return null|string
     */
    public function getTags()
    {
        return $this->fvTags;
    }

    /**
     * Get the associated File instance.
     *
     * @return \Concrete\Core\Entity\File\File
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set the associated File instance.
     *
     * @param \Concrete\Core\Entity\File\File $file
     */
    public function setFile(\Concrete\Core\Entity\File\File $file)
    {
        $this->file = $file;
    }

    /**
     * Get the ID of the associated file instance.
     *
     * @return int
     */
    public function getFileID()
    {
        return $this->file->getFileID();
    }

    /**
     * Get the progressive file version identifier.
     *
     * @return int
     */
    public function getFileVersionID()
    {
        return $this->fvID;
    }

    /**
     * Delete this version of the file.
     *
     * @param bool $deleteFilesAndThumbnails should we delete the actual file and the thumbnails?
     */
    public function delete($deleteFilesAndThumbnails = false)
    {
        $db = Database::get();

        $category = $this->getObjectAttributeCategory();

        foreach ($this->getAttributes() as $attribute) {
            $category->deleteValue($attribute);
        }

        $db->Execute('DELETE FROM FileVersionLog WHERE fID = ? AND fvID = ?', [$this->getFileID(), $this->fvID]);

        $types = Type::getVersionList();

        if ($deleteFilesAndThumbnails) {
            try {
                foreach ($types as $type) {
                    $this->deleteThumbnail($type);
                }

                $fsl = $this->getFile()->getFileStorageLocationObject()->getFileSystemObject();
                $fre = $this->getFileResource();
                if ($fsl->has($fre->getPath())) {
                    $fsl->delete($fre->getPath());
                }
            } catch (FileNotFoundException $e) {
            }
        }

        $em = \ORM::entityManager();
        $em->remove($this);
        $em->flush();
    }

    /**
     * Delete the thumbnail for a specific thumbnail type version.
     *
     * @param \Concrete\Core\File\Image\Thumbnail\Type\Version|string $type the thumbnail type version (or its handle)
     */
    public function deleteThumbnail($type)
    {
        if (!($type instanceof ThumbnailTypeVersion)) {
            $type = ThumbnailTypeVersion::getByHandle($type);
        }
        $fsl = $this->getFile()->getFileStorageLocationObject()->getFileSystemObject();
        $path = $type->getFilePath($this);
        if ($fsl->has($path)) {
            $fsl->delete($path);
        }
    }

    /**
     * Copy the thumbnail of a specific thumbnail type version from another file version (useful for instance when duplicating a file).
     *
     * @param \Concrete\Core\File\Image\Thumbnail\Type\Version|string $type the thumbnail type version (or its handle)
     * @param Version $source The File Version instance to copy the thumbnail from
     */
    public function duplicateUnderlyingThumbnailFiles($type, Version $source)
    {
        if (!($type instanceof ThumbnailTypeVersion)) {
            $type = ThumbnailTypeVersion::getByHandle($type);
        }
        $new = $this->getFile()->getFileStorageLocationObject()->getFileSystemObject();
        $current = $source->getFile()->getFileStorageLocationObject()->getFileSystemObject();
        $newPath = $type->getFilePath($this);
        $currentPath = $type->getFilePath($source);
        $manager = new \League\Flysystem\MountManager([
            'current' => $current,
            'new' => $new,
        ]);
        try {
            $manager->copy('current://' . $currentPath, 'new://' . $newPath);
        } catch (FileNotFoundException $e) {
        }
    }

    /**
     * Move the thumbnail of a specific thumbnail type version to a new storage location.
     *
     * @param \Concrete\Core\File\Image\Thumbnail\Type\Version|string $type the thumbnail type version (or its handle)
     * @param StorageLocation $location The destination storage location
     */
    public function updateThumbnailStorageLocation($type, StorageLocation $location)
    {
        if (!($type instanceof ThumbnailTypeVersion)) {
            $type = ThumbnailTypeVersion::getByHandle($type);
        }
        $fsl = $this->getFile()->getFileStorageLocationObject()->getFileSystemObject();
        $path = $type->getFilePath($this);
        $manager = new \League\Flysystem\MountManager([
            'current' => $fsl,
            'new' => $location->getFileSystemObject(),
        ]);
        try {
            $manager->move('current://' . $path, 'new://' . $path);
        } catch (FileNotFoundException $e) {
        }
    }

    /**
     * Get an abstract object to work with the actual file resource (note: this is NOT a concrete5 File object).
     *
     * @return \League\Flysystem\File
     */
    public function getFileResource()
    {
        $cf = Core::make('helper/concrete/file');
        $fs = $this->getFile()->getFileStorageLocationObject()->getFileSystemObject();
        $fo = $fs->get($cf->prefix($this->fvPrefix, $this->fvFilename));

        return $fo;
    }

    /**
     * Get the mime type of the file if known.
     *
     * @return string|false
     */
    public function getMimeType()
    {
        $fre = $this->getFileResource();

        return $fre->getMimetype();
    }

    /**
     * Get the formatted file size.
     *
     * @return string
     *
     * @example 123 KB
     */
    public function getSize()
    {
        return Core::make('helper/number')->formatSize($this->fvSize, 'KB');
    }

    /**
     * Get the file size of the file (in bytes).
     *
     * @return int
     */
    public function getFullSize()
    {
        return $this->fvSize;
    }

    /**
     * Get the username of the user that created the file version (or "Unknown").
     *
     * @return string
     */
    public function getAuthorName()
    {
        $ui = \UserInfo::getByID($this->fvAuthorUID);
        if (is_object($ui)) {
            return $ui->getUserDisplayName();
        }

        return t('(Unknown)');
    }

    /**
     * Get the ID of the user that created the file version.
     *
     * @return int
     */
    public function getAuthorUserID()
    {
        return $this->fvAuthorUID;
    }

    /**
     * Get the date/time when the file version has been added.
     *
     * @return string
     *
     * @example '2017-31-12 23:59:59'
     */
    public function getDateAdded()
    {
        return $this->fvDateAdded;
    }

    /**
     * Get the extension of the file version.
     *
     * @return null|string
     */
    public function getExtension()
    {
        return $this->fvExtension;
    }

    /**
     * Set the progressive file version identifier.
     *
     * @param int $fvID
     */
    public function setFileVersionID($fvID)
    {
        $this->fvID = $fvID;
    }

    /**
     * Create a new copy of this file version.
     * The new Version instance will have the current user as the author (if available), and a new version ID.
     *
     * @return Version
     */
    public function duplicate()
    {
        $em = \ORM::entityManager();
        $qq = $em->createQuery('SELECT max(v.fvID) FROM \Concrete\Core\Entity\File\Version v where v.file = :file');
        $qq->setParameter('file', $this->file);
        $fvID = $qq->getSingleScalarResult();
        ++$fvID;

        $fv = clone $this;
        $fv->fvID = $fvID;
        $fv->fvIsApproved = false;
        $fv->fvDateAdded = new DateTime();
        $uID = (int) (new User())->getUserID();
        if ($uID !== 0) {
            $fv->fvAuthorUID = $uID;
        }

        $em->persist($fv);

        $this->deny();

        foreach ($this->getAttributes() as $value) {
            $value = clone $value;
            /*
             * @var $value AttributeValue
             */
            $value->setVersion($fv);
            $em->persist($value);
        }

        $em->flush();

        $fe = new \Concrete\Core\File\Event\FileVersion($fv);
        Events::dispatch('on_file_version_duplicate', $fe);

        return $fv;
    }

    /**
     * Mark this file version as not approved.
     */
    public function deny()
    {
        $this->fvIsApproved = false;
        $this->save();
        $fe = new \Concrete\Core\File\Event\FileVersion($this);
        Events::dispatch('on_file_version_deny', $fe);
    }

    /**
     * Get the name of the file type.
     *
     * @return string
     */
    public function getType()
    {
        $ftl = $this->getTypeObject();

        return $ftl->getName();
    }

    /**
     * Get the localized name of the file type.
     *
     * @return string
     */
    public function getDisplayType()
    {
        $ftl = $this->getTypeObject();

        return $ftl->getDisplayName();
    }

    /**
     * Get the type of the file.
     *
     * @return \Concrete\Core\File\Type\Type
     */
    public function getTypeObject()
    {
        $fh = Core::make('helper/file');
        $ext = $fh->getExtension($this->fvFilename);

        $ftl = FileTypeList::getType($ext);

        return $ftl;
    }

    /**
     * Get an array containing human-readable descriptions of everything that happened to this file version.
     *
     * @return string[]
     */
    public function getVersionLogComments()
    {
        $updates = [];
        $db = Database::get();
        $ga = $db->GetAll(
            'SELECT fvUpdateTypeID, fvUpdateTypeAttributeID FROM FileVersionLog WHERE fID = ? AND fvID = ? ORDER BY fvlID ASC',
            [$this->getFileID(), $this->getFileVersionID()]
        );
        foreach ($ga as $a) {
            switch ($a['fvUpdateTypeID']) {
                case self::UT_REPLACE_FILE:
                    $updates[] = t('File');
                    break;
                case self::UT_TITLE:
                    $updates[] = t('Title');
                    break;
                case self::UT_DESCRIPTION:
                    $updates[] = t('Description');
                    break;
                case self::UT_TAGS:
                    $updates[] = t('Tags');
                    break;
                case self::UT_CONTENTS:
                    $updates[] = t('File Content');
                    break;
                case self::UT_RENAME:
                    $updates[] = t('File Name');
                    break;
                case self::UT_EXTENDED_ATTRIBUTE:
                    $val = $db->GetOne(
                        'SELECT akName FROM AttributeKeys WHERE akID = ?',
                        [$a['fvUpdateTypeAttributeID']]
                    );
                    if ($val != '') {
                        $updates[] = $val;
                    }
                    break;
            }
        }
        $updates = array_unique($updates);
        $updates1 = [];
        foreach ($updates as $val) {
            // normalize the keys
            $updates1[] = $val;
        }

        return $updates1;
    }

    /**
     * Update the title of the file.
     *
     * @param string $title
     */
    public function updateTitle($title)
    {
        $this->fvTitle = $title;
        $this->save();
        $this->logVersionUpdate(self::UT_TITLE);
        $fe = new \Concrete\Core\File\Event\FileVersion($this);
        Events::dispatch('on_file_version_update_title', $fe);
    }

    /**
     * Duplicate the underlying file and assign its new position to this instance.
     */
    public function duplicateUnderlyingFile()
    {
        $importer = new Importer();
        $fi = Core::make('helper/file');
        $cf = Core::make('helper/concrete/file');
        $filesystem = $this->getFile()->
            getFileStorageLocationObject()->getFileSystemObject();
        do {
            $prefix = $importer->generatePrefix();
            $path = $cf->prefix($prefix, $this->getFilename());
        } while ($filesystem->has($path));
        $filesystem->write(
            $path,
            $this->getFileResource()->read(),
            [
                'visibility' => AdapterInterface::VISIBILITY_PUBLIC,
                'mimetype' => Core::make('helper/mime')->mimeFromExtension($fi->getExtension($this->getFilename())),
            ]
        );
        $this->updateFile($this->getFilename(), $prefix);
    }

    /**
     * Log updates to files.
     *
     * @param int $updateTypeID One of the Version::UT_... constants
     * @param int $updateTypeAttributeID the ID of the attribute that has been updated (if any - useful when $updateTypeID is UT_EXTENDED_ATTRIBUTE)
     */
    public function logVersionUpdate($updateTypeID, $updateTypeAttributeID = 0)
    {
        $db = Database::get();
        $db->Execute(
            'INSERT INTO FileVersionLog (fID, fvID, fvUpdateTypeID, fvUpdateTypeAttributeID) VALUES (?, ?, ?, ?)',
            [
                $this->getFileID(),
                $this->getFileVersionID(),
                $updateTypeID,
                $updateTypeAttributeID,
            ]
        );
    }

    /**
     * Update the tags associated to the file.
     *
     * @param string $tags List of tags separated by newlines and/or commas
     */
    public function updateTags($tags)
    {
        $tags = self::cleanTags($tags);
        $this->fvTags = $tags;
        $this->save();
        $this->logVersionUpdate(self::UT_TAGS);
        $fe = new \Concrete\Core\File\Event\FileVersion($this);
        Events::dispatch('on_file_version_update_tags', $fe);
    }

    /**
     * Update the description of the file.
     *
     * @param string $descr
     */
    public function updateDescription($descr)
    {
        $this->fvDescription = $descr;
        $this->save();
        $this->logVersionUpdate(self::UT_DESCRIPTION);
        $fe = new \Concrete\Core\File\Event\FileVersion($this);
        Events::dispatch('on_file_version_update_description', $fe);
    }

    /**
     * Rename the file.
     *
     * @param string $filename
     */
    public function rename($filename)
    {
        $cf = Core::make('helper/concrete/file');
        $storage = $this->getFile()->getFileStorageLocationObject();
        $oldFilename = $this->fvFilename;
        if (is_object($storage)) {
            $path = $cf->prefix($this->fvPrefix, $oldFilename);
            $newPath = $cf->prefix($this->fvPrefix, $filename);
            $filesystem = $storage->getFileSystemObject();
            if ($filesystem->has($path)) {
                $filesystem->rename($path, $newPath);
            }
            $this->fvFilename = $filename;
            if ($this->fvTitle == $oldFilename) {
                $this->fvTitle = $filename;
            }
            $this->logVersionUpdate(self::UT_RENAME);
            $this->save();
        }
    }

    /**
     * Update the contents of the file.
     *
     * @param string $contents
     */
    public function updateContents($contents)
    {
        $cf = Core::make('helper/concrete/file');
        $storage = $this->getFile()->getFileStorageLocationObject();
        if (is_object($storage)) {
            $path = $cf->prefix($this->fvPrefix, $this->fvFilename);
            $filesystem = $storage->getFileSystemObject();
            if ($filesystem->has($path)) {
                $filesystem->delete($path);
            }
            $filesystem->write($path, $contents);
            $this->logVersionUpdate(self::UT_CONTENTS);
            $fe = new \Concrete\Core\File\Event\FileVersion($this);
            Events::dispatch('on_file_version_update_contents', $fe);
            $this->refreshAttributes();
        }
    }

    /**
     * Update the filename and the path prefix of the file.
     *
     * @param string $filename The new name of file
     * @param string $prefix The new path prefix
     */
    public function updateFile($filename, $prefix)
    {
        $this->fvFilename = $filename;
        $this->fvPrefix = $prefix;
        $this->save();
        $this->logVersionUpdate(self::UT_REPLACE_FILE);
    }

    /**
     * Mark this file version as approved (and disapprove all the other versions of the file).
     * The currently logged in user (if any) will be stored as the approver.
     */
    public function approve()
    {
        foreach ($this->file->getFileVersions() as $fv) {
            $fv->fvIsApproved = false;
            $fv->save(false);
        }

        $this->fvIsApproved = true;
        $uID = (int) (new User())->getUserID();
        if ($uID !== 0) {
            $this->fvApproverUID = $uID;
        }
        $this->save();

        $fe = new \Concrete\Core\File\Event\FileVersion($this);
        Events::dispatch('on_file_version_approve', $fe);

        $fo = $this->getFile();
        $fo->reindex();

        Core::make('cache/request')->delete('file/version/approved/' . $this->getFileID());
    }

    /**
     * Get the contents of the file.
     *
     * @return string|null return NULL if the actual file does not exist or can't be read
     */
    public function getFileContents()
    {
        $cf = Core::make('helper/concrete/file');
        $fsl = $this->getFile()->getFileStorageLocationObject();
        if (is_object($fsl)) {
            $result = $fsl->getFileSystemObject()->read($cf->prefix($this->fvPrefix, $this->fvFilename));
            if ($result === false) {
                $result = null;
            }
        } else {
            $result = null;
        }

        return $result;
    }

    /**
     * Get an URL that can be used to download the file (it will force the download of all file types, even if the browser can display them).
     *
     * @return \League\URL\URLInterface
     */
    public function getForceDownloadURL()
    {
        $c = Page::getCurrentPage();
        $cID = ($c instanceof Page) ? $c->getCollectionID() : 0;

        return View::url('/download_file', 'force', $this->getFileID(), $cID);
    }

    /**
     * Send the file to the browser (forcing its download even if the browser can display it), and terminate the execution.
     */
    public function forceDownload()
    {
        session_write_close();
        $fre = $this->getFileResource();

        $fs = $this->getFile()->getFileStorageLocationObject()->getFileSystemObject();
        $response = new FlysystemFileResponse($fre->getPath(), $fs);

        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);
        $response->prepare(\Request::getInstance());

        ob_end_clean();
        $response->send();
        Core::shutdown();
        exit;
    }

    /**
     * Get the name of the file.
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->fvFilename;
    }

    /**
     * Get the path to the file relative to the webroot (may not exist).
     *
     * @return string|\League\URL\URLInterface|null
     *
     * @example /application/files/0000/0000/0000/file.png
     */
    public function getRelativePath()
    {
        $cf = Core::make('helper/concrete/file');
        $fsl = $this->getFile()->getFileStorageLocationObject();
        $url = null;
        if (is_object($fsl)) {
            $configuration = $fsl->getConfigurationObject();
            if ($configuration->hasRelativePath()) {
                $url = $configuration->getRelativePathToFile($cf->prefix($this->fvPrefix, $this->fvFilename));
            }
            if ($configuration->hasPublicURL() && !$url) {
                $url = $configuration->getPublicURLToFile($cf->prefix($this->fvPrefix, $this->fvFilename));
            }
            if (!$url) {
                $url = $this->getDownloadURL();
            }
        }

        return $url;
    }

    /**
     * Get the list of all the thumbnails.
     *
     * @throws \Concrete\Core\File\Exception\InvalidDimensionException
     *
     * @return \Concrete\Core\File\Image\Thumbnail\Thumbnail[]
     */
    public function getThumbnails()
    {
        $thumbnails = [];
        $types = Type::getVersionList();
        $width = $this->getAttribute('width');
        $height = $this->getAttribute('height');
        $file = $this->getFile();

        if (!$width || $width < 0) {
            throw new InvalidDimensionException($this->getFile(), $this, t('Invalid dimensions.'));
        }

        foreach ($types as $type) {
            if ($width < $type->getWidth()) {
                continue;
            }

            if ($width == $type->getWidth() && (!$type->getHeight() || $height <= $type->getHeight())) {
                continue;
            }

            $thumbnailPath = $type->getFilePath($this);
            $location = $file->getFileStorageLocationObject();
            $configuration = $location->getConfigurationObject();
            $filesystem = $location->getFileSystemObject();
            if ($filesystem->has($thumbnailPath)) {
                $thumbnails[] = new Thumbnail($type, $configuration->getPublicURLToFile($thumbnailPath));
            }
        }

        return $thumbnails;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\ObjectInterface::getObjectAttributeCategory()
     *
     * @return \Concrete\Core\Attribute\Category\FileCategory
     */
    public function getObjectAttributeCategory()
    {
        return Core::make(FileCategory::class);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\ObjectInterface::getAttributeValueObject()
     *
     * @return \Concrete\Core\Entity\Attribute\Value\FileValue|null
     */
    public function getAttributeValueObject($ak, $createIfNotExists = false)
    {
        if (!is_object($ak)) {
            $ak = FileKey::getByHandle($ak);
        }
        $value = false;
        if (is_object($ak)) {
            $value = $this->getObjectAttributeCategory()->getAttributeValue($ak, $this);
        }

        if ($value) {
            return $value;
        } elseif ($createIfNotExists) {
            if (!is_object($ak)) {
                $ak = FileKey::getByHandle($ak);
            }
            $attributeValue = new FileValue();
            $attributeValue->setVersion($this);
            $attributeValue->setAttributeKey($ak);

            return $attributeValue;
        }
    }

    /**
     * Get an \Imagine\Image\ImageInterface representing the image.
     *
     * @return \Imagine\Image\ImageInterface|false return false if the image coulnd't be read, an ImageInterface otherwise
     */
    public function getImagineImage()
    {
        if (null === $this->imagineImage) {
            $resource = $this->getFileResource();
            $mimetype = $resource->getMimeType();
            $imageLibrary = \Image::getFacadeRoot();

            switch ($mimetype) {
                case 'image/svg+xml':
                case 'image/svg-xml':
                    if ($imageLibrary instanceof \Imagine\Gd\Imagine) {
                        try {
                            $app = Application::getFacadeApplication();
                            $imageLibrary = $app->make('image/imagick');
                        } catch (\Exception $x) {
                            $this->imagineImage = false;
                        }
                    }
                    break;
            }

            $metadataReader = $imageLibrary->getMetadataReader();
            if (!$metadataReader instanceof ExifMetadataReader) {
                if (\Config::get('concrete.file_manager.images.use_exif_data_to_rotate_images')) {
                    try {
                        $imageLibrary->setMetadataReader(new ExifMetadataReader());
                    } catch (NotSupportedException $e) {
                    }
                }
            }

            $this->imagineImage = $imageLibrary->load($resource->read());
        }

        return $this->imagineImage;
    }

    /**
     * Unload the loaded Image instance.
     */
    public function releaseImagineImage()
    {
        $this->imagineImage = null;
    }

    /**
     * Delete and re-create all the thumbnail types (only applicable to image files).
     *
     * @return bool|null return false on failure
     */
    public function rescanThumbnails()
    {
        if ($this->fvType != \Concrete\Core\File\Type\Type::T_IMAGE) {
            return false;
        }

        $imagewidth = $this->getAttribute('width');
        $imageheight = $this->getAttribute('height');
        $types = Type::getVersionList();

        try {
            $image = $this->getImagineImage();

            if ($image) {
                if (!$imagewidth) {
                    $imagewidth = $image->getSize()->getWidth();
                }
                if (!$imageheight) {
                    $imageheight = $image->getSize()->getHeight();
                }

                foreach ($types as $type) {
                    // delete the file if it exists
                    $this->deleteThumbnail($type);

                    // if image is smaller than size requested, don't create thumbnail
                    if ($imagewidth < $type->getWidth() && $imageheight < $type->getHeight()) {
                        continue;
                    }

                    // This should not happen as it is not allowed when creating thumbnail types and both width and heght
                    // are required for Exact sizing but it's here just in case
                    if ($type->getSizingMode() === Type::RESIZE_EXACT && (!$type->getWidth() || !$type->getHeight())) {
                        continue;
                    }

                    // If requesting an exact size and any of the dimensions requested is larger than the image's
                    // don't process as we won't get an exact size
                    if ($type->getSizingMode() === Type::RESIZE_EXACT && ($imagewidth < $type->getWidth() || $imageheight < $type->getHeight())) {
                        continue;
                    }

                    // if image is the same width as thumbnail, and there's no thumbnail height set,
                    // or if a thumbnail height set and the image has a smaller or equal height, don't create thumbnail
                    if ($imagewidth == $type->getWidth() && (!$type->getHeight() || $imageheight <= $type->getHeight())) {
                        continue;
                    }

                    // if image is the same height as thumbnail, and there's no thumbnail width set,
                    // or if a thumbnail width set and the image has a smaller or equal width, don't create thumbnail
                    if ($imageheight == $type->getHeight() && (!$type->getWidth() || $imagewidth <= $type->getWidth())) {
                        continue;
                    }

                    // otherwise file is bigger than thumbnail in some way, proceed to create thumbnail
                    $this->generateThumbnail($type);
                }
            }
            unset($image);
            $this->releaseImagineImage();
        } catch (\Imagine\Exception\InvalidArgumentException $e) {
            unset($image);
            $this->releaseImagineImage();

            return false;
        } catch (\Imagine\Exception\RuntimeException $e) {
            unset($image);
            $this->releaseImagineImage();

            return false;
        }
    }

    /**
     * @deprecated
     *
     * @param $level
     *
     * @return bool
     */
    public function hasThumbnail($level)
    {
        switch ($level) {
            case 1:
                return $this->fvHasListingThumbnail;
            case 2:
                return $this->fvHasDetailThumbnail;
        }

        return false;
    }

    /**
     * Get the HTML that renders the thumbnail for the details (a generic type icon if the thumbnail does not exist).
     * Return the thumbnail for an image or a generic type icon for a file.
     *
     * @return string
     */
    public function getDetailThumbnailImage()
    {
        if ($this->fvHasDetailThumbnail) {
            $type = Type::getByHandle(\Config::get('concrete.icons.file_manager_detail.handle'));
            $baseSrc = $this->getThumbnailURL($type->getBaseVersion());
            $doubledSrc = $this->getThumbnailURL($type->getDoubledVersion());

            return '<img src="' . $baseSrc . '" data-at2x="' . $doubledSrc . '" />';
        } else {
            return $this->getTypeObject()->getThumbnail();
        }
    }

    /**
     * Resolve a path using the default core path resolver.
     * Avoid using this method when you have access to your a resolver instance.
     *
     * @param \Concrete\Core\File\Image\Thumbnail\Type\Version|string $type the thumbnail type version (or its handle)
     *
     * @return \League\URL\URLInterface|string|null
     *
     * @example /application/files/thumbnails/file_manager_listing/0000/0000/0000/file.png
     */
    public function getThumbnailURL($type)
    {
        $app = Application::getFacadeApplication();

        if (!($type instanceof ThumbnailTypeVersion)) {
            $type = ThumbnailTypeVersion::getByHandle($type);
        }

        $path_resolver = $app->make(Resolver::class);

        if ($path = $path_resolver->getPath($this, $type)) {
            return $path;
        }

        return $this->getURL();
    }

    /**
     * Import an existing file as a thumbnail type version.
     *
     * @param \Concrete\Core\File\Image\Thumbnail\Type\Version $version
     * @param string $path
     */
    public function importThumbnail(ThumbnailTypeVersion $version, $path)
    {
        $thumbnailPath = $version->getFilePath($this);
        $filesystem = $this->getFile()
            ->getFileStorageLocationObject()
            ->getFileSystemObject();
        if ($filesystem->has($thumbnailPath)) {
            $filesystem->delete($thumbnailPath);
        }

        $filesystem->write(
            $thumbnailPath,
            file_get_contents($path),
            [
                'visibility' => AdapterInterface::VISIBILITY_PUBLIC,
                'mimetype' => 'image/jpeg',
            ]
        );

        if ($version->getHandle() == \Config::get('concrete.icons.file_manager_listing.handle')) {
            $this->fvHasListingThumbnail = true;
        }

        if ($version->getHandle() == \Config::get('concrete.icons.file_manager_detail.handle')) {
            $this->fvHasDetailThumbnail = true;
        }

        $this->save();
    }

    /**
     * Get an URL that points to the file on disk (if not available, you'll get the result of the getDownloadURL method).
     *
     * @return \League\URL\URLInterface|string|null Url to a file
     */
    public function getURL()
    {
        $cf = Core::make('helper/concrete/file');
        $fsl = $this->getFile()->getFileStorageLocationObject();
        if (is_object($fsl)) {
            $configuration = $fsl->getConfigurationObject();
            if ($configuration->hasPublicURL()) {
                return $configuration->getPublicURLToFile($cf->prefix($this->fvPrefix, $this->fvFilename));
            } else {
                return $this->getDownloadURL();
            }
        }
    }

    /**
     * Get an URL that can be used to download the file.
     * This passes through the download_file single page.
     *
     * @return \League\URL\URLInterface
     */
    public function getDownloadURL()
    {
        $c = Page::getCurrentPage();
        $cID = ($c instanceof Page) ? $c->getCollectionID() : 0;

        return View::url('/download_file', $this->getFileID(), $cID);
    }

    /**
     * Get the list of attributes associated to this file version.
     *
     * @return \Concrete\Core\Entity\Attribute\Value\FileValue[]
     */
    public function getAttributes()
    {
        return $this->getObjectAttributeCategory()->getAttributeValues($this);
    }

    /**
     * Rescan all the attributes of this file version.
     * This will run any type-based import routines, and store those attributes, generate thumbnails, etc...
     *
     * @param bool $rescanThumbnails Should thumbnails be rescanned as well?
     *
     * @return null|int Return one of the \Concrete\Core\File\Importer::E_... constants in case of errors, NULL otherwise.
     */
    public function refreshAttributes($rescanThumbnails = true)
    {
        $fh = Core::make('helper/file');
        $ext = $fh->getExtension($this->fvFilename);
        $ftl = FileTypeList::getType($ext);

        if (is_object($ftl)) {
            if ($ftl->getCustomImporter() != false) {
                $this->fvGenericType = $ftl->getGenericType();
                $cl = $ftl->getCustomInspector();
                $cl->inspect($this);
            }
        }

        \ORM::entityManager()->refresh($this);

        $fsr = $this->getFileResource();
        if (!$fsr->isFile()) {
            return Importer::E_FILE_INVALID;
        }

        $size = $fsr->getSize();

        $this->fvExtension = $ext;
        $this->fvType = $ftl->getGenericType();
        if ($this->fvTitle === null) {
            $this->fvTitle = $this->getFilename();
        }
        $this->fvSize = $size;

        if ($rescanThumbnails) {
            $this->rescanThumbnails();
        }

        $this->save();

        $f = $this->getFile();
        $f->reindex();
    }

    /**
     * Get the title of the file version.
     *
     * @return null|string
     */
    public function getTitle()
    {
        return $this->fvTitle;
    }

    /**
     * Get a representation of this Version instance that's easily serializable.
     *
     * @return stdClass A \stdClass instance with all the information about a file (including permissions)
     */
    public function getJSONObject()
    {
        $r = new stdClass();
        $fp = new Permissions($this->getFile());
        $r->canCopyFile = $fp->canCopyFile();
        $r->canEditFileProperties = $fp->canEditFileProperties();
        $r->canEditFilePermissions = $fp->canEditFilePermissions();
        $r->canDeleteFile = $fp->canDeleteFile();
        $r->canReplaceFile = $fp->canEditFileContents();
        $r->canEditFileContents = $fp->canEditFileContents();
        $r->canViewFileInFileManager = $fp->canRead();
        $r->canRead = $fp->canRead();
        $r->canViewFile = $this->canView();
        $r->canEditFile = $this->canEdit();
        $r->url = $this->getURL();
        $r->urlInline = (string) View::url('/download_file', 'view_inline', $this->getFileID());
        $r->urlDownload = (string) View::url('/download_file', 'view', $this->getFileID());
        $r->title = $this->getTitle();
        $r->genericTypeText = $this->getGenericTypeText();
        $r->description = $this->getDescription();
        $r->fileName = $this->getFilename();
        $r->resultsThumbnailImg = $this->getListingThumbnailImage();
        $r->fID = $this->getFileID();
        $r->treeNodeMenu = new Menu($this->getfile());

        return $r;
    }

    /**
     * Check of there is a viewer for the type of the file.
     *
     * @return bool
     */
    public function canView()
    {
        $to = $this->getTypeObject();
        if ($to->getView() != '') {
            return true;
        }

        return false;
    }

    /**
     * Check of there is an editor for the type of the file.
     *
     * @return bool
     */
    public function canEdit()
    {
        $to = $this->getTypeObject();
        if ($to->getEditor() != '') {
            return true;
        }

        return false;
    }

    /**
     * Get the localized name of the generic category type.
     *
     * @return string
     */
    public function getGenericTypeText()
    {
        $to = $this->getTypeObject();

        return $to->getGenericDisplayType();
    }

    /**
     * Get the description of the file version.
     *
     * @return null|string
     */
    public function getDescription()
    {
        return $this->fvDescription;
    }

    /**
     * Get the HTML that renders the thumbnail for the file listing (a generic type icon if the thumbnail does not exist).
     *
     * @return string
     */
    public function getListingThumbnailImage()
    {
        if ($this->fvHasListingThumbnail) {
            $type = Type::getByHandle(\Config::get('concrete.icons.file_manager_listing.handle'));
            $baseSrc = $this->getThumbnailURL($type->getBaseVersion());
            $doubledSrc = $this->getThumbnailURL($type->getDoubledVersion());

            return sprintf('<img class="ccm-file-manager-list-thumbnail" src="%s" data-at2x="%s">', $baseSrc, $doubledSrc);
        } else {
            return $this->getTypeObject()->getThumbnail();
        }
    }

    /**
     * Generate a thumbnail for a specific thumbnail type version.
     *
     * @param \Concrete\Core\File\Image\Thumbnail\Type\Version $type
     */
    public function generateThumbnail(ThumbnailTypeVersion $type)
    {
        $image = $this->getImagineImage();

        $filesystem = $this->getFile()
            ->getFileStorageLocationObject()
            ->getFileSystemObject();

        $height = $type->getHeight();
        $width = $type->getWidth();
        $sizingMode = $type->getSizingMode();

        if ($height && $width) {
            $size = new Box($width, $height);
        } elseif ($width) {
            $size = $image->getSize()->widen($width);
        } else {
            $size = $image->getSize()->heighten($height);
        }

        if ($sizingMode === Type::RESIZE_EXACT) {
            $thumbnailMode = ImageInterface::THUMBNAIL_OUTBOUND;
        } elseif ($sizingMode === Type::RESIZE_PROPORTIONAL) {
            $thumbnailMode = ImageInterface::THUMBNAIL_INSET;
        }

        // isCropped only exists on the CustomThumbnail type
        if (method_exists($type, 'isCropped') && $type->isCropped()) {
            $thumbnailMode = ImageInterface::THUMBNAIL_OUTBOUND;
        }

        $thumbnail = $image->thumbnail($size, $thumbnailMode);
        $thumbnailFormat = Core::make(ThumbnailFormatService::class)->getFormatForFile($this);
        $thumbnailPath = $type->getFilePath($this);
        $thumbnailOptions = [];

        switch ($thumbnailFormat) {
            case ThumbnailFormatService::FORMAT_JPEG:
                $mimetype = 'image/jpeg';
                $thumbnailOptions = ['jpeg_quality' => \Config::get('concrete.misc.default_jpeg_image_compression')];
                break;
            case ThumbnailFormatService::FORMAT_PNG:
            default:
                $mimetype = 'image/png';
                $thumbnailOptions = ['png_compression_level' => \Config::get('concrete.misc.default_png_image_compression')];
                break;
        }

        $filesystem->write(
            $thumbnailPath,
            $thumbnail->get($thumbnailFormat, $thumbnailOptions),
            [
                'visibility' => AdapterInterface::VISIBILITY_PUBLIC,
                'mimetype' => $mimetype,
            ]
        );

        if ($type->getHandle() == \Config::get('concrete.icons.file_manager_listing.handle')) {
            $this->fvHasListingThumbnail = true;
        }

        if ($type->getHandle() == \Config::get('concrete.icons.file_manager_detail.handle')) {
            $this->fvHasDetailThumbnail = true;
        }

        unset($size);
        unset($thumbnail);
        unset($filesystem);
    }

    /**
     * Save the instance changes.
     *
     * @param bool $flush Flush the EM cache?
     */
    protected function save($flush = true)
    {
        $em = \ORM::entityManager();
        $em->persist($this);
        if ($flush) {
            $em->flush();
        }
    }
}
