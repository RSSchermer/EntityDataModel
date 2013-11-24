<?php

/*
 * This file is part of the Rolab Entity Data Model library.
 *
 * (c) Roland Schermer <roland0507@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rolab\EntityDataModel\Type;

use Rolab\EntityDataModel\Type\ComplexType;
use Rolab\EntityDataModel\Type\ResourcePropertyDescription;
use Rolab\EntityDataModel\Type\StructuralPropertyDescription;
use Rolab\EntityDataModel\Type\NavigationPropertyDescription;
use Rolab\EntityDataModel\Type\KeyPropertyDescription;
use Rolab\EntityDataModel\Type\ETagPropertyDescription;
use Rolab\EntityDataModel\Exception\InvalidArgumentException;

class EntityType extends ComplexType
{
    private $navigationPropertyDescriptions = array();

    private $keyPropertyDescriptions = array();

    private $eTagPropertyDescriptions = array();

    private $baseType;

    private $isAbstract;

    private $constructionCompleted = false;

    public function __construct($name, \ReflectionClass $reflection, array $propertyDescriptions,
        EntityType $baseType = null, $isAbstract = false
    ) {
        parent::__construct($name, $reflection, $propertyDescriptions);

        if (empty($this->keyPropertyDescriptions) && null === $baseType) {
            throw new InvalidArgumentException(sprintf(
                'Entity type "%s" must be given either atleast one KeyPropertyDescription or ' .
                'a base entity type.', $this->getFullName()));
        } elseif (count($this->keyPropertyDescriptions) > 0 && null !== $baseType) {
            throw new InvalidArgumentException(sprintf(
                'Entity type "%s" may be given either a base entity type or one or more KeyPropertyDescriptions, ' .
                'but it may not be given both a base type and KeyPropertyDescriptions.', $this->getFullName()
            ));
        }

        $this->baseType = $baseType;
        $this->isAbstract = false;
        $this->constructionCompleted = true;
    }

    public function getBaseType()
    {
        return $this->baseType;
    }

    public function isAbstract()
    {
        return $this->isAbstract;
    }

    public function addPropertyDescription(ResourcePropertyDescription $propertyDescription)
    {
        $propertyDescriptions = $this->getPropertyDescriptions();

        if (isset($propertyDescriptions[$propertyDescription->getName()])) {
            throw new InvalidArgumentException(sprintf('Type "%s" already has a property named "%s"',
                $this->getName(), $propertyDescription->getName()));
        }

        if ($this->constructionCompleted && $propertyDescription instanceof KeyPropertyDescription) {
            throw new InvalidArgumentException(sprintf(
                'Cannot add key properties after the initial construction of the entity type.',
                $this->getName(), $propertyDescription->getName()
            ));
        }

        if ($propertyDescription instanceof NavigationPropertyDescription) {
            $this->addNavigationPropertyDescription($propertyDescription);
        } elseif ($propertyDescription instanceof StructuralPropertyDescription) {
            $this->addStructuralPropertyDescription($propertyDescription);
        }
    }

    public function removePropertyDescription($propertyDescriptionName)
    {
        if (isset($this->keyPropertyDescriptions[$propertyDescriptionName])) {
            throw new InvalidArgumentException('Cannot remove key properties from an entity type.');
        }

        parent::removePropertyDescription($propertyDescriptionName);

        unset($this->navigationPropertyDescriptions[$propertyDescriptionName]);
        unset($this->eTagPropertyDescriptions[$propertyDescriptionName]);
    }

    public function getPropertyDescriptions()
    {
        return array_merge($this->getStructuralPropertyDescriptions(), $this->getNavigationPropertyDescriptions());
    }

    public function hasETag()
    {
        return isset($this->eTagPropertyDescriptions);
    }

    public function getKeyPropertyDescriptions()
    {
        return isset($this->baseType) ?
            array_merge($this->baseType->getKeyPropertyDescriptions(), $this->keyPropertyDescriptions) :
            $this->keyPropertyDescriptions;
    }

    public function getETagPropertyDescriptions()
    {
        return isset($this->baseType) ?
            array_merge($this->baseType->getETagPropertyDescriptions(), $this->eTagPropertyDescriptions) :
            $this->eTagPropertyDescriptions;
    }

    public function getStructuralPropertyDescriptions()
    {
        return isset($this->baseType) ? array_merge(
                $this->baseType->getStructuralPropertyDescriptions(), 
                parent::getStructuralPropertyDescriptions()
            ) : parent::getStructuralPropertyDescriptions();
    }

    public function getNavigationPropertyDescriptions()
    {
        return isset($this->baseType) ?
            array_merge($this->baseType->getNavigationPropertyDescriptions(), $this->navigationPropertyDescriptions) :
            $this->navigationPropertyDescriptions;
    }

    protected function addStructuralPropertyDescription(StructuralPropertyDescription $propertyDescription)
    {
        parent::addStructuralPropertyDescription($propertyDescription);

        if ($propertyDescription instanceof KeyPropertyDescription) {
            $this->keyPropertyDescriptions[$propertyDescription->getName()] = $propertyDescription;
        }

        if ($propertyDescription instanceof ETagPropertyDescription) {
            $this->eTagPropertyDescriptions[$propertyDescription->getName()] = $propertyDescription;
        }
    }

    protected function addNavigationPropertyDescription(NavigationPropertyDescription $propertyDescription)
    {
        $this->navigationPropertyDescriptions[$propertyDescription->getName()] = $propertyDescription;
    }
}
