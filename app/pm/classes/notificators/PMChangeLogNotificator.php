<?php

include_once SERVER_ROOT_PATH.'cms/classes/ChangeLogNotificator.php';
include_once SERVER_ROOT_PATH.'ext/html/html2text.php'; 

class PMChangeLogNotificator extends ChangeLogNotificator
{
    protected $required_by_transition = array();
     
	function is_active( $object_it ) 
	{
	    global $model_factory;
	    
	    if ( !is_object($this->entity_set) )
	    {
	        $set = $model_factory->getObject('ChangeLogEntitySet');
	        
	        $this->entity_set = $set->getAll();
	    }

		$check = in_array( $object_it->object->getEntityRefName(), $this->entity_set->fieldToArray('ReferenceName') )
		    || in_array( strtolower(get_class($object_it->object)), $this->entity_set->fieldToArray('ClassName') );
			
		return $check ? true : parent::is_active( $object_it );
	}

 	function modify( $prev_object_it, $object_it, $visibility = 1 ) 
	{
		$entity_ref_name = $object_it->object->getEntityRefName();
		
	    if ( ($object_it->object instanceof MetaobjectStatable) && $object_it->object->getAttributeType('Transition') != '' )
		{
		    $this->required_by_transition = $object_it->object->getAttributesRequired($object_it->getStateIt()); 
		}
		else
		{
		    $this->required_by_transition = array();
		}
		
		switch ( $entity_ref_name )
		{
			case 'BlogPost':
				parent::modify( $object_it, $prev_object_it, $visibility );
				break;
				
			default:
				parent::modify( $prev_object_it, $object_it, $visibility );
		}
	}

