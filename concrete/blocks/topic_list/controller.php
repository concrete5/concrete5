<?php
namespace Concrete\Block\TopicList;

use Concrete\Core\Attribute\Key\CollectionKey;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Tree\Tree;
use Concrete\Core\Tree\Type\Topic as TopicTree;
use Concrete\Core\Tree\Type\Topic;
use Core;

defined('C5_EXECUTE') or die('Access Denied.');

class Controller extends BlockController
{
    public $helpers = array('form');

    protected $btInterfaceWidth = 400;
    protected $btInterfaceHeight = 400;
    protected $btTable = 'btTopicList';
    protected $btExportPageColumns = ['cParentID'];

    public function getBlockTypeDescription()
    {
        return t("Displays a list of your site's topics, allowing you to click on them to filter a page list.");
    }

    public function getBlockTypeName()
    {
        return t("Topic List");
    }

    public function add()
    {
        $this->edit();
        $this->set('title', t('Topics'));
    }

    public function edit()
    {
        $this->requireAsset('core/topics');
        $tt = new TopicTree();
        $defaultTree = $tt->getDefault();
        $tree = $tt->getByID(Core::make('helper/security')->sanitizeInt($this->topicTreeID));
        if (!$tree) {
            $tree = $defaultTree;
        }
        $trees = $tt->getList();
        $keys = CollectionKey::getList();
        $attributeKeys = array();
        foreach ($keys as $ak) {
            if ($ak->getAttributeTypeHandle() == 'topics') {
                $attributeKeys[] = $ak;
            }
        }
        $this->set('attributeKeys', $attributeKeys);
        $this->set('tree', $tree);
        $this->set('trees', $trees);
    }

    public function view()
    {
        if ($this->mode == 'P') {
            $page = \Page::getCurrentPage();
            $topics = $page->getAttribute($this->topicAttributeKeyHandle);
            if (is_array($topics)) {
                $this->set('topics', $topics);
            }
        } else {
            $tt = new TopicTree();
            $tree = $tt->getByID(Core::make('helper/security')->sanitizeInt($this->topicTreeID));
            $this->set('tree', $tree);
        }
    }

    public function action_topic($treeNodeID = false, $topic = false)
    {
        $this->set('selectedTopicID', intval($treeNodeID));
        $this->view();
    }

    public function getTopicLink(\Concrete\Core\Tree\Node\Node $topic = null)
    {
        if ($this->cParentID) {
            $c = \Page::getByID($this->cParentID);
        } else {
            $c = \Page::getCurrentPage();
        }
        if ($topic) {
            $nodeName = $topic->getTreeNodeName();
            $nodeName = strtolower($nodeName); // convert to lowercase
            $nodeName = preg_replace('/[[:space:]]+/', '-', $nodeName);
            $nodeName = Core::make('helper/text')->encodePath($nodeName); // urlencode
            return \URL::page($c, 'topic', $topic->getTreeNodeID(), $nodeName);
        } else {
            return \URL::page($c);
        }
    }

    public static function replaceTreeWithPlaceHolder($treeID)
    {
        if ($treeID > 0) {
            $tree = Tree::getByID($treeID);
            if (is_object($tree)) {
                return '{ccm:export:tree:' . $tree->getTreeName() . '}';
            }
        }
    }

    public function export(\SimpleXMLElement $blockNode)
    {
        $tree = Tree::getByID($this->topicTreeID);
        $data = $blockNode->addChild('data');
        $data->addChild('mode', $this->mode);
        $data->addChild("title", $this->title);
        $data->addChild('topicAttributeKeyHandle', $this->topicAttributeKeyHandle);
        if (is_object($tree)) {
            $data->addChild('tree', $tree->getTreeName());
        }
        $path = null;
        if ($this->cParentID) {
            $parent = \Page::getByID($this->cParentID);
            $path = '{ccm:export:page:' . $parent->getCollectionPath() . '}';
        }
        $data->addChild('cParentID', $path);
    }

    public function getImportData($blockNode, $page)
    {
        $args = array();
        $treeName = (string) $blockNode->data->tree;
        $page = (string) $blockNode->data->cParentID;
        $tree = Topic::getByName($treeName);
        $args['topicTreeID'] = $tree->getTreeID();
        $args['cParentID'] = 0;
        $args['title'] = (string) $blockNode->data->title;
        $args['mode'] = (string) $blockNode->data->mode;
        if (!$args['mode']) {
            $args['mode'] = 'S';
        }
        $args['topicAttributeKeyHandle'] = (string) $blockNode->data->topicAttributeKeyHandle;
        if ($page) {
            if (preg_match('/\{ccm:export:page:(.*?)\}/i', $page, $matches)) {
                $c = \Page::getByPath($matches[1]);
                $args['externalTarget'] = 1;
                $args['cParentID'] = $c->getCollectionID();
            }
        }

        return $args;
    }

    public function save($data)
    {
        $data += array(
            'externalTarget' => 0,
        );
        $externalTarget = intval($data['externalTarget']);
        if ($externalTarget === 0) {
            $data['cParentID'] = 0;
        } else {
            $data['cParentID'] = intval($data['cParentID']);
        }

        parent::save($data);
    }
}
