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

use Rolab\EntityDataModel\NamedModelConstruct;
use Rolab\EntityDataModel\EntityDataModel;

/**
 * Representents a data element that can be part of a dynamic entity data model.
 * 
 * @author Roland Schermer <roland0507@gmail.com>
 */
abstract class NamedModelElement extends NamedModelConstruct
{
    /**
     * @var EntityDataModel
     */
    private $entityDataModel;
    
    /**
     * Sets the entity data model the named model element is a part of.
     * 
     * Sets the entity data model the named model element is a part of. A named model element should
     * always be part of some entity data model.
     * 
     * @param EntityDataModel $entityDataModel The entity data model the named model element is
     *                                         a part of.
     */
    public function setEntityDataModel(EntityDataModel $entityDataModel)
    {
        $this->entityDataModel = $entityDataModel;
    }
    
    /**
     * Returns the entity data model the named model element is a part of.
     * 
     * @return null|EntityDataModel The entity data model the named model element is a part of
     *                              or null if no entity data model is assigned yet.
     */
    public function getEntityDataModel()
    {
        return $this->entityDataModel;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getNamespace()
    {
        return isset($this->entityDataModel) ? $this->entityDataModel->getNamespace() : null;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getFullName()
    {
        $namespace = $this->getNamespace();
        
        return $namespace ? $namespace .'.'. $this->getName() : $this->getName();
    }
}
