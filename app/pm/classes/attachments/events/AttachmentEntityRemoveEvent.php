<?php
include_once SERVER_ROOT_PATH."cms/classes/ObjectFactoryNotificator.php";

class AttachmentEntityRemoveEvent extends ObjectFactoryNotificator
{
 	function add( $object_it )
	{
	}

 	function modify( $prev_object_it, $object_it ) 
	{
	}

 	function delete( $object_it ) 
	{
        $attachment = getFactory()->getObject('pm_Attachment');
        $attachment->removeNotificator( 'EmailNotificator' );

        $attachment->addFilter( new AttachmentObjectPredicate($object_it) );
        $attachment_it = $attachment->getAll();

        while ( !$attachment_it->end() ) {
            $attachment->delete( $attachment_it->getId() );
            $attachment_it->moveNext();
        }
	}
}
 