<?php

namespace Rolab\EntityDataModel\Definition\Driver;

interface DriverInterface
{
    public function loadStructuralTypeDefinitionForClass(\ReflectionClass $reflection);
}
