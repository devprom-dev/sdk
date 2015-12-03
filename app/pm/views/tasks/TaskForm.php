<?php

use Devprom\ProjectBundle\Service\Task\TaskDefaultsService;

include "FieldTaskResultDictionary.php";
include_once "FieldTaskTypeDictionary.php";
include_once SERVER_ROOT_PATH.'pm/views/time/FieldSpentTimeTask.php';
include_once SERVER_ROOT_PATH.'pm/views/ui/FieldAttachments.php';
include_once SERVER_ROOT_PATH."pm/views/watchers/FieldWatchers.php";
include_once SERVER_ROOT_PATH."pm/methods/c_watcher_methods.php";
include_once SERVER_ROOT_PATH."pm/methods/c_task_methods.php";
include_once SERVER_ROOT_PATH."pm/classes/tasks/WorkflowTransitionTaskModelBuilder.php";
include_once SERVER_ROOT_PATH."pm/classes/tasks/validators/ModelValidatorTaskDeadlines.php";
include_once SERVER_ROOT_PATH."pm/views/project/FieldParticipantDictionary.php";
include_once SERVER_ROOT_PATH."pm/views/tasks/FieldTaskTrace.php";
include_once SERVER_ROOT_PATH."pm/views/tasks/FieldTaskInverseTrace.php";
include_once SERVER_ROOT_PATH."pm/methods/SpendTimeWebMethod.php";

class TaskForm extends PMPageForm
{
 	var $request_it;
 	
 	private $move_iteration_methods = array();
 	private $method_spend_time = null;
 	
    protected function extendModel()
    {
    	$this->getObject()->setAttributeVisible('Fact', is_object($this->getObjectIt()));
		$this->getObject()->addPersister( new WatchersPersister() );

		foreach ( array('PlannedStartDate','PlannedFinishDate') as $attribute ) {
			$this->getObject()->setAttributeVisible($attribute, true);
		}

		if ( is_object($this->getObjectIt()) )
		{
			if ( $this->getObjectIt()->get('IssueDescription') != '' ) {
				$this->getObject()->setAttributeVisible('IssueDescription', true);
			}
		}

		parent::extendModel();

		$transition_it = $this->getTransitionIt();
		if ( $transition_it->getId() > 0 )
		{
			$builder = new WorkflowTransitionTaskModelBuilder($transition_it);
			$builder->build( $this->getObject() );
		}
		
		if ( is_object($this->getObjectIt()) )
		{
			$result_field_required = 
					$transition_it->getRef('TargetState')->get('IsTerminal') == 'Y'
					&& in_array($this->getObjectIt()->getRef('TaskType')->get('ReferenceName'), array('testing'))
					&& getFactory()->getObject('pm_TestExecutionResult')->getAll()->count() > 0;
			
			if ( $result_field_required )
			{
				$this->getObject()->setAttributeRequired('Result', true);	
				$this->getObject()->setAttributeVisible('Result', true);
			}
		}
		
		$this->buildMethods();
    }

	function buildModelValidator()
	{
		$validator = parent::buildModelValidator();
		$validator->addValidator( new ModelValidatorTaskDeadlines() );
		return $validator;
	}

	public function buildMethods()
	{
		$project_roles = getSession()->getRoles();
		$project_it = getSession()->getProjectIt();
		
		if( $project_roles['lead'] && !$project_it->IsPortfolio() && $project_it->getMethodologyIt()->HasPlanning() ) 
		{
			$release_it = getFactory()->getObject('Iteration')->getRegistry()->Query(
						array (
								new IterationTimelinePredicate('not-passed'),
								new FilterBaseVpdPredicate()
						)
				);
			
			while( !$release_it->end() )
			{
				$method = new MoveTaskWebMethod($release_it->copy());
				$method->setRedirectUrl('donothing');
				
				$this->move_iteration_methods[$release_it->getId()] = $method; 
						   			
				$release_it->moveNext();
			}
		}
		
	 	$method = new SpendTimeWebMethod( $this->getObjectIt() );
 		if ( $method->hasAccess() )
 		{
 			$method->setRedirectUrl('donothing');
 			$this->method_spend_time = $method;
 		}
	}
    
