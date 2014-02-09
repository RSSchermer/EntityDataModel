<?php

/*
 * This file is part of the Rolab Entity Data Model library.
 *
 * (c) Roland Schermer <roland0507@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rolab\EntityDataModel\Definition;

use Metadata\PropertyMetadata;

class NavigationPropertyMetadata extends PropertyMetadata
{
    public $targetEntity;
    
    public $role;
    
    public $targetRole;
    
    public $multiplicity = '0..1';
    
    public $deleteAction = 'none';
    
    public function __construct($class, $property, $targetEntity, $role = null)
    {
        parent::__construct($class, $property);
        
        $this->targetEntity = $targetEntity;
        $this->role = $role !== null ? $role : $propertyName;
    }
    
    public function serialize()
    {
        return serialize(array(
            $this->role,
            $this->targetEntity,
            $this->targetRole,
            $this->multiplicity,
            $this->deleteAction,
            parent::serialize()
        ));
    }
    
    public function unserialize($data)
    {
        list(
            $this->role,
            $this->targetEntity,
            $this->targetRole,
            $this->multiplicity,
            $this->deleteAction,
            $parentData
        ) = unserialize($data);
        
        parent::unserialize($parentData);
    }
}
