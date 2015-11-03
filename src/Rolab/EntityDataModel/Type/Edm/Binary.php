<?php

declare(strict_types=1);

namespace Rolab\EntityDataModel\Type\Edm;

class Binary extends EdmPrimitiveType
{
    public function getName() : string
    {
        return 'Binary';
    }
}
