<?php

declare(strict_types=1);

namespace Rolab\EntityDataModel\Type\Edm;

class Int64 extends EdmPrimitiveType
{
    public function getName() : string
    {
        return 'Int64';
    }
}
