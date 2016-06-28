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
		$notification_type = getFactory()->getObject('Notification')->getType( $participant_it );
		return $notification_type != '';
	}

	function participantHasAccess( $participant_it, $object_it )
	{
		return true;
	}

	function getParticipants( $object_it, $prev_object_it, $action ) 
	{
		$project_it = getSession()->getProjectIt();
		$result = array();
		
		if ( $action != 'add' ) return $result;

		$anchor_it = $object_it->getAnchorIt();
		if ( $anchor_it->getId() == '' ) return $result;

		switch( $anchor_it->object->getClassName() )
		{
			case 'pm_ChangeRequest':
				$implementor_it = $anchor_it->getImplementors();
				while( !$implementor_it->end() )
				{
					$result[] = $implementor_it->getId();
					$implementor_it->moveNext();
				}
				$result = array_merge( $result, $project_it->getLeadIt()->idsToArray() );
				break;
				
			case 'pm_Task':
				$result = array_merge( $result, $project_it->getLeadIt()->idsToArray() );
				break;
				
			case 'WikiPage':
			    $author_it = $anchor_it->getRef('Author');
				$result[] = $author_it->getId(); 
				break;
				
			case 'BlogPost':
			    array_push($result, $anchor_it->get('AuthorId'));
			    break;
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
			case 'pm_Question':
				if ( $anchor_it->get('Author') > 0 ) $result[] = $anchor_it->get('Author');
				if ( $anchor_it->object->getAttributeType('Owner') != '' && $anchor_it->get('Owner') != '' ) $result[] = $anchor_it->get('Owner');
				break;
				
			case 'pm_Task':
				if ( $anchor_it->get('Assignee') != '' ) $result[] = $anchor_it->get('Assignee');
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
 		$parser = $editor->getPageParser();
		$uid = new ObjectUID();

 		do 
 		{
			$info = $uid->getUidInfo($comment_it);
 			$data[] = array (
				'id' => $comment_it->getId(),
				'author' => $comment_it->get('AuthorName'),
				'date' => $comment_it->getDateTimeFormat('RecordCreated'),
				'text' => $parser->parse( $comment_it->getHtmlDecoded('Caption') ),
				'url' => $info['url']
 			);
			
			$comment_it->moveNext();
 		} 
 		while ( !$comment_it->end() );
 		
 		return $data;
 	}
}
