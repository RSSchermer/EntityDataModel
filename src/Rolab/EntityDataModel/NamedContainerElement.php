<?php

declare(strict_types=1);

namespace Rolab\EntityDataModel;

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
    public function __construct(string $name)
    {
        if (!preg_match('/^[A-Za-z0-9_]+$/', $name)) {
            throw new InvalidArgumentException(sprintf(
                '"%s" is an illegal name for a container element. The name for a container element may only contain ' .
                'alphanumeric characters and underscores.',
                $name
            ));
        }
        
        $this->name = $name;
    }
    
    /**
     * Returns the name of the named container element.
     *
     * @return string The name of the named container element.
     */
    public function getName() : string
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

    /**
     * Returns true if the container element is defined on the given entity container or a parent container of that
     * entity container, false otherwise.
     *
     * @param EntityContainer $entityContainer The entity container on which to check if this element is contained.
     *
     * @return bool Whether or not this element is contained in the given entity container.
     */
    public function isContainedIn(EntityContainer $entityContainer) : bool
    {
        if (null === $this->getEntityContainer()) {
            return false;
        }

        if ($entityContainer === $this->getEntityContainer()) {
            return true;
        }

        $parentContainer = $entityContainer->getParentContainer();

        while (null !== $parentContainer) {
            if ($parentContainer === $this->getEntityContainer()) {
                return true;
            }

            $parentContainer = $parentContainer->getParentContainer();
        }

        return false;
    }
}
