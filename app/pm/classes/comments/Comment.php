<?php
include_once "CommentBase.php";
include "CommentIterator.php";

class Comment extends CommentBase
{
 	function __construct( $registry = null )
 	{
 		parent::__construct($registry);
 		$this->setSortDefault( array(new SortRecentClause()) );
 	}
 	
	function createIterator() 
	{
		return new CommentBaseIterator( $this );
	}
	
	function DeletesCascade( $object )
	{
	    return false;
	}

 	function IsDeletedCascade( $object )
	{
	    return false;
	}
}