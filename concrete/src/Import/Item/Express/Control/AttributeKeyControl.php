<?php
namespace Concrete\Core\Import\Item\Express\Control;

use Concrete\Core\Application\Application;
use Concrete\Core\Attribute\Category\ExpressCategory;
use Concrete\Core\Entity\Attribute\Key\ExpressKey;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Import\Item\Express\ItemInterface;
use Doctrine\ORM\EntityManager;

defined('C5_EXECUTE') or die('Access Denied.');

class AttributeKeyControl implements ItemInterface
{

    protected $entityManager;
    protected $application;

    public function __construct(Application $application, EntityManager $entityManager)
    {
        $this->application = $application;
        $this->entityManager = $entityManager;
    }

    /**
     * @param $control \Concrete\Core\Entity\Express\Control\AttributeKeyControl
     * @param \SimpleXMLElement $xml
     */
    public function import(\SimpleXMLElement $xml, Entity $entity)
    {
        if (isset($xml->attributekey)) {
            $category = new ExpressCategory($entity, $this->application, $this->entityManager);
            $control = new \Concrete\Core\Entity\Express\Control\AttributeKeyControl();
            $control->setCustomLabel((string) $xml['custom-label']);
            if (((string) $xml['required']) == '1') {
                $control->setIsRequired(true);
            }
            $control->setId((string) $xml['id']);
            $ak = $xml->attributekey;
            //$key = $category->getAttributeKeyByHandle((string) $ak['handle']);
            //The caching on the call above makes this unreliable. I don't know why. It's driving me nuts.
            //But for the time being we're going to skip it.
            $r = $this->entityManager->getRepository(ExpressKey::class);
            $key = $r->findOneBy(['akHandle' => (string) $ak['handle']]);
            $control->setAttributeKey($key);
            return $control;
        }

    }

}
