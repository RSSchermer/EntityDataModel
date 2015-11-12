<?php

declare(strict_types=1);

namespace Rolab\EntityDataModel;

use Rolab\EntityDataModel\Type\StructuredType;
use Rolab\EntityDataModel\Exception\InvalidArgumentException;

/**
 * Representation of an entity data model. The entity data model defines a
 * collection of structured types. It should be uniquely identifiable by a URI.
 *
 * @author Roland Schermer <roland0507@gmail.com>
 */
class EntityDataModel
{
    /**
     * @var string
     */
    private $uri;

    /**
     * @var string
     */
    private $realNamespace;

    /**
     * @var string
     */
    private $namespaceAlias;

    /**
     * @var EntityDataModel[]
     */
    private $referencedModels = array();

    /**
     * @var StructuredType[]
     */
    private $structuredTypes = array();

    /**
     * @var StructuredType[]
     */
    private $structuredTypesByClassName = array();

    /**
     * Create new entity data model.
     *
     * @param string      $uri            URI for the entity data model
     * @param string      $realNamespace  Fully qualified namespace for the entity data model (must consist of
     *                                    only alphanumeric characters, underscores and dots).
     * @param null|string $namespaceAlias Optional shorter namespace alias for easier referencing and to increase
     *                                    readability (must consist of only alphanumeric characters, underscores
     *                                    and dots).
     *
     * @throws InvalidArgumentException Thrown if the real namespace contains illegal characters or
     *                                  if the namespace alias contains illegal characters.
     */
    public function __construct(string $uri, string $realNamespace, string $namespaceAlias = null)
    {
        $this->uri = $uri;

        if (!preg_match('/^[A-Za-z0-9_\.]+$/', $realNamespace)) {
            throw new InvalidArgumentException(sprintf(
                '"%s" is an illegal namespace for an entity data model. The namespace for an entity data model may ' .
                'only contain alphanumeric characters, underscores and dots.',
                $realNamespace
            ));
        }

        $this->realNamespace = $realNamespace;

        if (null !== $namespaceAlias) {
            $this->setNamespaceAlias($namespaceAlias);
        }
    }

    /**
     * Returns the URI of the entity data model.
     *
     * @return string The URI of the entity data model.
     */
    public function getUri() : string
    {
        return $this->uri;
    }

    /**
     * Returns the fully qualified namespace of the entity data model.
     *
     * @return string The fully qualified namespace of the entity data model.
     */
    public function getRealNamespace() : string
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
    public function getNamespace() : string
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
    public function setNamespaceAlias(string $namespaceAlias)
    {
        if (!preg_match('/^[A-Za-z0-9_\.]+$/', $namespaceAlias)) {
            throw new InvalidArgumentException(sprintf(
                '"%s" is an illegal namespace alias for an entity data model. The namespace alias for an entity ' .
                'data model may only contain alphanumeric characters, underscores and dots.',
                $namespaceAlias
            ));
        }

        $this->namespaceAlias = $namespaceAlias;
    }

    /**
     * Adds a referenced entity data model.
     *
     * Add an entity data model that can be referenced by elements of this entity data model.
     * All structured types defined on the referenced model can be referenced by elements of
     * the referencing model by prepending the names of the elements with either the referenced
     * models namespace or, if a namespace alias is specified for the referenced model, by the
     * namespace alias of the referenced model. If an alias is specified for the referenced model,
     * the alias MUST be used to reference the model. This is to potentially allow references to
     * models that share a real namespace, which can then still be unique identified through the
     * alias.
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
    public function addReferencedModel(EntityDataModel $referencedModel, string $namespaceAlias = null)
    {
        $namespace = $namespaceAlias ?? $referencedModel->getNamespace();

        if (isset($this->referencedModels[$namespace]) || $namespace === $this->getNamespace()) {
            throw new InvalidArgumentException(sprintf(
                'Namespace "%s" is already used by some other referenced entity data model. Specify a (different) ' .
                'namespace alias if you wish to reference this model.',
                $namespace
            ));
        }

        $this->referencedModels[$namespace] = $referencedModel;
    }

    /**
     * Returns the entity data models referenced by the current entity data model.
     *
     * @return EntityDataModel[] An array of the entity data models referenced by the entity data model.
     */
    public function getReferencedModels() : array
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
    public function getReferencedModelByNamespace(string $namespace)
    {
        return isset($this->referencedModels[$namespace]) ? $this->referencedModels[$namespace] : null;
    }

