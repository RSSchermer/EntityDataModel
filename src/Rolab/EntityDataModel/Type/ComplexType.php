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

use Rolab\EntityDataModel\Type\StructuralType;
use Rolab\EntityDataModel\Type\ResourcePropertyDescription;
use Rolab\EntityDataModel\Type\StructuralPropertyDescription;
use Rolab\EntityDataModel\Exception\InvalidArgumentException;
use Rolab\EntityDataModel\Exception\RuntimeException;

class ComplexType extends StructuralType
{
    private $structuralPropertyDescriptions = array();

    public function __construct($name, \ReflectionClass $reflection, array $propertyDescriptions)
    {
        parent::__construct($name, $reflection);

        if (count($propertyDescriptions) === 0) {
            throw new InvalidArgumentException('May not pass an empty array of property descriptions. A complex ' .
                'type must always have atleast one property.');
        }

        foreach ($propertyDescriptions as $propertyDescription) {
            $this->addPropertyDescription($propertyDescription);
        }
    }

    public function addPropertyDescription(ResourcePropertyDescription $propertyDescription)
    {
        $propertyDescriptions = $this->getPropertyDescriptions();

        if (isset($propertyDescriptions[$propertyDescription->getName()])) {
            throw new InvalidArgumentException(sprintf('Type "%s" already has a property named "%s"',
                $this->getName(), $propertyDescription->getName()));
        }

        $this->addStructuralPropertyDescription($propertyDescription);
    }

    public function removePropertyDescription($propertyDescriptionName)
    {
        unset($this->structuralPropertyDescriptions[$propertyDescriptionName]);

        if (count($this->getPropertyDescriptions()) === 0) {
            throw new RuntimeException('A complex type must keep atleast one property.');
        }
    }

    public function getPropertyDescriptions()
    {
        return $this->getStructuralPropertyDescriptions();
    }

    public function getStructuralPropertyDescriptions()
    {
        return $this->structuralPropertyDescriptions;
    }

    public function getPropertyDescriptionByName($propertyDescriptionName)
    {
        $propertyDescriptions = $this->getPropertyDescriptions();

        return isset($propertyDescriptions[$propertyDescriptionName]) ?
            $propertyDescriptions[$propertyDescriptionName] : null;
    }

    protected function addStructuralPropertyDescription(StructuralPropertyDescription $propertyDescription)
    {
        $this->structuralPropertyDescriptions[$propertyDescription->getName()] = $propertyDescription;
    }
}