 	function IsAttributeVisible( $attr_name ) 
	{
		$this->object_it = $this->getObjectIt();

		switch ( $attr_name )
		{
			case 'LeftWork':
				if ( !is_object($this->object_it) )
				{
					return false;
				}
				break;
			
			case 'ChangeRequest':
				$hide = is_object($this->object_it) && 
					$this->object_it->get('ChangeRequest') < 1 && 
						$this->getAction() == 'view';
				
				if ( $hide )
				{
					return false;
				}
				break;
		}

		return parent::IsAttributeVisible( $attr_name );
	}
	
	function getTransitionAttributes()
	{
		$fields = array();

		if ( $this->getFieldValue( 'Caption' ) )
		{
		    $fields[] = 'Caption';
		}
		
		return $fields;
	}

	function createFieldObject( $name )
	{
		global $_REQUEST, $model_factory;
		
		$this->object_it = $this->getObjectIt();

		$object_it_for_trace = $this->object_it;  

		switch ( $name )
		{
			case 'TestExecution':
				return new FieldTaskTrace( $object_it_for_trace, 
					$model_factory->getObject('TaskTraceTestExecution') );

			case 'HelpPage':
				return new FieldTaskTrace( $object_it_for_trace, 
					$model_factory->getObject('TaskTraceHelpPage') );

			case 'TestScenario':
				return new FieldTaskTrace( $object_it_for_trace, 
					$model_factory->getObject('TaskTraceTestScenario') );

			case 'Requirement':
				return new FieldTaskTrace( $object_it_for_trace, 
					$model_factory->getObject('TaskTraceRequirement') );

			case 'TraceTask':
				return new FieldTaskTrace( $object_it_for_trace, 
					getFactory()->getObject('TaskTraceTask') );

			case 'TraceInversedTask':
				return new FieldTaskInverseTrace( $object_it_for_trace,
					getFactory()->getObject('TaskInversedTraceTask') );

			case 'SourceCode':
				return new FieldTaskTrace( $object_it_for_trace, 
					$model_factory->getObject('TaskTraceSourceCode') );
				
			case 'Fact':
				return new FieldSpentTimeTask( $this->object_it );
				
			case 'Watchers':
			    return new FieldWatchers( is_object($this->object_it) ? $this->object_it : $this->object );
			    
			case 'TaskType':
				$tasktype = $model_factory->getObject('TaskType');

				$tasktype->addFilter( new FilterBaseVpdPredicate() );
				
				return new FieldTaskTypeDictionary( $tasktype );

			case 'Assignee':
				$object = getFactory()->getObject('User');
	    		$object->addFilter( new UserWorkerPredicate() );

				return new FieldParticipantDictionary( $object );
				
			case 'Release':
				
				if ( !is_object($this->getObjectIt()) )
				{
					// filter not-passed only when creating a new task
					$iteration = $model_factory->getObject('Iteration');
					
					$iteration->addFilter( new IterationTimelinePredicate(IterationTimelinePredicate::NOTPASSED) );
					
					return new FieldDictionary( $iteration );
				}
				else
				{
					return parent::createFieldObject( $name );
				}

			case 'Result':
   				return new FieldTaskResultDictionary();
				
			case 'ResultArtefact':
				return new FieldDictionary( $model_factory->getObject('pm_Artefact') );
				
			case 'Attachment':
				return new FieldAttachments( is_object($this->object_it) ? $this->object_it : $this->object );
				
			default:
				return parent::createFieldObject( $name );
		}
	}
	
	function createField( $attr )
	{
		$field = parent::createField( $attr );
		
		$object_it = $this->getObjectIt();
		
    	if ( $_REQUEST['Transition'] > 0 )
    	{
    		switch ( $attr )
    		{
    			case 'Caption':
    				$field->setReadonly( true );
    				break;
    		}
    	} 
		
    	switch ( $attr )
		{
			case 'Release':
				if ( is_object($object_it) && $object_it->getId() > 0 ) return $field;
				
				$value = $this->getObject()->getDefaultAttributeValue( $attr );
				if ( $value != '' ) return $field;
				
				$field->setValue( $field->getObject()->getFirst()->getId() );
				return $field;
			
			case 'ChangeRequest':
				$field->setDefault($this->getDefaultValue($attr));
				return $field;

			case 'IssueDescription':
				$field->setReadOnly(true);
				return $field;
		}
		return $field;
	}
	
