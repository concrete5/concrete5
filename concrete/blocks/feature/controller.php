<?php
namespace Concrete\Block\Feature;

use Concrete\Core\Editor\LinkAbstractor;
use Page;
use Concrete\Core\Block\BlockController;
use Less_Parser;
use Less_Tree_Rule;
use Core;

defined('C5_EXECUTE') or die('Access Denied.');

class Controller extends BlockController
{
    public $helpers = array('form');

    protected $btInterfaceWidth = 400;
    protected $btCacheBlockOutput = true;
    protected $btCacheBlockOutputOnPost = true;
    protected $btCacheBlockOutputForRegisteredUsers = true;
    protected $btExportPageColumns = array('internalLinkCID');
    protected $btInterfaceHeight = 520;
    protected $btTable = 'btFeature';

    public function getBlockTypeDescription()
    {
        return t("Displays an icon, a title, and a short paragraph description.");
    }

    public function getBlockTypeName()
    {
        return t("Feature");
    }

    public function getLinkURL()
    {
        if (!empty($this->externalLink)) {
            return $this->externalLink;
        } else {
            if (!empty($this->internalLinkCID)) {
                $linkToC = Page::getByID($this->internalLinkCID);

                return (empty($linkToC) || $linkToC->error) ? '' : Core::make('helper/navigation')->getLinkToCollection(
                    $linkToC
                );
            } else {
                return '';
            }
        }
    }

    public function getParagraph()
    {
        return LinkAbstractor::translateFrom($this->paragraph);
    }

    public function getParagraphEditMode()
    {
        return LinkAbstractor::translateFromEditMode($this->paragraph);
    }

    public function registerViewAssets($outputContent = '')
    {
        $this->requireAsset('css', 'font-awesome');
        if (is_object($this->block) && $this->block->getBlockFilename() == 'hover_description') {
            // this isn't great but it's the only way to do this and still make block
            // output caching available to this block.
            $this->requireAsset('javascript', 'bootstrap/tooltip');
            $this->requireAsset('css', 'bootstrap/tooltip');
        }
    }

    public function add()
    {
        $this->edit();
    }

    public function view()
    {
        $this->set('paragraph', LinkAbstractor::translateFrom($this->paragraph));
        $this->set('linkURL', $this->getLinkURL());
    }

    protected function getIconClasses()
    {
        $iconLessFile = DIR_BASE_CORE . '/css/build/vendor/font-awesome/variables.less';
        $icons = array();

        $l = new Less_Parser();
        $parser = $l->parseFile($iconLessFile, false, true);
        $rules = $parser->rules;

        foreach ($rules as $rule) {
            if ($rule instanceof Less_Tree_Rule) {
                if (strpos($rule->name, '@fa-var') === 0) {
                    $name = str_replace('@fa-var-', '', $rule->name);
                    $icons[] = $name;
                }
            }
        }
        asort($icons);

        return $icons;
    }

    public function edit()
    {
        $this->requireAsset('css', 'font-awesome');
        $classes = $this->getIconClasses();

        // let's clean them up
        $icons = array('' => t('Choose Icon'));
        $txt = Core::make('helper/text');
        foreach ($classes as $class) {
            $icons[$class] = $txt->unhandle($class);
        }
        $this->set('icons', $icons);
    }

    public function getSearchableContent()
    {
        return $this->title . ' ' . $this->paragraph;
    }

    public function save($args)
    {
        switch (isset($args['linkType']) ? intval($args['linkType']) : 0) {
            case 1:
                $args['externalLink'] = '';
                break;
            case 2:
                $args['internalLinkCID'] = 0;
                break;
            default:
                $args['externalLink'] = '';
                $args['internalLinkCID'] = 0;
                break;
        }
        $args['paragraph'] = LinkAbstractor::translateTo($args['paragraph']);
        unset($args['linkType']);
        parent::save($args);
    }
}
