<?php

namespace RSSchermer\EntityModel;

use Metadata\MetadataFactory;

use RSSchermer\EntityModel\ClassMetadata\AbstractStructuredTypeMetadata;
use RSSchermer\EntityModel\ClassMetadata\ComplexPropertyMetadata;
use RSSchermer\EntityModel\ClassMetadata\ComplexTypeMetadata;
use RSSchermer\EntityModel\ClassMetadata\EntityTypeMetadata;
use RSSchermer\EntityModel\ClassMetadata\NavigationPropertyMetadata;
use RSSchermer\EntityModel\ClassMetadata\PrimitivePropertyMetadata;
use RSSchermer\EntityModel\Exception\DefinitionException;
use RSSchermer\EntityModel\Exception\InvalidArgumentException;
use RSSchermer\EntityModel\Type\ComplexPropertyDescription;
use RSSchermer\EntityModel\Type\ComplexType;
use RSSchermer\EntityModel\Type\EntityType;
use RSSchermer\EntityModel\Type\Edm;
use RSSchermer\EntityModel\Type\NavigationPropertyDescription;
use RSSchermer\EntityModel\Type\PrimitivePropertyDescription;

/**
 * Builder that simplifies the construction of a schema.
 *
 * Uses metadata on php classes to define the schema's structured types.
 */
class SchemaBuilder
{
    /**
     * @var Schema
     */
    private $schema;

    /**
     * @var MetadataFactory
     */
    private $metadataFactory;

    /**
     * @var bool
     */
    private $autoLoadMissingReferences;

    /**
     * @var AbstractStructuredTypeMetadata[]
     */
    private $structuredTypeDefinitions = array();

    /**
     * Array to keep track of complex types that were added to the schema during construction,
     * either through being explicitly added to the builder or being autoloaded.
     *
     * Keyed by class name.
     *
     * @var ComplexType[]
     */
    private $addedComplexTypes = array();

    /**
     * Array to keep track of entity types that were added to the schema during construction,
     * either through being explicitly added to the builder or being autoloaded.
     *
     * Keyed by class name.
     *
     * @var EntityType[]
     */
    private $addedEntityTypes = array();

    /**
     * Array to keep track of the navigation property descriptions that were added to entity
     * types during construction.
     *
     * Used to set navigation property partners after all entity types have been added to
     * the schema. Keyed by "className::propertyName".
     *
     * @var NavigationPropertyDescription[]
     */
    private $addedNavigationPropertyDescriptions = array();

    /**
     * Array to keep track of the navigation property definitions that were added to entity
     * types during construction.
     *
     * Used to set navigation property partners after all entity types have been added to
     * the schema. Keyed by "className::propertyName".
     *
     * @var NavigationPropertyMetadata[]
     */
    private $addedNavigationPropertyDefinitions = array();

    /**
     * Creates a new schema builder.
     *
     * @param MetadataFactory $metadataFactory           The metadata factory to use for loading class metadata for
     *                                                   structured types.
     * @param string          $namespace                 The namespace for the schema.
     * @param string|null     $namespaceAlias            An optional namespace alias for the schema.
     * @param bool            $autoLoadMissingReferences Whether or not to attempt to autoload referenced structured
     *                                                   type classes that were not explicitly added to the builder.
     */
    public function __construct(
        MetadataFactory $metadataFactory,
        string $namespace,
        string $namespaceAlias = null,
        bool $autoLoadMissingReferences = true
    ) {
        $this->schema = new Schema($namespace, $namespaceAlias);
        $this->metadataFactory = $metadataFactory;
        $this->autoLoadMissingReferences = $autoLoadMissingReferences;
    }

    /**
     * Adds a referenced schema.
     *
     * Add a schema that can be referenced by elements of this schema.
     * All structured types defined on the referenced schema can be referenced by elements of
     * the referencing model by prepending the names of the elements with either the referenced
     * models namespace or, if a namespace alias is specified for the referenced schema, by the
     * namespace alias of the referenced schema. If an alias is specified for the referenced schema,
     * the alias MUST be used to reference the schema. This is to potentially allow references to
     * models that share a real namespace, which can then still be unique identified through the
     * alias.
     *
     * @param Schema $referencedModel          The model that is referenced by the current schema.
     * @param null|string     $namespaceAlias  An optional namespace alias for the schema that is
     *                                         being referenced (an alias must be given if the
     *                                         referenced schema shares a namespace with another
     *                                         referenced schema or the current schema).
     *
     * @return SchemaBuilder The current schema builder for convenient method chaining.
     *
     * @throws InvalidArgumentException Thrown if the referenced schema's namespace is the same as the
     *                                  namespace (alias) of another referenced schema or the current
     *                                  schema or if (when specified) the namespace alias for the
     *                                  referenced schema is the same as the namespace (alias) of another
     *                                  referenced schema of the current schema.
     */
    public function addReferencedModel(
        Schema $referencedModel,
        string $namespaceAlias = null
    ) : SchemaBuilder {
        $this->schema->addReferencedSchema($referencedModel, $namespaceAlias);

        return $this;
    }

