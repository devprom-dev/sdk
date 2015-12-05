<?php

include_once "LicenseSAASALM.php";
include "LicenseSAASALM20Iterator.php";
		
class LicenseSAASALM20 extends LicenseSAASALM
{
	public function __construct()
	{
		parent::__construct();
		$this->setAttributeCaption('Caption', 'text(saasassist43)');
	}
	
	function createIterator()
	{
		return new LicenseSAASALM20Iterator( $this );
	}
}

