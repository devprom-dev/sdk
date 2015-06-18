<?php

include_once SERVER_ROOT_PATH."pm/methods/c_watcher_methods.php";
include_once SERVER_ROOT_PATH."pm/methods/c_request_methods.php";
include_once SERVER_ROOT_PATH."pm/methods/DuplicateIssuesWebMethod.php";
include_once SERVER_ROOT_PATH."pm/methods/c_priority_methods.php";
include_once SERVER_ROOT_PATH."pm/views/time/FieldSpentTimeRequest.php";
include_once SERVER_ROOT_PATH."pm/views/watchers/FieldWatchers.php";
include_once SERVER_ROOT_PATH."pm/views/ui/FieldAttachments.php";
include_once SERVER_ROOT_PATH."core/views/c_issue_type_view.php";
include_once SERVER_ROOT_PATH."pm/views/project/FieldParticipantDictionary.php";
include_once SERVER_ROOT_PATH."pm/views/issues/FieldIssueTrace.php";
include_once SERVER_ROOT_PATH."pm/methods/SpendTimeWebMethod.php";
include_once SERVER_ROOT_PATH."pm/classes/issues/validators/ModelValidatorIssueFeatureLevel.php";

include "FieldTasksRequest.php";
include "FieldLinkedRequest.php";
include "FieldRequestState.php";
include "FieldRequestTagTrace.php";
include "FieldIssueDeadlines.php";
include "FieldAuthor.php";

class RequestForm extends PMPageForm
{
	private $template_it;
	private $method_create_task = null;
	private $method_duplicate = null;
	private $method_move = null;
	private $method_watch = null;
	private $new_template_url = '';
	private $target_projects = array();
 	private $method_spend_time = null;
 	private $feature_types = array();
	
	function __construct( $object ) 
	{
		parent::__construct($object);
		
		$this->checkTemplateDefined();
		$this->buildMethods();
	}

    protected function extendModel()
    {
		$this->getObject()->setAttributeOrderNum('State', 15);
		
		$this->getObject()->setAttributeVisible('State', !$this->getEditMode());
    	$this->getObject()->setAttributeVisible('Fact', is_object($this->getObjectIt()));
    	
    	if ( getFactory()->getObject('RequestType')->getAll()->count() < 1 ) {
    		$this->getObject()->setAttributeVisible('Type', false);
    	}
    	parent::extendModel();
    }
    
 	function buildModelValidator()
 	{
 		$validator = parent::buildModelValidator();
		$validator->addValidator( new ModelValidatorIssueFeatureLevel() );
 		return $validator;
 	}
    
	public function checkTemplateDefined()
	{
		global $model_factory;
		
		if ( $_REQUEST['template'] == '' ) return;

		$template = $model_factory->getObject('RequestTemplate');
		
		$template->addPersister( new ObjectTemplatePersister() );
		
		$template_it = $template->getExact($_REQUEST['template']);
		
		if ( $template_it->getId() < 1 ) return;
		
		$this->template_it = $template_it;
	}
	
	public function buildMethods()
	{
		$object = $this->getObject();
		$object_it = $object->getEmptyIterator();
		
 		$method = new RequestCreateTaskWebMethod($object_it);
 		if ( $method->hasAccess() )
 		{
 			if ( !$this->IsFormDisplayed() ) $method->setRedirectUrl('donothing');
 			$this->method_create_task = $method;
 		}
 		
		$method = new ObjectCreateNewWebMethod($object);
		if ( $method->hasAccess() )
		{
 			if ( !$this->IsFormDisplayed() ) $method->setRedirectUrl('donothing');
			$this->method_duplicate = $method;
		}

		$method = new MoveToProjectWebMethod($object_it);
		if ( $method->hasAccess() )
		{
 			if ( !$this->IsFormDisplayed() ) $method->setRedirectUrl('donothing');
			$this->method_move = $method;
		}
		
		$method = new WatchWebMethod($object_it);
		if ( $method->hasAccess() )
		{
 			if ( !$this->IsFormDisplayed() ) $method->setRedirectUrl('donothing');
			$this->method_watch = $method;
		}
		
	 	$method = new SpendTimeWebMethod($object_it);
 		if ( $method->hasAccess() )
 		{
 			if ( !$this->IsFormDisplayed() ) $method->setRedirectUrl('donothing');
 			$this->method_spend_time = $method;
 		}
		
		$this->new_template_url = getFactory()->getObject('RequestTemplate')->getPageNameObject().'&ObjectId=%object-id%&items=%object-id%';
		
		if ( getSession()->getProjectIt()->get('LinkedProject') != '' )
		{
			$linked_it = getFactory()->getObject('Project')->getRegistry()->Query(
					array (
							new FilterInPredicate(preg_split('/,/', getSession()->getProjectIt()->get('LinkedProject'))),
							new SortAttributeClause('Caption')
					)
			);
	
			while( !$linked_it->end() )
			{
				$this->target_projects[$linked_it->getId()] = $linked_it->getDisplayName();
				$linked_it->moveNext();
			}
			if ( !getSession()->getProjectIt()->IsPortfolio() )
			{
				$this->target_projects[getSession()->getProjectIt()->getId()] = getSession()->getProjectIt()->getDisplayName();
			}
		}
		
		$this->feature_types = getFactory()->getObject('pm_FeatureType')->getAll()->fieldToArray('ReferenceName');
	}
	
