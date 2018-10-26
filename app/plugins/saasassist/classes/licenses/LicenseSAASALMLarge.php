<?php

include_once "LicenseSAASALM.php";
include "LicenseSAASALMLargeIterator.php";
		
class LicenseSAASALMLarge extends LicenseSAASALM
{
	public function __construct()
	{
		parent::__construct();
		
		$this->setAttributeCaption('Caption', 'text(saasassist39)');
	}
	
	function createIterator()
	{
		return new LicenseSAASALMLargeIterator( $this );
	}
}

