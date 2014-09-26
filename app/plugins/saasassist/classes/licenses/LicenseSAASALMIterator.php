<?php

class LicenseSAASALMIterator extends LicenseSAASBaseIterator
{
	function getName()
	{
		return 'Devprom.SaaS (S)';
	}
	
	protected function getActiveUsers()
	{
	    return getFactory()->getObject('User')->getRegistry()->Count(
	    		array (
	    				new UserStatePredicate('active')
	    		)
	    );
	}
	
	protected function getLimit()
	{
		return 10;
	}
	
	function allowCreate( & $object )
	{
		if ( !$object instanceof User ) return true;
		
	    return $this->getActiveUsers() < $this->getLimit();    
	}
}