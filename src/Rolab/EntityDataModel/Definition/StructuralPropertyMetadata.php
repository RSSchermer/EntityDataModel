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

abstract class StructuralPropertyMetadata extends PropertyMetadata
{
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
