<?php

namespace Rolab\EntityDataModel\Definition;

class PrimitivePropertyMetadata extends ResourcePropertyMetadata
{
    public $primitiveTypeName;
    
    public $partOfKey = false;
    
    public $partOfETag = false;
    
    public function serialize()
    {
        return serialize(array(
            $this->primitiveTypeName,
            $this->partOfKey,
            $this->partOfETag,
            parent::serialize()
        ));
    }
    
    public function unserialize($data)
    {
        list(
            $this->primitiveTypeName,
            $this->partOfKey,
            $this->partOfETag,
            $parentData
        ) = unserialize($data);
        
        parent::unserialize($parentData);
    }
}
