<?php

declare(strict_types=1);

namespace RSSchermer\Tests\EntityModel;

use RSSchermer\EntityModel\AbstractNamedSchemaElement;
use RSSchermer\EntityModel\Schema;

abstract class NamedModelElementTestCase extends \PHPUnit_Framework_TestCase
{
    abstract public function testConstructor();

    /**
     * @depends testConstructor
     */
    public function testSetEntityDataModel(AbstractNamedSchemaElement $modelElement)
    {
        $schema = new Schema('SomeNamespace');

        $modelElement->setSchema($schema);

        $this->assertSame($schema, $modelElement->getSchema()->get());
        $this->assertEquals('SomeNamespace', $modelElement->getNamespace());
        $this->assertEquals('SomeNamespace.' . $modelElement->getName(), $modelElement->getFullName());
    }

    public function invalidNameProvider()
    {
        return array(
            array('A-dashed-name'),
            array('N@meW|thS%mbols'),
            array('Name With Spaces'),
            array('Name.With.Dots')
        );
    }
}
