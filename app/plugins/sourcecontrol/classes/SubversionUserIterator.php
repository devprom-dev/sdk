<?php

class SubversionUserIterator extends OrderedIterator
{
	function getDisplayName()
	{
		return $this->get('UserName').' - '.$this->getRef('SystemUser')->getDisplayName();
	}
}