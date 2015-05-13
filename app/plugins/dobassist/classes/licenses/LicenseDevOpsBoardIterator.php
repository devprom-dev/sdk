<?php

class LicenseDevOpsBoardIterator extends LicenseSAASBaseIterator
{
	function getName()
	{
		return text('dobassist10');
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
		return 5;
	}
	
	function allowCreate( & $object )
	{
		if ( !$object instanceof User ) return true;
	    return $this->getActiveUsers() < $this->getLimit();    
	}
}