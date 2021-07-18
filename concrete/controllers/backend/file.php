<?php
namespace Concrete\Controller\Backend;

use Concrete\Core\Controller\Controller;
use Concrete\Core\Entity\File\File as FileEntity;
use Concrete\Core\Entity\File\Version as FileVersionEntity;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\File\EditResponse as FileEditResponse;
use Concrete\Core\File\Filesystem;
use Concrete\Core\File\Importer;
use Concrete\Core\File\ImportProcessor\AutorotateImageProcessor;
use Concrete\Core\File\ImportProcessor\ConstrainImageProcessor;
use Concrete\Core\File\Incoming;
use Concrete\Core\File\Service\VolatileDirectory;
use Concrete\Core\Foundation\Queue\QueueService;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Page\Page as CorePage;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Tree\Node\Type\FileFolder;
use Concrete\Core\Url\Url;
use Concrete\Core\View\View;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use FileSet;
use IPLib\Factory as IPFactory;
use IPLib\Range\Type as IPRangeType;
use Permissions as ConcretePermissions;
use RuntimeException;
use stdClass;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use ZipArchive;

class File extends Controller
{
    /**
     * The file to be replaced (if any).
     *
     * @var \Concrete\Core\Entity\File\File|null|false FALSE when uninitialized, NULL when none
     */
    private $fileToBeReplaced = false;

    /**
     * The destination folder where the uploaded files should be placed.
     *
     * @var \Concrete\Core\Tree\Node\Type\FileFolder|false FALSE when uninitialized
     */
    private $destinationFolder = false;

    /**
     * The original page to be used when importing files (if any).
     *
     * @var \Concrete\Core\Page\Page|null|false FALSE when uninitialized, NULL when none
     */
    private $importOriginalPage = false;

    public function star()
    {
        $fs = FileSet::createAndGetSet('Starred Files', FileSet::TYPE_STARRED);
        $files = $this->getRequestFiles();
        $r = new FileEditResponse();
        $r->setFiles($files);
        foreach ($files as $f) {
            if ($f->inFileSet($fs)) {
                $fs->removeFileFromSet($f);
                $r->setAdditionalDataAttribute('star', false);
            } else {
                $fs->addFileToSet($f);
                $r->setAdditionalDataAttribute('star', true);
            }
        }
        $r->outputJSON();
    }

    public function rescan()
    {
        $files = $this->getRequestFiles('canEditFileContents');
        $r = new FileEditResponse();
        $r->setFiles($files);
        $error = $this->app->make('error');

        try {
            $this->doRescan($files[0]);
            $r->setMessage(t('File rescanned successfully.'));
        } catch (UserMessageException $e) {
            $error->add($e->getMessage());
        } catch (Exception $e) {
            $error->add($e->getMessage());
        }
        $r->setError($error);
        $r->outputJSON();
    }

    public function rescanMultiple()
    {
        $files = $this->getRequestFiles('canEditFileContents');
        $q = $this->app->make(QueueService::class)->get('rescan_files');
        if ($this->request->request->get('process')) {
            $obj = new stdClass();
            $em = $this->app->make(EntityManagerInterface::class);
            $messages = $q->receive(5);
            foreach ($messages as $msg) {
                // delete the page here
                $file = unserialize($msg->body);
                if ($file !== false) {
                    $f = $em->find(FileEntity::class, $file['fID']);
                    if ($f !== null) {
                        $this->doRescan($f);
                    }
                }
                $q->deleteMessage($msg);
            }
            $obj->totalItems = $q->count();
            if ($q->count() == 0) {
                $q->deleteQueue();
            }

            return $this->app->make(ResponseFactoryInterface::class)->json($obj);
        } elseif ($q->count() == 0) {
            foreach ($files as $f) {
                $q->send(serialize([
                    'fID' => $f->getFileID(),
                ]));
            }
        }

        $totalItems = $q->count();
        View::element('progress_bar', ['totalItems' => $totalItems, 'totalItemsSummary' => t2('%d file', '%d files', $totalItems)]);
    }

    public function approveVersion()
    {
        $files = $this->getRequestFiles('canEditFileContents');
        $fvID = $this->request->request->get('fvID', $this->request->query->get('fvID'));
        $fvID = $this->app->make('helper/security')->sanitizeInt($fvID);
        $fv = $files[0]->getVersion($fvID);
        if ($fv === null) {
            throw new UserMessageException(t('Invalid file version.'), 400);
        }
        $fv->approve();
        $r = new FileEditResponse();
        $r->setFiles($files);
        $r->outputJSON();
    }

