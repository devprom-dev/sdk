<?php

include_once SERVER_ROOT_PATH.'pm/views/wiki/editors/WikiEditorBuilder.php';
include_once SERVER_ROOT_PATH.'pm/views/watchers/FieldWatchers.php';

include_once "fields/FieldWikiPage.php";
include_once "fields/FieldWikiAttachments.php";
include_once "fields/FieldWikiTagTrace.php";
include_once "fields/FieldWikiTrace.php";
include_once "fields/FieldCompareToContent.php";
include_once "fields/FieldCompareToCaption.php";

class PMWikiForm extends PMPageForm
{
    private $template_object;
    
 	var $review_mode = false;
 	var $readonly_mode = false;
 	var $form_index = '';
 	
 	private $revision_it;
 	
 	private $page_to_compare_it;
 	
 	private $document_it;
 	
 	private $descriminator_value = null;
 	
 	private $trace_actions_template = array();
 	
 	private $template_mode = false;
 	
 	private $editable = true;
 	
 	private $appendable = true;
 	
 	private $append_methods = array();
 	
 	function __construct( $object, $template_object ) 
	{
		global $_REQUEST, $model_factory;
		
		$this->template_object = $template_object;
		
		$object->addPersister( new WatchersPersister() );
		
		parent::__construct( $object );

		$this->object->setAttributeOrderNum( 'TransitionComment', 0 );
		
		$this->template_mode = is_a($object, get_class($template_object));
		
		$this->editable = getFactory()->getAccessPolicy()->can_modify($object);
		
		$this->appendable = getFactory()->getAccessPolicy()->can_create($object);
		
		$this->buildMethods();
	}
 	
	function buildMethods()
	{
		$url = $this->getObject()->getPageNameObject().'&ParentPage=%object-id%';
		
		$this->append_methods[] = array( 
				'name' => translate('Добавить раздел'),
				'url' => $url 
		);
		
		$type_it = $this->getTypeIt();
		
		while ( is_object($type_it) && !$type_it->end() )
		{
			$this->append_methods[] = array( 
					'name' => translate('Добавить').': '.$type_it->getDisplayName(),
					'url' => $url.'&PageType='.$type_it->getId()
			);
			
			$type_it->moveNext();
		}
	}
	
    protected function extendModel()
    {
    	$object = $this->getObject();
    	
    	foreach( array('OrderNum', 'ContentEditor') as $attribute )
    	{
    		$object->setAttributeVisible($attribute, false);
    	}
		
		$object->setAttributeVisible('ParentPage', $this->getEditMode());
		$object->setAttributeVisible('Author', !$this->getEditMode());
		$object->setAttributeVisible('PageType', is_object($this->getTypeIt()));
		
        foreach( array('Caption', 'Content', 'Tags', 'Watchers', 'Attachments') as $attribute )
    	{
    		$object->setAttributeVisible($attribute, true);
    	}
 				
 		parent::extendModel();
    }
	
   	function getDiscriminatorField()
 	{
 		return 'PageType';
 	}
	
	function getDiscriminator()
 	{
 		if ( !is_null($this->descriminator_value) ) return $this->descriminator_value;
 		 
 		$value = $this->getFieldValue($this->getDiscriminatorField());

 		if ( $value == '' ) return $this->descriminator_value = '';
 		
 		return $this->descriminator_value = 
 			$this->getObject()->getAttributeObject($this->getDiscriminatorField())
 				->getExact($value)->get('ReferenceName');
 	}
 	
	function getRedirectUrl()
	{
		if ( $this->getAction() == 'delete' )
		{
			$object_it = $this->getObjectIt();
			
			$root_it = $object_it->getRootIt();
			
			if ( $root_it->getId() > 0 )
			{
				$uid = new ObjectUID();
				
				$info = $uid->getUidInfo($root_it);
				
				return $info['url'];
			}
			else
			{
				return $this->getObject()->getPage();
			}
		}
		
		$object_it = $this->getObjectIt();
		
		if ( is_object($object_it) )
		{
			if ( $this->IsTemplate($object_it) ) 
			{
				return parent::getRedirectUrl(); 
			}
			else
			{
				$uid = new ObjectUID();
				
				$info = $uid->getUidInfo($object_it);
				
				return $info['url'];
			}
		}
		
		return '';
	}
	
