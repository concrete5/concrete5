<?php
namespace Concrete\Block\SocialLinks;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Sharing\SocialNetwork\Link;
use Database;
use Core;

defined('C5_EXECUTE') or die('Access Denied.');

class Controller extends BlockController
{
    public $helpers = ['form'];

    protected $btInterfaceWidth = 400;
    protected $btCacheBlockOutput = true;
    protected $btCacheBlockOutputOnPost = true;
    protected $btCacheBlockOutputForRegisteredUsers = true;
    protected $btInterfaceHeight = 400;
    protected $btTable = 'btSocialLinks';

    public function getBlockTypeDescription()
    {
        return t("Allows users to add social icons to their website");
    }

    public function getBlockTypeName()
    {
        return t("Social Links");
    }

    public function edit()
    {
        $all = Link::getList();

        // first we populate the links list with the selected ones in the proper order.
        $final = $selected = $this->getSelectedLinks();
        foreach ($all as $link) {
            if (!in_array($link, $selected)) {
                $final[] = $link;
            }
        }
        $this->set('links', $final);
        $this->set('selectedLinks', $selected);
    }

    public function add()
    {
        $links = Link::getList();
        $this->set('links', $links);
    }

    protected function getSelectedLinks()
    {
        $links = [];
        $db = Database::get();
        $slIDs = $db->GetCol('select slID from btSocialLinks where bID = ? order by displayOrder asc',
            [$this->bID]
        );
        foreach ($slIDs as $slID) {
            $link = Link::getByID($slID);
            if (is_object($link)) {
                $links[] = $link;
            }
        }

        return $links;
    }

    public function export(\SimpleXMLElement $blockNode)
    {
        foreach ($this->getSelectedLinks() as $link) {
            $linkNode = $blockNode->addChild('link');
            $linkNode->addAttribute('service', $link->getServiceObject()->getHandle());
        }
    }

    public function getImportData($blockNode, $page)
    {
        $args = [];
        foreach ($blockNode->link as $link) {
            $link = Link::getByServiceHandle((string) $link['service']);
            $args['slID'][] = $link->getID();
        }

        return $args;
    }

    public function validate($args)
    {
        $e = Core::make('helper/validation/error');
        if (!isset($args['slID']) || empty($args['slID'])) {
            $e->add(t('You must choose at least one link.'));
        }

        return $e;
    }

    public function duplicate($newBlockID)
    {
        $db = Database::get();
        foreach ($this->getSelectedLinks() as $link) {
            $db->insert('btSocialLinks', ['bID' => $newBlockID, 'slID' => $link->getID(), 'displayOrder' => $this->displayOrder]);
        }
    }

    public function save($args)
    {
        $db = Database::get();
        $db->delete('btSocialLinks', ['bID' => $this->bID]);
        $slIDs = $args['slID'];

        $statement = $db->prepare('insert into btSocialLinks (bID, slID, displayOrder) values (?, ?, ?)');
        $displayOrder = 0;
        foreach ($slIDs as $linkID) {
            $statement->bindValue(1, $this->bID);
            $statement->bindValue(2, $linkID);
            $statement->bindValue(3, $displayOrder);
            $statement->execute();
            ++$displayOrder;
        }
    }

    public function delete()
    {
        $db = Database::get();
        $db->delete('btSocialLinks', ['bID' => $this->bID]);
    }

    public function view()
    {
        $links = $this->getSelectedLinks();
        $this->set('links', $links);
    }

    public function registerViewAssets($outputContent = '')
    {
        $this->requireAsset('css', 'font-awesome');
    }
}
