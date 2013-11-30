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

/**
 * Representation of an entity data model. The entity data model consists of
 * structural types, associations and entity containers. It should be uniquely
 * identified by a name in order to enable entity data model caching.
 *
 * @author Roland Schermer <roland0507@gmail.com>
 */
class EntityDataModel
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $realNamespace;

    /**
     * @var string
     */
    private $namespaceAlias;

    /**
     * @var array
     */
    private $referencedModels = array();

    /**
     * @var array
     */
    private $structuralTypes = array();

    /**
     * @var array
     */
    private $structuralTypesByClassName = array();

    /**
     * @var array
     */
    private $associations = array();

    /**
     * @var array
     */
    private $entityContainers = array();

    /**
     * @var EntityContainer
     */
    private $defaultEntityContainer;

    /**
     * Create new entity data model
     *
     * @param string      $name           Name for the entity data model, should be unique to allow caching.
     * @param string      $realNamespace  Fully qualified namespace for the entity data model (must consist of
     *                                    only alphanumeric characters, underscores and dots).
     * @param null|string $namespaceAlias Optional shorter namespace alias for easier referencing and to increase
     *                                    readability (must consist of only alphanumeric characters, underscores
     *                                    and dots).
     *
     * @throws InvalidArgumentException Thrown if the real namespace contains illegal characters or
     *                                  if the namespace alias contains illegal characters.
     */
    public function __construct($name, $realNamespace, $namespaceAlias = null)
    {
        $this->name = $name;

        if (!preg_match('/^[A-Za-z0-9_\.]+$/', $realNamespace)) {
            throw new InvalidArgumentException(sprintf('"%s" is an illegal namespace for an entity data model. ' .
                'The namespace for an entity data model may only contain alphanumeric characters, underscores and ' .
                'dots.', $realNamespace));
        }

        $this->realNamespace = $realNamespace;
        $this->setNamespaceAlias($namespaceAlias);
    }

    /**
     * Returns the name of the entity data model.
     *
     * @return string The name of the entity data model.
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the fully qualified namespace of the entity data model.
     *
     * @return string The fully qualified namespace of the entity data model.
     */
    public function getRealNamespace()
    {
        return $this->realNamespace;
    }

    /**
     * Returns the namespace alias used by this entity data model to allow easy
     * referencing and enhance readibility.
     *
     * @return string The namespace alias of the entity data model.
     */
    public function getNamespaceAlias()
    {
        return $this->namespaceAlias;
    }

    /**
     * Returns the namespace used to reference this entity data model in the current
     * context.
     *
     * Returns either the fully qualified namespace if no alias is specified for
     * this entity data model, or returns the namespace alias for this entity data
     * model if it was specified.
     *
     * @return string The namespace or namespace alias of the entity data model.
     */
    public function getNamespace()
    {
        return isset($this->namespaceAlias) ? $this->namespaceAlias : $this->realNamespace;
    }

    /**
     * Sets the namespace alias of the entity data model.
     *
     * Sets the namespace alias of the entity data model to a different value.
     * An alias can allow for easier referencing and can enhance readibility.
     *
     * @param string $namespaceAlias Namespace alias for the entity data model (must consist of only
     *                               alphanumeric characters, underscores and dots).
     *
     * @throws InvalidArgumentException Thrown if the namespace alias contains illegal characters.
     */
    public function setNamespaceAlias($namespaceAlias)
    {
        if (null !== $namespaceAlias && !preg_match('/^[A-Za-z0-9_\.]+$/', $namespaceAlias)) {
            throw new InvalidArgumentException(sprintf('"%s" is an illegal namespace alias for an entity data model. ' .
                'The namespace alias for an entity data model may only contain alphanumeric characters, ' .
                'underscores and dots.', $namespaceAlias));
        }

        $this->namespaceAlias = $namespaceAlias;
    }

    /**
     * Adds a referenced entity data model.
     *
     * Add an entity data model that can be referenced by elements of this entity data model.
     * All of the structural types, associations and entity containers of the referenced
     * model can be used by elements of the referencing model by prepending the names of the
     * elements with either the referenced models namespace or, if a namespace alias is specified
     * for the referenced model, by the namespace alias of the referenced model. If an alias is
     * specified for the referenced model, the alias MUST be used to reference the model. This
     * is to potentially allow references to models that share a real namespace, which can then
     * still be unique identified through the alias.
     *
     * @param EntityDataModel $referencedModel The model that is referenced by the current entity
     *                                         data model.
     * @param null|string     $namespaceAlias  An optional namespace alias for the model that is
     *                                         being referenced (an alias must be given if the
     *                                         referenced model shares a namespace with another
     *                                         referenced model or the current entity data model).
     *
     * @throws InvalidArgumentException Thrown if the referenced model's namespace is the same as the
     *                                  namespace (alias) of another referenced model or the current
     *                                  entity data model or if (when specified) the namespace alias
     *                                  for the referenced model is the same as the namespace (alias)
     *                                  of another referenced model of the current entity data model.
     */
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

    /**
     * Returns the entity data models referenced by the current entity data model.
     *
     * @return array An array of the entity data models referenced by the entity data model.
     */
    public function getReferencedModels()
    {
        return $this->referencedModels;
    }

    /**
     * Returns a referenced model based on its namespace (alias).
     *
     * Returns a referenced model based on either its real fully qualified namespace if
     * no alias was specified or on the namespace alias if it was specified.
     *
     * @param string $namespace The namespace or namespace alias of the referenced model.
     *
     * @return null|EntityDataModel Returns the referenced entity data model if a match was
     *                              found for the namespace or null.
     */
    public function getReferencedModelByNamespace($namespace)
    {
        return isset($this->referencedModels[$namespace]) ? $this->referencedModels[$namespace] : null;
    }

    /**
     * Adds a structural type to the entity data model.
     *
     * Adds a structural type to the entity data model. Structural types within one
     * entity data model must have unique names and no two structural types within one
     * entity data model may describe the same class.
     *
     * @param StructuralType $structuralType The structural type to be added to the entity data model.
     *
     * @throws InvalidArgumentException Thrown if the structural type has a name that is already
     *                                  used by another structural type in the entity data model
     *                                  or if the structural type describes a class that is already
     *                                  described by another structural type in the entity data model.
     */
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

    /**
     * Returns the structural types contained in the entity data model.
     *
     * Returns the structural typse contained in the current entity data model, but
     * will not return any structural types contained in the referenced entity data
     * models.
     *
     * @return array An array of the structural types contained in the entity data model.
     */
    public function getStructuralTypes()
    {
        return $this->structuralTypes;
    }

    /**
     * Returns a structural type contained in the model based on its name.
     *
     * Returns a structural type contained in the entity data model based on its name.
     * The name should NOT be prefixed with the entity data models namespace. Only
     * searches the current entity data model, not the referenced models. To search
     * not only the current entity data model but also the referenced models, see
     * findStructuralTypeByFullName().
     *
     * @param string $name The name of the structural type to be searched for (case sensitive)
     *                     without a namespace prefix.
     *
     * @return null|StructuralType A structural type with the name searched for or null if no
     *                             structural type could be found with that name in the
     *                             entity data model.
     *
     * @see EntityDataModel::findStructuralTypeByFullName() Including the referenced models in
     *                                                      the search based on a namespace
     *                                                      prefix.
     */
    public function getStructuralTypeByName($name)
    {
        return isset($this->structuralTypes[$name]) ? $this->structuralTypes[$name] : null;
    }

    /**
     * Returns a structural type contained in the entity data model based on the name
     * of the class it describes.
     *
     * Returns a structural type contained in the entity data model based on the name
     * of the class it describes. The class name should be a fully qualified class name
     * including the complete namespace without a leading backslash. Only the current
     * entity data model is searched, the referenced models are not searched.
     *
     * @param string $className The fully qualified class name (including namespace, with out
     *                          leading backslash) described by the structural type.
     *
     * @return null|StructuralType Returns a structural type if a structural type describing
     *                             the class was found in the entity data model or null if
     *                             no such structural type was found.
     */
    public function getStructuralTypeByClassName($className)
    {
        return isset($this->structuralTypesByClassName[$className]) ?
            $this->structuralTypesByClassName[$className] : null;
    }

    /**
     * Adds an association to the entity data model.
     *
     * Adds an association to the entity data model. Associations within one entity data
     * model must have unique names.
     *
     * @param Association $association The association to be added to the entity data model.
     *
     * @throws InvalidArgumentException Thrown if the association has a name that is already
     *                                  used by another association in the same entity data model.
     */
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

    /**
     * Returns all associations contained in the entity data model.
     *
     * Returns all associations contained in the entity data model, but will not return
     * the associations contained in any of the referenced entity data models.
     *
     * @return array An array of the associations contained in the entity data model.
     */
    public function getAssociations()
    {
        return $this->associations;
    }

    /**
     * Returns an association contained in the entity data model based on the name of the
     * association.
     *
     * Returns an association contained in the entity data model based on the name of the
     * association. The name should NOT be prefixed with the namespace of the entity data
     * model. Only searches the current entity data model, not the referenced models. To
     * search not only the current entity data model but also the referenced models, see
     * findAssociationByFullName().
     *
     * @param string $name The name of the association to be searched for (case sensitive)
     *                     without a namespace prefix.
     *
     * @return null|Association An association with the name searched for or null if no
     *                          association could be found with that name in the entity
     *                          data model.
     *
     * @see EntityDataModel::findAssociationByFullName() Including the referenced models
     *                                                   in the search based on a namespace
     *                                                   prefix.
     */
    public function getAssociationByName($name)
    {
        return isset($this->associations[$name]) ? $this->associations[$name] : null;
    }

    /**
     * Adds an entity container to the entity data model.
     *
     * Adds an entity container to the entity data model. Entity containers contained
     * within the same entity data model must have unique names.
     *
     * @param EntityContainer $entityContainer The entity container to be added to the entity
     *                                         data model.
     *
     * @throws InvalidArgumentException Thrown if the entity container has a name that is
     *                                  already being used by another entity container contained
     *                                  in the entity data model.
     */
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

    /**
     * Returns all entity containers contained in the entity data model.
     *
     * Returns all entity containers contained in the entity data model, but will not return
     * the entity containers contained in any of the referenced entity data models.
     *
     * @return array An array of the entity containers contained in the entity data model.
     */
    public function getEntityContainers()
    {
        return $this->entityContainers;
    }

    /**
     * Returns an entity container contained in the entity data model based on the name of the
     * entity container.
     *
     * Returns an entity container contained in the entity data model based on the name of the
     * entity container. The name should NOT be prefixed with the namespace of the entity data
     * model. Only searches the current entity data model, not the referenced models. To search
     * not only the current entity data model but also the referenced models, see
     * findEntityContainerByFullName().
     *
     * @param string $name The name of the entity container to be searched for (case sensitive)
     *                     without a namespace prefix.
     *
     * @return null|EntityContainer An entity container with the name searched for or null if
     *                              no entity container could be found with that name in the
     *                              entity data model.
     *
     * @see EntityDataModel::findEntityContainerByFullName() Including the referenced models in
     *                                                       the search based on a namespace
     *                                                       prefix.
     */
    public function getEntityContainerByName($name)
    {
        return isset($this->entityContainers[$name]) ? $this->entityContainers[$name] : null;
    }

    /**
     * Sets the default entity container of the entity data model.
     *
     * Sets the default entity container of the entity data model. Entity sets and
     * association sets contained in the default entity container can be referenced
     * without having to be prefixed with the entity container name. If no default
     * entity container is explicitly set, the first entity container that was
     * added to the entity data model will be used as the default entity container.
     *
     * @param string $containerName The name of the entity container that is to be set as
     *                              the default container (case sensitive) without a namespace
     *                              prefix.
     *
     * @throws InvalidArgumentException Thrown if no entity container with the specified name
     *                                  could be found in the entity data model.
     */
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

    /**
     * Returns the default entity container of the entity data model.
     *
     * @return EntityContainer The default entity container
     */
    public function getDefaultEntityContainer()
    {
        return $this->defaultEntityContainer;
    }

    /**
     * Searches the entity data model and referenced model for a structural type.
     *
     * Searches for a structural type based on its name, in the current entity data model
     * as well as the referenced models. If the name of the type is not prefixed with
     * a namespace the current model is searched. Otherwise the namespace is resolved
     * to a specific entity data model, either the current model or one of the referenced
     * models, and that specific model is then searched. If a namespace alias was specified
     * for a referenced model, only the namespace alias is used to resolve the referenced
     * entity data model.
     *
     * @param string $fullName The full name of the structural type to be searched for (case
     *                         sensitive).
     *
     * @return null|StructuralType The structural type with the name searched for or null
     *                             if no structural type with that name could be found.
     */
    public function findStructuralTypeByFullName($fullName)
    {
        list($namespace, $name) = $this->getNamespaceNameFromFullName($fullName);

        if ($namespace === null || $namespace === $this->getRealNamespace()
            || $namespace === $this->getNamespace()
        ) {
            return $this->getStructuralTypeByName($name);
        } elseif ($referencedModel = $this->getReferencedModelByNamespace($namespace)) {
            return $referencedModel->getStructuralTypeByName($name);
        }

        return null;
    }

    /**
     * Searches the entity data model and referenced model for an association.
     *
     * Searches for an association based on its name, in the current entity data model
     * as well as the referenced models. If the name of the association is not prefixed with
     * a namespace the current model is searched. Otherwise the namespace is resolved
     * to a specific entity data model, either the current model or one of the referenced
     * models, and that specific model is then searched. If a namespace alias was specified
     * for a referenced model, only the namespace alias is used to resolve the referenced
     * entity data model.
     *
     * @param string $fullName The full name of the association to be searched for (case
     *                         sensitive).
     *
     * @return null|Association The association with the name searched for or null if no
     *                          structural type with that name could be found.
     */
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

    /**
     * Searches the entity data model and referenced model for an entity container.
     *
     * Searches for an entity container based on its name, in the current entity data model
     * as well as the referenced models. If the name of the container is not prefixed with
     * a namespace the current model is searched. Otherwise the namespace is resolved
     * to a specific entity data model, either the current model or one of the referenced
     * models, and that specific model is then searched. If a namespace alias was specified
     * for a referenced model, only the namespace alias is used to resolve the referenced
     * entity data model.
     *
     * @param string $fullName The full name of the entity container to be searched for
     *                         (case sensitive).
     *
     * @return null|EntityContainer The entity container with the name searched for or null
     *                              if no entity container with that name could be found.
     */
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

    /**
     * Helper method to get a namespace and a name for an element of the entity data
     * model from its full name.
     *
     * @param string $fullName The full name of an element of the entity data model.
     *
     * @return array An array with the namespace at index 0 and the name at index 1.
     */
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
