<?php

include_once "EmailNotificatorHandler.php";
include_once SERVER_ROOT_PATH.'pm/views/wiki/editors/WikiEditorBuilder.php';

class CommentHandler extends EmailNotificatorHandler
{
	function getSubject( $subject, $object_it, $prev_object_it, $action, $recipient ) 
	{
		global $project_it, $model_factory;

		$commented_it = $object_it->getAnchorIt();

		$uid = new ObjectUid;
		$commented_uid = $uid->getObjectUid($commented_it);

		if ( strtolower($commented_it->object->getClassName()) == 'pm_vacancy' )
		{
			$other_it = $commented_it->getRef('Project');
			$subject = '['.$other_it->get('CodeName').']';
		}
		else
		{
			$subject = '['.$project_it->get('CodeName').']';
		}
			
		if ( $uid->IsValidUid( $commented_uid ) )
		{
			$subject .= ' ['.$commented_uid.'] '.
				translate('Комментарий').': ';
		}
		
		$subject .= ' '.substr($commented_it->getDisplayName(), 0, 80);
			
		return $subject;
	}
	
	function getParticipants( $object_it, $prev_object_it, $action ) 
	{
		global $model_factory, $project_it;

		$result = array();
		
		if ( $action != 'add' ) return $result;

		$anchor_it = $object_it->getAnchorIt();
		
		if ( $anchor_it->count() < 1 ) return $result;

		switch( $anchor_it->object->getClassName() )
		{
			case 'pm_ChangeRequest':
			    
				if ( $anchor_it->object->getAttributeType('Owner') != '' && $anchor_it->get('Owner') != '' )
				{
					$owner_it = $anchor_it->getRef('Owner');
					
					if ( $owner_it->count() > 0 )
					{
						array_push($result, $owner_it->getId());
					}
				}
				
				$implementor_it = $anchor_it->getImplementors();
				
				while( !$implementor_it->end() )
				{
					$result[] = $implementor_it->getId();
					
					$implementor_it->moveNext();
				}
				
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
		global $model_factory, $project_it;

		$result = array();
		
		if ( $action != 'add' ) return $result;

		$anchor_it = $object_it->getAnchorIt();
		
		if ( $anchor_it->count() < 1 ) return $result;
		
		switch( $anchor_it->object->getClassName() )
		{
			case 'pm_ChangeRequest':
			case 'pm_Question':
				if ( $anchor_it->get('Author') > 0 )
				{
					array_push($result, $anchor_it->get('Author'));
				}
				break;
		}
		
		$user_it = $object_it->getThreadUserIt();
		
		while ( !$user_it->end() )
		{
			array_push($result, $user_it->getId());
			$user_it->moveNext();
		}
		
		return $result;
	}	
	
	function getBody( $action, $object_it, $prev_object_it, $recipient )
	{
		global $model_factory, $session;
		
		$body = '';

		$commented_class = $object_it->get('ObjectClass');
		
		switch(strtolower($commented_class))
		{
			case 'pm_changerequest':
			case 'request':

			    $request = $model_factory->getObject('pm_ChangeRequest');
				
			    $request_it = $request->getExact($object_it->get('ObjectId'));
			
				$url = $this->getObjectItUid($request_it);
				
				$editor = WikiEditorBuilder::build();
				
				$parser = $editor->getHtmlParser();
				
				$parser->setObjectIt( $request_it );
				
				$body .= $parser->parse( $request_it->getHtmlDecoded('Description') ).'<br/><br/>';
				
				$body .= translate('Комментарии').':<br/><br/>';
				
				break;
				
			default:
				
			    $class = $model_factory->getObject($commented_class);
				
			    $commented_it = $class->getExact($object_it->get('ObjectId'));
				
				$body .= translate('Комментарии').':<br/><br/>';
		}
		
		$body .= $this->getRecentComments($object_it);

		return $body; 
	}	

	function getPreBody( $action, $object_it, $prev_object_it ) 
	{
		$anchor_it = $object_it->getAnchorIt();
		
		if ( $anchor_it->getId() < 1 ) return "";
		
		$url = $this->getObjectItUid( $anchor_it );
		
		return $anchor_it->getDisplayName().'<br/><a href="'.$url.'">'.$url.'</a><br/><br/>';
	}	
	
	function getRecentComments( $comment_it )
 	{
		$comment_it = $comment_it->getRollupIt(2);
		
		$editor = WikiEditorBuilder::build();
 		
		return $this->_getThreadText( $comment_it, 0, $editor );
 	}
 	
 	function _getThreadText( $comment_it, $level, $editor )
 	{
 		if ( $level > 50 || $comment_it->count() < 1 ) return;
 		
 		$text = '';
		$session = getSession();
		$parser = $editor->getPageParser();
		
 		do 
 		{
 			$user_it = $comment_it->getRef('AuthorId');
 			$name = $user_it->getDisplayName();
			
			$text .= '<a href="'.$this->getObjectItUid($comment_it).'">'.
				$comment_it->getDateTimeFormat('RecordCreated').', '.$name.'</a><br/>';
			
			$text .= $parser->parse( $comment_it->getHtmlDecoded('Caption') ).'<br/><br/>';
			
			$comment_it->moveNext();
 		} 
 		while ( !$comment_it->end() );
 		
 		return $text;
 	}

	function participantHasAccess( $participant_it, $object_it )
	{
		if ( !parent::participantHasAccess( $participant_it, $object_it ) ) return false;
		
		return parent::participantHasAccess( $participant_it, $object_it->getAnchorIt() );
	}
}
