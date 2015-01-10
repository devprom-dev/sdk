<?php
 
include_once SERVER_ROOT_PATH."cms/c_form_embedded.php";

class ManageComment extends CommandForm
{
 	function validate()
 	{
		// proceeds with validation
		$this->checkRequired( array('Caption') );

		// check authorization was successfull
		if ( getSession()->getUserIt()->getId() < 1 )
		{
			return false;
		}
		
		return true;
 	}
 	
 	function create()
	{
		global $_SERVER, $_REQUEST, $model_factory;
		
 		$object = $model_factory->getObject($_REQUEST['ObjectClass']);
 		
 		$object_it = $object->getExact($_REQUEST['ObjectId']);
 		
		if ( $object_it->getId() < 1 ) $this->replyError( text(1062) );
		
		$comment = $model_factory->getObject('Comment');

		$comment_text = $object_it->utf8towin($_REQUEST['Caption']);

 		if ( $_REQUEST['PrevComment'] > 0 )
 		{
 			$comment_it = $comment->getExact($_REQUEST['PrevComment']);

 			$last_comment_id = $comment_it->getId();
 		}
 		else
 		{
 			$last_comment_id = '';
 		}

 		$comment->setVpdContext( $object_it );
 		
 		$comment_id = $comment->add_parms( 
 			array('AuthorId' => getSession()->getUserIt()->getId(),
 				  'ObjectId' => $object_it->getId(),
 				  'ObjectClass' => get_class($object),
 				  'PrevComment' => $last_comment_id,
 				  'Caption' => $comment_text)
 			);

 		$comment_it = $comment->getExact($comment_id);
 		
		$this->processEmbedded( $comment_it );
 			
		$comment_id > 0 
			? $this->replySuccess( text(1178) ) 
				: $this->replyError( text(1062) );
	}
 	
	function modify( $object_id )
	{
		global $_REQUEST, $model_factory;

		$comment = $model_factory->getObject('Comment');
		$comment_it = $comment->getExact( $object_id ); 
		
		if ( $comment_it->getId() < 1 ) $this->replyError( text(1062) );

		$comment_text = IteratorBase::utf8towin($_REQUEST['Caption']);
	
		$comment_it = $comment->getExact(
		        $comment->modify_parms( 
		        		$comment_it->getId(), 
		        		array(
		        				'Caption' => $comment_text
						)
				)
		);
 
		$this->processEmbedded( $comment_it );
		
		$this->replySuccess( text(1238) );
	}
	
	function processEmbedded( $comment_it )
	{
		$embedded = new FormEmbedded();
		$embedded->process( $comment_it );
	}
}
 