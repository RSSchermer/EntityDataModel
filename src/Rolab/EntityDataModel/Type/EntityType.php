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
use Rolab\EntityDataModel\Type\PropertyDescription\RegularPropertyDescription;
use Rolab\EntityDataModel\Type\PropertyDescription\NavigationPropertyDescription;
use Rolab\EntityDataModel\Type\PropertyDescription\KeyPropertyDescription;
use Rolab\EntityDataModel\Type\PropertyDescription\ETagPropertyDescription;
use Rolab\EntityDataModel\Exception\InvalidArgumentException;

class EntityType extends ComplexType
{
    private $navigationPropertyDescriptions = array();

    private $keyPropertyDescriptions = array();

    private $eTagPropertyDescriptions = array();

    public function __construct($name, $namespace, \ReflectionClass $reflection, array $propertyDescriptions,
        ComplexType $baseType = null
    ){
        parent::__construct($name, $namespace, $reflection, $propertyDescriptions, $baseType);
    }

    public function setPropertyDescriptions(array $propertyDescriptions)
    {
        $hasKey = false;
        foreach ($propertyDescriptions as $propertyDescription) {
            $this->addPropertyDescription($propertyDescription);

            if ($propertyDescription instanceof KeyPropertyDescription) {
                $hasKey = true;
            }
        }

        if (!$hasKey) {
            throw new InvalidArgumentException(sprintf('Entity type "%s" must be given atleast one property of type ' .
                '\Rolab\EntityDataModel\Type\PropertyDescription\KeyPropertyDescription', $this->getFullName()));
        }
    }

    public function getPropertyDescriptions()
    {
        return array_merge($this->getRegularPropertyDescriptions(), $this->getNavigationPropertyDescriptions());
    }

    public function addPropertyDescription(ResourcePropertyDescription $propertyDescription)
    {
        if (isset($this->properties[$propertyDescription->getName()])) {
            throw new InvalidArgumentException(sprintf('Type "%s" already has a property named "%s"',
                $this->getFullName(), $propertyDescription->getName()));
        }

        if ($propertyDescription instanceof NavigationPropertyDescription) {
            $this->addNavigationPropertyDescription($propertyDescription);
        } elseif ($propertyDescription instanceof RegularPropertyDescription) {
            $this->addRegularPropertyDescription($propertyDescription);
        }

        if ($propertyDescription instanceof KeyPropertyDescription) {
            $this->keyPropertyDescriptions[$propertyDescription->getName()] = $propertyDescription;
        }

        if ($propertyDescription instanceof ETagPropertyDescription) {
            $this->eTagPropertyDescriptions[$propertyDescription->getName()] = $propertyDescription;
        }
    }

    public function addNavigationPropertyDescription(NavigationPropertyDescription $propertyDescription)
    {
        $propertyDescriptions = $this->getPropertyDescriptions();

        if (isset($propertyDescriptions[$propertyDescription->getName()])) {
            throw new InvalidArgumentException(sprintf('Type "%s" already has a property named "%s"',
                $this->getFullName(), $propertyDescription->getName()));
        }

        $this->navigationPropertyDescriptions[$propertyDescription->getName()] = $propertyDescription;
    }

    public function getNavigationPropertyDescriptions()
    {
        return $this->navigationPropertyDescriptions;
    }

    public function removePropertyDescription($propertyDescriptionName)
    {
        parent::removePropertyDescription($propertyDescriptionName);

        unset($this->navigationPropertyDescriptions[$propertyDescriptionName]);
        unset($this->keyPropertyDescriptions[$propertyDescriptionName]);
        unset($this->eTagPropertyDescriptions[$propertyDescriptionName]);

        if (count($this->keyPropertyDescriptions) === 0) {
            throw new InvalidArgumentException(sprintf('Entity type "%s" must keep atleast one property of type ' .
                '\Rolab\EntityDataModel\Type\PropertyDescription\KeyPropertyDescription', $this->getFullName()));
        }
    }

    public function getKeyPropertyDescriptions()
    {
        return $this->keyPropertyDescriptions;
    }

    public function hasETag()
    {
        return isset($this->eTagPropertyDescriptions);
    }

    public function getETagPropertyDescriptions()
    {
        return $this->eTagPropertyDescriptions;
    }
}