	function getTransitionAttributes()
	{
		return array('Caption');
	}
	
	function getNewObjectAttributes()
	{
		$attributes = array('Caption', 'Description', 'Priority', 'Function', 'Tasks', 'Attachment', 'OrderNum');
		if ( !getSession()->getProjectIt()->getMethodologyIt()->HasTasks() ) {
			$attributes[] = 'Owner';
		}
		return $attributes;
	}

	function createFieldObject( $name )
	{
		global $_REQUEST, $model_factory;
		
		$plugins = getSession()->getPluginsManager();
		$plugins_interceptors = is_object($plugins) ? $plugins->getPluginsForSection($this->getSite()) : array();
   	    foreach( $plugins_interceptors as $plugin )
        {
        	$field = $plugin->interceptMethodFormCreateFieldObject( $this, $name );
        	if ( is_object($field) ) return $field;
		}
		
		$this->object_it = $this->getObjectIt();
		
		switch ( $name )
		{		
			case 'TestExecution':
			    
			    if ( $_REQUEST['TestCaseExecution'] > 0 )
			    {
			        return new FieldAutoCompleteObject( getFactory()->getObject('pm_Test') );
			    }
			    else
			    {
    				return new FieldIssueTrace( $this->object_it, 
    					$model_factory->getObject('RequestTraceTestExecution') );
			    }

			case 'HelpPage':
				return new FieldIssueTrace( $this->object_it, 
					$model_factory->getObject('RequestTraceHelpPage') );

			case 'TestScenario':
				return new FieldIssueTrace( $this->object_it, 
					$model_factory->getObject('RequestTraceTestScenario') );

			case 'Requirement':
				return new FieldIssueTrace( $this->object_it, 
					$model_factory->getObject('RequestTraceRequirement') );

			case 'SourceCode':
				return new FieldIssueTrace( $this->object_it, 
					$model_factory->getObject('RequestTraceSourceCode') );
				
			case 'Question':
				return new FieldIssueTrace( $this->object_it, 
					$model_factory->getObject('RequestTraceQuestion') );

			case 'Fact':
				$field = new FieldSpentTimeRequest( $this->object_it );
				 
				if ( !$this->getEditMode() )
				{
					$field->setShortMode();
				}
				
				return $field;
				
			case 'Estimation':
				$strategy = getSession()->getProjectIt()->getMethodologyIt()->getEstimationStrategy();
				
				$field = $strategy->getEstimationFormField( $this );

				if ( !is_object($field) )
				{
					return parent::createFieldObject( $name );
				}
				else
				{
					return $field;
				}
				break;
				
			case 'Tasks':
				return new FieldTasksRequest( $this->object_it );

			case 'Deadlines':
				return new FieldIssueDeadlines( $this->object_it );
					
			case 'State':
				return new FieldRequestState( $this->object_it );
					
			case 'Tags':
				return new FieldRequestTagTrace( is_object($this->object_it)
					? $this->object_it : null ); 
				
			case 'Caption':
				if ( !$this->getEditMode() )
			    {
    				$field = new FieldWYSIWYG();
     					
     				is_object($this->object_it) ? 
    					$field->setObjectIt( $this->object_it ) : 
    						$field->setObject( $this->getObject() );
    						
    			    $field->getEditor()->setMode( WIKI_MODE_INPLACE_INPUT );
			    }
			    else
			    {
			        $field = parent::createFieldObject($name);
			    }
			    return $field;
			    
			case 'Function':
				return count($this->feature_types) > 0 
					? new FieldHierarchySelector($this->getObject()->getAttributeObject($name))
					: new FieldAutocompleteObject($this->getObject()->getAttributeObject($name));
		}
		
		if( $name == 'Attachment' )
		{
			return new FieldAttachments( is_object($this->object_it) ? $this->object_it : $this->object );
		}
		elseif( $name == 'Watchers' )
		{
			return new FieldWatchers( is_object($this->object_it) ? $this->object_it : $this->object );
		}
		else if( $name == 'Links' )
		{
			return new FieldLinkedRequest( $this->object_it );
		}
		elseif($name == 'PlannedRelease') 
		{
			$release = getFactory()->getObject('Release');
			$release->addFilter( new ReleaseTimelinePredicate('not-passed') );
			return new FieldAutoCompleteObject( $release );
		}
		elseif($name == 'Type') 
		{
			$type = $model_factory->getObject('pm_IssueType');
			
			$type->addFilter( new FilterBaseVpdPredicate() );
			
			return new FieldDictionary( $type );
		}
		elseif($name == 'Author') 
		{
			return new FieldAuthor();
		}
		elseif($name == 'Owner') 
		{
			$worker = getFactory()->getObject('User');
   			$worker->addFilter( new UserWorkerPredicate() );
			
			return new FieldParticipantDictionary( $worker );
		}
		elseif ( in_array($name, array('SubmittedVersion', 'ClosedInVersion')) )
		{
		    $field = new FieldAutoCompleteObject( $model_factory->getObject('Version') );
		    $field->setAppendable();
		    return $field;
		}
		else
		{
			return parent::createFieldObject( $name );
		}
	}	
	
