<?php

include "BlackListIterator.php";

class BlackList extends Metaobject
{
	function __construct()
	{
		parent::__construct('cms_BlackList');
	}

	function createIterator()
	{
		return new BlackListIterator($this);
	}
}
