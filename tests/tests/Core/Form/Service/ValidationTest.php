<?php
namespace Concrete\Tests\Core\Form\Service;

use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Form\Service\Validation;

class ValidationTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        $val = new Validation();
        $val->setData([
            'empty' => '',
            'notEmpty' => 'foo',
            'integer' => 5,
            'notInteger' => 'bar',
            'email' => 'concrete5@example.com',
            'notEmail' => 'baz',
        ]);
        $val->addRequired('empty'); // invalid
        $val->addRequired('notEmpty'); // valid
        $val->addInteger('integer'); // valid
        $val->addInteger('notInteger'); // invalid
        $val->addRequiredEmail('email'); // valid
        $val->addRequiredEmail('notEmail'); // invalid
        $pass = $val->test();
        $this->assertEquals(false, $pass);

        /** @var ErrorList $e */
        $e = $val->getError();
        $this->assertEquals('<ul class="ccm-error"><li>Field "empty" is invalid</li><li>Field "notInteger" is invalid</li><li>Field "notEmail" is invalid</li></ul>', (string) $e);

        $this->assertEquals(true, $e->containsField('empty'));
        $this->assertEquals(false, $e->containsField('notEmpty'));
        $this->assertEquals(false, $e->containsField('integer'));
        $this->assertEquals(true, $e->containsField('notInteger'));
        $this->assertEquals(false, $e->containsField('email'));
        $this->assertEquals(true, $e->containsField('notEmail'));
    }

    public function testAllValid()
    {
        /** @var $val Validation */
        $val = new Validation();
        $val->setData([
            'foo' => 'bar',
        ]);
        $val->addRequired('foo');
        $this->assertEquals(true, $val->test());
    }
}