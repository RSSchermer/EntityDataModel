<?php

declare(strict_types=1);

namespace RSSchermer\EntityModel;

use PhpCollection\Map;
use PhpCollection\MapInterface;
use PhpOption\Option;
use PhpOption\Some;

use RSSchermer\EntityModel\Type\AbstractStructuredType;
use RSSchermer\EntityModel\Exception\InvalidArgumentException;

/**
 * Representation of a schema.
 *
 * A schema defines a set of types. A schema may reference other schemas,
 * in which case the types defined on the schema can reference types in these
 * referenced schema (for example by extending a referenced type).
 *
 * @author Roland Schermer <roland0507@gmail.com>
 */
class Schema
{
    /**
     * @var string
     */
    private $namespace;

    /**
     * @var Option
     */
    private $namespaceAlias;

    /**
     * @var MapInterface
     */
    private $referencedSchemas;

    /**
     * @var MapInterface
     */
    private $structuredTypes;

    /**
     * @var MapInterface
     */
    private $structuredTypesByClassName;

    /**
     * Create new schema.
     *
     * @param string      $namespace      Fully qualified namespace for the schema (must consist of
     *                                    only alphanumeric characters, underscores and dots).
     * @param null|string $namespaceAlias Optional shorter namespace alias for easier referencing and to increase
     *                                    readability (must consist of only alphanumeric characters, underscores
     *                                    and dots).
     *
     * @throws InvalidArgumentException Thrown if the real namespace contains illegal characters or
     *                                  if the namespace alias contains illegal characters.
     */
    public function __construct(string $namespace, string $namespaceAlias = null)
    {
        if (!preg_match('/^[A-Za-z0-9_\.]+$/', $namespace)) {
            throw new InvalidArgumentException(sprintf(
                '"%s" is an illegal namespace for a schema. The namespace for a schema may only contain alphanumeric ' .
                'characters, underscores and dots.',
                $namespace
            ));
        }

        $this->namespace = $namespace;
        $this->namespaceAlias = Option::fromValue($namespaceAlias);
        $this->referencedSchemas = new Map();
        $this->structuredTypes = new Map();
        $this->structuredTypesByClassName = new Map();
    }

    /**
     * Returns the fully qualified namespace of the schema.
     *
     * @return string The fully qualified namespace of the schema.
     */
    public function getFullNamespace() : string
    {
        return $this->namespace;
    }

    /**
     * Returns the namespace alias of the schema wrapped on Some or None if
     * no namespace alias was specified.
     *
     * @return Option The namespace alias of the schema wrapped on Some or None if no namespace alias
     *                was specified.
     */
    public function getNamespaceAlias() : Option
    {
        return $this->namespaceAlias;
    }

    /**
     * Returns the namespace used to reference this schema in the current
     * context.
     *
     * Returns either the fully qualified namespace if no alias is specified for
     * this schema, or returns the namespace alias for this schema if it was
     * specified.
     *
     * @return string The namespace or namespace alias of the schema.
     */
    public function getNamespace() : string
    {
        return $this->namespaceAlias->getOrElse($this->namespace);
    }

    /**
     * Sets the namespace alias of the schema.
     *
     * Sets the namespace alias of the schema to a different value.
     * An alias can allow for easier referencing and can enhance readability.
     *
     * @param string $namespaceAlias Namespace alias for the schema (must consist of only
     *                               alphanumeric characters, underscores and dots).
     *
     * @throws InvalidArgumentException Thrown if the namespace alias contains illegal characters.
     */
    public function setNamespaceAlias(string $namespaceAlias)
    {
        if (!preg_match('/^[A-Za-z0-9_\.]+$/', $namespaceAlias)) {
            throw new InvalidArgumentException(sprintf(
                '"%s" is an illegal namespace alias for a schema. The namespace alias for a schema may only contain ' .
                'alphanumeric characters, underscores and dots.',
                $namespaceAlias
            ));
        }

        $this->namespaceAlias = new Some($namespaceAlias);
    }

    /**
     * Adds a referenced schema.
     *
     * Add a schema that can be referenced by elements of this schema.
     * All structured types defined on the referenced schema can be referenced by elements of
     * the referencing schema by prepending the names of the elements with either the referenced
     * models namespace or, if a namespace alias is specified for the referenced schema, by the
     * namespace alias of the referenced schema. If an alias is specified for the referenced schema,
     * the alias MUST be used to reference the schema. This is to potentially allow references to
     * models that share a real namespace, which can then still be unique identified through the
     * alias.
     *
     * @param Schema      $referencedSchema The model that is referenced by the current entity
     *                                      data model.
     * @param null|string $namespaceAlias   An optional namespace alias for the schema that is
     *                                      being referenced (an alias must be given if the
     *                                      referenced schema shares a namespace with another
     *                                      referenced schema or the current schema).
     *
     * @throws InvalidArgumentException Thrown if the referenced schema's namespace is the same as the
     *                                  namespace (alias) of another referenced schema or the current
     *                                  schema or if (when specified) the namespace alias for the
     *                                  referenced schema is the same as the namespace (alias) of another
     *                                  referenced schema of the current schema.
     */
    public function addReferencedSchema(Schema $referencedSchema, string $namespaceAlias = null)
    {
        $namespace = $namespaceAlias ?? $referencedSchema->getNamespace();

        if ($this->referencedSchemas->containsKey($namespace) || $namespace === $this->getNamespace()) {
            throw new InvalidArgumentException(sprintf(
                'Namespace "%s" is already used by some other referenced schema. Specify a (different) namespace ' .
                'alias if you wish to reference this model.',
                $namespace
            ));
        }

        $this->referencedSchemas->set($namespace, $referencedSchema);
    }

