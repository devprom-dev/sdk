<?php

class LicenseIterator extends OrderedIterator
{
	function valid()
	{
		return false;
	}
	
	function allowCreate( & $object )
	{
	    return true;    
	}
	
	function getName()
	{
		return '';
	}
	
	function restrictionMessage( $license_key = '' )
	{
	    return '';
	}
}
