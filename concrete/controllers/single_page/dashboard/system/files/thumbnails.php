<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Files;

use Concrete\Core\Entity\File\Image\Thumbnail\Type\Type as TypeEntity;
use Concrete\Core\Entity\File\Image\Thumbnail\Type\TypeFileSet;
use Concrete\Core\File\Image\Thumbnail\Type\Type as TypeService;
use Concrete\Core\File\Set\Set as FileSet;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Page\Controller\DashboardPageController;
use Doctrine\ORM\EntityManagerInterface;

class Thumbnails extends DashboardPageController
{
    const FILESETOPTION_ALL = 'all';

    const FILESETOPTION_ALL_EXCEPT = 'except';

    const FILESETOPTION_ONLY = 'only';

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Controller\AbstractController::$helpers
     */
    public $helpers = ['form', 'form/color'];

    public function view()
    {
        $list = TypeService::getList();
        $this->set('types', $list);
    }

    public function edit($ftTypeID = false)
    {
        if ($ftTypeID === 'new') {
            $type = new TypeEntity();
        } else {
            $type = $ftTypeID ? $this->app->make(EntityManagerInterface::class)->find(TypeEntity::class, $ftTypeID) : null;
            if ($type === null) {
                $this->flash('error', t('Invalid thumbnail type object.'));

                return $this->app->make(ResponseFactoryInterface::class)->redirect($this->action(''), 302);
            }
        }
        $config = $this->app->make('config');
        $this->set('type', $type);
        $this->set('sizingModes', $this->getSizingModes());
        $this->set('sizingModeHelps', $this->getSizingModeHelps());
        $imageManipulationLibrary = $config->get('concrete.file_manager.images.manipulation_library');
        $this->set('manipulationLibrarySupportsAnimations', (string) $imageManipulationLibrary !== '' && $imageManipulationLibrary !== 'gd');
        if ($type->getID() && $type->isRequired()) {
            $this->set('allowConditionalThumbnails', false);
        } else {
            $this->set('allowConditionalThumbnails', true);
            $this->set('fileSetOptions', $this->getFileSetOptions());
            $this->set('fileSets', $this->getPublicFileSets(false));
        }
    }