    /**
     * Add the structured type to the schema based on class metadata defined on
     * the specified class name.
     *
     * Metadata describing a structured type must be available for the class through one
     * of the metadata drivers available to this schema builder.
     *
     * Structured types within one schema must have unique names and no two structured
     * types within one schema may be defined by the same class.
     *
     * @param string $className The name of the class (including namespace) that defines the
     *                          structured type.
     *
     * @return SchemaBuilder The current schema builder for convenient method chaining.
     *
     * @throws InvalidArgumentException Thrown if no metadata describing a structured type is available for
     *                                  the specified class.
     */
    public function addClass(string $className) : SchemaBuilder
    {
        $metadata = $this->metadataFactory->getMetadataForClass($className);

        if ($metadata instanceof AbstractStructuredTypeMetadata) {
            $this->structuredTypeDefinitions[$metadata->name] = $metadata;
        } else {
            throw new InvalidArgumentException(sprintf(
                'Could not find metadata for class "%s".',
                $className
            ));
        }

        return $this;
    }

    /**
     * Constructs the resulting schema.
     *
     * @return Schema The resulting schema.
     */
    public function result() : Schema
    {
        $complexTypeDefinitions = array_filter($this->structuredTypeDefinitions, function ($definition) {
            return $definition instanceof ComplexTypeMetadata;
        });

        // Add complex types to the schema.
        foreach($complexTypeDefinitions as $complexTypeDefinition) {
            $this->addComplexTypeToEDM($complexTypeDefinition, $this->schema);
        }

        $entityTypeDefinitions = array_filter($this->structuredTypeDefinitions, function ($definition) {
            return $definition instanceof EntityTypeMetadata;
        });

        // Add entity types to the schema.
        foreach($entityTypeDefinitions as $entityTypeDefinition) {
            $this->addEntityTypeToEDM($entityTypeDefinition, $this->schema);
        }

        // Set navigation property partners.
        foreach ($this->addedNavigationPropertyDescriptions as $key => $propertyDescription) {
            $propertyDefinition = $this->addedNavigationPropertyDefinitions[$key];
            $partnerPropertyName = $propertyDefinition->partner;

            if (null !== $partnerPropertyName) {
                $targetEntityType = $propertyDescription->getStructuredType()->get();
                $targetPropertyDescription = $targetEntityType->getPropertyDescriptionByName($partnerPropertyName);

                if (null === $targetPropertyDescription) {
                    throw new DefinitionException(sprintf(
                        'Navigation property "%s" defined on class "%s" which targets class "%s" declares its ' .
                        'partner navigation property to be "%s", but the target class does not define a property ' .
                        'by that name.',
                        $propertyDescription->getName(),
                        $propertyDefinition->class,
                        $propertyDefinition->targetEntityClassName,
                        $partnerPropertyName
                    ));
                } elseif (!$targetPropertyDescription instanceof NavigationPropertyDescription) {
                    throw new DefinitionException(sprintf(
                        'Navigation property "%s" defined on class "%s" which targets class "%s" declares its ' .
                        'partner navigation property to be "%s", but this property is not a navigation property.',
                        $propertyDescription->getName(),
                        $propertyDefinition->class,
                        $propertyDefinition->targetEntityClassName,
                        $partnerPropertyName
                    ));
                }

                $propertyDescription->setPartner($targetPropertyDescription);
            }
        }

        return $this->schema;
    }