    function createField( $name )
    {
    	global $_REQUEST;
    	
		$object_it = $this->getObjectIt();
		
		$field = parent::createField( $name );
		
   		switch ( $name )
   		{
   			case 'TestExecution':

   			    if ( $_REQUEST['TestCaseExecution'] > 0 )
   			    {
   			        $field->setReadonly( true );
   			    }
   				
   			    break;
   				
   			case 'Caption':
   			    
   		   		if ( is_a($field, 'FieldText') )
   			    {
   			        $field->setRows( 1 );
   			    }

   			    if ( $this->getTransitionIt()->getId() > 0 )
   			    {
   			        $field->setReadonly( true );
   			    }
   			    
   			    break;
   			    
   			case 'Description':
   			    
   			    if ( is_a($field, 'FieldText') )
   			    {
   			        $field->setRows( 6 );
   			    }

   		        if ( $field instanceof FieldWYSIWYG && !$this->getEditMode() )
		    	{
		    		$field->setCssClassName( 'wysiwyg-text' );
		    	}
   			    
   			    break;
        }

    	return $field;
    }
   
   	function getDefaultValue( $attr )
   	{
   		$value = parent::getDefaultValue( $attr );
   		
   		switch( $attr )
   		{
   		    case 'Author':
   		    	if ( $value == '' ) return getSession()->getUserIt()->getId();
   		    	break;

   		    case 'Project':
   		    	if ( $value == '' ) return getSession()->getProjectIt()->getId();
   		    	break;
   		    	
   		    case 'PlannedRelease':
   		    	if ( $value == '' && $this->IsAttributeRequired($attr) ) {
	   		    	return getFactory()->getObject('Release')->getRegistry()->Query(
	   		    				array (
	   		    					new FilterVpdPredicate(),
	   		    					new ReleaseTimelinePredicate('not-passed')
	   		    				)
	   		    		)->getId();
   		    	}
   		    	break;
   		}

   		return $value;
   	}
   	
    function getFieldValue( $attr )
    {
        global $model_factory;
        
        if ( is_object($this->template_it) && $this->template_it->get($attr) != '' )
        {
        	return $this->template_it->get($attr);
        }
        
    	switch ( $attr )
    	{
    		case 'Tasks': return '1';
    		
    		case 'TestExecution':

    		    $object_it = $this->getObjectIt();
    		    
    		    if ( is_object($object_it) && $object_it->get('TestExecution') != '' )
    		    {
					return $object_it->getRef('TestExecution')->getId();
    		    }
    		    elseif ( $_REQUEST['TestCaseExecution'] > 0 )
    		    {
    		        return getFactory()->getObject('pm_TestCaseExecution')->getExact($_REQUEST['TestCaseExecution'])->getRef('Test')->getId();
    		    }
    		    else
    		    {
    		        return '';
    		    }
    		    
    		case 'SubmittedVersion':

    			if ( !is_object($this->getObjectIt()) )
    			{
    				$test_id = $this->getFieldValue('TestExecution');
    				
    				if ( $test_id > 0 )
    				{
    					$test_it = getFactory()->getObject('pm_Test')->getExact($test_id);
    					
						if ( $test_it->get('Build') > 0 )
						{
							return $test_it->getRef('Build')->getFullNumber(); 
						}
						else if ( $test_it->get('Release') > 0 )
						{
							return $test_it->getRef('Release')->getDisplayName(); 
						}
    				}
    			}
    			
				return parent::getFieldValue( $attr );
    			
    		default:

    			if ( $_REQUEST['Question'] > 0 )
        		{
        			$question = $model_factory->getObject('pm_Question');
        			
        			$question_it = $question->getExact($_REQUEST['Question']);
        			
        			switch ( $attr )
        			{
        				case 'Description':
        					return $question_it->get_native('Content');
        					
        				case 'Author':
        					return $question_it->get('Author');
        			}
        		}
    		    
    			return parent::getFieldValue( $attr );
    	}
    }
	
