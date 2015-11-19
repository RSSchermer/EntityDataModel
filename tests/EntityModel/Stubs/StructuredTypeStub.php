<?php

declare(strict_types=1);

namespace RSSchermer\Tests\EntityModel\Stubs;

use RSSchermer\EntityModel\Type\AbstractStructuredType;

class StructuredTypeStub extends AbstractStructuredType
{
    public function __construct(
        string $name = "StructuredType",
        string $className = 'RSSchermer\Tests\EntityModel\Fixtures\Address'
    ) {
        parent::__construct($name, new \ReflectionClass($className));
    }
}
