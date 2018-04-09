<?php

include_once "EmailNotificatorHandler.php";
include_once SERVER_ROOT_PATH.'pm/views/wiki/editors/WikiEditorBuilder.php';

class CommentHandler extends EmailNotificatorHandler
{
	function getTemplate()
	{
		return 'discussion.twig';
	}
	
	function getSubject( $subject, $object_it, $prev_object_it, $action, $recipient ) 
	{
		$commented_it = $object_it->getAnchorIt();
		if ( $commented_it->getId() == '' ) return '';

		return parent::getSubject( $subject, $commented_it, $commented_it, $action, $recipient );
	}

	function IsParticipantNotified( $participant_it )
	{
		return $participant_it->get('NotificationEmailType') != '';
	}

	function getParticipants($object_it, $prev_object_it, $action)
    {
        $result = array();

        $anchor_it = $object_it->getAnchorIt();
        if ( $anchor_it->getId() == '' ) return $result;

        switch( $anchor_it->object->getClassName() )
        {
            case 'pm_ChangeRequest':
            case 'pm_Task':
            case 'pm_Question':
                return $this->getProject($object_it)->getLeadIt()->idsToArray();
        }

        return $result;
    }

    function getUsers( $object_it, $prev_object_it, $action )
	{
		$result = array();
		if ( $action != 'add' ) return $result;

		$anchor_it = $object_it->getAnchorIt();
		if ( $anchor_it->getId() == '' ) return $result;
		
		switch( $anchor_it->object->getClassName() )
		{
			case 'pm_ChangeRequest':
			    $taskAssignee = getFactory()->getObject('Task')->getRegistry()->Query(
			        array(
			            new FilterAttributePredicate('ChangeRequest', $anchor_it->getId())
                    )
                )->fieldToArray('Assignee');
			    $result = array_merge($result, $taskAssignee);

			case 'pm_Question':
				if ( $anchor_it->get('Author') != '' ) $result[] = $anchor_it->get('Author');
				if ( $anchor_it->get('Owner') != '' ) $result[] = $anchor_it->get('Owner');
				break;
				
			case 'pm_Task':
				if ( $anchor_it->get('Assignee') != '' ) $result[] = $anchor_it->get('Assignee');
                if ( $anchor_it->get('Author') != '' ) $result[] = $anchor_it->get('Author');
				break;

            case 'WikiPage':
                if ( $anchor_it->get('Author') != '' ) $result[] = $anchor_it->get('Author');
                break;
		}
		return array_merge(
				$result, $object_it->getRollupIt()->fieldToArray('AuthorId')
		);
	}	
	
	function getRenderParms($action, $object_it, $prev_object_it)
	{
		$anchor_it = $object_it->getAnchorIt();
		if ( $anchor_it->getId() == '' ) return array();

		$uid = new ObjectUID();
		$info = $uid->getUidInfo($anchor_it);
		
		return array (
			'entity' => $anchor_it->object->getDisplayName(),
			'title' => $anchor_it->getDisplayName(),
			'url' => $info['url'],
			'fields' => array(0),
			'comments' => $this->getRecentComments($object_it)
		);
	}
	
	function getRecentComments( $comment_it )
 	{
 		$comments_data = array ( $comment_it->getData() );
 		if ( $comment_it->get('PrevComment') != '' )
 		{
 			$comments_data[] = $comment_it->getRef('PrevComment')->getData();
 		}
		
		return $this->_getThreadText( 
				$comment_it->object->createCachedIterator($comments_data), 
				WikiEditorBuilder::build()
 		);
 	}
 	
 	function _getThreadText( $comment_it, $editor )
 	{
 		$data = array();
 		$parser = $editor->getHtmlParser();
        $parser->setObjectIt($comment_it);
		$uid = new ObjectUID();

 		do 
 		{
			$info = $uid->getUidInfo($comment_it);
            $text = $parser->parse( $comment_it->getHtmlDecoded('Caption') );
            $text = TextUtils::breakLongWords($text);

 			$data[] = array (
				'id' => $comment_it->getId(),
				'author' => $comment_it->get('AuthorName'),
				'date' => $comment_it->getDateTimeFormat('RecordCreated'),
				'text' => $text,
				'url' => $info['url']
 			);
			
			$comment_it->moveNext();
 		} 
 		while ( !$comment_it->end() );
 		
 		return $data;
 	}
}