    public function save($ftTypeID = false)
    {
        if (!$this->token->validate('thumbnailtype-save-' . $ftTypeID)) {
            $this->error->add($this->token->getErrorMessage());
        } else {
            $em = $this->app->make(EntityManagerInterface::class);
            $repo = $em->getRepository(TypeEntity::class);
            if ($ftTypeID === 'new') {
                $type = new TypeEntity();
            } else {
                $type = $ftTypeID ? $this->app->make(EntityManagerInterface::class)->find(TypeEntity::class, $ftTypeID) : null;
                if ($type === null) {
                    $this->flash('error', t('Invalid thumbnail type object.'));

                    return $this->app->make(ResponseFactoryInterface::class)->redirect($this->action(''), 302);
                }
            }
            $post = $this->request->request;
            $valNumbers = $this->app->make('helper/validation/numbers');
            $valStrings = $this->app->make('helper/validation/strings');

            if ($type->getID() === null || !$type->isRequired()) {
                $handle = $post->get('ftTypeHandle');
                $handle = is_string($handle) ? trim($handle) : '';
                if ($handle === '') {
                    $this->error->add(t('Your thumbnail type must have a handle.'));
                } elseif (!$valStrings->handle($handle)) {
                    $this->error->add(t('Your thumbnail type handle must only contain lowercase letters and underscores.'));
                } elseif (substr($handle, -strlen(TypeEntity::HIGHDPI_SUFFIX)) === TypeEntity::HIGHDPI_SUFFIX) {
                    $this->error->add(t('Thumbnail type handles can\'t end with "%s".', TypeEntity::HIGHDPI_SUFFIX));
                } elseif (stripos($handle, 'ccm_') === 0) {
                    $this->error->add(t('Thumbnail type handles can\'t start with "%s".', 'ccm_'));
                } else {
                    $alreadyExists = false;
                    foreach ($repo->findBy(['ftTypeHandle' => $handle]) as $existingType) {
                        if ($existingType !== $type) {
                            $alreadyExists = true;
                            break;
                        }
                    }
                    if ($alreadyExists) {
                        $this->error->add(t('That handle is in use.'));
                    } else {
                        $type->setHandle($handle);
                    }
                }
            }

            $name = $post->get('ftTypeName');
            $name = is_string($name) ? trim($name) : '';
            if ($name === '') {
                $this->error->add(t('Your thumbnail type must have a name.'));
            } else {
                $alreadyExists = false;
                foreach ($repo->findBy(['ftTypeName' => $name]) as $existingType) {
                    if ($existingType !== $type) {
                        $alreadyExists = true;
                        break;
                    }
                }
                if ($alreadyExists) {
                    $this->error->add(t('Another thumbnail type exists with the name "%s".', h($name)));
                } else {
                    $type->setName($name);
                }
            }
            $width = $post->get('ftTypeWidth');
            if ($valNumbers->integer($width, 1)) {
                $width = (int) $width;
            } else {
                $width = null;
            }
            $height = $post->get('ftTypeHeight');
            if ($valNumbers->integer($height, 1)) {
                $height = (int) $height;
            } else {
                $height = null;
            }
            if ($width === null && $height === null) {
                $this->error->add(t("Width and height can't both be empty or less than zero."));
            } else {
                $type->setWidth($width);
                $type->setHeight($height);
            }
            $sizingMode = $post->get('ftTypeSizingMode');
            if (!is_string($sizingMode) || !array_key_exists($sizingMode, $this->getSizingModes())) {
                $this->error->add(t('Please specify the sizing mode.'));
            } elseif ($sizingMode === TypeEntity::RESIZE_EXACT && ($width === null || $height === null)) {
                $this->error->add(t("With the 'Exact' sizing mode (with cropping), both width and height must be specified and greater than zero."));
            } else {
                $type->setSizingMode($sizingMode);
            }
            $type->setIsUpscalingEnabled($post->get('ftUpscalingEnabled') ? true : false);
            $type->setAvailableInBlocks($post->get('ftAvailableInBlocks') ? true : false);
            $type->setKeepAnimations($post->get('ftKeepAnimations'));
            $type->setSaveAreaBackgroundColor($post->get('ftSaveAreaBackgroundColor'));
            if ($ftTypeID === 'new' || !$type->isRequired()) {
                $fileSetOption = $post->get('fileSetOption');
                if (!in_array($fileSetOption, array_keys($this->getFileSetOptions()), true)) {
                    $this->error->add(t('Please specify the Conditional Thumbnails criteria.'));
                } elseif ($fileSetOption === static::FILESETOPTION_ALL) {
                    $type->getAssociatedFileSets()->clear();
                    $type->setLimitedToFileSets(false);
                } else {
                    $publicFileSets = $this->getPublicFileSets(true);
                    $receivedFileSetIDs = [];
                    $ids = $post->get('fileSets', []);
                    if (is_array($ids)) {
                        foreach ($ids as $id) {
                            if ($valNumbers->integer($id, 1)) {
                                $id = (int) $id;
                                if (!in_array($id, $receivedFileSetIDs, true) && isset($publicFileSets[$id])) {
                                    $receivedFileSetIDs[] = $id;
                                }
                            }
                        }
                    }
                    if (empty($receivedFileSetIDs)) {
                        $this->error->add(t('Please specify the file sets for the Conditional Thumbnails criteria.'));
                    } else {
                        $type->setLimitedToFileSets($fileSetOption === static::FILESETOPTION_ONLY);
                        foreach ($type->getAssociatedFileSets() as $afs) {
                            $index = array_search($afs->getFileSetID(), $receivedFileSetIDs, true);
                            if ($index === false) {
                                $type->getAssociatedFileSets()->removeElement($afs);
                            } else {
                                unset($receivedFileSetIDs[$index]);
                            }
                        }
                        foreach ($receivedFileSetIDs as $newFileSetID) {
                            $type->getAssociatedFileSets()->add(new TypeFileSet($type, $publicFileSets[$newFileSetID]));
                        }
                    }
                }
            }

            if (!$this->error->has()) {
                if ($ftTypeID === 'new') {
                    $em->persist($type);
                }
                $em->flush($type);
                if ($ftTypeID === 'new') {
                    $this->flash('success', t('Thumbnail type added.'));
                } else {
                    $this->flash('success', t('Thumbnail type updated.'));
                }

                return $this->app->make(ResponseFactoryInterface::class)->redirect($this->action(''), 302);
            }
        }
        $this->edit($ftTypeID);
    }

