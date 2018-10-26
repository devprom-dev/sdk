<?php

include_once "LicenseSAASALM.php";
include "LicenseSAASALMMiddleIterator.php";
		
class LicenseSAASALMMiddle extends LicenseSAASALM
{
	public function __construct()
	{
		parent::__construct();
		
		$this->setAttributeCaption('Caption', 'text(saasassist38)');
	}
	
	function createIterator()
	{
		return new LicenseSAASALMMiddleIterator( $this );
	}
}

