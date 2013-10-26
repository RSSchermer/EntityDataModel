<?php

namespace Rolab\EntityDataModel\Annotations;

/**
 * @Annotation
 * @Target("PROPERTY")
 */
class PrimitiveProperty extends BasePrimitiveProperty
{
	/** @var boolean */
	public $isBag = false;
}