	function process( $object_it, $kind, $content = '', $visibility = 1, $author_email = '') 
	{
		global $model_factory;

		if( !$this->is_active($object_it) ) return;
		
		$modified_attributes = $this->getModifiedAttributes();
		
		$methodology_it = getSession()->getProjectIt()->getMethodologyIt();
		
		$uid = new ObjectUID;
		
		switch ( $kind )
		{
			case 'added':
				$caption = translate('добавлено');
				break;
			case 'modified':
				$caption = translate('изменено');
				break;
			case 'deleted':
				$caption = translate('удалено');
				break;
		}
		
		if ( $object_it->object->getEntityRefName() == 'pm_ChangeRequest' )
		{
			$author_email = $object_it->get('Author'); 
		}

		switch( $object_it->object->getEntityRefName() )
		{
			case 'pm_Task':
				
			    if ( !$methodology_it->HasTasks() ) break;
			    
				if ( $object_it->get('ChangeRequest') > 0 )
				{
					$request_it = $object_it->getRef('ChangeRequest');
					
					$this->setModifiedAttributes(array('Tasks'));
					
					if($kind == 'added' || $kind == 'deleted')
					{
						if ( $object_it->getDisplayName() != $request_it->getDisplayName() )
						{
							$task_caption = ': '.$object_it->getDisplayName();
						}
						
						parent::process( $request_it, 'modified', 
							$uid->getObjectUid($object_it).' '.$task_caption.' ('.$caption.')', $visibility, $author_email );
					}
					else
					{
						parent::process( $request_it, 'modified', '', $visibility + 1, $author_email );
					}
				}
				
				$this->setModifiedAttributes($modified_attributes);
				
				parent::process( $object_it, $kind, $content, $visibility, $author_email );
				
				break;
				
			case 'pm_Attachment':
				$anchor_it = $object_it->getAnchorIt();
				
				switch ( $anchor_it->object->getClassName() )
				{
					case 'pm_TestCaseExecution':
						return false;
						
					default:
						
						$this->setModifiedAttributes(array('Attachments'));
						
						parent::process( $anchor_it, 'modified', 
							$object_it->object->getDisplayName().': '.$object_it->getFileName('File').
								' ('.$caption.')', $visibility, $author_email );
				}
					
				break;

			case 'WikiPageFile':
				$anchor_it = $object_it->getRef('WikiPage');
				
				$this->setModifiedAttributes(array('Attachments'));
				
				parent::process( $anchor_it, 'modified', 
					$object_it->object->getDisplayName().': '.$object_it->getFileName('Content').
						' ('.$caption.')', $visibility, $author_email );
				break;
				
			case 'BlogPostFile':
				$anchor_it = $object_it->getRef('BlogPost');
				
				$this->setModifiedAttributes(array('Attachments'));
				
				parent::process( $anchor_it, 'modified', 
					$object_it->object->getDisplayName().': '.$object_it->getFileName('Content').
						' ('.$caption.')', $visibility, $author_email );
				break;
			
			case 'pm_ChangeRequestLink':
				$request_it = $object_it->getRef('SourceRequest');
				
				$this->setModifiedAttributes(array('Links'));
				
				parent::process( $request_it, 'modified', 
					html_entity_decode($object_it->getTraceDisplayName(), ENT_QUOTES | ENT_HTML401, APP_ENCODING).' ('.$caption.')', $visibility, $author_email );
					
				break;

			case 'pm_FunctionTrace':
				
				$base_it = $object_it->getRef('Feature');
				
				$related_it = $object_it->getObjectIt();
				
				if ( $kind != 'modified' && $base_it->getId() > 0 && $related_it->getId() > 0 )
				{
					parent::process( $base_it, 'modified', 
						html_entity_decode($object_it->getTraceDisplayName(), ENT_QUOTES | ENT_HTML401, APP_ENCODING).' ('.$caption.')', $visibility + 2, $author_email );
				
					parent::process( $related_it, 'modified', 
						html_entity_decode($object_it->getBacktraceDisplayName(), ENT_QUOTES | ENT_HTML401, APP_ENCODING).' ('.$caption.')', $visibility + 2, $author_email );
				}

				break;
				
			case 'pm_ChangeRequestTrace':
				
				if ( strtolower($object_it->get('ObjectClass')) == strtolower('SubversionRevision') ) break;
				
				$request_it = $object_it->getRef('ChangeRequest');
				$related_it = $object_it->getObjectIt();
				
				if ( $kind != 'modified' && $request_it->getId() > 0 && $related_it->getId() > 0 )
				{
					parent::process( $request_it, 'modified', 
						html_entity_decode($object_it->getTraceDisplayName(), ENT_QUOTES | ENT_HTML401, APP_ENCODING).' ('.$caption.')', $visibility + 2, $author_email );
				
					parent::process( $related_it, 'modified', 
						html_entity_decode($object_it->getBacktraceDisplayName(), ENT_QUOTES | ENT_HTML401, APP_ENCODING).' ('.$caption.')', $visibility + 2, $author_email );
				}

				break;

			case 'pm_TaskTrace':
				if ( strtolower($object_it->get('ObjectClass')) == strtolower('SubversionRevision') ) break;
				
				$task_it = $object_it->getRef('Task');
				$related_it = $object_it->getObjectIt();
				
				if ( $task_it->getId() > 0 && $related_it->getId() > 0 )
				{
					parent::process( $task_it, 'modified', 
						htmlspecialchars ($object_it->getTraceDisplayName(), ENT_QUOTES | ENT_HTML401, APP_ENCODING).' ('.$caption.')', $visibility + 2, $author_email );
				}
				break;

			case 'WikiPageTrace':
				$page_it = $object_it->getRef('TargetPage');

				if ( $kind == 'modified' )
				{
					if ( $content != '' )
					{
						parent::process( 
								$page_it, 'modified', 
								str_replace('%1', $object_it->getBacktraceDisplayName(), $object_it->get('IsActual') == 'N' ? text(1068) : text(1069)),
								$visibility, $author_email
			    		);
					}
				}
				else
				{
					parent::process( $object_it->getRef('SourcePage'), 'modified', 
						html_entity_decode($object_it->getTraceDisplayName(), ENT_QUOTES | ENT_HTML401, APP_ENCODING).' ('.$caption.')', $visibility + 2, $author_email );
				
					parent::process( $page_it, 'modified', 
						html_entity_decode($object_it->getBacktraceDisplayName(), ENT_QUOTES | ENT_HTML401, APP_ENCODING).' ('.$caption.')', $visibility + 2, $author_email );
				}
				
				break;
				
			case 'pm_RequestTag':
				$request_it = $object_it->getRef('Request');
				$tag_it = $object_it->getRef('Tag');

				$this->setModifiedAttributes(array('Tags'));
				
				if ( $kind == 'added' )
				{
					parent::process( $request_it, 'modified', 
						translate('Прикреплен тэг').': '.$tag_it->getDisplayName(), $visibility + 1, $author_email );
				}
				else
				{
					parent::process( $request_it, 'modified', 
						translate('Удален тэг').': '.$tag_it->getDisplayName(), $visibility + 1, $author_email );
				}
				break;

			case 'pm_Question':
				parent::process( $object_it, $kind == 'added' ? 'commented' : $kind, '', $visibility, $author_email );
					
				break;

			case 'pm_TestPlanItem':
				$plan_it = $object_it->getRef('TestPlan');
				
				parent::process( $plan_it, 'modified', 
					$object_it->getDisplayName().' ('.$caption.')', $visibility + 1, $author_email );
					
				break;

			case 'pm_StateObject':
				if ( $kind == 'added' )
				{
					$ref_it = getFactory()->getObject($object_it->get('ObjectClass'))->getRegistry()->Query(
						array (
								new FilterInPredicate($object_it->get('ObjectId'))
						)
					);
					
					$text = $object_it->getRef('State')->getDisplayName();
					
					if ( $object_it->get('Comment') != '' )
					{
						$text .= ": ".$object_it->getHtmlDecoded('Comment');
					}
					
					$transition_it = $object_it->getRef('Transition');
	
					if ( $transition_it->count() > 0 )
					{
						$text .= chr(10).preg_replace('/%1/', $transition_it->getDisplayName(), text(904)).chr(10);
					}
					
					$this->setModifiedAttributes(array('State'));
					
					if ( $ref_it->object->getClassName() == 'pm_ChangeRequest' )
					{
						parent::process( $ref_it, 'modified', $text, $visibility + 1, $author_email );
					}
					elseif ( $ref_it->object->getClassName() == 'pm_Task' )
					{
						if ( $methodology_it->HasTasks() )
						{
							parent::process( $ref_it, 'modified', $text, $visibility + 1, $author_email );
						}
					}
					else
					{
						parent::process( $ref_it, 'modified', $text, $visibility, $author_email );
					}
				}
				break;

			case 'Comment':
				
				switch ( $kind )
				{
					case 'added':
						$content = str_replace( '%2', $object_it->getPlainText('Caption'), 
							str_replace('%1', '['.$uid->getObjectUid( $object_it ).']', text(1057) ) );
						 
						parent::process( $object_it->getAnchorIt(), 'commented', $content, $visibility + 1, $object_it->get('ExternalEmail') );
						break;
						
					case 'modified':
						$content = str_replace( '%2', $object_it->getPlainText('Caption'), 
							str_replace('%1', '['.$uid->getObjectUid( $object_it ).']', text(1200) ) );
						 
						parent::process( $object_it->getAnchorIt(), 'comment_modified', $content, $visibility + 1, $object_it->get('ExternalEmail') );
						break;
						
					case 'deleted':
						$content = str_replace( '%2', $object_it->getPlainText('Caption'), str_replace('%1', '', text(1199) ) );
						parent::process( $object_it->getAnchorIt(), 'comment_deleted', $content, $visibility + 1, $object_it->get('ExternalEmail') );
						break;
				}
				break;
		    
			case 'pm_Watcher':
				switch ( $kind )
				{
					case 'added':
						 
						parent::process( $object_it->getAnchorIt(), 'modified', 
							str_replace('%1', $object_it->getDisplayName(), text(1503)).'.', $visibility + 1, $author_email );

						break;
						
					case 'deleted':
						
						parent::process( $object_it->getAnchorIt(), 'modified', 
							str_replace('%1', $object_it->getDisplayName(), text(1504)).'.', $visibility + 1, $author_email );

						break;
				}
				break;
				
			case 'pm_Activity':
			    
			    if ( $object_it->get('Task') == '' ) break;
			    
			    $anchor_it = getFactory()->getObject('Task')->getExact($object_it->get('Task'));
			    
			    if ( $object_it->get('Iteration') < 1 && $anchor_it->object->getAttributeType('ChangeRequest') != "" )
			    {
			        $anchor_it = $anchor_it->getRef('ChangeRequest');
			    }

				$this->setModifiedAttributes(array('Fact'));
			    
			    parent::process( $anchor_it, 'modified',
			        $object_it->getDisplayNameShort().' ('.$caption.')', $visibility, $author_email );
			    
			    break;
			    
			case 'WikiPageChange':
			    
			    switch( $kind )
			    {
			        case 'added':

					    $page_it = $object_it->getRef('WikiPage');
					    
					    $content = '[url='.$page_it->getHistoryUrl().'&version='.$object_it->getId().' text='.translate('История изменений').']';
					    
					    parent::process( $page_it, 'modified', $content, $visibility, $author_email );
			        	
			        	break;
			        	
			        case 'deleted':
			        	
					    $page_it = $object_it->getRef('WikiPage');
					    
					    $content = str_replace('%2', $object_it->getHtmlDecoded('Content'),
					    				str_replace('%1', $object_it->getDateTimeFormat('RecordCreated'), text(1507)));

					    parent::process( $page_it, 'modified', $content, $visibility, $author_email );
			        	
			        	break;
			    }
			    
			    break;

			default:
				if ( $kind != 'modified' || $content != '' ) parent::process( $object_it, $kind, $content, $visibility, $author_email );
			    
				break;
		}
	}
	