	function IsAttributeEditable( $attribute )
	{
	    switch( $attribute )
	    {
			case 'ExternalAuthor': return false;

			default: return parent::IsAttributeEditable( $attribute );
	    }
	}
	
	function getActions()
	{
		$actions = parent::getActions();
		
		$object_it = $this->getObjectIt();
		
		if ( !is_object($object_it) ) return $actions;

		if ( $actions[count($actions) - 1]['name'] != '' ) array_push($actions, array( '' ) );
		
		if ( is_object($this->method_create_task) )
		{
			$this->method_create_task->setRequestIt($object_it);
			
			$actions[] = array ( 
				'name' => $this->method_create_task->getCaption(), 
				'url' => $this->method_create_task->getJSCall() 
			);
		}

		if ( $this->IsFormDisplayed() && is_object($this->method_duplicate) )
		{
			$method = new SetRequestIterationWebMethod( $object_it );
			if ( $method->hasAccess() )
			{
				if ( $actions[count($actions) - 1]['name'] != '' ) array_push($actions, array( '' ) );
				$actions[] = array( 
						'name' => $method->getCaption(),
						'url' => $method->getJSCall()
				);
			}
		}
		
		if ( is_object($this->method_duplicate) )
		{
			if ( $actions[count($actions) - 1]['name'] != '' ) $actions[] = array( '' );
	
			$actions[] = array( 
					'name' => text(1519),
					'url' => preg_replace('/%object-id%/', $object_it->getId(), $this->new_template_url)
			);
		}		
		
		return $actions;
	}
	
	function getDeleteActions()
	{
		$actions = parent::getDeleteActions();
		
		$object_it = $this->getObjectIt();
		if ( !is_object($object_it) ) return $actions;
		
		if ( is_object($this->method_watch) )
		{
			$this->method_watch->setObjectIt($object_it);			
		
			array_unshift($actions, array());
			array_unshift($actions, array( 
					'name' => $this->method_watch->getCaption(),
					'url' => $this->method_watch->getJSCall()
			));
		}

		return $actions;
	}
	
 	function getTransitionActions( $object_it )
	{
		$actions = parent::getTransitionActions( $object_it );
		
		$actions[] = array (
				'uid' => 'middle'
		);
		
		if ( is_object($this->method_duplicate) )
		{
			$parms = array(
					'Request' => $object_it->getId()
			);
			if ( $actions[count($actions) - 1]['name'] != '' ) $actions[] = array();
			if ( count($this->target_projects) > 0 )
			{
				$items = array();
				
				foreach( $this->target_projects as $id => $title )
				{
					if ( $id == $object_it->get('Project') ) continue;
					$items[] = array (
							'name' => $title,
							'url' => $this->method_duplicate->getJSCall(
											array_merge($parms, array('Project'=>$id))
									 )							
					);
				}
				
				$items[] = array();
				$items[] = array (
						'name' => translate('Выбрать'),
						'url' => $this->method_duplicate->getJSCall($parms)							
				);
				
				$actions[] = array( 
					'name' => text(867),
					'items' => $items
				);
			}
			else
			{
				$actions[] = array( 
						'name' => translate('Реализовать'),
						'url' => $this->method_duplicate->getJSCall($parms)
				);
			}
		}

		if ( is_object($this->method_move) )
		{
			$this->method_move->setRequestIt($object_it);
			
			if ( count($this->target_projects) > 0 )
			{
				$items = array();
				
				foreach( $this->target_projects as $id => $title )
				{
					if ( $id == $object_it->get('Project') ) continue;
					$items[] = array (
							'name' => $title,
							'url' => $this->method_move->getJsCall(array('Project'=>$id))							
					);
				}
				
				$items[] = array();
				$items[] = array (
						'name' => translate('Выбрать'),
						'url' => $this->method_move->getJsCall()							
				);
				
				$actions[] = array( 
					'name' => $this->method_move->getCaption(),
					'items' => $items
				);
			}
			else
			{
				$actions[] = array( 
						'name' => $this->method_move->getCaption(),
						'url' => $this->method_move->getJsCall()
				);
			}
		}
		
		if ( $this->IsFormDisplayed() )
		{
			$method = new ObjectCreateNewWebMethod($this->getObject());
			$actions[] = array( 
					'name' => text(2025),
					'url' => $method->getJSCall(
								array('Request' => $object_it->getId(), 'Project' => $object_it->get('Project'))
							 )
			);
		}

		if ( is_object($this->method_spend_time) )
		{
			$this->method_spend_time->setAnchorIt($object_it);
			
			$actions[] = array();
			$actions[] = array ( 
				'name' => $this->method_spend_time->getCaption(), 
				'url' => $this->method_spend_time->getJSCall() 
			);
		}

		return $actions;
	}
	
