<?php

namespace Rolab\EntityDataModel\Annotations;

/**
 * @Annotation
 * @Target("PROPERTY")
 */
class ComplexProperty extends BaseNavigationProperty
{
	/** @var boolean */
	public $isBag = false;
}
