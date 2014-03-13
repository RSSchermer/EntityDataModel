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

use Rolab\EntityDataModel\Definition\StructuralPropertyMetadata;

abstract class ComplexPropertyMetadata extends StructuralPropertyMetadata
{
    public $className;
    
    public function serialize()
    {
        return serialize(array(
            $this->className,
            parent::serialize()
        ));
    }
    
    public function unserialize($data)
    {
        list(
            $this->className,
            $parentData
        ) = unserialize($data);
        
        parent::unserialize($parentData);
    }
}
