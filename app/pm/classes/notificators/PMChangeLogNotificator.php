<?php
include_once SERVER_ROOT_PATH.'cms/classes/ChangeLogNotificator.php';

class PMChangeLogNotificator extends ChangeLogNotificator
{
	function is_active( $object_it )
	{
		if ( !is_object($object_it) ) return false;

	    if ( !is_object($this->entity_set) ) {
	        $this->entity_set = getFactory()->getObject('ChangeLogEntitySet')->getAll();
	    }

		$check = in_array( $object_it->object->getEntityRefName(), $this->entity_set->fieldToArray('ReferenceName') )
		    || in_array( strtolower(get_class($object_it->object)), $this->entity_set->fieldToArray('ClassName') );
			
		return $check ? true : parent::is_active( $object_it );
	}

 	function modify( $prev_object_it, $object_it, $visibility = 1 ) 
	{
		$entity_ref_name = $object_it->object->getEntityRefName();
		
		switch ( $entity_ref_name )
		{
			case 'BlogPost':
				parent::modify( $object_it, $prev_object_it, $visibility );
				break;
				
			default:
				parent::modify( $prev_object_it, $object_it, $visibility );
		}
	}

	function process( $object_it, $kind, $content = '', $visibility = 1, $author_email = '', $parms = array())
	{
		if( !is_object($object_it) ) return;
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
			    
				if ( $object_it->get('ChangeRequest') > 0 && $object_it->object->getAttributeType('ChangeRequest') != '' ) {
					$request_it = $object_it->getRef('ChangeRequest');
					$this->setModifiedAttributes(array('Tasks'));
					
					if($kind == 'added' || $kind == 'deleted')
					{
						if ( $object_it->getDisplayName() != $request_it->getDisplayName() ) {
							$task_caption = ': '.$object_it->getDisplayName();
						}
						parent::process( $request_it, 'modified',
							$uid->getObjectUid($object_it).' '.$task_caption.' ('.$caption.')', $visibility, $author_email );
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
				$anchor_it = $object_it->getPageIt();

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
				$content = str_replace( '%2', $object_it->getHtmlDecoded('Content'),
					str_replace('%1', '['.$uid->getObjectUid( $object_it ).']', text(1057) ) );

				parent::process( $object_it, $kind == 'added' ? 'commented' : $kind, $content, $visibility, $author_email );
					
				break;

			case 'cms_Snapshot':
				if ( $object_it instanceof SnapshotIterator )
				{
					$anchor_it = $object_it->getAnchorIt();
					if ( $anchor_it->getId() != '' ) {
						parent::process( $anchor_it, 'modified', str_replace('%1', $object_it->getDisplayName(), text(2096)), $visibility, $author_email );
					}
				}
				break;

			case 'cms_SnapshotItem':
				if ( $object_it instanceof SnapshotItemIterator )
				{
					$anchor_it = $object_it->getAnchorIt();
					if ( $anchor_it->getId() != '' ) {
						parent::process( $anchor_it, 'modified', str_replace('%1', $object_it->getDisplayName(), text(2096)), $visibility + 2, $author_email );
					}
				}
				break;

			case 'pm_StateObject':
				if ( $kind == 'added' )
				{
					$ref_it = getFactory()->getObject($object_it->get('ObjectClass'))->getRegistry()->Query(
						array (
								new FilterInPredicate($object_it->get('ObjectId'))
						)
					);

                    $text = '';

					$transition_it = $object_it->getRef('Transition');
					if ( $transition_it->count() > 0 ) {
						$text .= $transition_it->getDisplayName().' &rarr; ';
					}

                    $text .= $object_it->getRef('State')->getDisplayName();
                    if ( $object_it->get('Comment') != '' ) {
                        $text .= ": ".$object_it->getHtmlDecoded('Comment');
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
				if ( $object_it->object instanceof Activity ) {
					if ( $object_it->get('Task') == '' ) break;

					$anchor_it = getFactory()->getObject('Task')->getExact($object_it->get('Task'));
					if ( $object_it->get('Iteration') < 1 && $anchor_it->get('ChangeRequest') != '' ) {
						if ( $anchor_it->object->getAttributeType('ChangeRequest') != "" ) {
							$anchor_it = $anchor_it->getRef('ChangeRequest');
						}
					}

					$this->setModifiedAttributes(array('Fact'));

					parent::process( $anchor_it, 'modified',
						$object_it->getDisplayNameShort().' ('.$caption.')', $visibility, $author_email );
				}
			    break;
			    
			case 'WikiPageChange':
			    
			    switch( $kind )
			    {
			        case 'added':
					    $page_it = $object_it->getRef('WikiPage');
						$history_url = $page_it->getHistoryUrl();
					    $content = '[url='.$history_url.' text='.text(2238).']';
					    
					    parent::process( $page_it, 'modified', $content, $visibility, $author_email,
								array('ObjectUrl' => $history_url.'&version='.$object_it->getId()) );
			        	break;
			        	
			        case 'deleted':
			        	
					    $page_it = $object_it->getRef('WikiPage');
					    
					    $content = str_replace('%2', $object_it->getHtmlDecoded('Content'),
					    				str_replace('%1', $object_it->getDateTimeFormat('RecordCreated'), text(1507)));

					    parent::process( $page_it, 'modified', $content, $visibility, $author_email );
			        	
			        	break;
			    }
			    break;

			case 'WikiPage':
				switch( $kind )
				{
					case 'deleted':
						if ( $object_it->get('ParentPage') != '' ) {
							$page_it = $object_it->getRef('ParentPage');
							parent::process( $page_it, 'modified', $object_it->getDisplayName().' ('.$caption.')', $visibility, $author_email );
							return;
						}
						break;
				}
				if ( $kind != 'modified' || $content != '' ) {
					parent::process( $object_it, $kind, $content, $visibility, $author_email );
				}
				break;

			default:
				if ( $kind != 'modified' || $content != '' ) parent::process( $object_it, $kind, $content, $visibility, $author_email );
				break;
		}
	}
	
	function isAttributeVisible( $attribute_name, $object_it, $action )
	{
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

            case 'pm_Build':
                return parent::isAttributeVisible( $attribute_name, $object_it, $action );
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
		        // trace changes of custom attributes always
		        if ( $object_it->object->getAttributeOrigin($attribute_name) == ORIGIN_CUSTOM ) return true;
		        
		        return parent::isAttributeVisible( $attribute_name, $object_it, $action );
		}
	}
}
