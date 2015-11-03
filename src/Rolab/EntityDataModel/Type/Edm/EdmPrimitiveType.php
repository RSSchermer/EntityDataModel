<?php

declare(strict_types=1);

namespace Rolab\EntityDataModel\Type\Edm;

use Rolab\EntityDataModel\Type\PrimitiveType;

abstract class EdmPrimitiveType extends PrimitiveType
{
    public function __construct()
    {
    }
    
    public function getNamespace() : string
    {
        return 'Edm';
    }

    public function getFullName() : string
    {
        return 'Edm.'. $this->getName();
    }
}
