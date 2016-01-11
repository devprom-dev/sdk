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
		$this->setAttributeDescription('Caption', text(2104));
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
	
	function delete( $id )
	{
		global $model_factory;
		
		$object_it = $this->getExact( $id );
		
		// delete attachments
		$attachment = $model_factory->getObject('pm_Attachment');
		$attachment->removeNotificator( 'EmailNotificator' );
		
		$attachment->addFilter( new AttachmentObjectPredicate($object_it) );
		$attachment_it = $attachment->getAll();
		
		while ( !$attachment_it->end() )
		{
			$attachment->delete( $attachment_it->getId() );
			$attachment_it->moveNext();
		}
		
		return parent::delete( $id );
	}
}