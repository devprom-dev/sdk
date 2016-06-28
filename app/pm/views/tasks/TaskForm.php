<?php

use Devprom\ProjectBundle\Service\Task\TaskDefaultsService;

include "FieldIssueTraces.php";
include "FieldIssueState.php";
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
 	private $request_it = null;
 	private $method_spend_time = null;

	function __construct( $object )
	{
		parent::__construct( $object );
		$this->buildMethods();
	}

	protected function extendModel()
    {
    	$this->getObject()->setAttributeVisible('Fact', is_object($this->getObjectIt()));
		$this->getObject()->addPersister( new WatchersPersister() );

		foreach ( array('PlannedStartDate','PlannedFinishDate') as $attribute ) {
			$this->getObject()->setAttributeVisible($attribute, true);
		}

		if ( $this->getObject()->getAttributeType('ChangeRequest') != '' && is_object($this->getObjectIt()) && $this->getObjectIt()->get('ChangeRequest') != '' )
		{
			$this->getObject()->addAttribute('IssueState', 'TEXT', text(2128),
				true, false, '', $this->getObject()->getAttributeOrderNum('ChangeRequest')+1);
			$this->getObject()->addAttributeGroup('IssueState', 'source-issue');
			$this->getObject()->setAttributeVisible('IssueAttachment', true);

			if ( $this->getObjectIt()->get('IssueDescription') != '' ) {
				$this->getObject()->setAttributeVisible('IssueDescription', true);
			}
			if ( $this->getObjectIt()->get('IssueTraces') != '' ) {
				$this->getObject()->setAttributeVisible('IssueTraces', true);
			}
		}

		parent::extendModel();

		$transition_it = $this->getTransitionIt();
		if ( $transition_it->getId() > 0 )
		{
			$builder = new WorkflowTransitionTaskModelBuilder($transition_it);
			$builder->build( $this->getObject() );
		}
    }

	function buildModelValidator()
	{
		$validator = parent::buildModelValidator();
		$validator->addValidator( new ModelValidatorTaskDeadlines() );
		return $validator;
	}

	public function buildMethods()
	{
	 	$method = new SpendTimeWebMethod( $this->getObjectIt() );
 		if ( $method->hasAccess() ) {
			if ( !$this->IsFormDisplayed() ) $method->setRedirectUrl('donothing');
 			$this->method_spend_time = $method;
 		}
	}

 	function IsAttributeVisible( $attr_name )
	{
		$this->object_it = $this->getObjectIt();

		switch ( $attr_name )
		{
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

	function IsAttributeEditable( $attr_name )
	{
		switch ( $attr_name )
		{
			case 'IssueDescription':
				return false;
			default:
				if ( $this->getObject()->getAttributeType($attr_name) == 'wysiwyg' && !$this->getEditMode() ) {
					return false;
				}
				return parent::IsAttributeEditable( $attr_name );
		}
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

			case 'ResultArtefact':
				return new FieldDictionary( $model_factory->getObject('pm_Artefact') );

			case 'Attachment':
				return new FieldAttachments( is_object($this->object_it) ? $this->object_it : $this->object );

			case 'IssueAttachment':
				$req_it = is_object($this->object_it) && $this->object_it->get('ChangeRequest') != ''
					? $this->object_it->getRef('ChangeRequest')
					: null;
				return new FieldAttachments( is_object($req_it) ? $req_it : $this->object->getAttributeObject('ChangeRequest') );

			case 'IssueTraces':
				return new FieldIssueTraces(is_object($this->object_it) ? $this->object_it->get($name) : '');

			case 'IssueState':
				$req_it = is_object($this->object_it) && $this->object_it->get('ChangeRequest') != ''
					? $this->object_it->getRef('ChangeRequest')
					: $this->object->getEmtpyIterator();
				return new FieldIssueState($req_it);

			case 'Planned':
			case 'LeftWork':
				return new FieldHours();

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

	function getMoreActions()
	{
		$object_it = $this->getObjectIt();
		if ( !is_object($object_it) ) return array();

		$actions = parent::getMoreActions();

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
 		return 'TaskType';
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