    /**
     * Adds a complex type to the schema.
     *
     * @param ComplexTypeMetadata $complexTypeDefinition The class metadata for the complex type.
     * @param Schema              $schema                The schema to add the complex type to.
     *
     * @return ComplexType The resulting complex type.
     */
    private function addComplexTypeToEDM(ComplexTypeMetadata $complexTypeDefinition, Schema $schema) : ComplexType
    {
        $class = $complexTypeDefinition->name;

        // Check if the complex type was already added previously (through autoloading). If this
        // is the case, just return that complex type and skip the rest of this method.
        if (isset($this->addedComplexTypes[$class])) {
            return $this->addedComplexTypes[$class];
        }

        $reflection = $complexTypeDefinition->reflection;
        $name = $complexTypeDefinition->typeName ?? $reflection->getShortName();
        $complexType = new ComplexType($name, $complexTypeDefinition->reflection);

        // Add the complex type to the schema before adding properties to the complex type
        // to allow circular references (one of the complex type's properties is a complex
        // property that itself has a complex property back to this complex type) without
        // getting stuck in an endless loop.
        $schema->addStructuredType($complexType);

        // Now add property descriptions.
        foreach($complexTypeDefinition->propertyMetadata as $propertyDefinition) {
            if ($propertyDefinition instanceof PrimitivePropertyMetadata) {
                $propertyDescription = $this->buildPrimitivePropertyDescription($propertyDefinition);
                $complexType->addStructuralPropertyDescription($propertyDescription);
            } elseif ($propertyDefinition instanceof ComplexPropertyMetadata) {
                $propertyDescription = $this->buildComplexPropertyDescription($propertyDefinition, $schema);
                $complexType->addStructuralPropertyDescription($propertyDescription);
            }
        }

        $this->addedComplexTypes[$class] = $complexType;

        return $complexType;
    }

    /**
     * Adds an entity type to the schema.
     *
     * This will not yet partner the navigation properties. These still have to be
     * partnered after all entity types have been added to the schema.
     *
     * @param EntityTypeMetadata $entityTypeDefinition The class metadata for the entity type.
     * @param Schema             $schema               The schema to add the entity type to.
     *
     * @return EntityType The resulting entity type.
     */
    private function addEntityTypeToEDM(EntityTypeMetadata $entityTypeDefinition, Schema $schema) : EntityType
    {
        $className = $entityTypeDefinition->name;

        // The base type has to be resolved before the bailout check below, because the
        // base type could potentially reference this current type in a navigation property
        // which would cause this current type to be added to the schema twice resulting in an
        // error.
        $baseType = $this->getBaseTypeForEntityTypeDefinition($entityTypeDefinition, $schema);

        // Check if the entity type was already added previously (through autoloading). If this
        // is the case, adding the entity type again would result in an error.
        if (isset($this->addedEntityTypes[$className])) {
            return $this->addedEntityTypes[$className];
        }

        $reflection = $entityTypeDefinition->reflection;
        $name = $entityTypeDefinition->typeName ?? $reflection->getShortName();
        $structuralProperties = array();

        foreach($entityTypeDefinition->propertyMetadata as $propertyDefinition) {
            if ($propertyDefinition instanceof PrimitivePropertyMetadata) {
                $structuralProperties[] = $this->buildPrimitivePropertyDescription($propertyDefinition);
            } elseif ($propertyDefinition instanceof ComplexPropertyMetadata) {
                $structuralProperties[] = $this->buildComplexPropertyDescription($propertyDefinition, $schema);
            }
        }

        $entityType = new EntityType($name, $reflection, $structuralProperties, $baseType);

        // Add the entity type to the schema before adding navigation properties to the entity type
        // to allow circular references without getting stuck in a non-terminating loop.
        $schema->addStructuredType($entityType);

        // Now add navigation property descriptions.
        foreach($entityTypeDefinition->propertyMetadata as $propertyDefinition) {
            if ($propertyDefinition instanceof NavigationPropertyMetadata) {
                $propertyDescription = $this->buildNavigationPropertyDescription($propertyDefinition, $schema);
                $entityType->addNavigationPropertyDescription($propertyDescription);

                $key = $className . '::' . $propertyDescription->getName();
                $this->addedNavigationPropertyDefinitions[$key] = $propertyDefinition;
                $this->addedNavigationPropertyDescriptions[$key] = $propertyDescription;
            }
        }

        $this->addedEntityTypes[$className] = $entityType;

        return $entityType;
    }