	function processEmbeddedForms( $object_it )
	{
	    if ( !is_object($object_it) )
	    {
	        throw new Exception('Trying to process empty iterator object');
	    }
	    
		if ( $object_it->getId() == '' )
	    {
	        throw new Exception('Trying to process empty object');
	    }
	    
	    parent::processEmbeddedForms( $object_it );
	}
	
	function getTemplateObject()
	{
	    return $this->template_object;
	}
	
	function getTraceObject()
	{
		return getFactory()->getObject('WikiPageTrace');	
	}
	
	function setFormIndex( $index )
	{
	    $this->form_index = $index;
	}
	
	function setReviewMode()
	{
		$this->review_mode = true;
	}
	
	function getReviewMode()
	{
		return $this->review_mode;
	}
	
	function setReadonly( $readonly = true )
	{
	    $this->readonly_mode = $readonly;
	}
	
	function getReadonly()
	{
	    return $this->readonly_mode;
	}
	
	function setRevisionIt( $revision_it )
	{
		$this->revision_it = $revision_it;
	}
	
	function getRevisionIt()
	{
		return $this->revision_it;
	}
	
	function setCompareTo( $page_it )
	{
		$this->page_to_compare_it = $page_it;
	}
	
	function getCompareTo()
	{
		return $this->page_to_compare_it;
	}
	
	function setDocumentIt( $document_it )
	{
		$this->document_it = $document_it;
	}
	
	function getDocumentIt()
	{
		return $this->document_it;
	}
	
	function IsWatchable( $page_it )
	{
		return !$this->IsTemplate($page_it);
	}

 	function IsTemplate( $page_it = null )
	{
	    if ( is_object($page_it) )
	    {
	        return is_a($page_it->object, get_class($this->getTemplateObject()) );
	    }
	    else
	    {
	        return is_a($this->object, get_class($this->getTemplateObject()) );
	    }
	}
	
	function getTraceActionsTemplate()
	{
		if ( count($this->trace_actions_template) > 0 ) return $this->trace_actions_template;
		
		return $this->trace_actions_template = $this->buildTraceActionsTemplate();
	}
	
	function buildTraceActionsTemplate()
	{
		$actions = array();
		
		$report_it = getFactory()->getObject('PMReport')->getExact('currenttasks');
		
		if ( getSession()->getProjectIt()->getMethodologyIt()->HasTasks() && getFactory()->getAccessPolicy()->can_read($report_it) )
		{
			$actions[] = array( 
					'name' => translate('Задачи'),
					'url' => $report_it->getUrl().'&state=all&trace='.strtolower(get_class($this->getObject())).':%page-id%'
			);
		}

		return $actions;
	}
	
 	function getCreateActions( $page_it )
	{
		return array();
	}
	
	function getDeleteActions()
	{
	    $actions = array();
	    
		$object_it = $this->getObjectIt();
	    
		if ( !$this->getReadonly() && is_object($object_it) && !$this->getEditMode() )
		{
			$method = new DeleteObjectWebMethod($object_it);
			
			if ( $method->hasAccess() )
			{
			    if ( $this->IsFormDisplayed() || $this->getReviewMode() )
			    {
			    	$method->setRedirectUrl('function() { if ( typeof loadContentTree != \'undefined\' ) loadContentTree();}');
			    }
			    else
			    {
			    	$method->setRedirectUrl('donothing');
			    }
			    
			    $actions[] = array(
				    'name' => $method->getCaption(), 'url' => $method->getJSCall() 
			    );
			}
		}
		
		return $actions;
	}
	
