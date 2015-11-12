<?php

namespace Rolab\EntityDataModel\Definition;

class ComplexPropertyMetadata extends ResourcePropertyMetadata
{
    public $complexTypeClassName;
    
    public function serialize()
    {
        return serialize(array(
            $this->complexTypeClassName,
            parent::serialize()
        ));
    }
    
    public function unserialize($data)
    {
        list(
            $this->complexTypeClassName,
            $parentData
        ) = unserialize($data);
        
        parent::unserialize($parentData);
    }
}
