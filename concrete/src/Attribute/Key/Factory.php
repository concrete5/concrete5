<?php
namespace Concrete\Core\Attribute\Key;

use Concrete\Core\Attribute\Category\CategoryService;
use Concrete\Core\Attribute\Category\CategoryInterface;
use Concrete\Core\Support\Facade\Facade;
use Doctrine\ORM\EntityManager;
use Gettext\Translations;

class Factory
{

    protected $entityManager;
    protected $categoryService;

    public function __construct(CategoryService $categoryService, EntityManager $entityManager)
    {
        $this->categoryService = $categoryService;
        $this->entityManager = $entityManager;
    }


    public function getInstanceByID($akID)
    {
        return $this->getByID($akID);
    }

    public function getByID($akID)
    {
        $cache = Facade::getFacadeApplication()->make("cache/request");
        $item = $cache->getItem(sprintf('/attribute/id/%s', $akID));
        if (!$item->isMiss()) {
            $key = $item->get();
        } else {
            $key = $this->entityManager->getRepository('Concrete\Core\Entity\Attribute\Key\Key')
                ->findOneBy(array('akID' => $akID));
            $cache->save($item->set($key));
        }
        return $key;
    }

    public function getAttributeKeyList($category)
    {
        return $this->getList($category);
    }

    public function getList($category)
    {
        if (!is_object($category)) {
            $category = $this->categoryService->getByHandle($category);
            if (is_object($category)) {
                $category = $category->getController();
            }
        }

        if (is_object($category)) {
            /**
             * @var $category CategoryInterface
             */
            return $category->getList();
        }
    }

    /**
     * @deprecated
     */
    public function exportTranslations()
    {
        $translations = new Translations();
        $keys = $this->entityManager->getRepository('\Concrete\Core\Entity\Attribute\Key\Key')
            ->findAll();
        foreach($keys as $key) {
            $translations->insert('AttributeKeyName', $key->getAttributeKeyName());

            $type = $key->getAttributeKeySettings();

            // text attribute: placeholder
            if ($type instanceof \Concrete\Core\Entity\Attribute\Key\Settings\TextSettings) {
                $placeholder = $type->getPlaceholder();
                if ($placeholder !== '') {
                    $translations->insert('AttributeKeyPlaceholder', $placeholder);
                }
            }

            // checkbox attribute: label
            if ($type instanceof \Concrete\Core\Entity\Attribute\Key\Settings\BooleanSettings) {
                $label = $type->getCheckboxLabel();
                if ($label !== '') {
                    $translations->insert('AttributeKeyLabel', $label);
                }
            }
        }
        return $translations;
    }


}
