<?php

namespace Rolab\EntityDataModel\Definition\StructuralTypeDefinitionFactory;

use Rolab\EntityDataModel\Definition\Driver\DriverInterface;
use Rolab\EntityDataModel\Cache\StructuralTypeDefinitionCacheInterface;

class StructuralTypeDefinitionFactory
{
    private $driver;

    private $cache;

    public function __construct(DriverInterface $driver)
    {
        $this->driver = $driver;
    }

    public function setCache(StructuralTypeDefinitionCacheInterface $cache)
    {
        $this->cache = $cache;
    }

    public function getStructuralTypeDefinitionForClass($className)
    {

    }
}
