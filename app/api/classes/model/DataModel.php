<?php

include "DataModelRegistry.php";

class DataModel extends Metaobject
{
	public function __construct()
	{
		parent::__construct('entity', new DataModelRegistry($this));
	}
}