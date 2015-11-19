<?php

namespace RSSchermer\EntityModel\Cache;

interface EntityDataModelCacheInterface
{
    public function loadEntityDataModelFromCache($schemaName);
}
