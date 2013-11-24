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

use Rolab\EntityDataModel\EntityDataModel;
use Rolab\EntityDataModel\Type\ResourceType;
use Rolab\EntityDataModel\Exception\InvalidArgumentException;

abstract class StructuralType extends ResourceType
{
    private $name;

    private $reflection;

    private $entityDataModel;

    public function __construct($name, \ReflectionClass $reflection)
    {
        if (!preg_match('/^[A-Za-z0-9_]+$/', $name)) {
            throw new InvalidArgumentException(sprintf('"%s" is an illegal name for a structural type. The name for ' .
                'a structural type may only contain alphanumeric characters and underscores.', $name));
        }

        $this->name = $name;
        $this->reflection = $reflection;
    }

    public function setEntityDataModel(EntityDataModel $entityDataModel)
    {
        $this->entityDataModel = $entityDataModel;
    }

    public function getEntityDataModel()
    {
        return $this->entityDataModel;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getNamespace()
    {
        return isset($this->entityDataModel) ? $this->entityDataModel->getNamespace() : null;
    }

    public function getReflection()
    {
        return $this->reflection;
    }

    public function getClassName()
    {
        return $this->reflection->getName();
    }

    public function getFullName()
    {
        return isset($this->entityDataModel) ? $this->entityDataModel->getNamespace() .'.'. $this->name : $this->name;
    }
}
