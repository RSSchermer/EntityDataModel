<?php

namespace Rolab\EntityDataModel\Definition;

use Metadata\PropertyMetadata;

abstract class ResourcePropertyMetadata extends PropertyMetadata
{
    public $nameOverride;

    public $isNullable = true;
    
    public $isCollection = false;
    
    public function serialize()
    {
        return serialize(array(
            $this->isNullable,
            $this->isCollection,
            parent::serialize()
        ));
    }
    
    public function unserialize($data)
    {
        list(
            $this->isNullable,
            $this->isCollection,
            $parentData
        ) = unserialize($data);
        
        parent::unserialize($parentData);
    }
}
