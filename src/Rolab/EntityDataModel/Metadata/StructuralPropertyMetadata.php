<?php

/*
 * This file is part of the Rolab Entity Data Model library.
 *
 * (c) Roland Schermer <roland0507@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rolab\EntityDataModel\Metadata;

use Metadata\PropertyMetadata;

class StructuralPropertyMetadata extends PropertyMetadata
{
	public $targetClass;
	
	public $isEntityReference;
	
	public $isCollection;
	
	public function serialize()
    {
        return serialize(array(
            $this->class,
            $this->targetClass,
            $this->isEntityReference,
            $this->isCollection,
            $this->name,
        ));
    }

    public function unserialize($str)
    {
        list(
        	$this->class,
        	$this->targetClass,
        	$this->isEntityReference,
            $this->isCollection,
        	$this->name
		) = unserialize($str);

        $this->reflection = new \ReflectionProperty($this->class, $this->name);
        $this->reflection->setAccessible(true);
    }
}
	