	function getExportActions( $page_it )
	{
		global $model_factory;
		
		$actions =  array();

		if ( $this->IsTemplate($page_it) ) return $actions;
		
		$method = new WikiExportPreviewWebMethod();
		
		array_push($actions, array( 
			'name' => $method->getCaption(), 
			'url' => $method->getJSCall( $page_it ) 
		));

		if ( !is_object($this->template_it) )
		{
			$this->template_it = getFactory()->getObject('TemplateHTML')->getAll();
		}
		else
		{
			$this->template_it->moveFirst();
		}
		
		while ( !$this->template_it->end() )
		{
			$method = new WikiExportPreviewWebMethod();
			
			array_push($actions, array( 
				'name' => $method->getCaption().': '.$this->template_it->getDisplayName(), 
				'url' => $method->getJSCall( $page_it, $this->template_it->getId() ) 
			));

			$this->template_it->moveNext();
		}
		
		$method = new WikiExportPdfWebMethod();
		
		array_push($actions, array( 
			'name' => $method->getCaption(), 
			'url' => $method->getJSCall( $page_it ) 
		));
			
		return array_merge($actions, $this->getEditor()->getExportActions($page_it));
	}
	
 	function getTraceActions( $page_it )
	{
		global $model_factory;
		
		$actions = array();
		
		if ( $this->IsTemplate($page_it) ) return $actions;
			 
		$report = $model_factory->getObject('PMReport');
		
		$report_it = $report->getExact('currenttasks');
		
		if ( getSession()->getProjectIt()->getMethodologyIt()->HasTasks() && getFactory()->getAccessPolicy()->can_read($report_it) )
		{
			$class_name = strtolower(get_class($page_it->object));
			
			array_push ( $actions, 
				array( 'name' => translate('Задачи'),
					   'url' => $report_it->getUrl().
							'&state=all&trace='.$class_name.':'.join(',',$page_it->idsToArray()) ) 
				);
		}

		return $actions;
	}

	function getActions( $page_it = null )
	{
		global $model_factory;

		$actions = array();
		
        if ( !is_object($page_it) ) $page_it = $this->getObjectIt();
        
        $not_readonly = !$this->getReadonly() && !$this->getEditMode();
         
		if( $not_readonly && $this->editable ) 
		{
			$actions['edit'] = array( 
    	        'name' => translate('Изменить'),
    			'url' => $page_it->getEditUrl(),
    			'type' => 'button' 
			);
		} 

		if ( $this->template_mode ) return $actions;
		
		if ( $this->appendable && $not_readonly )
		{
			if ( $actions[array_pop(array_keys($actions))]['name'] != '' ) $actions[] = array();
		    
			foreach( $this->append_methods as $action )
			{
				$action['url'] = preg_replace('/%object-id%/', is_object($page_it) ? $page_it->getId() : '', $action['url']);
				
				$actions[] = $action;
			}
		}
		
		if ( !is_object($page_it) ) return $actions;
		
		if ( $actions[array_pop(array_keys($actions))]['name'] != '' ) $actions[] = array();

		$actions['history'] = array( 
		        'name' => translate('История изменений'),
				'url' => $page_it->getHistoryUrl().'&start='.(is_object($this->getRevisionIt()) ? $this->getRevisionIt()->getDateTimeFormat('RecordCreated') : ''),
		        'uid' => 'history'
		);

		$transit_actions = $this->getTransitionActions($page_it);
		
		if ( !$this->getReadonly() && count($transit_actions) > 0 )
		{
			array_splice( $actions, array_key_exists('edit', $actions) ? 1 : 0, 0, array_merge(array(array()),$transit_actions) );
		}
		
		if ( $this->IsWatchable($page_it) )
		{
 			$watch_method = new WatchWebMethod( $page_it );
 			
 			$watch_method->setRedirectUrl('donothing');
 			
 			if ( $watch_method->hasAccess() )
 			{
				$actions[] = array();
				
				$actions[] = array( 
						'name' => $watch_method->getCaption(),
						'url' => $watch_method->getJSCall() 
				);
 			}
		}
		
		$create_actions = $this->getCreateActions( $page_it );
		
		if ( count($create_actions) > 0 )
		{
			if ( $actions[array_pop(array_keys($actions))]['name'] != '' ) $actions[] = array();
			
			$actions[] = array( 
				'name' => translate('Создать'),
				'items' => $create_actions,
				'uid' => 'export' 
			);
		}
		
		$trace_actions = $this->getTraceActions( $page_it );

		if ( count($trace_actions) > 0 )
		{
			if ( $actions[array_pop(array_keys($actions))]['name'] != '' ) $actions[] = array();
		    
		    array_push ( $actions, array( 
				'id' => 'tracing',
				'name' => translate('Отчеты'),
				'items' => $trace_actions
			));
		}

		return $actions;
	}
	
