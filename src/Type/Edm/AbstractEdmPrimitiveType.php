<?php

declare(strict_types=1);

namespace RSSchermer\EntityModel\Type\Edm;

use RSSchermer\EntityModel\Type\AbstractPrimitiveType;

abstract class AbstractEdmPrimitiveType extends AbstractPrimitiveType
{
    public function getNamespace() : string
    {
        return 'Edm';
    }

    public function getFullName() : string
    {
        return 'Edm.'. $this->getName();
    }
}
