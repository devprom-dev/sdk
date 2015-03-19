<?php

class LicenseDevOpsBoardIterator extends LicenseSAASBaseIterator
{
	function getName()
	{
		return 'DevOps Board';
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
		return 100;
	}
	
	function allowCreate( & $object )
	{
		if ( !$object instanceof User ) return true;
	    return $this->getActiveUsers() < $this->getLimit();    
	}
}