	function getCompareActions( $object_it )
	{
		if ( !is_object($this->getCompareTo()) ) return array();
		
		if ( !$object_it->IsPersisted() && $object_it->get('ParentPage') != '' ) 
		{
			$trace_it = $this->getTraceObject()->getRegistry()->Query(
					array (
							new FilterAttributePredicate('SourcePage', $this->getCompareTo()->get('ParentPage')),
							new FilterAttributePredicate('Type', 'branch'),
							new WikiTraceTargetDocumentPredicate($this->getDocumentIt()->getId())
					)
				);

			if ( $trace_it->getId() == '' ) return array();
			
			$method = $this->getDuplicateMethod( $object_it );
			
			if ( !is_object($method) ) return array();
			
			$parms = array (
					'class' => get_class($object_it->object),
					'objects' => $object_it->getId(),
					'CopyOption' => 'on',
					'parent' => $trace_it->get('TargetPage')
			);
			
			$script = "javascript: runMethod('methods.php?method=".get_class($method)."', ".str_replace('"', "'", JsonWrapper::encode($parms)).", donothing, '')";
						
			$actions[] = array( 
				'url' => $script,
				'name' => text(1735) 
			);
			
			return $actions;
		}
		
		$trace_it = $this->getTraceObject()->getRegistry()->Query(
				array (
						new FilterAttributePredicate('SourcePage', $this->getCompareTo()->getId()),
						new FilterAttributePredicate('TargetPage', $object_it->getId())
				)
		);
		
		if ( $trace_it->get('Type') != 'branch' || $trace_it->get('IsActual') == 'Y' || $trace_it->get('UnsyncReasonType') != 'text-changed' ) return array();
		
		$actions = array();
		
		$method = new SyncWikiLinkWebMethod($trace_it);
		
		$method->setRedirectUrl("donothing");
		
		$actions[] = array( 
			'url' => $method->getJSCall(),
			'name' => $method->getCaption() 
		);
		
		$method = new IgnoreWikiLinkWebMethod($trace_it);
				
		$method->setRedirectUrl("donothing");
		
		$actions[] = array( 
			'url' => $method->getJSCall(),
			'name' => $method->getCaption() 
		);

		return $actions;
	}
	
	function getDuplicateMethod( $object_it )
	{
		return null;
	}
	
