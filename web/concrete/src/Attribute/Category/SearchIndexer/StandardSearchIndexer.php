<?php
namespace Concrete\Core\Attribute\Category\SearchIndexer;

use Concrete\Core\Attribute\AttributeKeyInterface;
use Concrete\Core\Attribute\Category\CategoryInterface;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\Attribute\Value\Value;
use Doctrine\DBAL\Schema\Schema;

class StandardSearchIndexer implements SearchIndexerInterface
{
    protected $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    protected function isValid(CategoryInterface $category)
    {
        if (!($category instanceof StandardSearchIndexerInterface)) {
            throw new \Exception(t('Category %s must implement StandardSearchIndexerInterface.'), $category->getCategoryEntity()->getAttributeCategoryHandle());
        }

        return true;
    }

    public function indexEntry(CategoryInterface $category, $mixed)
    {
        if ($this->isValid($category)) {

            // Regenerate based on values.
            $values = $category->getAttributeValues($mixed);

            /** @var \Doctrine\DBAL\Schema\Column[] $columns */
            $columns = $this->connection->getSchemaManager()->listTableColumns($category->getIndexedSearchTable());

            foreach ($values as $value) {
                /**
                 * @var $value Value
                 */
                $attributeIndexer = $value->getAttributeKey()->getSearchIndexer();
                $attributeIndexer->indexEntry($category, $value, $mixed);
            }
        }
    }

    public function createRepository(CategoryInterface $category)
    {
        $schema = new Schema();
        if ($this->isValid($category)) {
            if (!$this->connection->tableExists($category->getIndexedSearchTable())) {
                $table = $schema->createTable($category->getIndexedSearchTable());
                $details = $category->getSearchIndexFieldDefinition();
                if (isset($details['columns'])) {
                    foreach ($details['columns'] as $column) {
                        $table->addColumn($column['name'], $column['type'], $column['options']);
                    }
                }

                if (isset($details['primary'])) {
                    $table->setPrimaryKey($details['primary']);
                }

                $queries = $schema->toSql($this->connection->getDatabasePlatform());
                foreach ($queries as $query) {
                    $this->connection->query($query);
                }
            }
        }
    }

    public function updateRepository(CategoryInterface $category, AttributeKeyInterface $key, $previousHandle = null)
    {
        if ($this->isValid($category)) {
            $attributeIndexer = $key->getSearchIndexer();
            $attributeIndexer->addSearchKey($category, $key, $previousHandle);
        }
    }
}
