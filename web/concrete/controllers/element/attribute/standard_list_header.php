<?php
namespace Concrete\Controller\Element\Attribute;

use Concrete\Core\Attribute\Category\CategoryInterface;
use Concrete\Core\Controller\ElementController;

class StandardListHeader extends ElementController
{
    protected $category;

    public function __construct(CategoryInterface $category)
    {
        parent::__construct();
        $this->category = $category;
    }

    public function getElement()
    {
        return 'attribute/standard_list_header';
    }

    public function view()
    {
        $entity = $this->category->getCategoryEntity();
        if (is_object($entity)) {
            $this->set('category', $entity);
            $this->set('sets', $entity->getAttributeSets());
        }
    }
}
