<?php

/*
 * This file is part of the Rolab Entity Data Model library.
 *
 * (c) Roland Schermer <roland0507@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rolab\EntityDataModel\Builder;

use Rolab\EntityDataModel\EntityDataModel;
use Rolab\EntityDataModel\EntityContainer;
use Rolab\EntityDataModel\Type\ComplexType;
use Rolab\EntityDataModel\Type\EntityType;
use Rolab\EntityDataModel\Type\PropertyDescription\PrimitivePropertyDescription;
use Rolab\EntityDataModel\Type\PropertyDescription\ETagPropertyDescription;
use Rolab\EntityDataModel\Type\PropertyDescription\KeyPropertyDescription;
use Rolab\EntityDataModel\Type\PropertyDescription\ComplexPropertyDescription;
use Rolab\EntityDataModel\Type\PropertyDescription\NavigationPropertyDescription;
use Rolab\EntityDataModel\Exception\BuilderException;

class EntityDataModelBuilder
{
    private $baseEntityDataModel;

    private $entityDataModelDefinition;

    private $structuralTypeBuilder;

    private $entityContainerBuilder;

    public function __construct()
    {
        $this->entityDataModelDefinition = new EntityDataModelDefinition();
    }

    public function setEntityDataModelDefinition(EntityDataModelDefinition $entityDataModelDefinition)
    {
        $this->entityDataModelDefinition = $entityDataModelDefinition;
    }

    public function setBaseEntityDataModel(EntityDataModel $entityDataModel)
    {
        $this->baseEntityDataModel = $entityDataModel;
    }

    public function entityType($className, $name, $namespace, $baseType = null)
    {
        $entityTypeDefinition = new EntityTypeDefition($className, $name, $namespace, $baseType);
        $entityTypeDefinition->setEntityDataModelBuilder($this);

        return $entityTypeDefinition;
    }

    public function complexType($className, $name, $namespace, $baseType = null)
    {
        $complexTypeDefinition = new ComplexTypeDefition($className, $name, $namespace, $baseType);
        $complexTypeDefinition->setEntityDataModelBuilder($this);

        return $entityTypeDefinition;
    }

    public function addStructuralType(StructuralTypeDefinition $structuralType)
    {
        $this->entityDataModelDefinition->addStructuralTypeDefinition($structuralType);
    }

    public function entityContainer($name, $namespace, $parentContainerName)
    {
        $container = new EntityContainerDefinition($name, $namespace, $parentContainerName);
        $container->setEntityDataModelBuilder($this);

        return $container;
    }

    public function addEntityContainer(EntityContainerDefinition $entityContainer)
    {
        $this->entityDataModelDefinition->addEntityContainerDefinition($entityContainer);
    }

    public function build()
    {
        $edm = isset($this->baseEntityDataModel) ? $this->baseEntityDataModel : new EntityDataModel();

        $entityTypeDefinitions = array();

        // Add structural types to entity data model
        foreach ($this->entityDataModelDefinition->getStructuralTypeDefinitions() as $typeDefinition) {
            $regularProperties = array();

            // Only regular properties now, because navigation properties need the entity sets to be added first
            foreach ($typeDefinition->getRegularPropertyDefinitions() as $regularPropertyDefinition) {
                if ($regularPropertyDefinition instanceof PrimitivePropertyDefinition) {
                    $typeName = $regularPropertyDefinition->getTypeName();

                    if (!strpos($typeName, '.')) {
                        $typeClassName = '\Rolab\EntityDataModel\Type\Edm\\'. $typeName;
                    } else {
                        list($partialNamespace, $typeClassName) = explode('.', $typeName, 2);

                        $typeClassName = '\Rolab\EntityDataModel\Type\\'. $partialNamespace .'\\'. $typeName;
                    }

                    if (!class_exists($typeClassName)) {
                        throw new BuilderException(sprintf('Could not find a class for primitive type "%s", tried "%s".',
                            $typeName, $typeClassName));
                    }

                    $type = new $typeClassName();

                    if ($regularPropertyDefinition instanceof KeyPropertyDefinition) {
                        $regularProperties[] = new KeyProperty($regularPropertyDefinition->getName(), $type);
                    } elseif ($regularPropertyDefinition instanceof ETagPropertyDefinition) {
                        $regularProperties[] = new ETagProperty($regularPropertyDefinition->getName(), $type);
                    } else {
                        $regularProperties[] = new PrimitiveProperty($regularPropertyDefinition->getName(), $type,
                            $regularPropertyDefinition->isCollection());
                    }
                } elseif ($regularPropertyDefinition instanceof ComplexPropertyDefition) {
                    $type = $edm->getStructuralTypeByName($regularPropertyDefinition->getTypeName());

                    if (is_null($type)) {
                        throw new BuilderException(sprintf('Could not find property type "%s" in the entity data model that ' .
                            'is being build. Please make sure that types that refer to other types are added to the ' .
                            'definition in the correct order.', $regularPropertyDefinition->getTypeName()));
                    }

                    $regularProperties[] = new ComplexProperty($regularPropertyDefinition->getName(), $type,
                        $regularPropertyDefinition->isCollection());
                }
            }

            $baseType = null;

            if (isset($typeDefinition->getBaseTypeName())) {
                $baseType = $emd->getStructuralTypeByName($typeDefinition->getBaseTypeName());

                if (is_null($baseType)) {
                    throw new BuilderException(sprintf('Could not find base type "%s" in the entity data model that is being build. ' .
                        'Please make sure that types that refer to other types are added to the definition in the correct order.',
                        $typeDefinition->getBaseTypeName()));
                }
            }

            if ($typeDefinition instanceof ComplexTypeDefinition) {
                $edm->addStructuralType(new ComplexType($typeDefinition->getClassName(), $typeDefinition->getName(),
                    $typeDefinition->getNamespace(), $regularProperties, $baseType));
            } elseif ($typeDefinition instanceof EntityTypeDefinition) {
                $entityTypeDefinitions[] = $typeDefinition;

                $edm->addStructuralType(new EntityType($typeDefinition->getClassName(), $typeDefinition->getName(),
                    $typeDefinition->getNamespace(), $regularProperties, $baseType));
            }
        }

        // Add entity containers to the entity data model
        foreach ($this->entityDataModelDefinition->getContainerDefinitions() as $containerDefinition) {
            $parentContainer = null;

            if (isset($containerDefinition->getParentContainerName())) {
                $parentContainer = $edm->getEntityContainerByName($containerDefinition->getParentContainerName());

                if (is_null($parentContainer)) {
                    throw new BuilderException(sprintf('Could not find parent container "%s" in the entity data model that is being build. '.
                        'Please make sure that containers that have parent containers are added tot the definition in the correct order.',
                        $containerDefinition->getParentContainerName()));
                }
            }

            $container = new EntityContainer($containerDefinition->getName(), $containerDefinition->getNamespace(), $parentContainer);

            foreach ($containerDefinition->getEntitySetDefinitions() as $entitySetDefinition) {
                $type = $edm->getStructuralTypeByName($entitySetDefinition->getTypeName());

                if (is_null($type)) {
                    throw new BuilderException(sprintf('Could not find type "%s" in the entity data model that is being build. ' .
                        'Please make sure that types that refer to other types are added to the definition in the correct order.',
                        $typeDefinition->getBaseTypeName()));
                }

                $container->addEntitySet($entitySetDefinition->getName(), $type);
            }

            $edm->addEntityContainer($container);
        }

        // Now that the entity sets are added, the navigation properties can finally be added as well
        foreach ($entityTypeDefinitions as $entityTypeDefinition) {
            foreach ($entityTypeDefinition->getNavigationPropertyDefinitions() as $navigationPropertyDefinition) {
                $targetEntitySet = $edm->getEntitySetByName($navigationPropertyDefinition->getEntitySetName());

                if (is_null($targetEntitySet)) {
                    throw new BuilderException(sprintf('Could not find entity set "%s" in the entity data model that is being build.',
                        $typeDefinition->getBaseTypeName()));
                }

                $edm->getStructuralTypeByName($entityTypeDefinition->getName())->addNavigationProperty(new NavigationProperty(
                    $navigationPropertyDefinition->getName(), $targetEntitySet, $navigationPropertyDefinition->isCollection()));
            }
        }

        if ($defaultContainerName = $this->entityDataModelDefinition->getDefaultContainer()) {
            $edm->setDefaultContainer($defaultContainerName);
        }

        return $edm;
    }
}