  	function getTreeMenu( $object_it )
 	{
 		$actions = array();

 		$uid = new ObjectUID;
 		
   		$actions[] = array( 
   		    'name' => text(1556),
       		'url' => $object_it->getViewUrl(),
   		    'uid' => 'open-new' 
   		);

   		$actions[] = array();
   		
   		$actions[] = array( 
   		    'name' => translate('Перейти'),
       		'url' => "javascript: gotoRandomPage(".$object_it->getId().", 3, true)" 
   		);
   		
 		if ( $this->IsTemplate($object_it) ) return $actions;
 		
 		if ( !$this->getReadonly() && getFactory()->getAccessPolicy()->can_modify($object_it) )
 		{
			if ( $actions[count($actions)-1]['name'] != '' ) array_push($actions, array());
			
 		    array_push($actions, 
				array( 'name' => translate('Редактировать'),
					   'url' => $object_it->getEditUrl() )
				);
 		}
 		
		if ( !$this->getReadonly() && getFactory()->getAccessPolicy()->can_create($object_it->object) )
		{
			if ( $actions[count($actions)-1]['name'] != '' ) array_push($actions, array());
			
			array_push($actions, 
				array( 'name' => translate('Добавить'),
					   'url' => $this->object->getPageName().'&ParentPage='.$object_it->getId() )
				);
		
			$type_it = $this->getTypeIt();
			
			while ( is_object($type_it) && !$type_it->end() )
			{
				array_push($actions, 
					array( 'name' => translate('Добавить').': '.$type_it->getDisplayName(),
						   'url' => $this->object->getPageName().'&ParentPage='.$object_it->getId().'&PageType='.$type_it->getId() )
					);
				$type_it->moveNext();
			}
		}

		$method = new DeleteObjectWebMethod( $object_it );
		
		if ( !$this->getReadonly() && $method->hasAccess() ) 
		{
	        $method->setRedirectUrl('function() {loadContentTree();}');
		    
			$actions[] = array();
			$actions[] = array (
				'name' => $method->getCaption(), 'url' => $method->getJSCall() 
			);
		}
			
		return $actions;
 	}
 	
 	function getEditor()
	{
		$object_it = $this->getObjectIt();
		
		$type_it = $this->getTypeIt();
		
		$editor_class = is_object($object_it) ? $object_it->get('ContentEditor') : "";
		
		if ( is_object($type_it) && $_REQUEST['PageType'] != '' )
		{
			$type_id = $_REQUEST['PageType'];
			
			$type_it->moveToId( $type_id );
	
			if ( $type_it->getId() == $type_id ) $editor_class = $type_it->get('WikiEditor');
		}
		
		$editor = WikiEditorBuilder::build($editor_class);
		
		if ( is_object($object_it) && $_REQUEST['wiki_mode'] != 'new' )
		{
			$editor->setObjectIt( $object_it );
		}
		else
		{
			$editor->setObject( $this->getObject() );
		}

		return $editor;
	}
	
	function getTypeIt()
	{
		return null;
	}
	
	function getDefaultTemplateIt()
	{
		global $_REQUEST, $model_factory;
		
		$template = $this->getTemplateObject();
		
		if ( !is_object($template) ) return $this->getObject()->getEmptyIterator();

		if ( $_REQUEST['PageType'] != '' )
		{
			$type_it = $this->getTypeIt();
			
			$type_it->moveToId( $_REQUEST['PageType'] );
			
			if ( $type_it->get('DefaultPageTemplate') > 0 )
			{
				return $template->getExact( $type_it->get('DefaultPageTemplate') );
			}
		}
		else
		{
			return $template->getDefaultIt();
		}
	}
	
	function getFieldValue( $field )
	{
		$object_it = $this->getObjectIt();
		
		switch ( $field )
		{
			case 'Author':
				if ( !is_object($object_it) || is_object($object_it) && $object_it->get('Author') == '' )
				{
					return getSession()->getParticipantIt()->getId();
				}
				break;
				
			case 'ContentEditor':
				
				return get_class( $this->getEditor() );
				
			case 'Content':
				
				$editor = $this->getEditor();
				
				if ( !is_object($object_it) && !$this->IsTemplate() )
				{
					$template_it = $this->getDefaultTemplateIt();
					
					if ( is_object($template_it) && $template_it->getId() > 0 )
					{
				        $editor->setObjectIt($template_it);
				    
				        $parser = $editor->getEditorParser($template_it->get('ContentEditor'));
				    
				        if ( is_object($parser) ) return $parser->parse($template_it->getHtmlDecoded('Content'));
					    
						return $template_it->get('Content');
					}
				}
				
				// if the page was created using other editor then convert the data
				if ( is_object($object_it) && $this->getEditMode() && $object_it->get('ContentEditor') != get_class($editor) )
				{  
				    $editor->setObjectIt($object_it);
				    
				    $parser = $editor->getEditorParser($object_it->get('ContentEditor'));
				    
				    if ( is_object($parser) ) return $parser->parse($object_it->get('Content'));
				}
				
				break;
				
			case 'Template':
				$template_it = $this->getDefaultTemplateIt();
				
				if ( is_object($template_it) && $template_it->getId() > 0 )
				{
					return $template_it->getId();
				}
				
				break;
		}
		
		return parent::getFieldValue( $field );
	}
	 
