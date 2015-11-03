<?php

declare(strict_types=1);

namespace Rolab\EntityDataModel\Type\Edm;

class Double extends EdmPrimitiveType
{
    public function getName() : string
    {
        return 'Double';
    }
}
