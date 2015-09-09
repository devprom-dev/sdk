<?php
 
include_once SERVER_ROOT_PATH."cms/c_form_embedded.php";

class ManageComment extends CommandForm
{
 	function validate()
 	{
		return getSession()->getUserIt()->getId() > 0;
 	}
 	
 	function create()
	{
 		$object_it = getFactory()->getObject($_REQUEST['ObjectClass'])->getExact($_REQUEST['ObjectId']);
		if ( $object_it->getId() < 1 ) $this->replyError( text(1062) );
		
		$comment = getFactory()->getObject('Comment');

 		if ( $_REQUEST['PrevComment'] > 0 )
 		{
 			$comment_it = $comment->getExact($_REQUEST['PrevComment']);

 			$last_comment_id = $comment_it->getId();
 		}
 		else
 		{
 			$last_comment_id = '';
 		}

		$comment_text = $object_it->utf8towin($_REQUEST['Caption']);
 		$comment->setVpdContext( $object_it );
 		
 		$comment_id = $comment->add_parms( 
 			array('AuthorId' => getSession()->getUserIt()->getId(),
 				  'ObjectId' => $object_it->getId(),
 				  'ObjectClass' => get_class($object_it->object),
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
		$comment = getFactory()->getObject('Comment');
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
 