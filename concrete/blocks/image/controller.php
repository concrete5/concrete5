<?php

namespace Concrete\Block\Image;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Error\Error;
use Concrete\Core\File\File;
use Concrete\Core\File\Tracker\FileTrackableInterface;
use Concrete\Core\Form\Service\DestinationPicker\DestinationPicker;
use Concrete\Core\Page\Page;
use Concrete\Core\Statistics\UsageTracker\AggregateTracker;

class Controller extends BlockController implements FileTrackableInterface
{
    protected $btInterfaceWidth = 400;
    protected $btInterfaceHeight = 550;
    protected $btTable = 'btContentImage';
    protected $btCacheBlockRecord = true;
    protected $btCacheBlockOutput = true;
    protected $btCacheBlockOutputOnPost = true;
    protected $btCacheBlockOutputForRegisteredUsers = false;
    protected $btWrapperClass = 'ccm-ui';
    protected $btExportFileColumns = ['fID', 'fFallbackID', 'fOnstateID', 'fFallbackOnstateID', 'fileLinkID'];
    protected $btExportPageColumns = ['internalLinkCID'];
    protected $btFeatures = [
        'image',
    ];

    /**
     * @var \Concrete\Core\Statistics\UsageTracker\AggregateTracker|null
     */
    protected $tracker;

    public function __construct($blockType = null, AggregateTracker $tracker = null)
    {
        parent::__construct($blockType);
        $this->tracker = $tracker;
    }

    public function getBlockTypeName()
    {
        return t('Image');
    }

    public function getBlockTypeDescription()
    {
        return t('Adds images and onstates from the library to pages.');
    }

    /**
     * @param string $outputContent
     */
    public function registerViewAssets($outputContent = '')
    {
        // Ensure we have jQuery if we have an onState image
        if (is_object($this->getFileOnstateObject())) {
            $this->requireAsset('javascript', 'jquery');
        }
    }

    /**
     * @return bool|null
     */
    public function view()
    {
        // Check for a valid File in the view
        $f = $this->getFileObject();
        $this->set('f', $f);

        // On-State image available
        $foS = $this->getFileOnstateObject();
        $this->set('foS', $foS);

        $imgPaths = [];

        if (is_object($f) && is_object($foS)) {
            if (!$f->getTypeObject()->isSVG() && !$foS->getTypeObject()->isSVG()) {
                $im = $this->app->make('helper/image');

                if ($this->maxWidth > 0 || $this->maxHeight > 0) {
                    $fIDThumb = $im->getThumbnail($f, $this->maxWidth, $this->maxHeight, $this->cropImage);
                    $imgPaths['default'] = $fIDThumb->src;

                    $fOnstateThumb = $im->getThumbnail($foS, $this->maxWidth, $this->maxHeight, $this->cropImage);
                    $imgPaths['hover'] = $fOnstateThumb->src;
                } else {
                    $imgPaths['default'] = File::getRelativePathFromID($this->getFileID());
                    $imgPaths['hover'] = File::getRelativePathFromID($this->fOnstateID);

                    // This is a bit of a hack. We need a non WEBP fallback for non-supporting browsers
                    // Because of the way C5 works, when trying to resize a WEBP image, even to its normal size,
                    // it is automatically converted to a PNG file so we can use that as a fallback.
                    if ($f->getTypeObject()->isWEBP()) {
                        $fIDThumb = $im->getThumbnail($f, $f->getAttribute('width'), $f->getAttribute('height'), false);
                        $imgPaths['defaultFallback'] = $fIDThumb->src;
                    }

                    if ($foS->getTypeObject()->isWEBP()) {
                        $fOnstateThumb = $im->getThumbnail($foS, $foS->getAttribute('width'), $foS->getAttribute('height'), false);
                        $imgPaths['hoverFallback'] = $fOnstateThumb->src;
                    }
                }
            }
        } elseif (is_object($f) && $f->getTypeObject()->isWEBP() && empty($this->maxWidth) && empty($this->maxHeight)) {
            $im = $this->app->make('helper/image');
            $fIDThumb = $im->getThumbnail($f, $f->getAttribute('width'), $f->getAttribute('height'), false);
            $imgPaths['defaultFallback'] = $fIDThumb->src;
        }

        $this->set('imgPaths', $imgPaths);
        $this->set('altText', $this->getAltText());
        $this->set('title', $this->getTitle());
        $this->set('linkURL', $this->getLinkURL());
        $this->set('openLinkInNewWindow', $this->shouldLinkOpenInNewWindow());
        $this->set('c', Page::getCurrentPage());
    }

