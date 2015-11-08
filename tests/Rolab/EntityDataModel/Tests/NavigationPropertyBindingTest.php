<?php

declare(strict_types=1);

namespace Rolab\EntityDataModel\Tests;

use Rolab\EntityDataModel\NavigationPropertyBinding;
use Rolab\EntityDataModel\Type\EntityType;
use Rolab\EntityDataModel\Exception\InvalidArgumentException;

/**
 * @covers NavigationPropertyBinding
 */
class NavigationPropertyBindingTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $ownerEntityTypeStub = $this->buildEntityTypeStub();
        $targetEntityTypeStub = $this->buildEntityTypeStub();
        $targetEntitySetStub = $this->buildEntitySetStub($targetEntityTypeStub);
        $navigationPropertyStub = $this->buildNavigationPropertyDescriptionStub(
            $ownerEntityTypeStub,
            $targetEntityTypeStub
        );

        $navigationPropertyBinding = new NavigationPropertyBinding($navigationPropertyStub, $targetEntitySetStub);

        $this->assertSame($navigationPropertyBinding->getNavigationPropertyDescription(), $navigationPropertyStub);
        $this->assertSame($navigationPropertyBinding->getTargetSet(), $targetEntitySetStub);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testExceptionOnTargetEntityTypeNotMatchingPropertyValueType()
    {
        $ownerEntityTypeStub = $this->buildEntityTypeStub();
        $targetEntityTypeStub = $this->buildEntityTypeStub();
        $otherEntityTypeStub = $this->buildEntityTypeStub();
        $targetEntitySetStub = $this->buildEntitySetStub($targetEntityTypeStub);
        $navigationPropertyStub = $this->buildNavigationPropertyDescriptionStub(
            $ownerEntityTypeStub,
            $otherEntityTypeStub
        );

        new NavigationPropertyBinding($navigationPropertyStub, $targetEntitySetStub);
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

    protected function buildEntitySetStub(EntityType $entityType)
    {
        $entitySetStub = $this->getMockBuilder('Rolab\EntityDataModel\EntitySet')
            ->disableOriginalConstructor()
            ->getMock();

        $entitySetStub->method('getEntityType')->willReturn($entityType);

        return $entitySetStub;
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