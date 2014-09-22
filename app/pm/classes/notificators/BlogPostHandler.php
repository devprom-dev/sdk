<?php

include_once "EmailNotificatorHandler.php";
include_once SERVER_ROOT_PATH.'pm/views/wiki/editors/WikiEditorBuilder.php';

class BlogPostHandler extends EmailNotificatorHandler
{
	function getParticipants( $object_it, $prev_object_it, $action ) 
	{
		$result = array();
		
		if ( $action != 'add' ) return $result; 
			
		$part_it = getSession()->getProjectIt()->getParticipantIt();
		
		while ( !$part_it->end() )
		{
			array_push($result, $part_it->getId());
			
			$part_it->moveNext();
		}

		return $result;
	}	

	function getBody( $action, $object_it, $prev_object_it, $recipient )
	{
		$more_text = false;
		
		$url = $this->getObjectItUid($object_it);
		
		$body .= $object_it->get('Caption').'<br/><br/>';
		
		$editor = WikiEditorBuilder::build( $object_it->get('ContentEditor') );
		
		$editor->setObjectIt( $object_it );
		
		$parser = $editor->getHtmlParser();

		$parser->setObjectIt( $object_it );
			
		$body .= $parser->parse_substr( $object_it->getHtmlDecoded('Content'), 620, $more_text );

		if ( $more_text )
		{
			$body .= '<br/><br/><a href="'.
				$this->getObjectItUid($object_it).'">'.translate('читать дальше').'</a>';
		}
			
		return $body;	
	}
}  
