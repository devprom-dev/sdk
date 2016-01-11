<?php
include_once "AttachmentIterator.php";
include "predicates/AttachmentObjectPredicate.php";

class Attachment extends Metaobject
{
 	function __construct( $registry = null )
 	{
 		parent::__construct('pm_Attachment', $registry);
 		$this->setSortDefault( array(
 		    new SortAttributeClause('ObjectId'),
 		    new SortRecentClause()
 		));
 	}
 	
 	function createIterator() {
 		return new AttachmentIterator( $this );
 	}
 	
 	function getAttributes()
 	{
 		$attrs = parent::getAttributes();
 		unset($attrs['Description']);
 		return $attrs;
 	}
}