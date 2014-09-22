<?php

include_once SERVER_ROOT_PATH.'pm/views/wiki/editors/WikiEditorBuilder.php';
include_once "EmailNotificatorHandler.php";

class WikiHandler extends EmailNotificatorHandler
{
	function getParticipants( $object_it, $prev_object_it, $action ) 
	{
		global $model_factory, $project_it;
		
		$result = array();

		switch ( $object_it->object->getReferenceName() )
		{
			case WikiTypeRegistry::Requirement:
				
		 		// notification will be sent only if content of the requirement was changed
		 		if ( $action == 'modify' && $object_it->get('Content') != $prev_object_it->get('Content') )
		 		{
		 			// notification is required only when state of the requirement was behind "In Progress"
		 			$state_it = $prev_object_it->getStateIt();
		 			
		 			if ( $state_it->get('ReferenceName') != 'submitted' )
		 			{ 
		 				$part_it = $project_it->getParticipantIt();
		 				
		 				while ( !$part_it->end() )
		 				{
							array_push($result, $part_it->getId());
		 					$part_it->moveNext();
		 				}
		 			}
		 		}
		 		
		 		break;
		 		
			case WikiTypeRegistry::KnowledgeBase:
				
				$part_it = $project_it->getParticipantIt();
				
				while ( !$part_it->end() )
				{
					array_push($result, $part_it->getId());
					$part_it->moveNext();
				}
				break;
		}
		
		return $result;
	}	
 	
	function getDiff( & $editor, & $change_it, & $object_it )
	{
	    $prev_content = html_entity_decode( $change_it->getHtmlDecoded('Content'), ENT_QUOTES | ENT_HTML401, 'cp1251' );
	    
	    $curr_content = html_entity_decode( $object_it->getHtmlDecoded('Content'), ENT_QUOTES | ENT_HTML401, 'cp1251' );
	    
	    $html2text = new Html2Text($prev_content);
	    
	    $prev_content = $html2text->get_text(); //preg_replace('/[\r\n]+/', '<br/>', $html2text->get_text());
	    
	    $html2text = new Html2Text($curr_content);
	    
	    $curr_content = $html2text->get_text(); //preg_replace('/[\r\n]+/', '<br/>', $html2text->get_text());
	    	  
	    return $editor->getDiff( $prev_content, $curr_content );
	}
	
	function getBody( $action, $object_it, $prev_object_it, $recipient )
	{
		global $model_factory;

		$body = '';

		switch ( $action )
		{
			case 'modify':
				if ( $object_it->get('IsArchived') != $prev_object_it->get('IsArchived') )
				{
					if ( $object_it->get('IsArchived') == 'Y' )
					{
						return text(836);
					}
					else
					{
						return text(837);
					}
				}
				
				// the last change of the page
				$change = $model_factory->getObject('WikiPageChange');
		        
				$change->defaultsort = 'RecordCreated DESC';
		        
				$change_it = $change->getByRefArray(array('WikiPage' => $object_it->getId()), 1);
								
				// the current page text
				$body = '<h3>'.$object_it->get('Caption').'</h3>';

				$editor = WikiEditorBuilder::build($object_it->get('ContentEditor'));
				
				$editor->setObjectIt( $object_it );
				
				$diff = $this->getDiff( $editor, $change_it, $object_it );

		        $body .= '<br/>';

		        if ( $diff == '' ) 
		        {
		         	$body .= translate('Нет изменений');
		        }
		        else 
		        {
		        	if ( strlen($diff) > 5000 )
		        	{
		        		$body .= '<a href="'._getServerUrl().$object_it->getHistoryUrl().'">'.
		        			translate('История изменений').'</a>';
		        	}
		        	else
		        	{
		        		$body .= $diff;
		        	}
		        }
		       
				$body = str_replace(chr(10), '', $body);
				return $body.'<br/><br/>'; 

			case 'add':
				$more_text = false;
				
				$editor = WikiEditorBuilder::build($object_it->get('ContentEditor'));

				$editor->setObjectIt( $object_it );
				
				$url = $this->getObjectItUid($object_it);
				
				$body = '<h3>'.$object_it->get('Caption').'</h3><br/>';
				
				$parser = $editor->getHtmlParser();
				
				$body .= $parser->parse_substr( $object_it->getHtmlDecoded('Content'), 620, $more_text );
		
				if ( $more_text )
				{
					$body .= '<br/><br/><a href="'.
						$this->getObjectItUid($object_it).'">'.translate('читать дальше').'</a>';
				}
					
				$body = str_replace(chr(10), '', $body);
				return $body;	
		}
	}	
}
