<?php

/*
 * This file is part of the Rolab Entity Data Model library.
 *
 * (c) Roland Schermer <roland0507@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rolab\EntityDataModel\Tests;

use Rolab\EntityDataModel\Tests\EntityDataModelTestCase;

use Rolab\EntityDataModel\EntitySet;

/**
 * @covers EntitySet
 */
class EntitySetTest extends EntityDataModelTestCase
{
    public function testConstructor()
    {
        $entityTypeStub = $this->buildEntityTypeStub();

        $entitySet = new EntitySet('SomeName', $entityTypeStub);

        $this->assertEquals('SomeName', $entitySet->getName());
        $this->assertSame($entityTypeStub, $entitySet->getEntityType());

        return $entitySet;
    }

    /**
     * @dataProvider invalidNameProvider
     * @expectedException InvalidArgumentException
     */
    public function testExceptionOnInvalidName($invalidName)
    {
        $entitySet = new EntitySet($invalidName, $this->buildEntityTypeStub());
    }

    /**
     * @depends testConstructor
     */
    public function testSetEntityContainer($entitySet)
    {
        $entityContainerStub = $this->getMockBuilder('Rolab\EntityDataModel\EntityContainer')
            ->disableOriginalConstructor()
            ->getMock();

        $entitySet->setEntityContainer($entityContainerStub);

        $this->assertSame($entityContainerStub, $entitySet->getEntityContainer());
    }

    protected function buildEntityTypeStub()
    {
        $entityTypeStub = $this->getMockBuilder('Rolab\EntityDataModel\Type\EntityType')
            ->disableOriginalConstructor()
            ->getMock();

        return $entityTypeStub;
    }
}
