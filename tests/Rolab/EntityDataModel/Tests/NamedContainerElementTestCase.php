<?php

declare(strict_types=1);

namespace Rolab\EntityDataModel\Tests;

use Rolab\EntityDataModel\NamedContainerElement;

abstract class NamedContainerElementTestCase extends \PHPUnit_Framework_TestCase
{
    abstract public function testConstructor();

    /**
     * @depends testConstructor
     */
    public function testSetEntityContainer(NamedContainerElement $containerElement)
    {
        $entityContainerStub = $this->getMockBuilder('Rolab\EntityDataModel\EntityContainer')
            ->disableOriginalConstructor()
            ->getMock();

        $containerElement->setEntityContainer($entityContainerStub);

        $this->assertSame($entityContainerStub, $containerElement->getEntityContainer());
    }

    /**
     * @depends testConstructor
     */
    public function testIsContainedIn(NamedContainerElement $containerElement) {
        $parentContainerStub = $this->getMockBuilder('Rolab\EntityDataModel\EntityContainer')
            ->disableOriginalConstructor()
            ->getMock();

        $parentContainerStub->method('getParentContainer')->willReturn(null);

        $childContainerStub = $this->getMockBuilder('Rolab\EntityDataModel\EntityContainer')
            ->disableOriginalConstructor()
            ->getMock();

        $childContainerStub->method('getParentContainer')->willReturn($parentContainerStub);

        $containerElement->setEntityContainer($parentContainerStub);

        $this->assertTrue($containerElement->isContainedIn($parentContainerStub));
        $this->assertTrue($containerElement->isContainedIn($childContainerStub));

        $containerElement->setEntityContainer($childContainerStub);

        $this->assertTrue($containerElement->isContainedIn($childContainerStub));
        $this->assertFalse($containerElement->isContainedIn($parentContainerStub));
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