    public function deleteVersion()
    {
        $token = $this->app->make('token');
        if (!$token->validate('delete-version')) {
            $files = $this->getRequestFiles('canEditFileContents');
        }
        $fvID = $this->request->request->get('fvID', $this->request->query->get('fvID'));
        $fvID = $this->app->make('helper/security')->sanitizeInt($fvID);
        $fv = $files[0]->getVersion($fvID);
        if ($fv === null || $fv->isApproved()) {
            throw new UserMessageException(t('Invalid file version.', 400));
        }
        if (!$token->validate('version/delete/' . $fv->getFileID() . '/' . $fv->getFileVersionId())) {
            throw new UserMessageException($token->getErrorMessage(), 401);
        }
        $expr = Criteria::expr();
        $criteria = Criteria::create()
            ->andWhere($expr->orX(
                $expr->neq('file', $fv->getFile()),
                $expr->neq('fvID', $fv->getFileVersionID())
            ))
            ->andWhere($expr->eq('fvPrefix', $fv->getPrefix()))
            ->andWhere($expr->eq('fvFilename', $fv->getFileName()))
        ;
        $em = $this->app->make(EntityManagerInterface::class);
        $repo = $em->getRepository(FileVersionEntity::class);
        $deleteFilesAndThumbnails = $repo->matching($criteria)->isEmpty();
        $fv->delete($deleteFilesAndThumbnails);
        $r = new FileEditResponse();
        $r->setFiles($files);
        $r->outputJSON();
    }

