<?php

namespace Rolab\EntityDataModel\Definition\Driver;

use Metadata\Driver\DriverInterface;
use Metadata\NullMetadata;

use Doctrine\Common\Annotations\Reader;

use Rolab\EntityDataModel\Definition\Annotations as EDM;
use Rolab\EntityDataModel\Definition\ComplexTypeMetadata;
use Rolab\EntityDataModel\Definition\EntityTypeMetadata;
use Rolab\EntityDataModel\Definition\PrimitivePropertyMetadata;
use Rolab\EntityDataModel\Definition\ComplexPropertyMetadata;
use Rolab\EntityDataModel\Definition\NavigationPropertyMetadata;
use Rolab\EntityDataModel\Exception\DefinitionException;

class AnnotationDriver implements DriverInterface
{
    private $reader;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    public function loadMetadataForClass(\ReflectionClass $class)
    {
        if ($this->readClassAnnotation($class, 'ComplexType') && $this->readClassAnnotation($class, 'EntityType')) {
            throw new DefinitionException(sprintf(
                'Class "%s" can either be marked as a complex type or as an entity type, but may not be marked as ' .
                'both.',
                $class->getName()
            ));
        }
        
        if ($typeAnnotation = $this->readClassAnnotation($class, 'EntityType')) {
            $typeMetadata = new EntityTypeMetadata($class->getName());
            $typeMetadata->typeName = $typeAnnotation->name;

            // Only properties defined on the class, not inherited properties as entity types can have
            // base types in an entity data model and inherit properties that way.
            $ownProperties = array_filter($class->getProperties(), function ($property) use ($class) {
                return $property->class === $class->getName();
            });
            
            foreach ($ownProperties as $property) {
                if ($this->readPropertyAnnotation($property, 'PrimitiveProperty')
                    && $this->readPropertyAnnotation($property, 'NavigationProperty')
                ) {
                    throw new DefinitionException(sprintf(
                        'Property "%s" on class "%s" was marked as both a primitive and a navigation property. A ' .
                        'property may not be marked as both a primitive and navigation property.',
                        $property->getName(),
                        $class->getName()
                    ));
                }
                
                if ($this->readPropertyAnnotation($property, 'PrimitiveProperty')
                    && $this->readPropertyAnnotation($property, 'ComplexProperty')
                ) {
                    throw new DefinitionException(sprintf(
                        'Property "%s" on class "%s" was marked as both a primitive and a complex property. A ' .
                        'property may not be marked as both a primitive and complex property.',
                        $property->getName(),
                        $class->getName()
                    ));
                }
                
                if ($this->readPropertyAnnotation($property, 'NavigationProperty')
                    && $this->readPropertyAnnotation($property, 'ComplexProperty')
                ) {
                    throw new DefinitionException(sprintf(
                        'Property "%s" on class "%s" was marked as both a navigation and a complex property. A ' .
                        'property may not be marked as both a navigation and complex property.',
                        $property->getName(),
                        $class->getName()
                    ));
                }
                
                if ($annotation = $this->readPropertyAnnotation($property, 'PrimitiveProperty')) {
                    $propertyMetadata = new PrimitivePropertyMetadata($class->getName(), $property->getName());
                    $propertyMetadata->nameOverride = $annotation->name;
                    $propertyMetadata->primitiveTypeName = $annotation->type;
                    $propertyMetadata->isNullable = $annotation->isNullable;
                    $propertyMetadata->isCollection = $annotation->isCollection;
                    $propertyMetadata->partOfKey = $this->readPropertyAnnotation($property, 'Key') ? true : false;
                    $propertyMetadata->partOfETag = $this->readPropertyAnnotation($property, 'ETag') ? true : false;
                    
                    $typeMetadata->addPropertyMetadata($propertyMetadata);
                } elseif ($annotation = $this->readPropertyAnnotation($property, 'ComplexProperty')) {
                    if ($this->readPropertyAnnotation($property, 'Key')) {
                        throw new DefinitionException(sprintf(
                            'Property "%s" on class "%s" was marked as both a complex property and a key property. ' .
                            'A complex property cannot be marked as a key property.',
                            $property->getName(),
                            $class->getName()
                        ));
                    }
                    
                    if ($this->readPropertyAnnotation($property, 'ETag')) {
                        throw new DefinitionException(sprintf(
                            'Property "%s" on class "%s" was marked as both a complex property and an e-tag ' .
                            'property. A complex property cannot be marked as an e-tag property.',
                            $property->getName(),
                            $class->getName()
                        ));
                    }
                    
                    $propertyMetadata = new ComplexPropertyMetadata($class->getName(), $property->getName());
                    $propertyMetadata->nameOverride = $annotation->name;
                    $propertyMetadata->complexTypeClassName = $annotation->class;
                    $propertyMetadata->isNullable = $annotation->isNullable;
                    $propertyMetadata->isCollection = $annotation->isCollection;
                    
                    $typeMetadata->addPropertyMetadata($propertyMetadata);
                } elseif ($annotation = $this->readPropertyAnnotation($property, 'NavigationProperty')) {
                    if ($this->readPropertyAnnotation($property, 'Key')) {
                        throw new DefinitionException(sprintf(
                            'Property "%s" on class "%s" was marked as both a navigation property and a key ' .
                            'property. A navigation property cannot be marked as a key property.',
                            $property->getName(),
                            $class->getName()
                        ));
                    }
                    
                    if ($this->readPropertyAnnotation($property, 'ETag')) {
                        throw new DefinitionException(sprintf(
                            'Property "%s" on class "%s" was marked as both a navigation property and an e-tag ' .
                            'property. A navigation property cannot be marked as an e-tag property.',
                            $property->getName(),
                            $class->getName()
                        ));
                    }
                    
                    $propertyMetadata = new NavigationPropertyMetadata($class->getName(), $property->getName());
                    $propertyMetadata->nameOverride = $annotation->name;
                    $propertyMetadata->targetEntityClassName = $annotation->target;
                    $propertyMetadata->partner = $annotation->partner;
                    $propertyMetadata->isNullable = $annotation->isNullable;
                    $propertyMetadata->isCollection = $annotation->isCollection;
                    $propertyMetadata->onDeleteAction = $annotation->onDeleteAction;
                    
                    $typeMetadata->addPropertyMetadata($propertyMetadata);
                }
            }
            
            return $typeMetadata;
        } elseif ($typeAnnotation = $this->readClassAnnotation($class, 'ComplexType')) {
            $typeMetadata = new ComplexTypeMetadata($class->getName());
            $typeMetadata->typeName = $typeAnnotation->typeName;
            
            foreach ($class->getProperties() as $property) {
                if ($this->readPropertyAnnotation($property, 'NavigationProperty')) {
                    throw new DefinitionException(sprintf(
                        'Class "%s" was marked as a complex type, but property "%s" was marked as a navigation ' .
                        'property. Only entity types can have navigation properties',
                        $class->getName(),
                        $property->getName()
                    ));
                }
                
                if ($this->readPropertyAnnotation($property, 'Key')) {
                    throw new DefinitionException(sprintf(
                        'Class "%s" was marked as a complex type, yet property "%s" was marked as a key property. ' .
                        'Only entity types can have key properties.',
                        $class->getName(),
                        $property->getName()
                    ));
                }
                
                if ($this->readPropertyAnnotation($property, 'ETag')) {
                    throw new DefinitionException(sprintf(
                        'Class "%s" was marked as a complex type, yet property "%s" was marked as an e-tag property. ' .
                        'Only entity types can have e-tag properties.',
                        $class->getName(),
                        $property->getName()
                    ));
                }
                
                if ($this->readPropertyAnnotation($property, 'PrimitiveProperty')
                    && $this->readPropertyAnnotation($property, 'ComplexProperty')
                ) {
                    throw new DefinitionException(sprintf(
                        'Property "%s" on class "%s" was marked as both a primitive and a complex property. A ' .
                        'property can either be marked as pirmitive or as complex, but may not be marked as both.',
                        $property->getName(),
                        $class->getName()
                    ));
                }
                
                if ($annotation = $this->readPropertyAnnotation($property, 'PrimitiveProperty')) {
                    $propertyMetadata = new PrimitivePropertyMetadata($class->getName(), $property->getName());
                    $propertyMetadata->nameOverride = $annotation->name;
                    $propertyMetadata->primitiveTypeName = $annotation->type;
                    $propertyMetadata->isNullable = $annotation->isNullable;
                    $propertyMetadata->isCollection = $annotation->isCollection;
                    
                    $typeMetadata->addPropertyMetadata($propertyMetadata);
                } elseif ($annotation = $this->readPropertyAnnotation($property, 'ComplexProperty')) {
                    $propertyMetadata = new ComplexPropertyMetadata($class->getName(), $property->getName());
                    $propertyMetadata->nameOverride = $annotation->name;
                    $propertyMetadata->complexTypeClassName = $annotation->className;
                    $propertyMetadata->isNullable = $annotation->isNullable === true ? true : false;
                    $propertyMetadata->isCollection = $annotation->isCollection === true ? true : false;
                    
                    $typeMetadata->addPropertyMetadata($propertyMetadata);
                }
            }
            
            return $typeMetadata;
        }
        
        return new NullMetadata($class->getName());
    }

    private function readClassAnnotation(\ReflectionClass $reflection, $annotationName)
    {
        $annotationClass = 'Rolab\EntityDataModel\Definition\Annotations\\' . $annotationName;

        if ($annotation = $this->reader->getClassAnnotation($reflection, $annotationClass)) {
            return $annotation;
        }

        return null;
    }
    
    private function readPropertyAnnotation(\ReflectionProperty $reflection, $annotationName)
    {
        $annotationClass = 'Rolab\EntityDataModel\Definition\Annotations\\' . $annotationName;

        if ($annotation = $this->reader->getPropertyAnnotation($reflection, $annotationClass)) {
            return $annotation;
        }

        return null;
    }
}
