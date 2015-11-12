<?php

namespace Rolab\EntityDataModel\Definition;

use Metadata\MetadataFactory;

use Rolab\EntityDataModel\EntityDataModel;
use Rolab\EntityDataModel\Exception\DefinitionException;
use Rolab\EntityDataModel\Exception\InvalidArgumentException;
use Rolab\EntityDataModel\Type\ComplexPropertyDescription;
use Rolab\EntityDataModel\Type\ComplexType;
use Rolab\EntityDataModel\Type\EntityType;
use Rolab\EntityDataModel\Type\Edm;
use Rolab\EntityDataModel\Type\NavigationPropertyDescription;
use Rolab\EntityDataModel\Type\PrimitivePropertyDescription;

/**
 * Builder that simplifies the construction of an entity data model.
 *
 * Uses metadata on php classes to define the entity data model's structured types.
 */
class EntityDataModelBuilder
{
    /**
     * @var EntityDataModel
     */
    private $entityDataModel;

    /**
     * @var MetadataFactory
     */
    private $metadataFactory;

    /**
     * @var bool
     */
    private $autoLoadMissingReferences;

    /**
     * @var StructuredTypeMetadata[]
     */
    private $structuredTypeDefinitions = array();

    /**
     * Array to keep track of complex types that were added to the model during construction,
     * either through being explicitly added to the builder or being autoloaded.
     *
     * Keyed by class name.
     *
     * @var ComplexType[]
     */
    private $addedComplexTypes = array();

    /**
     * Array to keep track of entity types that were added to the model during construction,
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
     * the entity data model. Keyed by "className::propertyName".
     *
     * @var NavigationPropertyDescription[]
     */
    private $addedNavigationPropertyDescriptions = array();

    /**
     * Array to keep track of the navigation property definitions that were added to entity
     * types during construction.
     *
     * Used to set navigation property partners after all entity types have been added to
     * the entity data model. Keyed by "className::propertyName".
     *
     * @var NavigationPropertyMetadata[]
     */
    private $addedNavigationPropertyDefinitions = array();