    public function upload()
    {
        $errors = $this->app->make('error');
        $importedFileVersions = [];
        $replacingFile = $this->getFileToBeReplaced();
        try {
            if ($post_max_size = $this->app->make('helper/number')->getBytes(ini_get('post_max_size'))) {
                if ($post_max_size < $_SERVER['CONTENT_LENGTH']) {
                    throw new UserMessageException(Importer::getErrorMessage(Importer::E_FILE_EXCEEDS_POST_MAX_FILE_SIZE), 400);
                }
            }
            $token = $this->app->make('token');
            if (!$token->validate()) {
                throw new UserMessageException($token->getErrorMessage(), 401);
            }
            if ($this->request->files->has('file')) {
                $importedFileVersion = $this->handleUpload('file');
                if ($importedFileVersion !== null) {
                    $importedFileVersions[] = $importedFileVersion;
                }
            }
            $postedFiles = $this->request->files->get('files');
            if (is_array($postedFiles)) {
                if (count($postedFiles) > 1 && $replacingFile !== null) {
                    throw new UserMessageException(t('Only one file should be uploaded when replacing a file.'));
                }
                $importedFileVersions = [];
                foreach (array_keys($postedFiles) as $i) {
                    try {
                        $importedFileVersion = $this->handleUpload('files', $i);
                        if ($importedFileVersion !== null) {
                            $importedFileVersions[] = $importedFileVersion;
                        }
                    } catch (UserMessageException $x) {
                        $errors->add($x);
                    }
                }
            }
        } catch (UserMessageException $e) {
            $errors->add($e);
        }

        return $this->buildImportResponse($importedFileVersions, $errors, $replacingFile !== null);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function importIncoming()
    {
        $errors = $this->app->make('error');
        $importedFileVersions = [];
        try {
            $token = $this->app->make('token');
            if (!$token->validate('import_incoming')) {
                throw new UserMessageException($token->getErrorMessage());
            }
            $filenames = $this->request->request->get('send_file');
            if (is_string($filenames)) {
                $filenames = [$filenames];
            } elseif (!is_array($filenames)) {
                $filenames = [];
            }
            $replacingFile = $this->getFileToBeReplaced();
            switch (count($filenames)) {
                case 0:
                    throw new UserMessageException($replacingFile === null ? t('You must select at least one file.') : t('You must select one file.'));
                case 1:
                    break;
                default:
                    if ($replacingFile !== null) {
                        throw new UserMessageException(t('You must select one file.'));
                    }
                    break;
            }
            $incoming = $this->app->make(Incoming::class);
            $this->checkExistingIncomingFiles($filenames, $incoming);
            $fi = $this->app->make(Importer::class);
            $removeFilesAfterPost = (bool) $this->request->request->get('removeFilesAfterPost');
            $incomingFileSystemObject = $incoming->getIncomingFilesystem();
            $originalPage = $this->getImportOriginalPage();
            foreach ($filenames as $filename) {
                $fileVersion = $fi->importIncomingFile($filename, $replacingFile ?: $this->getDestinationFolder());
                if (!$fileVersion instanceof FileVersionEntity) {
                    $errors->add($filename . ': ' . $fi->getErrorMessage($fileVersion));
                } else {
                    if ($originalPage !== null) {
                        $fileVersion->getFile()->setOriginalPage($originalPage->getCollectionID());
                    }
                    $importedFileVersions[] = $fileVersion;
                    if ($removeFilesAfterPost) {
                        $incomingFileSystemObject->delete($incoming->getIncomingPath() . '/' . $filename);
                    }
                }
            }
        } catch (UserMessageException $x) {
            $errors->add($x);
        }

        return $this->buildImportResponse($importedFileVersions, $errors, $replacingFile !== null);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function importRemote()
    {
        $errors = $this->app->make('error');
        $importedFileVersions = [];
        try {
            $token = $this->app->make('token');
            if (!$token->validate('import_remote')) {
                throw new UserMessageException($token->getErrorMessage());
            }
            $urls = $this->request->request->get('url_upload');
            if (is_string($urls)) {
                $urls = explode("\n", $urls);
            } elseif (!is_array($urls)) {
                $urls = [];
            }
            $urls = array_values(array_filter(array_map('trim', $urls), 'strlen'));
            $replacingFile = $this->getFileToBeReplaced();
            switch (count($urls)) {
                case 0:
                    throw new UserMessageException($replacingFile === null ? t('You must select at least one file.') : t('You must select one file.'));
                case 1:
                    break;
                default:
                    if ($replacingFile !== null) {
                        throw new UserMessageException(t('You must select one file.'));
                    }
                    break;
            }
            $this->checkRemoteURlsToImport($urls);
            $originalPage = $this->getImportOriginalPage();
            $fi = $this->app->make(Importer::class);
            $volatileDirectory = $this->app->make(VolatileDirectory::class);
            foreach ($urls as $url) {
                try {
                    $downloadedFile = $this->downloadRemoteURL($url, $volatileDirectory->getPath());
                    $fileVersion = $fi->import($downloadedFile, false, $replacingFile ?: $this->getDestinationFolder());
                    if (!$fileVersion instanceof FileVersionEntity) {
                        $errors->add($url . ': ' . $fi->getErrorMessage($fileVersion));
                    } else {
                        if ($originalPage !== null) {
                            $fileVersion->getFile()->setOriginalPage($originalPage->getCollectionID());
                        }
                        $importedFileVersions[] = $fileVersion;
                    }
                } catch (UserMessageException $x) {
                    $errors->add($x);
                }
            }
        } catch (UserMessageException $x) {
            $errors->add($x);
        }

        return $this->buildImportResponse($importedFileVersions, $errors, $replacingFile !== null);
    }

    public function duplicate()
    {
        $files = $this->getRequestFiles('canCopyFile');
        $r = new FileEditResponse();
        $newFiles = [];
        foreach ($files as $f) {
            $nf = $f->duplicate();
            $newFiles[] = $nf;
        }
        $r->setFiles($newFiles);
        $r->outputJSON();
    }

    public function getJSON()
    {
        $files = $this->getRequestFiles();
        $r = new FileEditResponse();
        $r->setFiles($files);
        $r->outputJSON();
    }

    public function download()
    {
        $files = $this->getRequestFiles('canViewFileInFileManager');
        if (count($files) > 1) {
            $fh = $this->app->make('helper/file');
            $vh = $this->app->make('helper/validation/identifier');

            // zipem up
            $zipFile = $fh->getTemporaryDirectory() . '/' . $vh->getString() . '.zip';
            if (class_exists('ZipArchive', false)) {
                $zip = new ZipArchive();
                if ($zip->open($zipFile, ZipArchive::CREATE) !== true) {
                    throw new UserMessageException(t('Could not open with ZipArchive::CREATE'));
                }
                foreach ($files as $key => $f) {
                    $filename = $f->getFilename();

                    // Change the filename if it's already in the zip
                    if ($zip->locateName($filename) !== false) {
                        $extension = $fh->getExtension($filename);
                        $filename = str_replace('.' . $extension, '', $filename) . '_' . $key . '.' . $extension;
                    }
                    $zip->addFromString($filename, $f->getFileContents());
                    $f->trackDownload();
                }
                $zip->close();
                $fh->forceDownload($zipFile);
            } else {
                throw new UserMessageException('Unable to zip files using ZipArchive. Please ensure the Zip extension is installed.');
            }
        } else {
            $f = $files[0];
            $fvID = $this->request->request->get('fvID', $this->request->query->get('fvID'));
            if (!empty($fvID)) {
                $fv = $f->getVersion($fvID);
            } else {
                $fv = $f->getApprovedVersion();
            }
            $f->trackDownload();
            $f->forceDownload();
        }
    }

    /**
     * @param \Concrete\Core\Entity\File\File $f
     */
    protected function doRescan($f)
    {
        $fv = $f->getApprovedVersion();
        $resp = $fv->refreshAttributes(false);
        switch ($resp) {
            case Importer::E_FILE_INVALID:
                $errorMessage = t('File %s could not be found.', $fv->getFilename()) . '<br/>';
                throw new UserMessageException($errorMessage, 404);
        }
        $config = $this->app->make('config');
        $newFileVersion = null;
        if ($config->get('concrete.file_manager.images.use_exif_data_to_rotate_images')) {
            $processor = new AutorotateImageProcessor();
            if ($processor->shouldProcess($fv)) {
                if ($newFileVersion === null) {
                    $fv = $newFileVersion = $f->createNewVersion(true);
                }
                $processor->setRescanThumbnails(false);
                $processor->process($newFileVersion);
            }
        }
        $width = (int) $config->get('concrete.file_manager.restrict_max_width');
        $height = (int) $config->get('concrete.file_manager.restrict_max_height');
        if ($width > 0 || $height > 0) {
            $processor = new ConstrainImageProcessor($width, $height);
            if ($processor->shouldProcess($fv)) {
                if ($newFileVersion === null) {
                    $fv = $newFileVersion = $f->createNewVersion(true);
                }
                $processor->setRescanThumbnails(false);
                $processor->process($newFileVersion);
            }
        }
        $fv->rescanThumbnails();
        $fv->releaseImagineImage();
    }

    protected function getRequestFiles($permission = 'canViewFileInFileManager')
    {
        $files = [];
        $fID = $this->request->request->get('fID', $this->request->query->get('fID'));
        if (is_array($fID)) {
            $fileIDs = $fID;
        } else {
            $fileIDs = [$fID];
        }
        $em = $this->app->make(EntityManagerInterface::class);
        foreach ($fileIDs as $fID) {
            $f = $fID ? $em->find(FileEntity::class, $fID) : null;
            if ($f !== null) {
                $fp = new ConcretePermissions($f);
                if ($fp->$permission()) {
                    $files[] = $f;
                }
            }
        }

        if (count($files) == 0) {
            $this->app->make('helper/ajax')->sendError(t('File not found.'));
        }

        return $files;
    }

    /**
     * @param string $property
     * @param int|null $index
     *
     * @throws \Concrete\Core\Error\UserMessageException
     *
     * @return \Concrete\Core\Entity\File\Version|null returns NULL if the upload is chunked and we still haven't received the full file
     */
    protected function handleUpload($property, $index = null)
    {
        if ($index !== null) {
            $list = $this->request->files->get($property);
            $file = is_array($list) && isset($list[$index]) ? $list[$index] : null;
        } else {
            $file = $this->request->files->get($property);
        }
        if (!$file instanceof UploadedFile) {
            throw new UserMessageException(Importer::getErrorMessage(Importer::E_FILE_INVALID));
        }
        if (!$file->isValid()) {
            throw new UserMessageException(Importer::getErrorMessage($file->getError()));
        }
        $cf = $this->app->make('helper/file');

        $deleteFile = false;
        $file = $this->getFileToImport($file, $deleteFile);
        if ($file === null) {
            return null;
        }

        try {
            $name = $file->getClientOriginalName();
            $tmp_name = $file->getPathname();
            $fp = new ConcretePermissions($this->getDestinationFolder());
            if (!$fp->canAddFileType($cf->getExtension($name))) {
                throw new UserMessageException(Importer::getErrorMessage(Importer::E_FILE_INVALID_EXTENSION), 403);
            }
            $importer = $this->app->make(Importer::class);
            $importedFileVersion = $importer->import($tmp_name, $name, $this->getFileToBeReplaced() ?: $this->getDestinationFolder());
            if (!$importedFileVersion instanceof FileVersionEntity) {
                throw new UserMessageException(Importer::getErrorMessage($importedFileVersion));
            }
            $originalPage = $this->getImportOriginalPage();
            if ($originalPage !== null) {
                $importedFileVersion->getFile()->setOriginalPage($originalPage->getCollectionID());
            }
        } finally {
            if ($deleteFile) {
                @unlink($file->getPathname());
            }
        }

        return $importedFileVersion;
    }

    /**
     * Get the file instance to be replaced by the uploaded file (if any).
     *
     * @throws \Concrete\Core\Error\UserMessageException in case the file couldn't be found or it's not accessible
     *
     * @return \Concrete\Core\Entity\File\File|null
     *
     * @since 8.5.0a3
     */
    protected function getFileToBeReplaced()
    {
        if ($this->fileToBeReplaced === false) {
            $fID = $this->request->request->get('fID');
            if (!$fID) {
                $this->fileToBeReplaced = null;
            } else {
                $fID = is_scalar($fID) ? (int) $fID : 0;
                $file = $fID === 0 ? null : $this->app->make(EntityManagerInterface::class)->find(FileEntity::class, $fID);
                if ($file === null) {
                    throw new UserMessageException(t('Unable to find the specified file.'));
                }
                $fp = new Checker($file);
                if (!$fp->canEditFileContents()) {
                    throw new UserMessageException(t('You do not have permission to modify this file.'));
                }
                $this->fileToBeReplaced = $file;
            }
        }

        return $this->fileToBeReplaced;
    }

    /**
     * Get the destination folder where the uploaded files should be placed.
     *
     * @throws \Concrete\Core\Error\UserMessageException in case the folder couldn't be found or it's not accessible
     *
     * @return \Concrete\Core\Tree\Node\Type\FileFolder
     *
     * @since 8.5.0a3
     */
    protected function getDestinationFolder()
    {
        if ($this->destinationFolder === false) {
            $replacingFile = $this->getFileToBeReplaced();
            if ($replacingFile !== null) {
                $folder = $replacingFile->getFileFolderObject();
                // Fix for 5.7 files that had their parents set to their own file id
                if ($folder instanceof \Concrete\Core\Tree\Node\Type\File) {
                    $folder = $folder->getTreeNodeParentObject();
                }
            } else {
                $treeNodeID = $this->request->request->get('currentFolder');
                if ($treeNodeID) {
                    $treeNodeID = is_scalar($treeNodeID) ? (int) $treeNodeID : 0;
                    $folder = $treeNodeID === 0 ? null : Node::getByID($treeNodeID);
                } else {
                    $filesystem = new Filesystem();
                    $folder = $filesystem->getRootFolder();
                }
            }
            if (!$folder instanceof FileFolder) {
                throw new UserMessageException(t('Unable to find the specified folder.'));
            }
            if ($replacingFile === null) {
                $fp = new Checker($folder);
                if (!$fp->canAddFiles()) {
                    throw new UserMessageException(t("You don't have the permission to upload to %s", $folder->getTreeNodeDisplayName()), 400);
                }
            }
            $this->destinationFolder = $folder;
        }

        return $this->destinationFolder;
    }

    /**
     * Get the original page to be used when importing files (if any).
     *
     * @throws \Concrete\Core\Error\UserMessageException in case the file couldn't be found
     *
     * @return \Concrete\Core\Page\Page|null
     *
     * @since 8.5.0a3
     */
    protected function getImportOriginalPage()
    {
        if ($this->importOriginalPage === false) {
            $ocID = $this->request->request->get('ocID');
            if (!$ocID) {
                $this->importOriginalPage = null;
            } else {
                $ocID = is_scalar($ocID) ? (int) $ocID : 0;
                $page = $ocID === 0 ? null : CorePage::getByID($ocID);
                if ($page === null || $page->isError()) {
                    throw new UserMessageException(t('Unable to find the specified page.'));
                }
                $this->importOriginalPage = $page;
            }
        }

        return $this->importOriginalPage;
    }

    /**
     * Check that a list of strings are valid "incoming" file names.
     *
     * @param array $incomingFiles
     * @param \Concrete\Core\File\Incoming $incoming
     *
     * @throws \Concrete\Core\Error\UserMessageException in case one or more of the specified files couldn't be found
     * @throws \Exception in case of generic errors
     *
     * @since 8.5.0a3
     */
    protected function checkExistingIncomingFiles(array $incomingFiles, Incoming $incoming)
    {
        $availableFileNames = [];
        foreach ($incoming->getIncomingFilesystem()->listContents($incoming->getIncomingPath()) as $availableFile) {
            $availableFileNames[] = $availableFile['basename'];
        }
        $invalidFiles = array_diff($incomingFiles, $availableFileNames);
        switch (count($invalidFiles)) {
            case 0:
                break;
            case 1:
                throw new UserMessageException(t("The file \"%s\" can't be found in the incoming directory.", array_pop($invalidFiles)));
            default:
                throw new UserMessageException(t("These files can't be found in the incoming directory: %s", "\n- \"" . implode("\"\n- \"", $invalidFiles) . '"'));
        }
    }

    /**
     * Check that a list of strings are valid "incoming" file names.
     *
     * @param string $urls
     *
     * @throws \Concrete\Core\Error\UserMessageException in case one or more of the specified URLs are not valid
     *
     * @since 8.5.0a3
     */
    protected function checkRemoteURlsToImport(array $urls)
    {
        foreach ($urls as $u) {
            try {
                $url = Url::createFromUrl($u);
            } catch (RuntimeException $x) {
                throw new UserMessageException(t('The URL "%s" is not valid: %s', $u, $x->getMessage()));
            }
            $scheme = (string) $url->getScheme();
            if ($scheme === '') {
                throw new UserMessageException(t('The URL "%s" is not valid.', $u));
            }
            $host = trim((string) $url->getHost());
            if (in_array(strtolower($host), ['', '0', 'localhost'], true)) {
                throw new UserMessageException(t('The URL "%s" is not valid.', $u));
            }

            $ipFormatBlocks = [
                '/^\d+$/', // No fully integer / octal hostnames http://2130706433 http://017700000001
                '/^0x[0-9a-f]+$/i', // No Hexadecimal hostnames http://0x07f000001
            ];

            foreach ($ipFormatBlocks as $block) {
                if (preg_match($block, $host) !== 0) {
                    throw new UserMessageException(t('The URL "%s" is not valid.', $u));
                }
            }

            $ip = IPFactory::addressFromString($host, true, true, true);
            if ($ip === null) {
                $dnsList = @dns_get_record($host, DNS_A | DNS_AAAA);
                while ($ip === null && $dnsList !== false && count($dnsList) > 0) {
                    $dns = array_shift($dnsList);
                    $ip = IPFactory::addressFromString($dns['ip'], true, true, true);
                }
            }
            if ($ip !== null && !in_array($ip->getRangeType(), [IPRangeType::T_PUBLIC, IPRangeType::T_PRIVATENETWORK], true)) {
                throw new UserMessageException(t('The URL "%s" is not valid.', $u));
            }
        }
    }

    /**
     * Download an URL to the temporary directory.
     *
     * @param string $url
     * @param string $temporaryDirectory
     *
     * @throws \Concrete\Core\Error\UserMessageException in case of errors
     *
     * @return string the local filename
     */
    protected function downloadRemoteURL($url, $temporaryDirectory)
    {
        $client = $this->app->make('http/client');

        $request = $client->getRequest()->setUri($url);
        $response = $client->sendWithoutRedirects();

        if (!$response->isSuccess()) {
            throw new UserMessageException(t(/*i18n: %1$s is an URL, %2$s is an error message*/'There was an error downloading "%1$s": %2$s', $url, $response->getReasonPhrase() . ' (' . $response->getStatusCode() . ')'));
        }
        $headers = $response->getHeaders();
        // figure out a filename based on filename, mimetype, ???
        $matches = null;
        if (preg_match('/^[^#\?]+[\\/]([-\w%]+\.[-\w%]+)($|\?|#)/', $request->getUri(), $matches)) {
            // got a filename (with extension)... use it
            $filename = $matches[1];
        } else {
            $contentType = $headers->get('ContentType')->getFieldValue();
            if ($contentType) {
                list($mimeType) = explode(';', $contentType, 2);
                $mimeType = trim($mimeType);
                // use mimetype from http response
                $extension = $this->app->make('helper/mime')->mimeToExtension($mimeType);
                if ($extension === false) {
                    throw new UserMessageException(t('Unknown mime-type: %s', h($mimeType)));
                }
                $filename = date('Y-m-d_H-i_') . mt_rand(100, 999) . '.' . $extension;
            } else {
                throw new UserMessageException(t(/*i18n: %s is an URL*/'Could not determine the name of the file at %s', $url));
            }
        }
        $fullFilename = $temporaryDirectory . '/' . $filename;
        // write the downloaded file to a temporary location on disk
        $handle = fopen($fullFilename, 'wb');
        fwrite($handle, $response->getBody());
        fclose($handle);

        return $fullFilename;
    }

    /**
     * @param \Concrete\Core\Entity\File\Version[] $importedFileVersions
     * @param \Concrete\Core\Error\ErrorList\ErrorList $errors
     * @param bool $isReplacingFile
     */
    protected function buildImportResponse(array $importedFileVersions, ErrorList $errors, $isReplacingFile)
    {
        $responseFactory = $this->app->make(ResponseFactoryInterface::class);
        switch ($this->request->request->get('responseFormat')) {
            case 'dropzone':
                if ($errors->has()) {
                    return $responseFactory->create(json_encode($errors->toText()), 422, ['Content-Type' => 'application/json; charset='.APP_CHARSET]);
                }
                break;
            default:
                
        }
        $editResponse = new FileEditResponse();
        $editResponse->setError($errors);
        $editResponse->setFiles($importedFileVersions);
        if (count($importedFileVersions) > 0) {
            if ($isReplacingFile) {
                $editResponse->setMessage(t('File replaced successfully.'));
            } else {
                $editResponse->setMessage(t2('%s file imported successfully.', '%s files imported successfully', count($importedFileVersions)));
            }
        }

        return $responseFactory->json($editResponse);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
     * @param bool $deleteFile output parameter that's set to true if the uploaded file should be deleted manually
     *
     * @return \Symfony\Component\HttpFoundation\File\UploadedFile|null
     */
    private function getFileToImport(UploadedFile $file, &$deleteFile)
    {
        $deleteFile = false;
        $post = $this->request->request;
        $dzuuid = $post->get('dzuuid');
        $dzIndex = $post->get('dzchunkindex');
        $dzTotalChunks = $post->get('dztotalchunkcount');
        if ($dzuuid !== null && $dzIndex !== null && $dzTotalChunks !== null) {
            $file->move($file->getPath(), $dzuuid . $dzIndex);
            if ($this->isFullChunkFilePresent($dzuuid, $file->getPath(), $dzTotalChunks)) {
                $deleteFile = true;

                return $this->combineFileChunks($dzuuid, $file->getPath(), $dzTotalChunks, $file);
            } else {
                return null;
            }
        } else {
            return $file;
        }
    }

    /**
     * @param string $fileUuid
     * @param string $tempPath
     * @param int $totalChunks
     *
     * @return bool
     */
    private function isFullChunkFilePresent($fileUuid, $tempPath, $totalChunks)
    {
        for ($i = 0; $i < $totalChunks; ++$i) {
            if (!file_exists($tempPath . DIRECTORY_SEPARATOR . $fileUuid . $i)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param string $fileUuid
     * @param string $tempPath
     * @param int $totalChunks
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $originalFile
     *
     * @return \Symfony\Component\HttpFoundation\File\UploadedFile
     */
    private function combineFileChunks($fileUuid, $tempPath, $totalChunks, UploadedFile $originalFile)
    {
        $finalFilePath = $tempPath . DIRECTORY_SEPARATOR . $fileUuid;
        $finalFile = fopen($finalFilePath, 'wb');
        for ($i = 0; $i < $totalChunks; ++$i) {
            $chunkFile = $tempPath . DIRECTORY_SEPARATOR . $fileUuid . $i;
            $addition = fopen($chunkFile, 'rb');
            stream_copy_to_stream($addition, $finalFile);
            fclose($addition);
            unlink($chunkFile);
        }
        fclose($finalFile);

        return new UploadedFile($finalFilePath, $originalFile->getClientOriginalName());
    }
}
