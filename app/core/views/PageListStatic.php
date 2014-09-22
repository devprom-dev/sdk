<?php

class StaticPageList extends PageList
 {
 	function StaticPageList ( $object )
 	{
 		parent::PageList( $object );
 	}

	function IsNeedToDisplayNumber( ) { return false; }
	function IsNeedToDelete( ) { return false; }
	
	function getItemActions( $dummy, $object_it )
	{
	    return array();
	}
 }