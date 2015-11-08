<?php

declare(strict_types=1);

namespace Rolab\EntityDataModel;

use Rolab\EntityDataModel\Exception\InvalidArgumentException;

/**
 * Representents a data element that can be part of a dynamic entity data model.
 * 
 * @author Roland Schermer <roland0507@gmail.com>
 */
abstract class NamedModelElement implements NamedModelConstruct
{
    /**
     * @var EntityDataModel
     */
    private $entityDataModel;

    /**
     * @var string
     */
    private $name;

    /**
     * Creates a new named model construct.
     *
     * @param string $name The name of the model construct (may contain only alphanumber characters
     *                     and underscores).
     *
     * @throws InvalidArgumentException Thrown if the model construct's name contains illegal characters.
     */
    public function __construct(string $name)
    {
        if (!preg_match('/^[A-Za-z0-9_]+$/', $name)) {
            throw new InvalidArgumentException(sprintf(
                '"%s" is an illegal name for a data model construct. The name for a data model construct may only ' .
                'contain alphanumeric characters and underscores.',
                $name
            ));
        }

        $this->name = $name;
    }

    /**
     * {@inheritDoc}
     */
    public function getName() : string
    {
        return $this->name;
    }

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
    public function getNamespace() : string
    {
        return isset($this->entityDataModel) ? $this->entityDataModel->getNamespace() : "";
    }
    
    /**
     * {@inheritDoc}
     */
    public function getFullName() : string
    {
        $namespace = $this->getNamespace();
        
        return $namespace ? $namespace .'.'. $this->getName() : $this->getName();
    }
}