	function getFieldDescription( $attr )
	{
		switch ( $attr )
		{
			case 'Content':
				if ( $this->getEditMode() )
				{
					$editor = $this->getEditor();
					return $editor->getDescription();
				}
				
			default:
				return parent::getFieldDescription( $attr );
		}
	}
	
	function drawButtons()
	{
		$editor = $this->getEditor();
		
		$editor->drawPreviewButton();

		parent::drawButtons();
	}
	
	function getTransitionAttributes()
	{
		return array('Caption');
	}
	
  	function IsAttributeVisible( $attr_name ) 
 	{
 		$object_it = $this->getObjectIt();
 		
		if ( $this->IsTemplate($object_it) )
 		{
 			if ( $this->getEditMode() )
 			{
 				return in_array($attr_name, array('Caption', 'Content', 'UserField1'));
 			}
 			else
 			{
 				return in_array($attr_name, array('Caption', 'Content'));
 			}
 		}

 		return parent::IsAttributeVisible( $attr_name );
	}

	function createField( $name )
	{
		$field = parent::createField( $name );
		
		if ( !is_object($field) ) return $field;

		if ( $this->getReadonly() )	$field->setReadonly( true );
		
		switch ( $name )
		{
			case 'Caption':
				
				$field->setTabIndex( 1 );

				$field->setId( $field->getId().$this->form_index );
				
		   		if ( $this->getTransitionIt()->getId() > 0 )
   			    {
   			        $field->setReadonly( true );
   			    }
				
				break;

			case 'Template':
				
				$field->setTabIndex( 2 );
				
				break;

			case 'Content':
				
				$field->setTabIndex( 3 );
				
				$field->setId( $field->getId().$this->form_index );

				break;    
		}
		
		return $field;
	}
	
