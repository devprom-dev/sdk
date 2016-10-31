<?php

class FieldLargeText extends FieldText
{
	function __construct()
	{
		parent::__construct();
		$this->setRows(4);
	}
}
