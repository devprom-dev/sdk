<?php

include "WorkTableCustomerRegistry.php";

class WorkTableCustomer extends Metaobject
{
	public function __construct()
	{
		parent::__construct('entity', new WorkTableCustomerRegistry()); 
	}
}