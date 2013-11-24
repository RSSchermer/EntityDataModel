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

use Rolab\EntityDataModel\Type\StructuralType;
use Rolab\EntityDataModel\Association;
use Rolab\EntityDataModel\EntityContainer;
use Rolab\EntityDataModel\Exception\InvalidArgumentException;

class EntityDataModel
{
    private $url;

    private $realNamespace;

    private $namespaceAlias;

    private $referencedModels = array();

    private $structuralTypes = array();

    private $structuralTypesByClassName = array();

    private $associations = array();

    private $entityContainers = array();

    private $defaultEntityContainer;

    public function __construct($url, $realNamespace, $namespaceAlias = null)
    {
        $this->url = $url;

        if (!preg_match('/^[A-Za-z0-9_\.]+$/', $realNamespace)) {
            throw new InvalidArgumentException(sprintf('"%s" is an illegal namespace for an entity data model. ' .
                'The namespace for an entity data model may only contain alphanumeric characters, underscores and ' .
                'dots.', $realNamespace));
        }

        $this->realNamespace = $realNamespace;
        $this->setNamespaceAlias($namespaceAlias);
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function getRealNamespace()
    {
        return $this->realNamespace;
    }

    public function getNamespaceAlias()
    {
        return $this->namespaceAlias;
    }

    public function getNamespace()
    {
        return isset($this->namespaceAlias) ? $this->namespaceAlias : $this->realNamespace;
    }

    public function setNamespaceAlias($namespaceAlias)
    {
        if (null !== $namespaceAlias && !preg_match('/^[A-Za-z0-9_\.]+$/', $namespaceAlias)) {
            throw new InvalidArgumentException(sprintf('"%s" is an illegal namespace alias for an entity data model. ' .
                'The namespace alias for an entity data model may only contain alphanumeric characters, ' .
                'underscores and dots.', $namespaceAlias));
        }

        $this->namespaceAlias = $namespaceAlias;
    }

    public function addReferencedModel(EntityDataModel $referencedModel, $namespaceAlias = null)
    {
        if (isset($namespaceAlias)) {
            $referencedModel->setNamespaceAlias($namespaceAlias);
        }

        $namespace = $referencedModel->getNamespace();

        if (isset($this->referencedModels[$namespace]) || $namespace === $this->getNamespace()) {
            throw new InvalidArgumentException(sprintf('Namespace "%s" is already used by some other referenced ' .
                'entity data model. Please specify an alias as the second argument.', $namespace));
        }

        $this->referencedModels[$namespace] = $referencedModel;
    }

    public function getReferencedModels()
    {
        return $this->referencedModels;
    }

    public function getReferencedModelByNamespace($namespace)
    {
        return isset($this->referencedModels[$namespace]) ? $this->referencedModels[$namespace] : null;
    }

    public function addStructuralType(StructuralType $structuralType)
    {
        if (isset($this->structuralTypes[$structuralType->getName()])) {
            throw new InvalidArgumentException(sprintf(
                'The entity data model already has a type by the name "%s".',
                $structuralType->getName()
            ));
        }

        if (isset($this->structuralTypesByClassName[$structuralType->getClassName()])) {
            throw new InvalidArgumentException(sprintf(
                'The entity data model already has a type of class "%s".',
                $structuralType->getClassName()
            ));
        }

        $this->structuralTypes[$structuralType->getName()] = $structuralType;
        $this->structuralTypesByClassName[$structuralType->getClassName()] = $structuralType;

        $structuralType->setEntityDataModel($this);
    }

    public function getStructuralTypes()
    {
        return $this->structuralTypes;
    }

    public function getStructuralTypeByName($name)
    {
        return isset($this->structuralTypes[$name]) ? $this->structuralTypes[$name] : null;
    }

    public function getStructuralTypeByClassName($className)
    {
        return isset($this->structuralTypesByClassName[$className]) ?
            $this->structuralTypesByClassName[$className] : null;
    }

    public function addAssociation(Association $association)
    {
        if (isset($this->associations[$association->getName()])) {
            throw new InvalidArgumentException(sprintf(
                'The entity data model already has an association by the name "%s"',
                $association->getName()
            ));
        }

        $this->associations[$association->getName()] = $association;

        $association->setEntityDataModel($this);
    }

    public function getAssociations()
    {
        return $this->associations;
    }

    public function getAssociationByName($name)
    {
        return isset($this->associations[$name]) ? $this->associations[$name] : null;
    }

    public function addEntityContainer(EntityContainer $entityContainer)
    {
        if (isset($this->entityContainers[$entityContainer->getName()])) {
            throw new InvalidArgumentException(sprintf(
                'The entity data model already has a container by the name "%s"',
                $entityContainer->getName()
            ));
        }

        $this->entityContainers[$entityContainer->getName()] = $entityContainer;

        $entityContainer->setEntityDataModel($this);

        if (count($this->entityContainers) === 1) {
            $this->setDefaultEntityContainer($entityContainer->getName());
        }
    }

    public function getEntityContainers()
    {
        return $this->entityContainers;
    }

    public function getEntityContainerByName($name)
    {
        return isset($this->entityContainers[$name]) ? $this->entityContainers[$name] : null;
    }

    public function setDefaultEntityContainer($containerName)
    {
        if (empty($this->entityContainers[$containerName])) {
            throw new InvalidArgumentException(sprintf(
                'Entity data model does not have container by the name "%s".',
                $containerName
            ));
        }

        $this->defaultEntityContainer = $this->entityContainers[$containerName];
    }

    public function getDefaultEntityContainer()
    {
        return $this->defaultEntityContainer;
    }

    public function findStructuralTypeByFullName($fullName)
    {
        list($namespace, $name) = $this->getNamespaceNameFromFullName($fullName);

        if ($namespace === null || $namespace === $this->getRealNamespace()
            || $namespace === $this->getNamespaceAlias()
        ) {
            return $this->getStructuralTypeByName($name);
        } elseif ($referencedModel = $this->getReferencedModelByNamespace($namespace)) {
            return $referencedModel->getStructuralTypeByName($name);
        }

        return null;
    }

    public function findAssociationByFullName($fullName)
    {
        list($namespace, $name) = $this->getNamespaceNameFromFullName($fullName);

        if ($namespace === null || $namespace === $this->getRealNamespace()
            || $namespace === $this->getNamespaceAlias()
        ) {
            return $this->getAssociationByName($name);
        } elseif ($referencedModel = $this->getReferencedModelByNamespace($namespace)) {
            return $referencedModel->getAssociationByName($name);
        }

        return null;
    }

    public function findEntityContainerByFullName($fullName)
    {
        list($namespace, $name) = $this->getNamespaceNameFromFullName($fullName);

        if ($namespace === null || $namespace === $this->getRealNamespace()
            || $namespace === $this->getNamespaceAlias()
        ) {
            return $this->getEntityContainerByName($name);
        } elseif ($referencedModel = $this->getReferencedModelByNamespace($namespace)) {
            return $referencedModel->getEntityContainerByName($name);
        }

        return null;
    }

    private function getNamespaceNameFromFullName($fullName)
    {
        $lastDotPos = stripos($fullName, '.');

        if (false !== $lastDotPos) {
            $namespace = substr($fullName, 0, $lastDotPos);
            $name = substr($fullName, $lastDotPos + 1);
        } else {
            $namespace = null;
            $name = $fullName;
        }

        return array($namespace, $name);
    }
}
