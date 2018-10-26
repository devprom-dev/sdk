<?php
include "MetricRegistry.php";
include "predicates/MetricReferencePredicate.php";

class Metric extends Metaobject
{
	public function __construct()
	{
		parent::__construct('entity', new MetricRegistry($this));
	}
}