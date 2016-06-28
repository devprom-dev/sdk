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
include_once SERVER_ROOT_PATH."pm/views/issues/FieldIssueEstimation.php";
include_once SERVER_ROOT_PATH."pm/classes/issues/validators/ModelValidatorIssueFeatureLevel.php";

include "FieldIssueTypeDictionary.php";
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
	private $links_it = null;
	private $linkTypes = array();
	
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
    	
    	if ( getFactory()->getObject('RequestType')->getAll()->count() < 1 || $_REQUEST['Type'] != '' ) {
    		$this->getObject()->setAttributeVisible('Type', false);
    	}

		if ( is_object($this->getObjectIt()) ) {
			$state_it = $this->getStateIt();
			if ( $state_it->get('IsTerminal') == 'Y' ) {
				$this->getObject()->setAttributeVisible('FinishDate', true);
			}
			else {
				$this->getObject()->setAttributeVisible('DeliveryDate', true);
			}

			if ( $this->getObjectIt()->get('Links') != '' ) {
				$this->links_it = $this->getObject()->getRegistry()->Query(
					array (
						new FilterInPredicate(preg_split('/,/', $this->getObjectIt()->get('Links'))),
						new AttachmentsPersister()
					)
				);
				$attachments = array_filter($this->links_it->fieldToArray('Attachment'), function($value) {
					return $value != '';
				});
				if ( count($attachments) > 0 ) {
					$this->getObject()->addAttribute('LinksAttachment', 'VARCHAR', text(2124),
						true, false, '', $this->getObject()->getAttributeOrderNum('Links') + 1);
					$this->getObject()->addAttributeGroup('LinksAttachment', 'trace');
				}
			}
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

		$referenceName = '';
		if ( $_REQUEST['Type'] != '' ) {
			$referenceName = getFactory()->getObject('RequestType')->getExact($_REQUEST['Type'])->get('ReferenceName');
		}
		if ( $referenceName != 'bug' ) {
			$object->addAttributeGroup('SubmittedVersion', 'additional');
			$object->addAttributeGroup('Environment', 'additional');
		}

 		$method = new RequestCreateTaskWebMethod($object_it);
		if ( $method->hasAccess() ) {
			if ( !$this->IsFormDisplayed() ) $method->setRedirectUrl('donothing');
			$this->method_create_task = $method;
		}

		$method = new ObjectCreateNewWebMethod($object);
		if ( !$this->IsFormDisplayed() ) $method->setRedirectUrl('donothing');
		$this->method_duplicate = $method;

		$method = new MoveToProjectWebMethod($object_it);
		if ( $method->hasAccess() )
		{
 			if ( !$this->IsFormDisplayed() ) $method->setRedirectUrl('donothing');
			$this->method_move = $method;
		}

		if ( $this->IsFormDisplayed() ) {
			$method = new WatchWebMethod($object_it);
			if ( $method->hasAccess() )
			{
				$this->method_watch = $method;
			}
		}

	 	$method = new SpendTimeWebMethod($object_it);
 		if ( $method->hasAccess() )
 		{
 			if ( !$this->IsFormDisplayed() ) $method->setRedirectUrl('donothing');
 			$this->method_spend_time = $method;
 		}
		
		$this->new_template_url = getFactory()->getObject('RequestTemplate')->getPageNameObject().'&ObjectId=%object-id%&items=%object-id%';

		$projects = array_filter(
				preg_split('/,/', getSession()->getProjectIt()->get('LinkedProject')),
				function ($value) { return $value != ''; }
		);

		$top_limit = getSession()->getProjectIt()->IsPortfolio() ? 11 : 199;
		if ( count($projects) > 0 && count($projects) < $top_limit )
		{
			$linked_it = getFactory()->getObject('ProjectLinked')->getRegistry()->Query();
			while( !$linked_it->end() )
			{
				$this->target_projects[$linked_it->getId()] = array (
					'title' => $linked_it->getDisplayName(),
					'vpd' => $linked_it->get('VPD')
				);
				$linked_it->moveNext();
			}
			if ( !getSession()->getProjectIt()->IsPortfolio() ) {
				$this->target_projects[getSession()->getProjectIt()->getId()] = array (
					'title' => getSession()->getProjectIt()->getDisplayName(),
					'vpd' => getSession()->getProjectIt()->get('VPD')
				);
			}
		}
		
		$this->feature_types = getFactory()->getObject('pm_FeatureType')->getAll()->fieldToArray('ReferenceName');

		$type_it = getFactory()->getObject('RequestLinkType')->getAll();
		while( !$type_it->end() ) {
			$this->linkTypes[$type_it->get('ReferenceName')] = $type_it->getId();
			$type_it->moveNext();
		}
	}
	
	function getTransitionAttributes()
	{
		return array('Caption');
	}
	
	function createFieldObject( $name )
	{
		global $_REQUEST, $model_factory;
		
		$plugins = getFactory()->getPluginsManager();
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
				return new FieldIssueTrace( $this->object_it,
					getFactory()->getObject('RequestTraceTestExecution') );

			case 'TestFound':
				return new FieldIssueTrace( $this->object_it,
					getFactory()->getObject('RequestTraceTestCaseExecution') );

			case 'HelpPage':
				return new FieldIssueTrace( $this->object_it,
					getFactory()->getObject('RequestTraceHelpPage') );

			case 'TestScenario':
				return new FieldIssueTrace( $this->object_it,
					getFactory()->getObject('RequestTraceTestScenario') );

			case 'Requirement':
				return new FieldIssueTrace( $this->object_it,
					getFactory()->getObject('RequestTraceRequirement') );

			case 'SourceCode':
				return new FieldIssueTrace( $this->object_it,
					getFactory()->getObject('RequestTraceSourceCode') );
				
			case 'Question':
				return new FieldIssueTrace( $this->object_it,
					getFactory()->getObject('RequestTraceQuestion') );

			case 'Fact':
				$field = new FieldSpentTimeRequest( $this->object_it );
				if ( !$this->getEditMode() ) $field->setShortMode();
				return $field;
				
			case 'Estimation':
				if ( $this->getEditMode() )
				{
					$field = getSession()->getProjectIt()->getMethodologyIt()->getEstimationStrategy()->getEstimationFormField( $this );
					if ( !is_object($field) ) {
						return parent::createFieldObject( $name );
					}
					else {
						return $field;
					}
				}
				else if (is_object($this->object_it) && $this->object_it->getId() > 0) {
					return new FieldIssueEstimation($this->object_it, true);
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
				if ( count($this->feature_types) > 0 ) {
					return new FieldHierarchySelectorAppendable($this->getObject()->getAttributeObject($name));
				}
				else {
					$field = new FieldAutocompleteObject($this->getObject()->getAttributeObject($name));
					$field->setAppendable();
					return $field;
				}

			case 'LinksAttachment':
				return new FieldAttachments( is_object($this->links_it) ? $this->links_it : $this->object );
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
			if ( $this->getTransitionIt()->getId() > 0 ) {
				$release = getFactory()->getObject('ReleaseActual');
			}
			else {
				$release = getFactory()->getObject('ReleaseRecent');
			}
			return new FieldAutoCompleteObject( $release );
		}
		elseif($name == 'Iterations')
		{
			if ( $this->getTransitionIt()->getId() > 0 ) {
				$release = getFactory()->getObject('IterationActual');
			}
			else {
				$release = getFactory()->getObject('IterationRecent');
			}
			return new FieldAutoCompleteObject( $release );
		}
		elseif($name == 'Type')
		{
			return new FieldIssueTypeDictionary($this->getObject());
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
		    $field = new FieldAutoCompleteObject( getFactory()->getObject('Version') );
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
   			    if ( is_a($field, 'FieldText') ) {
   			        $field->setRows( 6 );
   			    }
   		        if ( $field instanceof FieldWYSIWYG ) {
					if ( !$this->getEditMode() ) $field->setCssClassName( 'wysiwyg-text' );
					$field->setRows(10);
		    	}
   			    break;

			case 'LinksAttachment':
				$field->setReadonly(true);
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
   		    	if ( $value == '' && $this->IsAttributeRequired($attr) )
				{
					if ( $_REQUEST['Iterations'] != '' ) {
						$release_id = getFactory()->getObject('Iteration')->getExact(preg_split('/,/',$_REQUEST['Iterations']))->get('Version');
						if ( $release_id != '' ) return $release_id;
					}
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
				return parent::getFieldValue( $attr );
    			
    		default:
    			if ( $_REQUEST['Question'] > 0 )
        		{
        			$question_it = getFactory()->getObject('pm_Question')->getExact($_REQUEST['Question']);
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
			case 'DeliveryDate': return false;

			default: return parent::IsAttributeEditable( $attribute );
	    }
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
	
 	function getMoreActions()
	{
		$actions = parent::getMoreActions();

		$object_it = $this->getObjectIt();
		if ( is_object($this->method_duplicate) )
		{
			$parms = array(
				'Request' => $object_it->getId(),
				'LinkType' => $this->linkTypes['implemented']
			);
			if ( $actions[count($actions) - 1]['name'] != '' ) $actions[] = array();

			$vpd = $object_it->get('VPD');
			$other_projects = array_filter($this->target_projects, function($project) use ($vpd) {
				return $project['vpd'] != $vpd;
			});
			if ( count($other_projects) > 0 )
			{
				$items = array();
				foreach( $other_projects as $id => $data )
				{
					$this->method_duplicate->setVpd($data['vpd']);
					$items[] = array (
							'name' => $data['title'],
							'url' => $this->method_duplicate->getJSCall(
											array_merge($parms, array('Project'=>$id))
									 )
					);
				}

				$items[] = array();
				$this->method_duplicate->setVpd($object_it->get('VPD'));
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
			if ( count($other_projects) > 0 )
			{
				$items = array();
				foreach( $other_projects as $id => $data ) {
					$items[] = array (
							'name' => $data['title'],
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

	function getNewRelatedActions()
	{
		$actions = array();
		$object_it = $this->getObjectIt();

		if ( is_object($this->method_create_task) ) {
			$this->method_create_task->setRequestIt($object_it);
			$actions[] = array (
				'name' => $this->method_create_task->getCaption(),
				'url' => $this->method_create_task->getJSCall(),
				'uid' => 'new-task'
			);
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

		if ( $this->IsFormDisplayed() && is_object($this->method_duplicate) )
		{
			$actions[] = array(
				'name' => text(1519),
				'url' => preg_replace('/%object-id%/', $object_it->getId(), $this->new_template_url),
				'uid' => 'as-template'
			);
		}

		return $actions;
	}

   	function getDiscriminatorField()
 	{
 		return 'Type';
 	}
	
	function getRenderParms()
	{
		$object_it = $this->getObjectIt();

		$parms = array (
			'comments_count' =>
					is_object($object_it)
							? getFactory()->getObject('Comment')->getCountForIt($object_it)
							: '',
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
		$refs_actions['PlannedRelease'] = array(
				'name' => text(1828),
				'url' => $url.'&release='.$object_it->get('PlannedRelease')
		);
		$refs_actions['Iterations'] = array(
				'name' => text(1828),
				'url' => $url.'&iteration='.$object_it->get('Iterations')
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

	function getSourceIt()
	{
		if ( $_REQUEST['Requirement'] != '' )
		{
			$req = getFactory()->getObject('Requirement');
			if ( $_REQUEST['Baseline'] != '' ) {
				$req->addPersister(
					new SnapshotItemValuePersister($_REQUEST['Baseline'])
				);
			}
			return array($req->getExact($_REQUEST['Requirement']), 'Content');
		}
		return parent::getSourceIt();
	}

	function getCaption()
	{
		if ( is_object($this->getObjectIt()) && $this->getObjectIt()->get('TypeName') != ''  ) {
			return $this->getObjectIt()->get('TypeName');
		}
		else {
			return parent::getCaption();
		}
	}
}