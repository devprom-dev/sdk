<?php

include_once "CommentBase.php";
include "CommentIterator.php";

class Comment extends CommentBase
{
 	function __construct()
 	{
 		parent::__construct();
 	    
 		$this->addAttributeGroup('ObjectClass', 'system');

 		$this->addAttributeGroup('ObjectId', 'system');
 		
 		$this->addAttributeGroup('PrevComment', 'system');
 	}
 	
	function createIterator() 
	{
		return new CommentBaseIterator( $this );
	}
	
	function getDefaultAttributeValue( $attr_name )
	{
	    switch( $attr_name )
	    {
	        case 'AuthorId':

	            $user_it = getSession()->getUserIt();
	            
	            return $user_it->getId();
	            
	        default:
	            
	            return parent::getDefaultAttributeValue( $attr_name );        
	    }
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