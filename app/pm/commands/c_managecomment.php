<?php
use Devprom\ProjectBundle\Service\Files\UploadFileService;
include_once SERVER_ROOT_PATH . "cms/c_form.php";

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

		$comment_text = $object_it->utf8towin($_REQUEST['Caption']);
 		$comment->setVpdContext( $object_it );

        $prevCommentIt = $comment->getExact($_REQUEST['PrevComment']);
        if ( $prevCommentIt->get('IsPrivate') == 'Y' ) {
            $_REQUEST['IsPrivate'] = 'Y';
        }

        getFactory()->getEventsManager()->delayNotifications();

        $comment_it = getFactory()->createEntity($comment,
            array_merge( $_REQUEST,
                array(
                    'AuthorId' => getSession()->getUserIt()->getId(),
                    'ObjectId' => $object_it->getId(),
                    'ObjectClass' => get_class($object_it->object),
                    'PrevComment' => $prevCommentIt->getId(),
                    'Caption' => $comment_text
                )
            ));
		$this->processEmbedded( $comment_it );

        getFactory()->getEventsManager()->releaseNotifications();

        $comment_it->getId() > 0
			? $this->replySuccess( text(1178) ) 
				: $this->replyError( text(1062) );
	}
 	
	function modify( $object_id )
	{
		$comment = getFactory()->getObject('Comment');
		$comment_it = $comment->getExact( $object_id ); 
		if ( $comment_it->getId() < 1 ) $this->replyError( text(1062) );

        $comment_it = getFactory()->modifyEntity($comment_it, array(
            'Caption' => $_REQUEST['Caption']
        ));
		$this->processEmbedded( $comment_it );
		
		$this->replySuccess( text(1238) );
	}
	
	function processEmbedded( $comment_it )
	{
		$form = new Form($comment_it->object);
        $form->processEmbeddedForms( $comment_it );

        $service = new UploadFileService();
        $service->deleteFiles();
        $service->attachTemporaryFiles($comment_it, 'File', getFactory()->getObject('pm_Attachment'));
	}
}
 