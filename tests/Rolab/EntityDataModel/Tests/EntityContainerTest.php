<?php

declare(strict_types=1);

namespace Rolab\EntityDataModel\Tests;

use Rolab\EntityDataModel\EntityContainer;
use Rolab\EntityDataModel\Exception\InvalidArgumentException;

/**
 * @covers EntityContainer
 */
class EntityContainerTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $entityDataModel = $this->buildEntityDataModelStub();

        $entityContainer = new EntityContainer('SomeContainer', $entityDataModel);

        $this->assertEquals($entityContainer->getName(), 'SomeContainer');
        $this->assertSame($entityDataModel, $entityContainer->getEntityDataModel());
        $this->assertEquals('SomeNamespace', $entityContainer->getNamespace());
        $this->assertEquals('SomeNamespace.SomeContainer', $entityContainer->getFullName());

        return $entityContainer;
    }

    public function testConstructorWithParentContainer()
    {
        $parentContainerModel = $this->buildEntityDataModelStub();

        $parentEntityContainer = new EntityContainer('SomeContainer', $parentContainerModel);

        $childContainerModel = $this->getMockBuilder('Rolab\EntityDataModel\EntityDataModel')
            ->disableOriginalConstructor()
            ->getMock();

        $childContainerModel->method('getNamespace')->willReturn('SomeNamespace');
        $childContainerModel->method('getReferencedModels')->willReturn(array());

        $childContainerModel->expects($this->once())
            ->method('addReferencedModel')
            ->with($this->equalTo($parentContainerModel));

        $childEntityContainer = new EntityContainer(
            'ChildContainer',
            $childContainerModel,
            $parentEntityContainer
        );

        $this->assertSame($parentEntityContainer, $childEntityContainer->getParentContainer());

        return $childEntityContainer;
    }

    /**
     * @dataProvider invalidNameProvider
     * @expectedException InvalidArgumentException
     */
    public function testExceptionOnInvalidName(string $invalidName)
    {
        new EntityContainer($invalidName, $this->buildEntityDataModelStub());
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
        $parentContainer = new EntityContainer('ParentContainer', $this->buildEntityDataModelStub());

        $parentContainerEntitySet = $this->buildEntitySetStub('SomeEntitySet');

        $parentContainer->addEntitySet($parentContainerEntitySet);

        $childContainer = new EntityContainer('ChildContainer', $this->buildEntityDataModelStub(), $parentContainer);

        $this->assertSame($parentContainerEntitySet, $childContainer->getEntitySetByName('SomeEntitySet'));

        return $childContainer;
    }

    /**
     * @depends testGetEntitySetByNameWithParentContainer
     */
    public function testGetEntitySetByNameParentSetOverwrittenByChildSet(EntityContainer $childContainer)
    {
        $childContainerEntitySet = $this->buildEntitySetStub('SomeEntitySet');

        $childContainer->addEntitySet($childContainerEntitySet);

        $this->assertSame($childContainerEntitySet, $childContainer->getEntitySetByName('SomeEntitySet'));
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

    protected function buildEntitySetStub(string $name)
    {
        $entitySetStub = $this->getMockBuilder('Rolab\EntityDataModel\EntitySet')
            ->disableOriginalConstructor()
            ->getMock();

        $entitySetStub->method('getName')->willReturn($name);

        return $entitySetStub;
    }

    protected function buildEntityDataModelStub()
    {
        $entityDataModelStub = $this->getMockBuilder('Rolab\EntityDataModel\EntityDataModel')
            ->disableOriginalConstructor()
            ->getMock();

        $entityDataModelStub->method('getNamespace')->willReturn('SomeNamespace');

        return $entityDataModelStub;
    }
}