    /**
     * Creates a new entity data model builder.
     *
     * @param MetadataFactory $metadataFactory           The metadata factory to use for loading class metadata for
     *                                                   structured types.
     * @param string          $uri                       The URI of the entity data model.
     * @param string          $namespace                 The namespace of the entity data model.
     * @param string|null     $namespaceAlias            An optional namespace alias for the entity data model.
     * @param bool            $autoLoadMissingReferences Whether or not to attempt to autoload referenced structured
     *                                                   types that were not explicitly added to the builder
     */
    public function __construct(
        MetadataFactory $metadataFactory,
        string $uri,
        string $namespace,
        string $namespaceAlias = null,
        bool $autoLoadMissingReferences = true)
    {
        $this->entityDataModel = new EntityDataModel($uri, $namespace, $namespaceAlias);
        $this->metadataFactory = $metadataFactory;
        $this->autoLoadMissingReferences = $autoLoadMissingReferences;
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
     * @return EntityDataModelBuilder The current entity data model builder for convenient method chaining.
     *
     * @throws InvalidArgumentException Thrown if the referenced model's namespace is the same as the
     *                                  namespace (alias) of another referenced model or the current
     *                                  entity data model or if (when specified) the namespace alias
     *                                  for the referenced model is the same as the namespace (alias)
     *                                  of another referenced model of the current entity data model.
     */
    public function addReferencedModel(
        EntityDataModel $referencedModel,
        string $namespaceAlias = null
    ) : EntityDataModelBuilder {
        $this->entityDataModel->addReferencedModel($referencedModel, $namespaceAlias);

        return $this;
    }

    /**
     * Add the structured type with the specified class name to the entity data model.
     *
     * Metadata describing a structured type must be available for the class through one
     * of the metadata drivers available to this entity data model builder.
     *
     * Structured types within one entity data model must have unique names and no two
     * structured types within one entity data model may be defined by the same class.
     *
     * @param string $className The name of the class (including namespace) that defines the
     *                          structured type.
     *
     * @return EntityDataModelBuilder The current entity data model builder for convenient method chaining.
     *
     * @throws InvalidArgumentException Thrown if no metadata describing a structured type is available for
     *                                  the specified class.
     */
    public function addStructuredType(string $className) : EntityDataModelBuilder
    {
        $metadata = $this->metadataFactory->getMetadataForClass($className);

        if ($metadata instanceof StructuredTypeMetadata) {
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
     * Constructs the resulting entity data model.
     *
     * @return EntityDataModel The resulting entity data model.
     */
    public function result() : EntityDataModel
    {
        $complexTypeDefinitions = array_filter($this->structuredTypeDefinitions, function ($definition) {
            return $definition instanceof ComplexTypeMetadata;
        });

        // Add complex types to the entity data model.
        foreach($complexTypeDefinitions as $complexTypeDefinition) {
            $this->addComplexTypeToEDM($complexTypeDefinition, $this->entityDataModel);
        }

        $entityTypeDefinitions = array_filter($this->structuredTypeDefinitions, function ($definition) {
            return $definition instanceof EntityTypeMetadata;
        });

        // Add entity types to the entity data model.
        foreach($entityTypeDefinitions as $entityTypeDefinition) {
            $this->addEntityTypeToEDM($entityTypeDefinition, $this->entityDataModel);
        }

        // Set navigation property partners.
        foreach ($this->addedNavigationPropertyDescriptions as $key => $propertyDescription) {
            $propertyDefinition = $this->addedNavigationPropertyDefinitions[$key];
            $partnerPropertyName = $propertyDefinition->partner;

            if (null !== $partnerPropertyName) {
                $targetEntityType = $propertyDescription->getStructuredType();
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

        return $this->entityDataModel;
    }

    /**
     * Adds a complex type to the entity data model.
     *
     * @param ComplexTypeMetadata $complexTypeDefinition The class metadata for the complex type.
     * @param EntityDataModel     $edm                   The entity data model to add the complex type to.
     *
     * @return ComplexType The resulting complex type.
     */
    private function addComplexTypeToEDM(ComplexTypeMetadata $complexTypeDefinition, EntityDataModel $edm) : ComplexType
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

        // Add the complex type to the entity data model before adding properties to the complex type
        // to allow circular references (one of the complex type's properties is a complex property that
        // itself has a complex property back to this complex type) without getting stuck in an endless loop.
        $edm->addStructuredType($complexType);

        // Now add property descriptions.
        foreach($complexTypeDefinition->propertyMetadata as $propertyDefinition) {
            if ($propertyDefinition instanceof PrimitivePropertyMetadata) {
                $propertyDescription = $this->buildPrimitivePropertyDescription($propertyDefinition);
                $complexType->addStructuralPropertyDescription($propertyDescription);
            } elseif ($propertyDefinition instanceof ComplexPropertyMetadata) {
                $propertyDescription = $this->buildComplexPropertyDescription($propertyDefinition, $edm);
                $complexType->addStructuralPropertyDescription($propertyDescription);
            }
        }

        $this->addedComplexTypes[$class] = $complexType;

        return $complexType;
    }

    /**
     * Adds an entity type to the entity data model.
     *
     * This will not yet partner the navigation properties. These still have to be
     * partnered after all entity types have been added to the model.
     *
     * @param EntityTypeMetadata $entityTypeDefinition The class metadata for the entity type.
     * @param EntityDataModel    $edm                  The entity data model to add the entity type to.
     *
     * @return EntityType The resulting entity type.
     */
    private function addEntityTypeToEDM(EntityTypeMetadata $entityTypeDefinition, EntityDataModel $edm) : EntityType
    {
        $className = $entityTypeDefinition->name;

        // The base type has to be resolved before the bailout check below, because the
        // base type could potentially reference this current type in a navigation property
        // which would cause this current type to be added to the model twice resulting in an
        // error.
        $baseType = $this->getBaseTypeForEntityTypeDefinition($entityTypeDefinition, $edm);

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
                $structuralProperties[] = $this->buildComplexPropertyDescription($propertyDefinition, $edm);
            }
        }

        $entityType = new EntityType($name, $reflection, $structuralProperties, $baseType);

        // Add the entity type to the entity data model before adding navigation properties to the entity type
        // to allow circular references without getting stuck in a non-terminating loop.
        $edm->addStructuredType($entityType);

        // Now add navigation property descriptions.
        foreach($entityTypeDefinition->propertyMetadata as $propertyDefinition) {
            if ($propertyDefinition instanceof NavigationPropertyMetadata) {
                $propertyDescription = $this->buildNavigationPropertyDescription($propertyDefinition, $edm);
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
        $className = 'Rolab\EntityDataModel\Type\Edm\\' . $propertyDefinition->primitiveTypeName;

        if (!class_exists($className)) {
            throw new DefinitionException(
                'Primitive property "%s" on class "%s" specifies type "%s", but no primitive type by that ' .
                'name is supported. Note that primitive type names are case sensitive.'
            );
        }

        return new PrimitivePropertyDescription(
            $propertyDefinition->nameOverride ?? $propertyDefinition->name,
            $propertyDefinition->reflection,
            new $className,
            $propertyDefinition->isCollection,
            $propertyDefinition->isNullable,
            $propertyDefinition->partOfKey,
            $propertyDefinition->partOfETag
        );
    }

    /**
     * Builds a complex property description from the given property metadata.
     *
     * Will attempt to autoload a complex type into the entity data model if autoloading
     * is enabled and the complex type was not explicitly added to the builder or already
     * visible in the entity data model.
     *
     * @param ComplexPropertyMetadata $propertyDefinition The metadata for the property.
     * @param EntityDataModel         $edm                The entity data model the owner complex type is a part of.
     *
     * @return ComplexPropertyDescription The resulting complex property description.
     */
    private function buildComplexPropertyDescription(
        ComplexPropertyMetadata $propertyDefinition,
        EntityDataModel $edm
    ) : ComplexPropertyDescription {
        $complexType = null;

        if ($structuredType = $edm->getStructuredTypeByClassName($propertyDefinition->complexTypeClassName)) {
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

            $complexType = $this->addComplexTypeToEDM($definition, $edm);
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
     * Will attempt to autoload an entity type into the entity data model if autoloading
     * is enabled and the entity type was not explicitly added to the builder or already
     * visible in the entity data model.
     *
     * Does not yet set a partner navigation property. This has to be done after all entity
     * types have been added to the entity data model.
     *
     * @param NavigationPropertyMetadata $propertyDefinition The metadata for the property.
     * @param EntityDataModel            $edm                The entity data model the owner entity type is a part of.
     *
     * @return NavigationPropertyDescription The resulting navigation property description.
     */
    private function buildNavigationPropertyDescription(
        NavigationPropertyMetadata $propertyDefinition,
        EntityDataModel $edm
    ) : NavigationPropertyDescription {
        $entityType = null;

        if ($structuredType = $edm->getStructuredTypeByClassName($propertyDefinition->targetEntityClassName)) {
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

            $entityType = $this->addEntityTypeToEDM($definition, $edm);
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
     * @param EntityDataModel $edm
     * @return EntityType
     */
    private function getBaseTypeForEntityTypeDefinition(
        EntityTypeMetadata $entityTypeDefinition,
        EntityDataModel $edm
    ) {
        if ($parentReflection = $entityTypeDefinition->reflection->getParentClass()) {
            $parentClassName = $parentReflection->getName();
            $parentMetadata = $this->metadataFactory->getMetadataForClass($parentClassName);

            if ($parentMetadata instanceof EntityTypeMetadata) {
                if ($parentType = $edm->getStructuredTypeByClassName($parentClassName)) {
                    return $parentType;
                } else {
                    return $this->addEntityTypeToEDM($parentMetadata, $edm);
                }
            }
        }

        return null;
    }
}
