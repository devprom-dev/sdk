<?php

class BusinessRulePredicate
{
 	function getId()
 	{
 		return abs(crc32(strtolower(get_class($this))));
 	}
 	
 	function getDisplayName()
 	{
 		return '';
 	}
 	
 	function getObject()
 	{
 		return null;
 	}
 	
 	function check( $object_it )
 	{
 		return false;
 	}
 	
 	function getNegativeReason()
 	{
 		return '';
 	}
}
