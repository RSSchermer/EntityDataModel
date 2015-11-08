<?php

declare(strict_types=1);

namespace Rolab\EntityDataModel\Tests;

use Rolab\EntityDataModel\EntitySet;
use Rolab\EntityDataModel\Type\EntityType;
use Rolab\EntityDataModel\Exception\InvalidArgumentException;

/**
 * @covers EntitySet
 */
class EntitySetTest extends NamedContainerElementTestCase
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
    public function testExceptionOnInvalidName(string $invalidName)
    {
        new EntitySet($invalidName, $this->buildEntityTypeStub());
    }

    public function testBindNavigationPropertyDescription()
    {
        $ownerEntityTypeStub = $this->buildEntityTypeStub();
        $targetEntityTypeStub = $this->buildEntityTypeStub();

        $ownerEntitySet = new EntitySet('SomeOtherName', $ownerEntityTypeStub);
        $targetEntitySet = new EntitySet('SomeOtherName', $targetEntityTypeStub);

        $container = $this->buildEntityContainerStub();

        $ownerEntitySet->setEntityContainer($container);
        $targetEntitySet->setEntityContainer($container);

        $this->assertCount(0, $ownerEntitySet->getNavigationPropertyBindings());

        $navigationPropertyStub = $this->buildNavigationPropertyDescriptionStub(
            $ownerEntityTypeStub,
            $targetEntityTypeStub
        );

        $ownerEntitySet->bindNavigationProperty($navigationPropertyStub, $targetEntitySet);

        $this->assertCount(1, $ownerEntitySet->getNavigationPropertyBindings());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testExceptionOnBindNavigationPropertyDescriptionWithTargetSetInDifferentContainer()
    {
        $ownerEntityTypeStub = $this->buildEntityTypeStub();
        $targetEntityTypeStub = $this->buildEntityTypeStub();

        $ownerEntitySet = new EntitySet('SomeOtherName', $ownerEntityTypeStub);
        $targetEntitySet = new EntitySet('SomeOtherName', $targetEntityTypeStub);

        $container = $this->buildEntityContainerStub();
        $otherContainer = $this->buildEntityContainerStub();

        $ownerEntitySet->setEntityContainer($container);
        $targetEntitySet->setEntityContainer($otherContainer);

        $navigationPropertyStub = $this->buildNavigationPropertyDescriptionStub(
            $ownerEntityTypeStub,
            $targetEntityTypeStub
        );

        $ownerEntitySet->bindNavigationProperty($navigationPropertyStub, $targetEntitySet);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testExceptionOnBindNavigationPropertyDescriptionOnDifferentEntityType()
    {
        $ownerEntityTypeStub = $this->buildEntityTypeStub();
        $targetEntityTypeStub = $this->buildEntityTypeStub();
        $otherEntityTypeStub = $this->buildEntityTypeStub();

        $ownerEntitySet = new EntitySet('SomeOtherName', $otherEntityTypeStub);
        $targetEntitySet = new EntitySet('SomeOtherName', $targetEntityTypeStub);

        $container = $this->buildEntityContainerStub();

        $ownerEntitySet->setEntityContainer($container);
        $targetEntitySet->setEntityContainer($container);

        $navigationPropertyStub = $this->buildNavigationPropertyDescriptionStub(
            $ownerEntityTypeStub,
            $targetEntityTypeStub
        );

        $ownerEntitySet->bindNavigationProperty($navigationPropertyStub, $targetEntitySet);
    }

    protected function buildEntityTypeStub()
    {
        $entityTypeStub = $this->getMockBuilder('Rolab\EntityDataModel\Type\EntityType')
            ->disableOriginalConstructor()
            ->getMock();

        $entityTypeStub->method('isSubTypeOf')
            ->will($this->returnCallback(function () use ($entityTypeStub) {
                return func_get_args()[0] === $entityTypeStub;
            }));

        return $entityTypeStub;
    }

    protected function buildEntityContainerStub()
    {
        return $this->getMockBuilder('Rolab\EntityDataModel\EntityContainer')
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function buildNavigationPropertyDescriptionStub(EntityType $ownerType, EntityType $targetType)
    {
        $navigationPropertyStub = $this->getMockBuilder('Rolab\EntityDataModel\Type\NavigationPropertyDescription')
            ->disableOriginalConstructor()
            ->getMock();

        $navigationPropertyStub->method('getPropertyValueType')
            ->willReturn($targetType);

        $navigationPropertyStub->method('getStructuredType')
            ->willReturn($ownerType);

        return $navigationPropertyStub;
    }
}