   	function getDiscriminatorField()
 	{
 		return $this->getEditMode() ? 'Type' : '';
 	}
	
	function getDiscriminator()
 	{
 		global $model_factory, $_REQUEST;
 		
 		if ( $_REQUEST['Type'] > 0 )
 		{
 			$object = $this->getObject();
 			
 			$ref = $object->getAttributeObject('Type');
 			$ref_it = $ref->getExact($_REQUEST['Type']);
 			
 			return $ref_it->get('ReferenceName');
 		}
 		else
 		{
 			$object_it = $this->getObjectIt();
 			
	 		if ( is_object($object_it) )
	 		{
	 			$ref_it = $object_it->getRef('Type');
	 			return $ref_it->get('ReferenceName');
	 		}
 		}
 	}
 	
	function getRenderParms()
	{
		$object_it = $this->getObjectIt();
		
		$refs_actions = array();
		
		if ( is_object($object_it) )
		{
    		$comments_count = getFactory()->getObject('Comment')->getCount($object_it);
		}
		
		$parms = array (
			'comments_count' => $comments_count,
			'refs_actions' => 
					is_object($object_it) 
							? $this->buildReferencesActions( $object_it ) 
							: array()
		);
		
		return array_merge( parent::getRenderParms(), $parms ); 
	}
	
	function buildReferencesActions( $object_it )
	{
		$refs_actions = array();
		
		$url = getFactory()->getObject('PMReport')->getExact('allissues')->get('Url');
    											
		$references = array ('Function', 'Author', 'Priority');
		
	    $attr_it = getFactory()->getObject('pm_CustomAttribute')->getByEntity( $this->getObject() );
        
        while( !$attr_it->end() )
        {
            if ( in_array($attr_it->getRef('AttributeType')->get('ReferenceName'), array('dictionary','reference')) )
            {
            	$references[] = $attr_it->get('ReferenceName'); 
            } 

            $attr_it->moveNext();
        }
		
		foreach( $references as $reference )
		{
	   		$refs_actions[$reference] = array(
	   				'name' => text(1828),
					'url' => $url.'&'.strtolower($reference).'='.urlencode($object_it->get($reference))
	   		);
		}

		if ( $object_it->object->getAttributeType('Owner') != '' )
		{
	   		$refs_actions['Owner'] = array(
	   				'name' => text(1828),
					'url' => $url.'&owner='.$object_it->get('Owner')
	   		);
		}
		$refs_actions['Type'] = array(
   				'name' => text(1828),
				'url' => $url.'&type='.$object_it->getRef('Type')->get('ReferenceName')
   		);
   		$refs_actions['SubmittedVersion'] = array(
   				'name' => text(1828),
				'url' => $url.'&subversion='.$object_it->get('SubmittedVersion')
   		);
   		$refs_actions['ClosedInVersion'] = array(
   				'name' => text(1828),
				'url' => $url.'&version='.$object_it->get('ClosedInVersion')
   		);
   		$refs_actions['Environment'] = array(
   				'name' => text(1828),
				'url' => $url.'&environment='.$object_it->get('Environment')
   		);
   		
   		return $refs_actions;
	}	
 	
	function render( $view, $parms )
	{
		if ( $this->getAction() == 'view' && $_REQUEST['formonly'] == '' )
		{
			echo $view->render("pm/RequestPageForm.php", 
				array_merge($parms, $this->getRenderParms()) );
		}
		else
		{
			 parent::render( $view, $parms );
		}
	}
}