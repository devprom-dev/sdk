<?php
 
 class WikiCopy extends CommandForm
 {
 	function validate()
 	{
		global $_REQUEST, $model_factory;

		// proceeds with validation
		$this->checkRequired( array('Caption') );

		// check authorization was successfull
		if ( getSession()->getUserIt()->getId() < 1 )
		{
			return false;
		}
		
		return true;
 	}
 	
 	function modify( $object_id )
	{
		global $_REQUEST, $model_factory;
		
		$object = $model_factory->getObject('HelpPage');
		$object_it = $object->getExact($object_id);
		
		if ( $object_it->count() < 1 )
		{
			$this->replyError( text(1510) );
			return;
		}
		
		$new_object_id = $object->createLike($object_it->getId());
		
		$object->modify_parms($new_object_id,
			array('Caption' => $object_it->utf8towin($_REQUEST['Caption']) ));
		
		$object_it = $object->getExact($new_object_id);
		$page_it = $object_it->getRootIt();
		
		$page_it->object->removeNotificator( 'EmailNotificator' );
		$page_it->modify( array( 'Caption' => $object_it->utf8towin($_REQUEST['Caption']) ) );
		
		$this->replySuccess( text(439) );
	}
 }
 
?>