	function createFieldObject( $name )
	{
		global $model_factory;
		
		$this->object_it = $this->getObjectIt();

		switch ( $name )
		{		
			case 'Watchers':
				return new FieldWatchers( is_object($this->object_it) ? $this->object_it : $this->object );

			case 'Tags':
				return new FieldWikiTagTrace( is_object($this->object_it)
					? $this->object_it : null ); 
					
			case 'Caption':

				if ( is_object($this->getCompareTo()) )
				{
					return new FieldCompareToCaption( $this->getObjectIt(), $this->getCompareTo() );
				}
				
				if ( !$this->getEditMode() )
			    {
    				$field = new FieldWYSIWYG( get_class($this->getEditor()) );
     					
     				is_object($this->object_it) ? 
    					$field->setObjectIt( $this->object_it ) : 
    						$field->setObject( $this->getObject() );
    						
    			    $field->getEditor()->setMode( WIKI_MODE_INPLACE_INPUT );
			    }
			    else
			    {
			        $field = parent::createFieldObject($name);
			        
			        if ( is_object($field) )
			        {
	 				    $field->setDefault( translate('Название') );
			        }
			    }
 				
				return $field;
				
			case 'Content':
				
				if ( is_object($this->getCompareTo()) )
				{
					return new FieldCompareToContent( $this->getObjectIt(), $this->getCompareTo() );
				}
				
				$field = new FieldWYSIWYG( get_class($this->getEditor()) );
 					
 				is_object($this->object_it) ? 
					$field->setObjectIt( $this->object_it ) : 
						$field->setObject( $this->getObject() );

				$field->setAttachmentsField( new FieldWikiAttachments(
				        is_object($this->object_it) ? $this->object_it : $this->object
				));
				
				if ( $this->getEditMode() )
				{
					$field->setHasBorder( false );
					$field->getEditor()->setMode( WIKI_MODE_NORMAL );
				}
				else
				{
    				$field->setCssClassName( 'wysiwyg-text' );
				}
						
 				return $field;
 				
			case 'PageType':

				if ( is_object($this->getTypeIt()) )
				{
    				return new FieldDictionary( $this->getTypeIt()->object );
				}
				else
				{
				    return null;
				}
				
			case 'Attachments':
				return new FieldWikiAttachments( is_object($this->object_it) ? $this->object_it : $this->object );

			case 'ParentPage':
			    $object = clone($this->object->getAttributeObject( $name ));

			    if( !is_object($object) ) return null;
			    
		        $object->addFilter( new FilterBaseVpdPredicate() );
			    
			    return new FieldWikiPage( $object );
				
			case 'Template':
				$template = $this->getTemplateObject();
				
				if ( !is_object($template) ) return null;
				
				$editor = $this->getEditor();

				$editor->setFieldId( 'WikiPageContent' );
				$callback = $editor->getTemplateCallback();
								
				$script = "javascript: wikiChangeTemplate('".get_class($template)."','".get_class($editor)."', ".$callback."); ";
				
				$field = new FieldDictionary( $this->getTemplateObject() );
				$field->setScript( $script );

				return $field;
				
			default:
				return parent::createFieldObject( $name );
		}
	}

 	function validateInputValues( $id, $action )
	{
		global $_REQUEST, $model_factory;
		
		$message = parent::validateInputValues( $id, $action );
		
		if ( $message != '' ) return $message;
		
		// check parent page is correct
		if ( !$this->IsTemplate($this->object_it) && is_object($this->object_it) && $this->getAction() != 'add' )
		{
			if ( $this->object_it->getId() == $_REQUEST['ParentPage'] )
			{
				return text(971);
			}
			
			$ids = $this->object_it->getAllChildrenIds();
			
			if ( in_array( $_REQUEST['ParentPage'], $ids ) )
			{
				return text(971);
			}
		}
		
		return '';
	}
		
	function getRenderParms()
	{
		$object = $this->getObject();
		
		$object_it = $this->getObjectIt();
		
		$parms = array (
			'lifecycle' => is_object($object_it) && is_a($object_it, 'StatableIterator') ? $object_it->IsTransitable() : false,
			'formurl' =>  $object->getPage(),
		    'index' => $this->form_index,
		    'document_mode' => $this->getReviewMode(),
			'baseline' => is_object($this->getRevisionIt()) ? $this->getRevisionIt()->getId() : '',
			'broken_traces' => is_object($object_it) ? $object_it->get('BrokenTraces') != "" : false,
			'compare_actions' => $this->getCompareActions($object_it),
			'persisted' => is_object($object_it) ? $object_it->IsPersisted() : false,
			'has_properties' => !$this->IsTemplate($object_it),
			'trace_attributes' => $this->getObject()->getAttributesByGroup('trace')
		);
		
		return array_merge( parent::getRenderParms(), $parms ); 
	}
	
	function getTemplate()
	{
		if ( $_REQUEST['properties'] == 'true' )
		{
			if ( $_REQUEST['baseline'] != '' ) $this->setReadonly();
			
			return "pm/WikiPageProperties.php";
		}
		
	    if( !$this->getEditMode() )
	    {
	    	return "pm/WikiPageForm.php";
	    }
	    
	    return "core/PageForm.php";
	}
}