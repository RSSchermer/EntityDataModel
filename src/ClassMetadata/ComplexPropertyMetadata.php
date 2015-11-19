<?php

namespace RSSchermer\EntityModel\ClassMetadata;

class ComplexPropertyMetadata extends AbstractResourcePropertyMetadata
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
