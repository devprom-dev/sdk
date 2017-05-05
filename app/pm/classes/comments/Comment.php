<?php

include_once "CommentBase.php";
include "CommentIterator.php";
include "persisters/CommentAuthorPersister.php";

class Comment extends CommentBase
{
 	function __construct( $registry = null )
 	{
 		parent::__construct($registry);
 		$this->setSortDefault( array(new SortRecentClause()) );
 		$this->addAttributeGroup('ObjectClass', 'system');
 		$this->addAttributeGroup('ObjectId', 'system');
 		$this->addAttributeGroup('PrevComment', 'system');
		$this->setAttributeType('Caption', 'wysiwyg');
 		$this->addPersister( new CommentAuthorPersister() );
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