<?php
namespace Concrete\Core\Express\Search;

use Concrete\Core\Attribute\Category\ExpressCategory;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Express\Search\ColumnSet\DefaultSet;
use Concrete\Core\Search\AbstractSearchProvider;
use Concrete\Core\Search\ProviderInterface;
use Concrete\Core\Express\Search\ColumnSet\Available;
use Concrete\Core\Express\Search\ColumnSet\ColumnSet;
use Symfony\Component\HttpFoundation\Session\Session;

class SearchProvider extends AbstractSearchProvider
{

    protected $category;
    protected $entity;

    public function getSessionNamespace()
    {
        return 'express_' . $this->entity->getId();
    }

    public function __construct(Entity $entity, ExpressCategory $category, Session $session)
    {
        $category->setEntity($entity);
        $this->entity = $entity;
        $this->category = $category;
        parent::__construct($session);
    }

    public function getCustomAttributeKeys()
    {
        return $this->category->getList();
    }

    public function getAvailableColumnSet()
    {
        return new Available($this->category);
    }

    public function getCurrentColumnSet()
    {
        $current = $this->entity->getResultColumnSet();
        if (!is_object($current)) {
            $current = new DefaultSet($this->category);
        }
        return $current;
    }
}
