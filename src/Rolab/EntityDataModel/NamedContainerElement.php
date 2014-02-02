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
use Rolab\EntityDataModel\Exception\InvalidArgumentException;

/**
 * Representents a data element that can be part of an entity container.
 * 
 * @author Roland Schermer <roland0507@gmail.com>
 */
abstract class NamedContainerElement
{
    /**
     * @var string
     */
    private $name;
    
    /**
     * @var EntityContainer
     */
    private $entityContainer;
    
    /**
     * Creates a new named container element.
     * 
     * @param string $name The name of the container element (may contain only alphanumber characters 
     *                     and underscores).
     * 
     * @throws InvalidArgumentException Thrown if the container element's name contains illegal characters.
     */
    public function __construct($name)
    {
        if (!preg_match('/^[A-Za-z0-9_]+$/', $name)) {
            throw new InvalidArgumentException(sprintf('"%s" is an illegal name for a container element. The ' .
                'name for a container element may only contain alphanumeric characters and underscores.', $name));
        }
        
        $this->name = $name;
    }
    
    /**
     * Returns the name of the named container element.
     *
     * @return string The name of the named container element.
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Sets the entity container the container element is contained in.
     * 
     * Sets the entity container the container element is contained in. An container
     * element should always be part of some entity container.
     * 
     * @param EntityContainer $entityContainer The entity container the container element is a part of.
     */
    public function setEntityContainer(EntityContainer $entityContainer)
    {
        $this->entityContainer = $entityContainer;
    }
    
    /**
     * Returns the entity container the container element is contained in.
     * 
     * @return null|EntityContainer The entity container the container element is contained in or null if
     *                              no entity container was set.
     */
    public function getEntityContainer()
    {
        return $this->entityContainer;
    }
}
