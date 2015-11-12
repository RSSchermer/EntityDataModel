<?php

namespace Rolab\EntityDataModel\Definition;

use Metadata\MergeableClassMetadata;

abstract class StructuredTypeMetadata extends MergeableClassMetadata
{
    public $typeName;
    
    public function serialize()
    {
        return serialize(array(
            $this->typeName,
            parent::serialize()
        ));
    }
    
    public function unserialize($data)
    {
        list(
            $this->typeName,
            $parentData
        ) = unserialize($data);
        
        parent::unserialize($parentData);
    }
}