	function isAttributeVisible( $attribute_name, $object_it, $action )
	{
		global $project_it, $_REQUEST;
		
		switch ( $object_it->object->getClassName() )
		{
			case 'pm_Participant':

				if ( $attribute_name == 'Salary' )
				{
					return false;
				}
				break;
			
			case 'pm_Artefact':
				switch ( $attribute_name )
				{
					case 'Release':
					case 'Build':
					case 'Version':
						return false;
				}
				break;
				
			case 'pm_ChangeRequest':
				switch ( $attribute_name )
				{
					case 'EstimationLeft':
					case 'StartDate':
					case 'FinishDate':
					case 'Project':
					    return false;
				}
				break;
				
			case 'pm_Task':
				switch ( $attribute_name )
				{
					case 'StartDate':
					case 'FinishDate':
					case 'LeftWork':
					    return false;
				}
				break;
				
			case 'pm_AccessRight':
			case 'pm_ObjectAccess':
				switch ( $attribute_name )
				{
					case 'AccessType':
						return false;
				}
				break;
				
			case 'WikiPage':
				switch ( $attribute_name )
				{
					case 'UserField1':
					case 'UserField2':
					case 'UserField3':
					case 'Content':
						return false;

					case 'ParentPage':
					case 'PageType':
					case 'IsArchived':
						return true;
				}
				break;
		}

		switch ( $attribute_name )
		{
		    case 'State':
		    case 'StateObject':
		    case 'LifecycleDuration':
		    case 'RecordCreated':
		    case 'RecordModified':
		    case 'TransitionComment':
		    	return false;
		        
		    default:
				if ( in_array($attribute_name, $this->required_by_transition) ) return true;
		    	
		        // trace changes of custom attributes always
		        if ( $object_it->object->getAttributeOrigin($attribute_name) == ORIGIN_CUSTOM ) return true;
		        
		        return parent::isAttributeVisible( $attribute_name, $object_it, $action );
		}
	}
}
