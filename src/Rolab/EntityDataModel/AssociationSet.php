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

use Rolab\EntityDataModel\Association;
use Rolab\EntityDataModel\AssociationSetEnd;
use Rolab\EntityDataModel\AssociationSetEnd;
use Rolab\EntityDataModel\EntityContainer;

class AssociationSet
{
    private $name;
    
    private $association;
    
    private $setEndOne;
    
    private $setEndTwo;
    
    private $entityContainer;
    
    public function __construct($name, Association $association, AssociationSetEnd $setEndOne, AssociationSetEnd $setEndTwo,
        EntityContainer $entityContainer
    ){
        $this->name = $name;
        $this->association = $association;
        $this->setEndOne = $setEndOne;
        $this->setEndTwo = $setEndTwo;
        $this->entityContainer = $entityContainer;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function getAssociation()
    {
        return $this->association;
    }
    
    public function getSetEnds()
    {
        return array($this->setEndOne, $this->setEndTwo);
    }
    
    public function getSetEndByRole($role)
    {
        if ($this->setEndOne->getAssociationEnd()->getRole() === $role) {
            return $this->setEndOne;
        } elseif ($this->setEndTwo->getAssociationEnd()->getRole() === $role) {
            return $this->setEndTwo;
        }
        
        return null;
    }
    
    public function getEntityContainer()
    {
        return $this->entityContainer;
    }
}
