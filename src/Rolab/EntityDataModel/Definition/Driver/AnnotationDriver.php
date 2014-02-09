<?php

/*
 * This file is part of the Rolab Entity Data Model library.
 *
 * (c) Roland Schermer <roland0507@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
            throw new DefinitionException(sprintf('Class "%s" can either be marked as a complex type or as an entity ' .
                'type, but may not be marked as both.', $class->getName()));
        }
        
        if ($entityTypeAnnotation = $this->readClassAnnotation($class, 'EntityType')) {
            $entityTypeMetadata = new EntityTypeMetadata($class->getName());
            $entityTypeMetadata->typeName = $entityTypeAnnotation->typeName;
            $entityTypeMetadata->isAbstract = $entityTypeAnnotation->isAbstract === true ? true : false;
            $entityTypeMetadata->baseType = $entityTypeAnnotation->baseType;
            
            foreach ($class->getProperties() as $property) {
                if ($this->readPropertyAnnotation($property, 'PrimitiveProperty') 
                    && $this->readPropertyAnnotation($property, 'NavigationProperty')
                ) {
                    throw new DefinitionException('Property "%s" on class "%s" was marked as both a primitive and ' .
                        'a navigation property. A property may not be marked as both a primitive and navigation ' .
                        'property.', $property->getName(), $class->getName());
                }
                
                if ($this->readPropertyAnnotation($property, 'PrimitiveProperty') 
                    && $this->readPropertyAnnotation($property, 'ComplexProperty')
                ) {
                    throw new DefinitionException('Property "%s" on class "%s" was marked as both a primitive and ' .
                        'a complex property. A property may not be marked as both a primitive and complex ' .
                        'property.', $property->getName(), $class->getName());
                }
                
                if ($this->readPropertyAnnotation($property, 'NavigationProperty') 
                    && $this->readPropertyAnnotation($property, 'ComplexProperty')
                ) {
                    throw new DefinitionException('Property "%s" on class "%s" was marked as both a navigation and ' .
                        'a complex property. A property may not be marked as both a navigation and complex ' .
                        'property.', $property->getName(), $class->getName());
                }
                
                if ($annotation = $this->readPropertyAnnotation($property, 'PrimitiveProperty')) {
                    $primitivePropertyMetadata = new PrimitivePropertyMetadata(
                        $class->getName(),
                        $property->getName(),
                        $annotation->type
                    );
                    $primitivePropertyMetadata->isNullable = $annotation->isNullable === true ? true : false;
                    $primitivePropertyMetadata->isCollection = $annotation->isCollection === true ? true : false;
                    $primitivePropertyMetadata->isKey = $this->readPropertyAnnotation($property, 'Key') ? true : false;
                    $primitivePropertyMetadata->isKey = $this->readPropertyAnnotation($property, 'ETag') ? true : false;
                    
                    $entityTypeMetadata->addPropertyMetadata($primitivePropertyMetadata);
                }
                
                if ($annotation = $this->readPropertyAnnotation($property, 'ComplexProperty')) {
                    if ($this->readClassAnnotation($property, 'Key')) {
                        throw new DefinitionException('Property "%s" on class "%s" was marked as both a complex ' .
                            'property and a key property. A complex property cannot be marked as a key property.',
                            $property->getName(), $class->getName());
                    }
                    
                    if ($this->readClassAnnotation($property, 'ETag')) {
                        throw new DefinitionException('Property "%s" on class "%s" was marked as both a complex ' .
                            'property and an e-tag property. A complex property cannot be marked as an e-tag property.',
                            $property->getName(), $class->getName());
                    }
                    
                    $complexPropertyMetadata = new ComplexPropertyMetadata(
                        $class->getName(),
                        $property->getName(),
                        $annotation->className
                    );
                    $complexPropertyMetadata->isNullable = $annotation->isNullable === true ? true : false;
                    $complexPropertyMetadata->isCollection = $annotation->isCollection === true ? true : false;
                    
                    $entityTypeMetadata->addPropertyMetadata($complexPropertyMetadata);
                }

                if ($annotation = $this->readPropertyAnnotation($property, 'NavigationProperty')) {
                    if ($this->readClassAnnotation($property, 'Key')) {
                        throw new DefinitionException('Property "%s" on class "%s" was marked as both a navigation ' .
                            'property and a key property. A navigation property cannot be marked as a key property.',
                            $property->getName(), $class->getName());
                    }
                    
                    if ($this->readClassAnnotation($property, 'ETag')) {
                        throw new DefinitionException('Property "%s" on class "%s" was marked as both a navigation ' .
                            'property and an e-tag property. A navigation property cannot be marked as an e-tag ' . 
                            'property.', $property->getName(), $class->getName());
                    }
                    
                    $navigationPropertyMetadata = new NavigationPropertyMetadata(
                        $class->getName(),
                        $property->getName(),
                        $annotation->targetEntity,
                        $annotation->role
                    );
                    $navigationPropertyMetadata->targetRole = $annotation->targetRole;
                    $navigationPropertyMetadata->multiplicity = $annotation->multiplicity;
                    $navigationPropertyMetadata->deleteAction = $annotation->deleteAction;
                    
                    $entityTypeMetadata->addPropertyMetadata($navigationPropertyMetadata);
                }
            }
            
            return $entityTypeMetadata;
        } elseif ($complexTypeAnnotation = $this->readClassAnnotation($class, 'ComplexType')) {
            $complexTypeMetadata = new ComplexTypeMetadata($class->getName());
            $complexTypeMetadata->typeName = $complexTypeAnnotation->typeName;
            
            foreach ($class->getProperties() as $property) {
                if ($this->readPropertyAnnotation($property, 'NavigationProperty')) {
                    throw new DefinitionException(sprintf('Class "%s" was marked as a complex type, but property ' .
                        '"%s" was marked as a navigation property. Only entity types can have navigation properties',
                        $class->getName(), $property->getName()));
                }
                
                if ($this->readPropertyAnnotation($property, 'Key')) {
                    throw new DefinitionException(sprintf('Class "%s" was marked as a complex type, yet property ' . 
                        '"%s" was marked as a key property. Only entity types can have key properties.',
                        $class->getName(), $property->getName()));
                }
                
                if ($this->readPropertyAnnotation($property, 'ETag')) {
                    throw new DefinitionException(sprintf('Class "%s" was marked as a complex type, yet property ' . 
                        '"%s" was marked as an e-tag property. Only entity types can have e-tag properties.',
                        $class->getName(), $property->getName()));
                }
                
                if ($this->readPropertyAnnotation($property, 'PrimitiveProperty')
                    && $this->readPropertyAnnotation($property, 'ComplexProperty')
                ) {
                    throw new DefinitionException(sprintf('Property "%s" on class "%s" was marked as both a ' .
                        'primitive and a complex property. A property can either be marked as pirmitive or as ' .
                        'complex, but may not be marked as both.', $property->getName(), $class->getName()));
                }
                
                if ($annotation = $this->readPropertyAnnotation($property, 'PrimitiveProperty')) {
                    $primitivePropertyMetadata = new PrimitivePropertyMetadata(
                        $class->getName(),
                        $property->getName(),
                        $annotation->type
                    );
                    $primitivePropertyMetadata->isNullable = $annotation->isNullable === true ? true : false;
                    $primitivePropertyMetadata->isCollection = $annotation->isCollection === true ? true : false;
                    
                    $complexTypeMetadata->addPropertyMetadata($primitivePropertyMetadata);
                }
                
                if ($annotation = $this->readPropertyAnnotation($property, 'ComplexProperty')) {
                    $complexPropertyMetadata = new ComplexPropertyMetadata(
                        $class->getName(),
                        $property->getName(),
                        $annotation->className
                    );
                    $complexPropertyMetadata->isNullable = $annotation->isNullable === true ? true : false;
                    $complexPropertyMetadata->isCollection = $annotation->isCollection === true ? true : false;
                    
                    $complexTypeMetadata->addPropertyMetadata($complexPropertyMetadata);
                }
            }
            
            return $complexTypeMetadata;
        }
        
        return new NullMetadata();
    }

    private function readClassAnnotation(\ReflectionClass $reflection, $annotationName)
    {
        $annotationClass = 'Rolab\EntityDataModel\Definition\Annotations\\' . $annotationName;

        if ($annotation = $this->reader->getClassAnnotation($reflection, $annotationClass)) {
            return $annotation;
        }
    }
    
    private function readPropertyAnnotation(\ReflectionProperty $reflection, $annotationName)
    {
        $annotationClass = 'Rolab\EntityDataModel\Definition\Annotations\\' . $annotationName;

        if ($annotation = $this->reader->getPropertyAnnotation($reflection, $annotationClass)) {
            return $annotation;
        }
    }
}