    public function delete($ftTypeID = false)
    {
        if (!$this->token->validate('thumbnailtype-delete-' . $ftTypeID)) {
            $this->error->add($this->token->getErrorMessage());
        } else {
            $em = $this->app->make(EntityManagerInterface::class);
            $type = $ftTypeID ? $em->find(TypeEntity::class, $ftTypeID) : null;
            if ($type === null) {
                $this->flash('error', t('Invalid thumbnail type object.'));

                return $this->app->make(ResponseFactoryInterface::class)->redirect($this->action(''), 302);
            }
            if ($type->isRequired()) {
                $this->error->add(t('You may not delete a required thumbnail type.'));
            }
        }
        if (!$this->error->has()) {
            $em->remove($type);
            $em->flush($type);
            $this->flash('success', t('Thumbnail type removed.'));

            return $this->app->make(ResponseFactoryInterface::class)->redirect($this->action(''));
        }
        $this->edit($ftTypeID);
    }

    protected function getSizingModes()
    {
        return [
            TypeEntity::RESIZE_PROPORTIONAL => t('Resize Proportionally'),
            TypeEntity::RESIZE_EXACT => t('Resize and Crop to the Exact Size'),
        ];
    }

    protected function getSizingModeHelps()
    {
        $result = [];
        foreach (array_keys($this->getSizingModes()) as $sizingMode) {
            switch ($sizingMode) {
                case TypeEntity::RESIZE_PROPORTIONAL:
                    $result[$sizingMode] = t("The original image will be scaled down so it is fully contained within the thumbnail dimensions. The specified width and height will be considered maximum limits. Unless the given dimensions are equal to the original image's aspect ratio, one dimension in the resulting thumbnail will be smaller than the given limit.");
                    break;
                case TypeEntity::RESIZE_EXACT:
                    $result[$sizingMode] = t("The thumbnail will be scaled so that its smallest side will equal the length of the corresponding side in the original image. Any excess outside of the scaled thumbnail's area will be cropped, and the returned thumbnail will have the exact width and height specified. Both width and height must be specified.");
                    break;
                default:
                    $result[$sizingMode] = '';
            }
        }

        return $result;
    }

    protected function getFileSetOptions()
    {
        return [
            static::FILESETOPTION_ALL => t('Always generate thumbnails'),
            static::FILESETOPTION_ONLY => t('Generate thumbnails for images that are in the following file sets'),
            static::FILESETOPTION_ALL_EXCEPT => t('Generate thumbnails for images that are NOT in the following file sets'),
        ];
    }

    protected function getPublicFileSets($asObjects)
    {
        $result = [];
        foreach (FileSet::getMySets() as $fileSet) {
            if ($fileSet->getFileSetType() == FileSet::TYPE_PUBLIC) {
                $result[$fileSet->getFileSetID()] = $asObjects ? $fileSet : $fileSet->getFileSetDisplayName();
            }
        }

        return $result;
    }
}
