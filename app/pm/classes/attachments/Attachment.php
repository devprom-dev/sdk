<?php
include_once "AttachmentIterator.php";
include "predicates/AttachmentObjectPredicate.php";

class Attachment extends Metaobject
{
 	var $size;
 	
 	function Attachment( $size = 1 ) 
 	{
		$this->size = $size;

 		parent::Metaobject('pm_Attachment');
 		
 		$this->setSortDefault( array( 
 		    new SortAttributeClause('ObjectId'),
 		    new SortRecentClause()
 		));
 	}
 	
 	function createIterator() 
 	{
 		return new AttachmentIterator( $this );
 	}
 	
 	function getAttributes()
 	{
 		$attrs = parent::getAttributes();
 		
 		unset($attrs['Description']);
 		
 		return $attrs;
 	}
}