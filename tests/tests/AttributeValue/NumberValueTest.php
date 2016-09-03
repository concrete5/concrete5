<?php
namespace Concrete\Tests\AttributeValue;

use ConcreteDatabaseTestCase;
use Database;
use ORM;
use Concrete\Core\Entity\Attribute\Value\Value\NumberValue;

class NumberValueTest extends ConcreteDatabaseTestCase
{
    protected $fixtures = [];
    protected $tables = [];

    protected $metadatas = [
        NumberValue::class,
        \Concrete\Core\Entity\Attribute\Value\Value\Value::class,
        \Concrete\Core\Entity\Attribute\Value\Value\RatingValue::class,
    ];

    public function renderProvider()
    {
        return [
            [null, null, ''],
            [1.23, '1.23', '1.23'],
            [200, '200', '200'],
            [199.9999, '199.9999', '199.9999'],
            [199.99999, '200', '200'],
            [1.2349, '1.2349', '1.2349'],
            [1000000000.2349, '1000000000.2349', '1000000000.2349'],
            [1000000000.234900000001, '1000000000.2349', '1000000000.2349'],
            [1000000000.234099999999, '1000000000.2341', '1000000000.2341'],
            [-1.23, '-1.23', '-1.23'],
            [-200, '-200', '-200'],
            [-199.9999, '-199.9999', '-199.9999'],
            [-199.99999, '-200', '-200'],
            [-1.2349, '-1.2349', '-1.2349'],
            [-1000000000.2349, '-1000000000.2349', '-1000000000.2349'],
            [-1000000000.234900000001, '-1000000000.2349', '-1000000000.2349'],
            [-1000000000.234099999999, '-1000000000.2341', '-1000000000.2341'],
        ];
    }

    /**
     * @dataProvider renderProvider
     */
    public function testRender($value, $expectedGetValue, $expectedToString)
    {
        $avID = isset($avID) ? $avID + 1 : 1;
        $db = Database::connection();
        $db->executeQuery('insert into AttributeValueValues (type) values (?)', ['numbervalue']);
        $avID = $db->lastInsertId();
        $db->executeQuery('insert into NumberAttributeValues (avID, value) values (?, ?)', [$avID, $value]);
        $em = ORM::entityManager();
        $repo = $em->getRepository(NumberValue::class);
        $entity = $repo->find($avID);
        $this->assertInstanceOf(NumberValue::class, $entity);
        $this->assertSame($expectedGetValue, $entity->getValue());
        $this->assertSame($expectedToString, (string) $entity);
    }
}