    public function add()
    {
        $this->set('bf', null);
        $this->set('bfo', null);
        $this->set('constrainImage', false);
        $this->set('destinationPicker', $this->app->make(DestinationPicker::class));
        $this->set('imageLinkPickers', $this->getImageLinkPickers());
        $this->set('imageLinkHandle', 'none');
        $this->set('imageLinkValue', null);
    }

    public function edit()
    {
        // Image file object
        $bf = null;
        if ($this->getFileID() > 0) {
            $bf = $this->getFileObject();
        }
        $this->set('bf', $bf);

        // Image On-State file object
        $bfo = null;
        if ($this->getFileOnstateID() > 0) {
            $bfo = $this->getFileOnstateObject();
        }
        $this->set('bfo', $bfo);

        // Constrain dimensions
        $constrainImage = $this->maxWidth > 0 || $this->maxHeight > 0;
        $this->set('constrainImage', $constrainImage);

        // Max width is saved as an integer
        if ($this->maxWidth == 0) {
            $this->set('maxWidth', '');
        }

        // Max height is saved as an integer
        if ($this->maxHeight == 0) {
            $this->set('maxHeight', '');
        }

        // None, Internal, or External
        $this->set('destinationPicker', $this->app->make(DestinationPicker::class));
        $this->set('imageLinkPickers', $this->getImageLinkPickers());
        if ($this->getInternalLinkCID()) {
            $this->set('imageLinkHandle', 'page');
            $this->set('imageLinkValue', $this->getInternalLinkCID());
        } elseif ($this->getFileLinkID()) {
            $this->set('imageLinkHandle', 'file');
            $this->set('imageLinkValue', $this->getFileLinkID());
        } elseif ((string) $this->getExternalLink() !== '') {
            $this->set('imageLinkHandle', 'external_url');
            $this->set('imageLinkValue', $this->getExternalLink());
        } else {
            $this->set('imageLinkHandle', 'none');
            $this->set('imageLinkValue', null);
        }
    }

    /**
     * @return array
     */
    public function getJavaScriptStrings()
    {
        return [
            'image-required' => t('You must select an image.'),
        ];
    }

    /**
     * @return bool
     */
    public function isComposerControlDraftValueEmpty()
    {
        $f = $this->getFileObject();
        if (is_object($f) && $f->getFileID()) {
            return false;
        }

        return true;
    }

    /**
     * @return \Concrete\Core\Entity\File\File|null
     */
    public function getImageFeatureDetailFileObject()
    {
        // i don't know why this->fID isn't sticky in some cases, leading us to query
        // every damn time
        $db = $this->app->make('database')->connection();

        $file = null;
        $fID = $db->fetchColumn('SELECT fID FROM btContentImage WHERE bID = ?', [$this->bID], 0);
        if ($fID) {
            $f = File::getByID($fID);
            if (is_object($f) && $f->getFileID()) {
                $file = $f;
            }
        }

        return $file;
    }

    /**
     * @return int
     */
    public function getFileID()
    {
        return isset($this->record->fID) ? $this->record->fID : (isset($this->fID) ? $this->fID : null);
    }

    /**
     * @return int
     */
    public function getFileOnstateID()
    {
        return $this->fOnstateID;
    }

    /**
     * @return int
     */
    public function getFileLinkID()
    {
        return $this->fileLinkID;
    }

    /**
     * @return \Concrete\Core\Entity\File\File|null
     */
    public function getFileOnstateObject()
    {
        if (isset($this->fOnstateID) && $this->fOnstateID) {
            return File::getByID($this->fOnstateID);
        }
    }

    /**
     * @return \Concrete\Core\Entity\File\File|null
     */
    public function getFileObject()
    {
        return File::getByID($this->getFileID());
    }

    /**
     * @return \Concrete\Core\Entity\File\File|null
     */
    public function getFileLinkObject()
    {
        if ($this->fileLinkID) {
            return File::getByID($this->getFileLinkID());
        }
    }

    /**
     * @return string
     */
    public function getAltText()
    {
        return $this->altText;
    }

    /**
     * @return string|null
     */
    public function getTitle()
    {
        return !empty($this->title) ? $this->title : null;
    }

    /**
     * @return string
     */
    public function getExternalLink()
    {
        return $this->externalLink;
    }

    /**
     * @return int
     */
    public function getInternalLinkCID()
    {
        return $this->internalLinkCID;
    }

