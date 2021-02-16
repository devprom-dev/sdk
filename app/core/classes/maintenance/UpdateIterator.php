<?php

class UpdateIterator extends CacheableIterator
{
	function getDisplayName() 
	{
		return trim(parent::getDisplayName(), " \n\r\t");
	}
} 