    /**
     * Returns the schemas referenced by the schema.
     *
     * @return MapInterface Map of the schemas referenced by the schema keyed by their
     *                      namespace or namespace alias of specified.
     */
    public function getReferencedSchemas() : MapInterface
    {
        return $this->referencedSchemas;
    }

    /**
     * Returns a referenced schema based on its namespace (alias).
     *
     * Returns a referenced schema based on either its real fully qualified namespace if
     * no alias was specified or on the namespace alias if it was specified.
     *
     * @param string $namespace The namespace or namespace alias of the referenced schema.
     *
     * @return Option Returns the referenced schema wrapped in Some if a match was
     *                found for the namespace, None otherwise.
     */
    public function getReferencedSchemaByNamespace(string $namespace) : Option
    {
        return $this->referencedSchemas->get($namespace);
    }

    /**
     * Adds a structured type to the schema.
     *
     * Adds a structured type to the schema. Structured types within one
     * schema must have unique names and no two structured types within one
     * schema may be defined by the same class.
     *
     * @param AbstractStructuredType $structuredType The structured type to be added to the schema.
     *
     * @throws InvalidArgumentException Thrown if the structured type has a name that is already
     *                                  used by another structured type in the schema or if the
     *                                  structured type describes a class that is already
     *                                  described by another structured type in the schema.
     */
    public function addStructuredType(AbstractStructuredType $structuredType)
    {
        if ($this->structuredTypes->containsKey($structuredType->getName())) {
            throw new InvalidArgumentException(sprintf(
                'Tried to add a structured type named "%s" to schema "%s", but a structured type with with the same ' .
                'name already exists in this schema. Structured type names must be unique within an schema.',
                $structuredType->getName(),
                $this->getNamespace()
            ));
        }

        if ($this->structuredTypesByClassName->containsKey($structuredType->getClassName())) {
            throw new InvalidArgumentException(sprintf(
                'Tried to add a structured type of class "%s" to schema "%s", but a structured type with of the same ' .
                'class already exists in this schema. Structured type classes must be unique within a schema.',
                $structuredType->getName(),
                $this->getNamespace()
            ));
        }

        $this->structuredTypes->set($structuredType->getName(), $structuredType);
        $this->structuredTypesByClassName->set($structuredType->getClassName(), $structuredType);

        $structuredType->setSchema($this);
    }

    /**
     * Returns the structured types contained in the schema.
     *
     * Returns the structural typse contained in the current schema, but will not
     * return any structured types contained in the referenced schema.
     *
     * @return MapInterface The structured types contained in the schema keyed by name.
     */
    public function getStructuredTypes() : MapInterface
    {
        return $this->structuredTypes;
    }

    /**
     * Returns a structured type contained in the schema based on its name.
     *
     * Returns a structured type contained in the schema based on its name.
     * The name should NOT be prefixed with the schemas namespace. Only
     * searches the current schema, not the referenced schemas. To search
     * not only the current schema but also the referenced schemas, see
     * findStructuredTypeByFullName().
     *
     * @param string $name The name of the structured type to be searched for (case sensitive)
     *                     without a namespace prefix.
     *
     * @return Option A structured type with the name searched wrapped in Some or None if no
     *                structured type with that name could be found in the schema.
     *
     * @see EntityDataModel::findStructuredTypeByFullName() Including the referenced schemas in
     *                                                      the search based on a namespace
     *                                                      prefix.
     */
    public function getStructuredTypeByName(string $name) : Option
    {
        return $this->structuredTypes->get($name);
    }

    /**
     * Returns a structured type contained in the schema based on the name
     * of the class it describes.
     *
     * Returns a structured type contained in the schema based on the name of the
     * class it describes. The class name should be a fully qualified class name
     * including the complete namespace without a leading backslash. Only the current
     * schema is searched, the referenced schemas are not searched.
     *
     * @param string $className The fully qualified class name (including namespace, without a
     *                          leading backslash) described by the structured type.
     *
     * @return Option Returns a structured type wrapped in Some if a structured type describing
     *                the class was found in the schema or None if no such structured
     *                type was found.
     */
    public function getStructuredTypeByClassName(string $className) : Option
    {
        return $this->structuredTypesByClassName->get($className);
    }

    /**
     * Searches the schema and referenced schema for a structured type.
     *
     * Searches for a structured type based on its name, in the current schema as
     * well as the referenced schemas. If the name of the type is not prefixed with
     * a namespace the current schema is searched. Otherwise the namespace is resolved
     * to a specific schema, either the current model or one of the referenced schema,
     * and that specific model is then searched. If a namespace alias was specified for
     * a referenced schema, only the namespace alias is used to resolve the referenced
     * schema.
     *
     * @param string $fullName The full name of the structured type to be searched for (case
     *                         sensitive).
     *
     * @return Option The structured type with the name searched for wrapped in Some or None
     *                if no structured type with that name could be found.
     */
    public function findStructuredTypeByFullName(string $fullName) : Option
    {
        list($namespace, $name) = $this->getNamespaceNameFromFullName($fullName);

        if ($namespace === null || $namespace === $this->getNamespace() || $namespace === $this->getNamespace()) {
            return $this->getStructuredTypeByName($name);
        } else {
            return $this->getReferencedSchemaByNamespace($namespace)->flatMap(function ($referencedModel) use ($name) {
                return $referencedModel->getStructuredTypeByName($name);
            });
        }
    }

    /**
     * Helper method to get a namespace and a name for an element of the schema
     * from its full name.
     *
     * @param string $fullName The full name of an element of the schema.
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
