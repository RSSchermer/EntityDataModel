<?php

/*
 * This file is part of the Rolab Entity Data Model library.
 *
 * (c) Roland Schermer <roland0507@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rolab\EntityDataModel;

use Rolab\EntityDataModel\AssociationEnd;
use Rolab\EntityDataModel\Exception\InvalidArgumentException;

class Association
{
    private $name;
    
    private $namespace;
    
    private $endOne;
    
    private $endTwo;
    
    public function __construct($name, $namespace, AssociationEnd $endOne, AssociationEnd $endTwo)
    {
        $this->name = $name;
        $this->namespace = $namespace;
        $this->endOne = $endOne;
        $this->endTwo = $endTwo;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function getNamespace()
    {
        return $this->namespace;
    }
    
    public function getFullName()
    {
        return isset($this->namespace) ?  $this->namespace .'.'. $this->name : $this->name;
    }
    
    public function getEnds()
    {
        return array($this->endOne, $this->endTwo);
    }
    
    public function getEndByRole($role)
    {
        if ($this->endOne->getRole() === $role) {
            return $this->endOne;
        } elseif ($this->endtwo->getRole() === $role) {
            return $this->endTwo;
        }
        
        return null;
    }
}
