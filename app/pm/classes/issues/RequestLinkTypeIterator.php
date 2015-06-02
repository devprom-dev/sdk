<?php

class RequestLinkTypeIterator extends OrderedIterator
{
	function getDisplayName()
	{
		return translate(parent::getDisplayName());
	}
}