	function getFieldValue( $attr )
	{
		switch( $attr )
		{
		    case 'TaskType':
		    	if ( $this->getMode() == 'new' )
		    	{
					$request_id = parent::getFieldValue('ChangeRequest');
					if ( $request_id > 0 )
					{
						$type_it = getFactory()->getObject('TaskType')->getRegistry()->Query(
							array (
								new FilterBaseVpdPredicate(),
								new TaskTypeStateRelatedPredicate(
									getFactory()->getObject('Request')->getExact($request_id)->get('State')
								)
							)
						);
						if ( $type_it->getId() != '' ) return $type_it->getId();
					}

			    	return getFactory()->getObject('TaskType')->getRegistry()->Query(
					    		array (
					    				new FilterBaseVpdPredicate(),
					    				new FilterAttributePredicate('IsDefault', 'Y')
					    		)
					    )->getId();
		    	}
		    	break;
		    	
		    case 'Caption':
		    case 'Priority':
		    	
		    	if ( $this->getMode() == 'new' && parent::getFieldValue('ChangeRequest') != '' )
		    	{
		    		$request_it = getFactory()->getObject('Request')->getExact(parent::getFieldValue('ChangeRequest'));
		    		return $request_it->getHtmlDecoded($attr);
		    	}
		    	
		    	break;
		}
		return parent::getFieldValue( $attr );
	}
	
	function getDefaultValue( $attr )
	{
		switch( $attr )
		{
			case 'Assignee':
				$type_id = $this->getFieldValue('TaskType');
				return $type_id > 0 
					? TaskDefaultsService::getAssignee($type_id) : parent::getDefaultValue( $attr );

			default:
				return parent::getDefaultValue( $attr );
		}
	}
	
	function getDeleteActions()
	{
		$actions = parent::getDeleteActions();
		
		$object_it = $this->getObjectIt();
		if ( !is_object($object_it) ) return $actions;

		if ( $this->IsFormDisplayed() ) {
			$method = new WatchWebMethod($object_it);
			if ($method->hasAccess()) {
				array_unshift($actions, array());
				array_unshift($actions, array(
					'name' => $method->getCaption(),
					'url' => $method->getJSCall()
				));
			}
		}

		return $actions;
	}
	
	function getTransitionActions()
	{
		$object_it = $this->getObjectIt();
		if ( !is_object($object_it) ) return array();

		$actions = parent::getTransitionActions();

		if( !$object_it->IsFinished() )
		{
			$move_actions = array();
			
			foreach( $this->move_iteration_methods as $iteration_id => $method )
			{
				if ( $iteration_id == $object_it->get('Release') ) continue;
				
				$move_actions[] = array(
						'name' => $method->getCaption(), 
						'url' => $method->getJSCall( 
										array( 
												'Task' => $object_it->getId(),
									   			'Release' => $iteration_id
										)
								 )
				);
			}
			
			if ( count($move_actions) > 0 )
			{
				$actions = array_merge( $actions, array(array()), $move_actions);
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
		return array(
			array()
		);
	}
	
	function getDiscriminatorField()
 	{
 		return $this->getEditMode() ? 'TaskType' : '';
 	}
	
 	function getDiscriminator()
 	{
 		$object_it = $this->getObjectIt();
 		if ( is_object($object_it) )
 		{
 			$ref_it = $object_it->getRef('TaskType');
 			return $ref_it->get('ReferenceName');
 		}
 		elseif ( $_REQUEST['TaskType'] > 0 )
 		{
 			$object = $this->getObject();
 			
 			$ref = $object->getAttributeObject('TaskType');
 			$ref_it = $ref->getExact($_REQUEST['TaskType']);
 			
 			return $ref_it->get('ReferenceName');
 		}
 	}

	function getSourceIt()
	{
		if ( $_REQUEST['ChangeRequest'] != '' ) {
			return array (
				getFactory()->getObject('Request')->getExact($_REQUEST['ChangeRequest']),
				'Description'
			);
		}
		return parent::getSourceIt();
	}
}