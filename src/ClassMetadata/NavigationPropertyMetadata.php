<?php

namespace RSSchermer\EntityModel\ClassMetadata;

class NavigationPropertyMetadata extends AbstractResourcePropertyMetadata
{
    public $targetEntityClassName;

    public $partner;

    public $onDeleteAction = 'none';

    public function serialize()
    {
        return serialize(array(
            $this->targetEntityClassName,
            $this->partner,
            $this->onDeleteAction,
            parent::serialize()
        ));
    }

    public function unserialize($data)
    {
        list(
            $this->targetEntityClassName,
            $this->partner,
            $this->onDeleteAction,
            $parentData
        ) = unserialize($data);

        parent::unserialize($parentData);
    }
}
