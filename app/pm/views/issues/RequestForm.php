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
include_once SERVER_ROOT_PATH.'pm/classes/wiki/converters/WikiConverter.php';

include "FieldIssueTypeDictionary.php";
include "FieldTasksRequest.php";
include "FieldLinkedRequest.php";
include "FieldRequestTagTrace.php";
include "FieldIssueDeadlines.php";
include "FieldAuthor.php";
include "FieldEstimationDictionary.php";

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
        $methodology_it = getSession()->getProjectIt()->getMethodologyIt();
        if ( $methodology_it->HasMilestones() ) {
            $this->getObject()->addAttribute('Deadlines', 'REF_pm_MilestoneId', text(2264), true, false, '', 180);
            $this->getObject()->addAttributeGroup('Deadlines', 'deadlines');
        }

		$this->getObject()->setAttributeOrderNum('State', 15);
		
		$this->getObject()->setAttributeVisible('State', !$this->getEditMode());
    	$this->getObject()->setAttributeVisible('Fact', is_object($this->getObjectIt()));

        $this->getObject()->setAttributeType('SubmittedVersion', 'REF_VersionId');
        $this->getObject()->setAttributeType('ClosedInVersion', 'REF_VersionId');
    	
    	if ( getFactory()->getObject('RequestType')->getAll()->count() < 1 || $_REQUEST['Type'] != '' ) {
    		$this->getObject()->setAttributeVisible('Type', false);
    	}

		if ( is_object($this->getObjectIt()) ) {
			$state_it = $this->getStateIt();
			if ( $state_it->get('IsTerminal') == 'Y' ) {
				$this->getObject()->setAttributeVisible('FinishDate', true);
			}
			else if ( $methodology_it->IsAgile() ) {
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

        if ( $this->getEditMode() ) {
            if ( !getFactory()->getAccessPolicy()->can_create(getFactory()->getObject('Task')) ) {
                $this->getObject()->setAttributeVisible('Tasks', false);
            }
        }

        $this->getObject()->setAttributeVisible('ProjectPage', true);

        parent::extendModel();

        $this->getObject()->setAttributeEditable('ResponseSLA', false);
        $this->getObject()->setAttributeEditable('LeadTimeSLA', false);
    }
    
 	function buildModelValidator()
 	{
 		$validator = parent::buildModelValidator();
		$validator->addValidator( new ModelValidatorIssueFeatureLevel() );
 		return $validator;
 	}

    function getEmbeddedForm( $object )
    {
	    if ( $object instanceof Task ) {
            $object->setAttributeVisible( 'Release', true );
            $object->setAttributeRequired( 'Release', true );
            return new FormTaskEmbedded($object);
        }
        else {
	        return parent::getEmbeddedForm($object);
        }
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

 		if ( defined('PERMISSIONS_ENABLED') && PERMISSIONS_ENABLED || defined('ENTERPRISE_ENABLED') && ENTERPRISE_ENABLED ) {
            $projects = array_filter(
                preg_split('/,/',
                    join(',', array(
                        getSession()->getProjectIt()->get('LinkedProject'),
                        getSession()->getProjectIt()->get('PortfolioProject')
                    ))
                ),
                function ($value) { return $value != ''; }
            );
            $linked_it = getFactory()->getObject('ProjectLinked')->getRegistry()->Query();
        }
        else {
            $linked_it = getFactory()->getObject('Project')->getAll();
            $projects = $linked_it->idsToArray();
        }

        $top_limit = getSession()->getProjectIt()->IsPortfolio() ? 11 : 199;
		if ( count($projects) > 0 && count($projects) < $top_limit )
		{
			while( !$linked_it->end() ) {
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

        $method = new WikiExportBaseWebMethod();
        $methodPageIt = $this->getObject()->createCachedIterator(
            array (
                array ('pm_ChangeRequestId' => '%id%')
            )
        );
        $converter = new WikiConverter( $this->getObject() );
        $converter_it = $converter->getAll();
        while( !$converter_it->end() ) {
            $this->exportMethods[] = array(
                'name' => $converter_it->get('Caption'),
                'url' => $method->url($methodPageIt, $converter_it->get('EngineClassName'))
            );
            $converter_it->moveNext();
        }
    }
	
	function getTransitionAttributes()
	{
		return array('Caption');
	}
	
	function createFieldObject( $name )
	{
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
				$field->setShortMode();
				return $field;
				
			case 'Estimation':
				if ( $this->getEditMode() )
				{
					if ( getSession()->getProjectIt()->getMethodologyIt()->getEstimationStrategy()->hasDiscreteValues() ) {
						return new FieldEstimationDictionary($this->getObject());
					}
					else {
						return parent::createFieldObject( $name );
					}
				}
				else if (is_object($this->object_it) && $this->object_it->getId() > 0) {
                    if ( in_array('hours', $this->getObject()->getAttributeGroups($name)) ) {
                        return parent::createFieldObject( $name );
                    }
                    else {
                        return new FieldIssueEstimation($this->object_it, true);
                    }
				}
				break;
				
			case 'Tasks':
				$field = new FieldTasksRequest( $this->object_it );
                $field->setRelease($this->getFieldValue('PlannedRelease'));
                return $field;

			case 'Deadlines':
				return new FieldIssueDeadlines( $this->object_it );
					
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

            case 'ProjectPage':
                if ( is_object($this->getObjectIt()) && $this->getObjectIt()->get($name) != '' ) {
                    return new FieldListOfReferences( $this->getObjectIt()->getRef($name) );
                }
                return null;
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
		elseif($name == 'Iteration')
		{
			if ( $this->getTransitionIt()->getId() > 0 ) {
				$iteration = getFactory()->getObject('IterationActual');
			}
			else {
                $iteration = getFactory()->getObject('IterationRecent');
			}
			return new FieldAutoCompleteObject( $iteration );
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

            case 'Fact':
                if ( !getFactory()->getAccessPolicy()->can_read(getFactory()->getObject('Activity')) ) return null;
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
					if ( $_REQUEST['Iteration'] != '' ) {
						$release_id = getFactory()->getObject('Iteration')->getExact(preg_split('/,/',$_REQUEST['Iteration']))->get('Version');
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

            case 'Type':
                if ( $value == '' && $_REQUEST['Requirement'] != '' ) {
                    return getFactory()->getObject('RequestType')->getByRef('ReferenceName', 'enhancement')->getId();
                }
   		}

   		return $value;
   	}

    function getTextTemplateIt()
    {
        if ( $_REQUEST['Type'] != '' ) {
            return $this->getObject()->getEmptyIterator();
        }
        return parent::getTextTemplateIt();
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
        		if ( $_REQUEST['Requirement'] > 0 && $attr == 'Description' )
                {
                    list($source_it, $dummy) = array_shift($this->getSourceIt());
                    if ( $source_it->getId() != '' && $source_it->get('Content') != "" ) {
                        $registry = getFactory()->getObject('RequestTraceRequirement')->getRegistry();
                        $traceIt = $registry->Query(
                            array (
                                new RequestTraceObjectPredicate($source_it),
                                new FilterAttributePredicate('Type', REQUEST_TRACE_PRODUCT),
                                new SortAttributeClause('Revision.D')
                            )
                        );
                        if ( $traceIt->get('Revision') != '' ) {
                            return '{{'.$source_it->get('UID').':'.$traceIt->get('Revision').'-}}';
                        }
                        else {
                            return '{{'.$source_it->get('UID').'}}';
                        }
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
				'url' => $this->method_spend_time->getJSCall(),
                'uid' => 'spend-time'
			);
		}

		return $actions;
	}

	function getTaskMethod( $object_it ) {
        if ( is_object($this->method_create_task) ) {
            $this->method_create_task->setRequestIt($object_it);
        }
        return $this->method_create_task;
    }

	function getNewRelatedActions()
	{
		$actions = array();
		$object_it = $this->getObjectIt();

        $taskMethod = $this->getTaskMethod( $object_it );
		if ( is_object($taskMethod) ) {
            $actions[] = array (
                'name' => $taskMethod->getCaption(),
                'url' => $taskMethod->getJSCall(),
                'uid' => 'new-task'
            );
		}

		if ( $this->IsFormDisplayed() )
		{
			$method = new ObjectCreateNewWebMethod($this->getObject());
			$actions[] = array(
				'name' => text(2025),
				'url' => $method->getJSCall(
					array('IssueLinked' => $object_it->getId())
				)
			);
			if ( is_object($this->method_duplicate) ) {
				$actions[] = array(
					'name' => text(1519),
					'url' => preg_replace('/%object-id%/', $object_it->getId(), $this->new_template_url),
					'uid' => 'as-template'
				);
			}
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
    											
		$references = array (
		    'function' => 'Function',
            'author' => 'Author',
            'priority' => 'Priority',
            'subversion' => 'SubmittedVersion',
            'version' => 'ClosedInVersion',
            'environment' => 'Environment',
            'release' => 'PlannedRelease',
            'iteration' => 'Iteration',
            'owner' => 'Owner'
        );
		
	    $attr_it = getFactory()->getObject('pm_CustomAttribute')->getByEntity( $this->getObject() );
        while( !$attr_it->end() )
        {
            if ( in_array($attr_it->getRef('AttributeType')->get('ReferenceName'), array('dictionary','reference')) ) {
            	$references[strtolower($attr_it->get('ReferenceName'))] = $attr_it->get('ReferenceName');
            } 
            $attr_it->moveNext();
        }
		
		foreach( $references as $parm => $reference )
		{
            if ( $object_it->object->getAttributeType($reference) == '' ) continue;
            if ( $object_it->object->IsReference($reference) && $object_it->get($reference) != '' ) {
                $ref_it = $object_it->getRef($reference);
                if ( !$ref_it->object instanceof Priority && !$ref_it->object instanceof User && !$ref_it->object instanceof IssueAuthor ) {
                    $method = new ObjectModifyWebMethod($ref_it);
                    $refs_actions[$reference][] = array (
                        'name' => translate('Открыть'),
                        'url' => $method->getJSCall()
                    );
                    $refs_actions[$reference][] = array();
                }
            }
	   		$refs_actions[$reference][] = array(
                'name' => text(1828),
                'url' => $url.'&'.$parm.'='.urlencode($object_it->get($reference))
	   		);
		}

		if ( $object_it->object->getAttributeType('Type') != '' ) {
			$refs_actions['Type'][] = array(
				'name' => text(1828),
				'url' => $url . '&type=' . $object_it->getRef('Type')->get('ReferenceName')
			);
		}

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
	    $result = array();
		if ( $_REQUEST['Requirement'] != '' )
		{
			$req = getFactory()->getObject('Requirement');
			if ( $_REQUEST['Baseline'] != '' ) {
				$req->addPersister(
					new SnapshotItemValuePersister($_REQUEST['Baseline'])
				);
			}
            $result[] = array($req->getExact($_REQUEST['Requirement']), 'WikiIteratorExportHtml');
		}
		return array_merge(parent::getSourceIt(), $result);
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

    function getExportActions( $object_it )
    {
        $actions = array();

        foreach( $this->exportMethods as $action ) {
            $action['url'] = preg_replace('/%id%/', $object_it->getId(), $action['url']);
            $actions[] = $action;
        }

        return $actions;
    }

    function getShortAttributes()
    {
        return array_merge(
            parent::getShortAttributes(),
            array('Type', 'Priority', 'Estimation', 'Iteration', 'PlannedRelease', 'OrderNum', 'Owner', 'Tags', 'Severity', 'Project')
        );
    }

    function getFieldDescription( $attr )
    {
        switch( $attr ) {
            case 'PlannedRelease':
            case 'Iteration':
                $report_it = getFactory()->getObject('PMReport')->getExact('projectplan');
                return str_replace('%1', $report_it->getUrl(),
                            str_replace('%2', $report_it->getDisplayName(),
                                text(2263)));
            case 'Function':
                $report_it = getFactory()->getObject('Module')->getExact('features-list');
                return str_replace('%1', $report_it->getUrl(),
                            str_replace('%2', $report_it->getDisplayName(),
                                text(2263)));
            case 'DeliveryDate':
                if ( is_object($this->getObjectIt()) ) {
                    $method = new DeliveryDateMethod();
                    return $method->getExact($this->getObjectIt()->get('DeliveryDateMethod'))->getDisplayName();
                }
                else {
                    return "";
                }
            default:
                return parent::getFieldDescription($attr);
        }
    }

    protected function getNeighbourAttributes() {
        return array('PlannedRelease', 'Iteration', 'State', 'Function', 'Priority');
    }
}