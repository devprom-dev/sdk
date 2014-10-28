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

include "FieldTasksRequest.php";
include "FieldLinkedRequest.php";
include "FieldRequestState.php";
include "FieldRequestTagTrace.php";
include "FieldIssueDeadlines.php";

class RequestForm extends PMPageForm
{
	private $template_it;
	
	private $method_create_task = null;

	private $method_duplicate = null;
	
	private $method_move = null;
	
	private $method_watch = null;
	
	private $new_template_url = '';
	
	function __construct( $object ) 
	{
		parent::__construct($object);
		
		$this->checkTemplateDefined();
		
		$this->buildMethods();
	}

    protected function extendModel()
    {
		$this->getObject()->setAttributeOrderNum( 'State', 15 );
		
		$this->getObject()->setAttributeVisible( 'State', !$this->getEditMode() );

    	$this->getObject()->setAttributeVisible('Fact', is_object($this->getObjectIt()));
    	
    	parent::extendModel();

		$this->getObject()->addPersister( new WatchersPersister() );
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
 		
		$method = new DuplicateIssuesWebMethod($object_it);
		
		if ( $method->hasAccess() )
		{
			$this->method_duplicate = $method;
		}

		$method = new MoveToProjectWebMethod($object_it);
		
		if ( $method->hasAccess() )
		{
			$this->method_move = $method;
		}
		
		$method = new WatchWebMethod($object_it);
		
		if ( $method->hasAccess() )
		{
 			if ( !$this->IsFormDisplayed() ) $method->setRedirectUrl('donothing');
						
			$this->method_watch = $method;
		}
		
		$this->new_template_url = getFactory()->getObject('RequestTemplate')->getPageNameObject().'&ObjectId=%object-id%&items=%object-id%';
	}
	
	function getTransitionAttributes()
	{
		return array('Caption');
	}
	
	function getNewObjectAttributes()
	{
		return array('Caption', 'Description', 'Priority', 'Function', 'Tasks', 'Attachment', 'OrderNum', 'Owner');
	}

	function createFieldObject( $name )
	{
		global $_REQUEST, $model_factory;
		
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
				return new FieldSpentTimeRequest( $this->object_it );
				
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
		}
		
		if( $name == 'Attachment' )
		{
			return new FieldAttachments( is_object($this->object_it) ? $this->object_it : $this->object );
		}
		elseif( $name == 'Watchers' )
		{
			return new FieldWatchers( is_object($this->object_it) ? $this->object_it : $this->object );
		}
		else if ( $name == 'Function' )
		{
			$field = new FieldAutoCompleteObject( $model_factory->getObject('pm_Function') );
			
			//$field->setAppendable();
			
			return $field;
		}
		else if( $name == 'Links' )
		{
			return new FieldLinkedRequest( $this->object_it );
		}
		elseif($name == 'PlannedRelease') 
		{
			$release = $model_factory->getObject('Release');
			
			$release->addFilter( new ReleaseTimelinePredicate('not-passed') );
			
			return new FieldDictionary( $release );
		}
		elseif($name == 'Type') 
		{
			$type = $model_factory->getObject('pm_IssueType');
			
			$type->addFilter( new FilterBaseVpdPredicate() );
			
			return new FieldDictionary( $type );
		}
		elseif($name == 'Author') 
		{
			return new FieldAutoCompleteObject( $model_factory->getObject('cms_User') );
		}
		elseif($name == 'Owner') 
		{
			$worker = $model_factory->getObject('pm_Participant');
			
			if ( $this->getEditMode() )
			{
    			$worker->addFilter( new ParticipantWorkerPredicate() );
    			
    			if ( is_object($this->object_it) )
    			{
    				$worker->setVpdContext( $this->object_it );
    			}
    			else
    			{
    				$worker->addFilter( new FilterBaseVpdPredicate() );
    			}
			}
			
			return new FieldParticipantDictionary( $worker );
		}
		elseif ( $name == 'SubmittedVersion' )
		{
		    return new FieldAutoCompleteObject( $model_factory->getObject('Version') );
		}
		elseif($name == 'ClosedInVersion') 
		{
			return new FieldAutoCompleteObject( $model_factory->getObject('Version') );
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
   			    
   			    if ( $_REQUEST['Transition'] > 0 )
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
    
	function IsAttributeVisible( $attr_name )
	{
	    switch( $attr_name )
	    {
			case 'ExternalAuthor':
				return parent::IsAttributeVisible( $attr_name ) && is_object($this->getObjectIt()) && $this->getObjectIt()->get('ExternalAuthor') != '';
                    
			case 'Author':
				if ( is_object($this->getObjectIt()) && $this->getObjectIt()->get('ExternalAuthor') != '' ) return false;
				break;
	    }
	    
	    return parent::IsAttributeVisible( $attr_name );
	}

	function IsAttributeRequired( $attr_name )
	{
	    switch( $attr_name )
	    {
				case 'Author':
					if ( is_object($this->getObjectIt()) && $this->getObjectIt()->get('ExternalAuthor') != '' ) return false;
				break;
	    }
	    
	    return parent::IsAttributeRequired( $attr_name );
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
		global $model_factory;
		
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
		
		if ( $this->IsFormDisplayed() )
		{
			$method = new SetRequestIterationWebMethod( $object_it );
			
			if ( $method->hasAccess() )
			{
				if ( $actions[count($actions) - 1]['name'] != '' ) array_push($actions, array( '' ) );
				
				array_push($actions, array( 'name' => $method->getCaption(),
					'url' => $method->getLink() ) );
			}
		}
		
		if ( is_object($this->method_duplicate) )
		{
			if ( $actions[count($actions) - 1]['name'] != '' )
			{
				array_push($actions, array( '' ) );
			}

			$this->method_duplicate->setObjectIt($object_it);
			
			$actions[] = array( 
					'name' => $this->method_duplicate->getCaption(),
					'url' => $this->method_duplicate->getLink()
			);
		}

		if ( is_object($this->method_move) )
		{
			$this->method_move->setRequestIt($object_it);
			
			$actions[] = array( 
					'name' => $this->method_move->getCaption(),
					'url' => $this->method_move->getLink()
			);
		}

		if ( is_object($this->method_duplicate) )
		{
			if ( $actions[count($actions) - 1]['name'] != '' ) $actions[] = array( '' );
	
			$actions[] = array( 
					'name' => text(1519),
					'url' => preg_replace('/%object-id%/', $object_it->getId(), $this->new_template_url)
			);
		}
		
		if ( is_object($this->method_watch) )
		{
			$this->method_watch->setObjectIt($object_it);			
			
			$actions[] = array('');
			$actions[] = array( 
					'name' => $this->method_watch->getCaption(),
					'url' => $this->method_watch->getJSCall()
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
					'url' => $url.'&'.strtolower($reference).'='.$object_it->get($reference)
	   		);
		}

		if ( $object_it->object->getAttributeType('Owner') != '' )
		{
	   		$refs_actions['Owner'] = array(
	   				'name' => text(1828),
					'url' => $url.'&owner='.$object_it->getRef('Owner')->get('SystemUser')
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
   		
   		return $refs_actions;
	}	
 	
	function render( &$view, $parms )
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