<?php

declare(strict_types=1);

namespace RSSchermer\Tests\EntityModel\Type;

use RSSchermer\EntityModel\Type\AbstractResourcePropertyDescription;
use RSSchermer\EntityModel\Type\ComplexType;

/**
 * @covers ResourcePropertyDescription
 */
abstract class ResourcePropertyDescriptionTestCase extends \PHPUnit_Framework_TestCase
{
    abstract public function testConstructor();

    /**
     * @depends testConstructor
     */
    public function testSetStructuredType(AbstractResourcePropertyDescription $resourcePropertyDescription)
    {
        $complexType = $this->propertyValueTypeFixture = new ComplexType(
            'Address',
            new \ReflectionClass('RSSchermer\Tests\EntityModel\Fixtures\Address')
        );

        $resourcePropertyDescription->setStructuredType($complexType);

        $this->assertSame($complexType, $resourcePropertyDescription->getStructuredType()->get());
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
