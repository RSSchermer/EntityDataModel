<?php

/*
 * This file is part of the Rolab Entity Data Model library.
 *
 * (c) Roland Schermer <roland0507@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rolab\EntityDataModel\Type\PropertyDescription;

abstract class ResourcePropertyDescription
{
    private $name;
    
    private $reflection;
    
    public function __construct($name, \ReflectionProperty $reflection)
    {
        $this->name = $name;
        $this->reflection = $reflection;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function getReflection()
    {
        return $this->reflection;
    }
}
