<?php

declare(strict_types=1);

namespace Rolab\EntityDataModel\Tests;

use Rolab\EntityDataModel\NamedModelElement;

abstract class NamedModelElementTestCase extends \PHPUnit_Framework_TestCase
{
    abstract public function testConstructor();

    /**
     * @depends testConstructor
     */
    public function testSetEntityDataModel(NamedModelElement $modelElement)
    {
        $entityDataModelStub = $this->getMockBuilder('Rolab\EntityDataModel\EntityDataModel')
            ->disableOriginalConstructor()
            ->getMock();

        $entityDataModelStub->method('getNamespace')->willReturn('SomeNamespace');

        $modelElement->setEntityDataModel($entityDataModelStub);

        $this->assertSame($entityDataModelStub, $modelElement->getEntityDataModel());
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