    /**
     * @return string
     */
    public function getLinkURL()
    {
        $linkUrl = '';
        if (!empty($this->externalLink)) {
            $sec = $this->app->make('helper/security');
            $linkUrl = $sec->sanitizeURL($this->externalLink);
        } elseif (!empty($this->internalLinkCID)) {
            $linkToC = Page::getByID($this->internalLinkCID);
            if (is_object($linkToC) && !$linkToC->isError()) {
                $linkUrl = $linkToC->getCollectionLink();
            }
        } elseif (!empty($this->fileLinkID)) {
            $fileLinkObject = $this->getFileLinkObject();
            if (is_object($fileLinkObject)) {
                $linkUrl = $fileLinkObject->getRelativePath();
            }
        }

        return $linkUrl;
    }

    /**
     * @return bool
     */
    public function shouldLinkOpenInNewWindow()
    {
        return (bool) $this->openLinkInNewWindow;
    }

    /**
     * @return Error
     */
    public function validate_composer()
    {
        $e = $this->app->make('helper/validation/error');

        $f = $this->getFileObject();
        if (!is_object($f) || !$f->getFileID()) {
            $e->add(t('You must specify a valid image file.'));
        }

        return $e;
    }

    /**
     * @param array $args
     *
     * @return Error
     */
    public function validate($args)
    {
        $e = $this->app->make('helper/validation/error');
        $f = File::getByID($args['fID']);
        $svg = false;
        if (is_object($f)) {
            $svg = $f->getTypeObject()->isSVG();
        }

        if (!$args['fID']) {
            $e->add(t('Please select an image.'));
        }

        if (isset($args['cropImage']) && ((int) $args['maxWidth'] <= 0 || ((int) $args['maxHeight'] <= 0)) && !$svg) {
            $e->add(t('Cropping an image requires setting a max width and max height.'));
        }

        if ($svg && isset($args['cropImage'])) {
            $e->add(t('SVG images cannot be cropped.'));
        }

        $this->app->make(DestinationPicker::class)->decode('imageLink', $this->getImageLinkPickers(), $e, t('Image Link'), $args);

        return $e;
    }

    /**
     * On delete update the tracker.
     */
    public function delete()
    {
        $this->getTracker()->forget($this);
        parent::delete();
    }

    /**
     * @param array $args
     */
    public function save($args)
    {
        $args = $args + [
            'fID' => 0,
            'fOnstateID' => 0,
            'maxWidth' => 0,
            'maxHeight' => 0,
            'constrainImage' => 0,
            'openLinkInNewWindow' => 0,
        ];

        $args['fID'] = $args['fID'] != '' ? $args['fID'] : 0;
        $args['fOnstateID'] = $args['fOnstateID'] != '' ? $args['fOnstateID'] : 0;
        $args['cropImage'] = isset($args['cropImage']) ? 1 : 0;
        $args['maxWidth'] = (int) $args['maxWidth'] > 0 ? (int) $args['maxWidth'] : 0;
        $args['maxHeight'] = (int) $args['maxHeight'] > 0 ? (int) $args['maxHeight'] : 0;

        if (!$args['constrainImage']) {
            $args['cropImage'] = 0;
            $args['maxWidth'] = 0;
            $args['maxHeight'] = 0;
        }

        list($imageLinkType, $imageLinkValue) = $this->app->make(DestinationPicker::class)->decode('imageLink', $this->getImageLinkPickers(), null, null, $args);
        $args['internalLinkCID'] = $imageLinkType === 'page' ? $imageLinkValue : 0;
        $args['fileLinkID'] = $imageLinkType === 'file' ? $imageLinkValue : 0;
        $args['externalLink'] = $imageLinkType === 'external_url' ? $imageLinkValue : '';

        $args['openLinkInNewWindow'] = $args['openLinkInNewWindow'] ? 1 : 0;

        parent::save($args);
        $this->getTracker()->track($this);
    }

    public function getUsedFiles()
    {
        return [$this->getFileID()];
    }

    public function getUsedCollection()
    {
        return $this->getCollectionObject();
    }

    /**
     * @return \Concrete\Core\Statistics\UsageTracker\AggregateTracker
     */
    protected function getTracker()
    {
        if ($this->tracker === null) {
            $this->tracker = $this->app->make(AggregateTracker::class);
        }

        return $this->tracker;
    }

    /**
     * @return string[]
     */
    protected function getImageLinkPickers()
    {
        return [
            'none',
            'page',
            'file',
            'external_url' => ['maxlength' => 255],
        ];
    }
}
