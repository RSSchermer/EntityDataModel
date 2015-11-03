<?php

declare(strict_types=1);

namespace Rolab\EntityDataModel\Type\Edm;

class Decimal extends EdmPrimitiveType
{
    public function getName() : string
    {
        return 'Decimal';
    }
}
