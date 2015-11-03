<?php

declare(strict_types=1);

namespace Rolab\EntityDataModel\Type\Edm;

class Stream extends EdmPrimitiveType
{
    public function getName() : string
    {
        return 'Stream';
    }
}
