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

use Rolab\EntityDataModel\EntityContainer;

/**
 * @covers EntityContainer
 */
class EntityContainerTest extends EntityDataModelTestCase
{
    public function testConstructor()
    {
        $entityContainer = new EntityContainer('SomeContainer');

        $this->assertEquals($entityContainer->getName(), 'SomeContainer');

        return $entityContainer;
    }

    /**
     * @depends testConstructor
     */
    public function testConstructorWithParentContainer(EntityContainer $parentEntityContainer)
    {
        $childEntityContainer = new EntityContainer('ChildContainer', $parentEntityContainer);

        $this->assertSame($parentEntityContainer, $childEntityContainer->getParentContainer());

        return $childEntityContainer;
    }

    /**
     * @dataProvider invalidNameProvider
     * @expectedException InvalidArgumentException
     */
    public function testExceptionOnInvalidName($invalidName)
    {
        $entityContainer = new EntityContainer($invalidName);
    }

    /**
     * @depends testConstructor
     */
    public function testGetEntitySetsInitiallyEmpty(EntityContainer $entityContainer)
    {
        $this->assertEmpty($entityContainer->getEntitySets());

        return $entityContainer;
    }

    /**
     * @depends testGetEntitySetsInitiallyEmpty
     */
    public function testAddEntitySet(EntityContainer $entityContainer)
    {
        $mockEntitySet = $this->buildEntitySetStub('SomeEntitySet');

        $mockEntitySet->expects($this->once())
            ->method('setEntityContainer')
            ->with($this->equalTo($entityContainer));

        $entityContainer->addEntitySet($mockEntitySet);

        $this->assertCount(1, $entityContainer->getEntitySets());
        $this->assertContains($mockEntitySet, $entityContainer->getEntitySets());

        return $entityContainer;
    }

    /**
     * @depends testAddEntitySet
     * @expectedException InvalidArgumentException
     */
    public function testExceptionOnAddEntitySetWithSameName(EntityContainer $entityContainer)
    {
        $entityContainer->addEntitySet($this->buildEntitySetStub('SomeEntitySet'));
    }

    /**
     * @depends testAddEntitySet
     */
    public function testGetEntitySetByName(EntityContainer $entityContainer)
    {
        $entitySetStub = $this->buildEntitySetStub('SomeOtherEntitySet');

        $entityContainer->addEntitySet($entitySetStub);

        $this->assertSame($entitySetStub, $entityContainer->getEntitySetByName('SomeOtherEntitySet'));
    }

    public function testGetEntitySetByNameWithParentContainer()
    {
        $parentContainer = new EntityContainer('ParentContainer');

        $parentContainerEntitySet = $this->buildEntitySetStub('SomeEntitySet');

        $parentContainer->addEntitySet($parentContainerEntitySet);

        $childContainer = new EntityContainer('ChildContainer', $parentContainer);

        $this->assertSame($parentContainerEntitySet,
            $childContainer->getEntitySetByName('SomeEntitySet'));

        return $childContainer;
    }

    /**
     * @depends testGetEntitySetByNameWithParentContainer
     */
    public function testGetEntitySetByNameParentSetOverwrittenByChildSet(EntityContainer $childContainer)
    {
        $childContainerEntitySet = $this->buildEntitySetStub('SomeEntitySet');

        $childContainer->addEntitySet($childContainerEntitySet);

        $this->assertSame($childContainerEntitySet,
            $childContainer->getEntitySetByName('SomeEntitySet'));
    }

    /**
     * @depends testConstructor
     */
    public function testGetAssociationSetsInitiallyEmpty(EntityContainer $entityContainer)
    {
        $this->assertEmpty($entityContainer->getAssociationSets());

        return $entityContainer;
    }

    /**
     * @depends testGetAssociationSetsInitiallyEmpty
     */
    public function testAddAssociationSet(EntityContainer $entityContainer)
    {
        $mockAssociationSet = $this->buildAssociationSetStub('SomeAssociationSet', 'SomeNamespace.SomeAssociation');

        $mockAssociationSet->expects($this->once())
            ->method('setEntityContainer')
            ->with($this->equalTo($entityContainer));

        $entityContainer->addAssociationSet($mockAssociationSet);

        $this->assertCount(1, $entityContainer->getAssociationSets());
        $this->assertContains($mockAssociationSet, $entityContainer->getAssociationSets());

        return $entityContainer;
    }

    /**
     * @depends testAddAssociationSet
     * @expectedException InvalidArgumentException
     */
    public function testExceptionOnAddAssociationSetWithSameName(EntityContainer $entityContainer)
    {
        $entityContainer->addAssociationSet($this->buildAssociationSetStub('SomeAssociationSet',
            'SomeNamespace.SomeOtherAssociation'));
    }

    /**
     * @depends testAddEntitySet
     */
    public function testGetAssociationSetByName(EntityContainer $entityContainer)
    {
        $associationSetStub = $this->buildAssociationSetStub('SomeOtherAssociationSet');

        $entityContainer->addAssociationSet($associationSetStub);

        $this->assertSame($associationSetStub, $entityContainer->getAssociationSetByName('SomeOtherAssociationSet'));
    }

    public function testGetAssociationSetByNameWithParentContainer()
    {
        $parentContainer = new EntityContainer('ParentContainer');

        $parentContainerAssociationSet = $this->buildAssociationSetStub('SomeAssociationSet');

        $parentContainer->addAssociationSet($parentContainerAssociationSet);

        $childContainer = new EntityContainer('ChildContainer', $parentContainer);

        $this->assertSame($parentContainerAssociationSet,
            $childContainer->getAssociationSetByName('SomeAssociationSet'));

        return $childContainer;
    }

    /**
     * @depends testGetAssociationSetByNameWithParentContainer
     */
    public function testGetAssociationSetByNameParentSetOverwrittenByChildSet(EntityContainer $childContainer)
    {
        $childContainerAssociationSet = $this->buildAssociationSetStub('SomeAssociationSet');

        $childContainer->addAssociationSet($childContainerAssociationSet);

        $this->assertSame($childContainerAssociationSet,
            $childContainer->getAssociationSetByName('SomeAssociationSet'));
    }

    protected function buildEntitySetStub($name)
    {
        $entitySetStub = $this->getMockBuilder('Rolab\EntityDataModel\EntitySet')
            ->disableOriginalConstructor()
            ->setMethods(array('setEntityContainer', 'getName'))
            ->getMock();

        $entitySetStub->expects($this->any())
            ->method('getName')
            ->will($this->returnValue($name));

        return $entitySetStub;
    }

    protected function buildAssociationSetStub($name)
    {
        $associationSetStub = $this->getMockBuilder('Rolab\EntityDataModel\AssociationSet')
            ->disableOriginalConstructor()
            ->setMethods(array('setEntityContainer', 'getName'))
            ->getMock();

        $associationSetStub->expects($this->any())
            ->method('getName')
            ->will($this->returnValue($name));

        return $associationSetStub;
    }
}
