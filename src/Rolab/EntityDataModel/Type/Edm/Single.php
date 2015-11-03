<?php

declare(strict_types=1);

namespace Rolab\EntityDataModel\Type\Edm;

class Single extends EdmPrimitiveType
{
    public function getName() : string
    {
        return 'Single';
    }
}