    /**
     * Adds a structured type to the entity data model.
     *
     * Adds a structured type to the entity data model. Structured types within one
     * entity data model must have unique names and no two structured types within one
     * entity data model may be defined by the same class.
     *
     * @param StructuredType $structuredType The structured type to be added to the entity data model.
     *
     * @throws InvalidArgumentException Thrown if the structured type has a name that is already
     *                                  used by another structured type in the entity data model
     *                                  or if the structured type describes a class that is already
     *                                  described by another structured type in the entity data model.
     */
    public function addStructuredType(StructuredType $structuredType)
    {
        if (isset($this->structuredTypes[$structuredType->getName()])) {
            throw new InvalidArgumentException(sprintf(
                'Tried to add a structured type named "%s" to entity data model "%s", but a structured type with ' .
                'with the same name already exists in this model. Structured type names must be unique within an ' .
                'entity data model.',
                $structuredType->getName(),
                $this->getRealNamespace()
            ));
        }

        if (isset($this->structuredTypesByClassName[$structuredType->getClassName()])) {
            throw new InvalidArgumentException(sprintf(
                'Tried to add a structured type of class "%s" to entity data model "%s", but a structured type with ' .
                'of the same class already exists in this model. Structured type classes must be unique within an ' .
                'entity data model.',
                $structuredType->getName(),
                $this->getRealNamespace()
            ));
        }

        $this->structuredTypes[$structuredType->getName()] = $structuredType;
        $this->structuredTypesByClassName[$structuredType->getClassName()] = $structuredType;

        $structuredType->setEntityDataModel($this);
    }

    /**
     * Returns the structured types contained in the entity data model.
     *
     * Returns the structural typse contained in the current entity data model, but
     * will not return any structured types contained in the referenced entity data
     * models.
     *
     * @return StructuredType[] An array of the structured types contained in the entity data model.
     */
    public function getStructuredTypes() : array
    {
        return $this->structuredTypes;
    }

    /**
     * Returns a structured type contained in the model based on its name.
     *
     * Returns a structured type contained in the entity data model based on its name.
     * The name should NOT be prefixed with the entity data models namespace. Only
     * searches the current entity data model, not the referenced models. To search
     * not only the current entity data model but also the referenced models, see
     * findStructuralTypeByFullName().
     *
     * @param string $name The name of the structured type to be searched for (case sensitive)
     *                     without a namespace prefix.
     *
     * @return null|StructuredType A structured type with the name searched for or null if no
     *                             structured type could be found with that name in the
     *                             entity data model.
     *
     * @see EntityDataModel::findStructuredTypeByFullName() Including the referenced models in
     *                                                      the search based on a namespace
     *                                                      prefix.
     */
    public function getStructuredTypeByName(string $name)
    {
        return isset($this->structuredTypes[$name]) ? $this->structuredTypes[$name] : null;
    }

    /**
     * Returns a structured type contained in the entity data model based on the name
     * of the class it describes.
     *
     * Returns a structured type contained in the entity data model based on the name
     * of the class it describes. The class name should be a fully qualified class name
     * including the complete namespace without a leading backslash. Only the current
     * entity data model is searched, the referenced models are not searched.
     *
     * @param string $className The fully qualified class name (including namespace, with out
     *                          leading backslash) described by the structured type.
     *
     * @return null|StructuredType Returns a structured type if a structured type describing
     *                             the class was found in the entity data model or null if
     *                             no such structured type was found.
     */
    public function getStructuredTypeByClassName(string $className)
    {
        if (isset($this->structuredTypesByClassName[$className])) {
            return $this->structuredTypesByClassName[$className];
        }

        return null;
    }

    /**
     * Searches the entity data model and referenced model for a structured type.
     *
     * Searches for a structured type based on its name, in the current entity data model
     * as well as the referenced models. If the name of the type is not prefixed with
     * a namespace the current model is searched. Otherwise the namespace is resolved
     * to a specific entity data model, either the current model or one of the referenced
     * models, and that specific model is then searched. If a namespace alias was specified
     * for a referenced model, only the namespace alias is used to resolve the referenced
     * entity data model.
     *
     * @param string $fullName The full name of the structured type to be searched for (case
     *                         sensitive).
     *
     * @return null|StructuredType The structured type with the name searched for or null
     *                             if no structured type with that name could be found.
     */
    public function findStructuredTypeByFullName(string $fullName)
    {
        list($namespace, $name) = $this->getNamespaceNameFromFullName($fullName);

        if ($namespace === null || $namespace === $this->getRealNamespace() || $namespace === $this->getNamespace()) {
            return $this->getStructuredTypeByName($name);
        } elseif ($referencedModel = $this->getReferencedModelByNamespace($namespace)) {
            return $referencedModel->getStructuredTypeByName($name);
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
    private function getNamespaceNameFromFullName(string $fullName) : array
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
