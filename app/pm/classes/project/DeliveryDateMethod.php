<?php
include "DeliveryDateMethodRegistry.php";

class DeliveryDateMethod extends MetaobjectCacheable
{
	public function __construct()
	{
		parent::__construct('entity', new DeliveryDateMethodRegistry($this));
	}
}