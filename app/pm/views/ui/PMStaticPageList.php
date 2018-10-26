<?php

class PMStaticPageList extends PMPageList
{
 	function __construct ( $object )
 	{
 		parent::__construct( $object );
 	}

	function IsNeedToDelete( ) { return false; }
	
	function getItemActions( $dummy, $object_it )
	{
	    return array();
	}
}
