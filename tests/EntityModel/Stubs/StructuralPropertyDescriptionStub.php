<?php

declare(strict_types=1);

namespace RSSchermer\Tests\EntityModel\Stubs;

use RSSchermer\EntityModel\Type\AbstractStructuralPropertyDescription;
use RSSchermer\EntityModel\Type\Edm\EdmString;
use RSSchermer\EntityModel\Type\ResourceTypeInterface;

class StructuralPropertyDescriptionStub extends AbstractStructuralPropertyDescription
{
    public function __construct(
        string $name = 'Street',
        string $class = 'RSSchermer\Tests\EntityModel\Fixtures\Address',
        string $property = 'street',
        ResourceTypeInterface $propertyValueType = null
    ) {
        $type = $propertyValueType ?? EdmString::create();
        $reflection = new \ReflectionProperty($class, $property);

        parent::__construct($name, $reflection, $type);
    }
}