    /**
     * Builds a primitive property description from the given metadata.
     *
     * @param PrimitivePropertyMetadata $propertyDefinition The metadata for the property.
     *
     * @return PrimitivePropertyDescription The resulting primitive property description.
     */
    private function buildPrimitivePropertyDescription(
        PrimitivePropertyMetadata $propertyDefinition
    ) {
        $className = 'RSSchermer\EntityModel\Type\Edm\Edm' . $propertyDefinition->primitiveTypeName;

        if (!class_exists($className)) {
            throw new DefinitionException(
                'Primitive property "%s" on class "%s" specifies type "%s", but no primitive type by that ' .
                'name is supported. Note that primitive type names are case sensitive.'
            );
        }

        return new PrimitivePropertyDescription(
            $propertyDefinition->nameOverride ?? $propertyDefinition->name,
            $propertyDefinition->reflection,
            call_user_func(array($className, 'create')),
            $propertyDefinition->isCollection,
            $propertyDefinition->isNullable,
            $propertyDefinition->partOfKey
        );
    }

    /**
     * Builds a complex property description from the given property metadata.
     *
     * Will attempt to autoload a complex type into the schema if autoloading is enabled
     * and the complex type was not explicitly added to the builder or already visible
     * in the schema.
     *
     * @param ComplexPropertyMetadata $propertyDefinition The metadata for the property.
     * @param Schema                  $schema             The schema the owner complex type is a part of.
     *
     * @return ComplexPropertyDescription The resulting complex property description.
     */
    private function buildComplexPropertyDescription(
        ComplexPropertyMetadata $propertyDefinition,
        Schema $schema
    ) : ComplexPropertyDescription {
        $complexType = null;

        if ($structuredType = $schema->getStructuredTypeByClassName($propertyDefinition->complexTypeClassName)) {
            if ($structuredType instanceof EntityType) {
                throw new DefinitionException(sprintf(
                    'The target class of complex property "%s" on class "%s" was set to "%s", but this class ' .
                    'defines an entity type, not a complex type. Only a class that defines a complex type can be set ' .
                    'as the target class of a complex property.',
                    $propertyDefinition->name,
                    $propertyDefinition->class,
                    $propertyDefinition->complexTypeClassName
                ));
            } elseif (!$structuredType instanceof ComplexType) {
                throw new DefinitionException(sprintf(
                    'The target class of complex property "%s" on class "%s" was set to "%s", but no valid complex ' .
                    'type metadata is available for this class.',
                    $propertyDefinition->name,
                    $propertyDefinition->class,
                    $propertyDefinition->complexTypeClassName
                ));
            }

            $complexType = $structuredType;
        } else {
            if (isset($this->structuredTypeDefinitions[$propertyDefinition->complexTypeClassName])) {
                $definition = $this->structuredTypeDefinitions[$propertyDefinition->complexTypeClassName];
            } elseif($this->autoLoadMissingReferences) {
                $definition = $this->metadataFactory->getMetadataForClass($propertyDefinition->complexTypeClassName);
            } else {
                throw new DefinitionException(sprintf(
                    'The target class of complex property "%s" on class "%s" was set to "%s", but no metadata was ' .
                    'explicitly added to the builder for this class and autoloading missing references was disabled.',
                    $propertyDefinition->name,
                    $propertyDefinition->class,
                    $propertyDefinition->complexTypeClassName
                ));
            }

            if ($definition instanceof EntityTypeMetadata) {
                throw new DefinitionException(sprintf(
                    'The target class of complex property "%s" on class "%s" was set to "%s", but this class ' .
                    'defines an entity type, not a complex type. Only a class that defines a complex type can be set ' .
                    'as the target class of a complex property.',
                    $propertyDefinition->name,
                    $propertyDefinition->class,
                    $propertyDefinition->complexTypeClassName
                ));
            } elseif(!$definition instanceof ComplexTypeMetadata) {
                throw new DefinitionException(sprintf(
                    'The target class of complex property "%s" on class "%s" was set to "%s", but no valid metadata ' .
                    'for a complex type was found for this class.',
                    $propertyDefinition->name,
                    $propertyDefinition->class,
                    $propertyDefinition->complexTypeClassName
                ));
            }

            $complexType = $this->addComplexTypeToEDM($definition, $schema);
        }

        return new ComplexPropertyDescription(
            $propertyDefinition->nameOverride ?? $propertyDefinition->name,
            $propertyDefinition->reflection,
            $complexType,
            $propertyDefinition->isCollection,
            $propertyDefinition->isNullable
        );
    }

