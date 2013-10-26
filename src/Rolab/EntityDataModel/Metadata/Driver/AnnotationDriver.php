<?php

namespace Rolab\EntityDataModel\Metadata\Driver;

use Metadata\Driver\DriverInterface;

use Doctrine\Common\Annotations\Reader;

use Rolab\EntityDataModel\Metadata\ClassMetadata;
use Rolab\EntityDataModel\Metadata\PrimitivePropertyMetadata;
use Rolab\EntityDataModel\Metadata\NavigationPropertyMetadata;
use Rolab\EntityDataModel\Annotations as Edm;

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
		
		foreach ($class->getProperties() as $property) {
            $propertyAnnotations = $this->reader->getPropertyAnnotations($property);
			
			foreach ($propertyAnnotations as $annot) {
				if ($annot instanceof Edm\PrimitiveProperty) {
					$propertyMetadata = new PrimitivePropertyMetadata($class->getName(), $property->getName());
					$propertyMetadata->dataType = $annot->dataType;
					$propertyMetadata->isBag = $annot->isBag;
				} elseif ($annot instanceof Edm\KeyProperty) {
					$propertyMetadata = new PrimitivePropertyMetadata($class->getName(), $property->getName());
					$propertyMetadata->dataType = $annot->dataType;
					$propertyMetadata->isKey = true;
				} elseif ($annot instanceof Edm\ETagProperty) {
					$propertyMetadata = new PrimitivePropertyMetadata($class->getName(), $property->getName());
					$propertyMetadata->dataType = $annot->dataType;
					$propertyMetadata->isETag = true;
				} elseif ($annot instanceof Edm\ComplexProperty) {
					$propertyMetadata = new NavigationPropertyMetadata($class->getName(), $property->getName());
					$propertyMetadata->targetClass = $annot->targetClass;
					$propertyMetadata->isBag = $annot->isBag;
				} elseif ($annot instanceof Edm\EntityReferenceProperty) {
					$propertyMetadata = new NavigationPropertyMetadata($class->getName(), $property->getName());
					$propertyMetadata->targetClass = $annot->targetClass;
					$propertyMetadata->isEntityReference = true;
				} elseif ($annot instanceof Edm\EntitySetReferenceProperty) {
					$propertyMetadata = new NavigationPropertyMetadata($class->getName(), $property->getName());
					$propertyMetadata->targetClass = $annot->targetClass;
					$propertyMetadata->isEntitySetReference = true;
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
