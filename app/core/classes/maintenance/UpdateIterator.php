<?php

class UpdateIterator extends OrderedIterator
{
	function getDisplayName() 
	{
		return trim(parent::getDisplayName(), " \n\r\t");
	}
} 