    /**
     * Builds a navigation property description from the given property metadata.
     *
     * Will attempt to autoload an entity type into the schema if autoloading
     * is enabled and the entity type was not explicitly added to the builder or already
     * visible in the schema.
     *
     * Does not yet set a partner navigation property. This has to be done after all entity
     * types have been added to the schema.
     *
     * @param NavigationPropertyMetadata $propertyDefinition The metadata for the property.
     * @param Schema                     $schema             The schema the owner entity type is a part of.
     *
     * @return NavigationPropertyDescription The resulting navigation property description.
     */
    private function buildNavigationPropertyDescription(
        NavigationPropertyMetadata $propertyDefinition,
        Schema $schema
    ) : NavigationPropertyDescription {
        $entityType = null;

        if ($structuredType = $schema->getStructuredTypeByClassName($propertyDefinition->targetEntityClassName)) {
            if ($structuredType instanceof ComplexType) {
                throw new DefinitionException(sprintf(
                    'The target class of navigation property "%s" on class "%s" was set to "%s", but this class ' .
                    'defines a complex type, not an entity type. Only a class that defines an entity type can be set ' .
                    'as the target class of a navigation property.',
                    $propertyDefinition->name,
                    $propertyDefinition->class,
                    $propertyDefinition->targetEntityClassName
                ));
            } elseif (!$structuredType instanceof EntityType) {
                throw new DefinitionException(sprintf(
                    'The target class of navigation property "%s" on class "%s" was set to "%s", but no valid entity ' .
                    'type metadata is available for this class.',
                    $propertyDefinition->name,
                    $propertyDefinition->class,
                    $propertyDefinition->targetEntityClassName
                ));
            }

            $entityType = $structuredType;
        } else {
            if (isset($this->structuredTypeDefinitions[$propertyDefinition->targetEntityClassName])) {
                $definition = $this->structuredTypeDefinitions[$propertyDefinition->targetEntityClassName];
            } elseif($this->autoLoadMissingReferences) {
                $definition = $this->metadataFactory->getMetadataForClass($propertyDefinition->targetEntityClassName);
            } else {
                throw new DefinitionException(sprintf(
                    'The target class of navigation property "%s" on class "%s" was set to "%s", but no metadata was ' .
                    'explicitly added to the builder for this class and autoloading missing references was disabled.',
                    $propertyDefinition->name,
                    $propertyDefinition->class,
                    $propertyDefinition->targetEntityClassName
                ));
            }

            if ($definition instanceof ComplexTypeMetadata) {
                throw new DefinitionException(sprintf(
                    'The target class of navigation property "%s" on class "%s" was set to "%s", but this class ' .
                    'defines a complex type, not an entity type. Only a class that defines an entity type can be set ' .
                    'as the target class of a navigation property.',
                    $propertyDefinition->name,
                    $propertyDefinition->class,
                    $propertyDefinition->targetEntityClassName
                ));
            } elseif(!$definition instanceof EntityTypeMetadata) {
                throw new DefinitionException(sprintf(
                    'The target class of navigation property "%s" on class "%s" was set to "%s", but no valid ' .
                    'metadata for an entity type was found for this class.',
                    $propertyDefinition->name,
                    $propertyDefinition->class,
                    $propertyDefinition->targetEntityClassName
                ));
            }

            $entityType = $this->addEntityTypeToEDM($definition, $schema);
        }

        return new NavigationPropertyDescription(
            $propertyDefinition->nameOverride ?? $propertyDefinition->name,
            $propertyDefinition->reflection,
            $entityType,
            $propertyDefinition->isCollection,
            $propertyDefinition->isNullable,
            $propertyDefinition->onDeleteAction
        );
    }

    /**
     * Resolves the base type for an entity type definition or null if no base type is
     * defined.
     *
     * @param EntityTypeMetadata $entityTypeDefinition
     * @param Schema             $schema
     *
     * @return EntityType
     */
    private function getBaseTypeForEntityTypeDefinition(
        EntityTypeMetadata $entityTypeDefinition,
        Schema $schema
    ) {
        if ($parentReflection = $entityTypeDefinition->reflection->getParentClass()) {
            $parentClassName = $parentReflection->getName();
            $parentMetadata = $this->metadataFactory->getMetadataForClass($parentClassName);

            if ($parentMetadata instanceof EntityTypeMetadata) {
                if ($parentType = $schema->getStructuredTypeByClassName($parentClassName)) {
                    return $parentType;
                } else {
                    return $this->addEntityTypeToEDM($parentMetadata, $schema);
                }
            }
        }

        return null;
    }
}
