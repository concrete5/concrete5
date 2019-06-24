<?php
namespace Concrete\Block\ShareThisPage;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Sharing\ShareThisPage\ServiceList;
use Concrete\Core\Sharing\ShareThisPage\Service;
use Database;
use Core;

defined('C5_EXECUTE') or die('Access Denied.');

class Controller extends BlockController
{
    public $helpers = array('form');

    protected $btInterfaceWidth = 400;
    protected $btCacheBlockOutput = false;
    protected $btCacheBlockOutputOnPost = false;
    protected $btCacheBlockOutputForRegisteredUsers = false;
    protected $btInterfaceHeight = 400;
    protected $btTable = 'btShareThisPage';

    protected $services = array();

    public function getBlockTypeDescription()
    {
        return t("Allows users to share this page with social networks.");
    }

    public function getBlockTypeName()
    {
        return t("Share This Page");
    }

    public function edit()
    {
        $selected = $this->getSelectedServices();
        $services = array();
        foreach ($selected as $s) {
            $services[] = $s->getHandle();
        }

        $this->set('selected', json_encode($services));
        $this->set('services', ServiceList::get());
    }

    public function add()
    {
        $this->edit();
    }

    public function addService($service)
    {
        $ss = Service::getByHandle($service);
        if (is_object($ss)) {
            $this->services[] = $ss;
        }
    }

    protected function getSelectedServices()
    {
        $this->services = array();
        $db = Database::get();
        $services = $db->GetCol('select service from btShareThisPage where bID = ? order by displayOrder asc',
            array($this->bID)
        );
        foreach ($services as $service) {
            $this->addService($service);
        }

        return $this->services;
    }

    public function duplicate($newBlockID)
    {
        $db = Database::get();
        foreach ($this->getSelectedServices() as $service) {
            $db->insert('btShareThisPage', array('bID' => $newBlockID, 'service' => $service->getHandle(), 'displayOrder' => $this->displayOrder));
        }
    }

    public function validate($args)
    {
        $e = Core::make('helper/validation/error');
        if (!isset($args['service']) || empty($args['service'])) {
            $e->add(t('You must choose at least one service.'));
        }

        return $e;
    }

    public function export(\SimpleXMLElement $blockNode)
    {
        $data = $blockNode->addChild('data');
        foreach ($this->getSelectedServices() as $link) {
            $data->addChild('service', $link->getHandle());
        }
    }

    public function getImportData($blockNode, $page)
    {
        $args = array();
        foreach ($blockNode->data->service as $service) {
            $link = Service::getByHandle((string) $service);
            $args['service'][] = $link->getHandle();
        }

        return $args;
    }

    public function save($args)
    {
        $db = Database::get();
        $db->delete('btShareThisPage', array('bID' => $this->bID));
        $services = $args['service'];

        $statement = $db->prepare('insert into btShareThisPage (bID, service, displayOrder) values (?, ?, ?)');
        $displayOrder = 0;
        foreach ($services as $service) {
            $statement->bindValue(1, $this->bID);
            $statement->bindValue(2, $service);
            $statement->bindValue(3, $displayOrder);
            $statement->execute();
            ++$displayOrder;
        }
    }

    public function delete()
    {
        $db = Database::get();
        $db->delete('btShareThisPage', array('bID' => $this->bID));
    }

    public function registerViewAssets($outputContent = '')
    {
        $this->requireAsset('css', 'font-awesome');
    }

    public function view()
    {
        if (count($this->services) == 0) {
            $selected = $this->getSelectedServices();
        } else {
            $selected = $this->services;
        }
        $this->set('selected', $selected);
    }
}
