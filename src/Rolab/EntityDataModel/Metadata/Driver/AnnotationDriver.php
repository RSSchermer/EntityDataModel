<?php

/*
 * This file is part of the Rolab Entity Data Model library.
 *
 * (c) Roland Schermer <roland0507@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rolab\EntityDataModel\Metadata\Driver;

use Metadata\Driver\DriverInterface;

use Doctrine\Common\Annotations\Reader;

use Rolab\EntityDataModel\Metadata\ClassMetadata;
use Rolab\EntityDataModel\Metadata\PrimitivePropertyMetadata;
use Rolab\EntityDataModel\Annotations;

class AnnotationDriver implements DriverInterface
{
    private $reader;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    public function loadMetadataForClass(\ReflectionClass $class)
    {
        $classMetadata = new ClassMetadata($class->getName());

        $classMetadata->typeName = $this->readClassAnnotation($class, 'TypeName');
        $classMetadata->typeNamespace = $this->readClassAnnotation($class, 'TypeNamespace');
        $classMetadata->setName = $this->readClassAnnotation($class, 'SetName');

        foreach ($class->getProperties() as $property) {
            $propertyAnnotations = $this->reader->getPropertyAnnotations($property);

            foreach ($propertyAnnotations as $annot) {
                if ($annot instanceof Edm\PrimitiveProperty) {
                    $propertyMetadata = new PrimitivePropertyMetadata($class->getName(), $property->getName());
                    $propertyMetadata->resourceType = $annot->resourceType;
                    $propertyMetadata->isCollection = $annot->isCollection;
                } elseif ($annot instanceof Edm\KeyProperty) {
                    $propertyMetadata = new PrimitivePropertyMetadata($class->getName(), $property->getName());
                    $propertyMetadata->resourceType = $annot->resourceType;
                    $propertyMetadata->isKey = true;
                } elseif ($annot instanceof Edm\ETagProperty) {
                    $propertyMetadata = new PrimitivePropertyMetadata($class->getName(), $property->getName());
                    $propertyMetadata->resourceType = $annot->resourceType;
                    $propertyMetadata->isETag = true;
                } elseif ($annot instanceof Edm\ComplexProperty) {
                    $propertyMetadata = new StructuralPropertyMetadata($class->getName(), $property->getName());
                    $propertyMetadata->targetClass = $annot->targetClass;
                    $propertyMetadata->isEntityReference = false;
                    $propertyMetadata->isCollection = $annot->isCollection;
                } elseif ($annot instanceof Edm\NavigationProperty) {
                    $propertyMetadata = new StructuralPropertyMetadata($class->getName(), $property->getName());
                    $propertyMetadata->targetClass = $annot->targetClass;
                    $propertyMetadata->isEntityReference = true;
                    $propertyMetadata->isCollection = $annot->isCollection;
                }

                if ($propertyMetadata) {
                    $classMetadata->addPropertyMetadata($propertyMetadata);
                }
            }
        }

        return $classMetadata;
    }

    private function readClassAnnotation(\ReflectionClass $reflection, $annotationName)
    {
        $annotationClass = "Rolab\\ODataProducerBundle\\Model\\Annotations\\$annotationName";

        if ($annotation = $this->reader->getClassAnnotation($reflection, $annotationClass)) {
            return $annotation;
        }
    }
}
