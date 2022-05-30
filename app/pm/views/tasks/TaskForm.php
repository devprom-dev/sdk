<?php
use Devprom\ProjectBundle\Service\Task\TaskDefaultsService;

include_once "FieldIssueTraces.php";
include_once "FieldTaskTypeDictionary.php";
include_once SERVER_ROOT_PATH.'pm/views/time/FieldSpentTimeTask.php';
include_once SERVER_ROOT_PATH.'pm/views/ui/FieldAttachments.php';
include_once SERVER_ROOT_PATH."pm/views/watchers/FieldWatchers.php";
include_once SERVER_ROOT_PATH."pm/methods/TaskConvertToIssueWebMethod.php";
include_once SERVER_ROOT_PATH."pm/classes/tasks/WorkflowTransitionTaskModelBuilder.php";
include_once SERVER_ROOT_PATH."pm/views/project/FieldParticipantDictionary.php";
include_once SERVER_ROOT_PATH."pm/views/tasks/FieldTaskTrace.php";
include_once SERVER_ROOT_PATH."pm/views/tasks/FieldTaskInverseTrace.php";
include_once SERVER_ROOT_PATH."pm/methods/SpendTimeWebMethod.php";
include_once SERVER_ROOT_PATH.'pm/classes/wiki/converters/WikiConverter.php';
include "FieldTaskTagTrace.php";

class TaskForm extends PMPageForm
{
 	private $method_spend_time = null;
    private $convertMethod = null;

	function __construct( $object )
	{
		parent::__construct( $object );
		$this->buildMethods();
	}

	protected function extendModel()
    {
        $object = $this->getObject();

        $object->setAttributeEditable('Fact', true);
		$object->addPersister( new WatchersPersister() );

        if ( $this->getEditMode() ) {
            $object->setAttributeVisible('OrderNum', true);
        }

		foreach ( array('PlannedStartDate','PlannedFinishDate') as $attribute ) {
			$object->setAttributeVisible($attribute, true);
		}

		if ( $object->getAttributeType('ChangeRequest') != '' && is_object($this->getObjectIt()) && $this->getObjectIt()->get('ChangeRequest') != '' )
		{
			if ( $this->getObjectIt()->get('IssueDescription') != '' ) {
				if ( getSession()->IsRDD() && $this->getObjectIt()->get('IssueType') != '' ) {
                    $object->setAttributeCaption('IssueDescription', text(2949));
                }
			}
			if ( $this->getObjectIt()->get('IssueTraces') != '' ) {
				$object->setAttributeVisible('IssueTraces', true);
			}
		}
		else if ( $_REQUEST['ChangeRequest'] != '' ) {
            $object->setAttributeVisible('ChangeRequest', false);
        }
        $object->setAttributeVisible('ProjectPage', true);

        $object->setAttributeType('Release', 'REF_IterationRecentId');
        $object->setAttributeType('Assignee', 'REF_ProjectUserId');

		parent::extendModel();

		$transition_it = $this->getTransitionIt();
		if ( $transition_it->getId() > 0 )
		{
			$builder = new WorkflowTransitionTaskModelBuilder($transition_it);
			$builder->build( $object );
		}

		if ( !$object->IsAttributeVisible('ChangeRequest') ) {
		    foreach( $object->getAttributesByGroup('source-issue') as $attribute ) {
                $object->setAttributeVisible($attribute, false);
            }
        }
    }

    function getValidators()
    {
        unset($_REQUEST['Fact']); // filled by embedded form instead of attribute
        $this->getObject()->setAttributeEditable('Fact', false);

        return parent::getValidators();
    }

    public function buildMethods()
	{
	 	$method = new SpendTimeWebMethod( $this->getObjectIt() );
 		if ( $method->hasAccess() ) {
 			$this->method_spend_time = $method;
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

        $method = new TaskConvertToIssueWebMethod();
        if ( $this->IsFormDisplayed() && $method->hasAccess() ) {
            $this->convertMethod = array (
                'name' => $method->getCaption(),
                'url' => $method->url('%1')
            );
        }

        if ( getFactory()->getAccessPolicy()->can_modify_attribute($this->getObject(), 'Assignee') ) {
            $this->assignMethod = array (
                'name' => translate('Назначить'),
                'url' => "javascript:processBulk('".translate('Назначить')."','?formonly=true&operation=AttributeAssignee','%ids');"
            );
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
				return parent::IsAttributeEditable( $attr_name );
		}
	}

	function getTransitionAttributes()
	{
		$fields = array('UID');

		if ( $this->getFieldValue( 'Caption' ) ) {
		    $fields[] = 'Caption';
		}

		return $fields;
	}

	function createFieldObject( $name )
	{
		$this->object_it = $this->getObjectIt();
		$object_it_for_trace = $this->object_it;

		switch ( $name )
		{
			case 'TestExecution':
				return new FieldTaskTrace( $object_it_for_trace,
					getFactory()->getObject('TaskTraceTestExecution') );

			case 'HelpPage':
				return new FieldTaskTrace( $object_it_for_trace,
					getFactory()->getObject('TaskTraceHelpPage') );

			case 'TestScenario':
				return new FieldTaskTrace( $object_it_for_trace,
					getFactory()->getObject('TaskTraceTestScenario') );

			case 'Requirement':
				return new FieldTaskTrace( $object_it_for_trace,
					getFactory()->getObject('TaskTraceRequirement') );

			case 'TraceTask':
				return new FieldTaskTrace( $object_it_for_trace,
					getFactory()->getObject('TaskTraceTask') );

			case 'TraceInversedTask':
				return new FieldTaskInverseTrace( $object_it_for_trace,
					getFactory()->getObject('TaskInversedTraceTask') );

			case 'SourceCode':
				return new FieldTaskTrace( $object_it_for_trace,
					getFactory()->getObject('TaskTraceSourceCode') );

			case 'Fact':
				return new FieldSpentTimeTask( $this->object_it );

			case 'Watchers':
			    return new FieldWatchers( is_object($this->object_it) ? $this->object_it : $this->object );

			case 'Assignee':
			    if ( $this->getAction() == 'view' ) return parent::createFieldObject($name);
				return new FieldParticipantDictionary($this->getFieldValue('Release'));

			case 'Attachment':
				return new FieldAttachments( is_object($this->object_it) ? $this->object_it : $this->object );

			case 'IssueAttachment':
				$req_it = is_object($this->object_it) && $this->object_it->get('ChangeRequest') != ''
					? $this->object_it->getRef('ChangeRequest')
					: null;
				return new FieldAttachments( is_object($req_it) ? $req_it : $this->object->getAttributeObject('ChangeRequest') );

			case 'IssueTraces':
				return new FieldIssueTraces(is_object($this->object_it) ? $this->object_it->get($name) : '');

            case 'ProjectPage':
                if ( is_object($this->getObjectIt()) && $this->getObjectIt()->get($name) != '' ) {
                    return new FieldListOfReferences( $this->getObjectIt()->getRef($name) );
                }
                return null;

            case 'Tags':
                return new FieldTaskTagTrace( is_object($this->object_it) ? $this->object_it : null );

            case 'Release':
                if ($this->getAction() == 'view') {
                    return new FieldReferenceAttribute(
                        $this->getObjectIt(),
                        $name,
                        $this->getObject()->getAttributeObject($name),
                        array(),
                        'btn-xs'
                    );
                } else {
                    return new FieldAutoCompleteObject($this->getObject()->getAttributeObject($name));
                }

			default:
				return parent::createFieldObject( $name );
		}
	}

	function createField( $attr )
	{
		$field = parent::createField( $attr );

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
			case 'ChangeRequest':
				$field->setDefault($this->getDefaultValue($attr));
                $field->setCrossProject();
				return $field;
		}
		return $field;
	}

	function getFieldValue( $attr )
	{
        $value = parent::getFieldValue( $attr );
        if ( $value != '' ) return $value;

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
		    	if ( $this->getMode() == 'new' && parent::getFieldValue('ChangeRequest') != '' ) {
		    		$request_it = getFactory()->getObject('Request')->getExact(parent::getFieldValue('ChangeRequest'));
		    		return $request_it->getHtmlDecoded($attr);
		    	}
		    	break;

            case 'Release':
                $value = parent::getFieldValue( $attr );
                if ( $value == '' && $_REQUEST['DocumentBaseline'] != '' ) {
                    $baselineIt = getFactory()->getObject('Baseline')->getBySnapshotId($_REQUEST['DocumentBaseline']);
                    if ( $baselineIt->getId() != '' ) {
                        $stageIt = $baselineIt->getStageIt();
                        if ( $stageIt->getId() != '' && $stageIt->object instanceof Iteration ) {
                            $value = $stageIt->getId();
                        }
                        if ( $stageIt->getId() != '' && $stageIt->object instanceof Release ) {
                            $iterationIt = getFactory()->getObject('IterationActual')
                                ->getByRef('Version', $stageIt->getId());
                            if ( $iterationIt->getId() != '' ) {
                                $value = $iterationIt->getId();
                            }
                        }
                    }
                }
                if ( $value != '' ) return $value;

                $request_id = parent::getFieldValue('ChangeRequest');
                if ( $request_id > 0 ) {
                    return getFactory()->getObject('Request')->getExact($request_id)->get('Iteration');
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

            case 'TaskType':
                return '';

			default:
				return parent::getDefaultValue( $attr );
		}
	}

	function getDeleteActions($objectIt)
	{
		$actions = parent::getDeleteActions($objectIt);
		if ( !is_object($objectIt) ) return $actions;

        if ( is_array($this->convertMethod) ) {
            $action = $this->convertMethod;
            $action['url'] = preg_replace('/%1/', $objectIt->getId(), $action['url']);
            array_unshift($actions, array());
            array_unshift($actions, $action);
        }

        if ( $this->IsFormDisplayed() ) {
			$method = new WatchWebMethod($objectIt);
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

        if ( is_array($this->assignMethod) && !$this->IsFormDisplayed() ) {
            $method = $this->assignMethod;
            $method['url'] = preg_replace('/%ids/', $object_it->getId(), $method['url']);
            $actions[] = array();
            $actions[] = $method;
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

	function getNewRelatedActions()
	{
		return array(
			array()
		);
	}

    protected function getSourceParms($attributes)
    {
        $visibleAttributes = array_filter($attributes, function($item) {
            return $item['visible'] && $item['id'] == 'pm_TaskFact';
        });
        if ( count($visibleAttributes) == 1 ) return array();

        return parent::getSourceParms($attributes);
    }

	function getSourceIt()
	{
        $result = array();
		if ( $_REQUEST['ChangeRequest'] != '' ) {
            $result[] = array (
				getFactory()->getObject('Request')->getExact($_REQUEST['ChangeRequest']),
				'Description'
			);
		}
		return array_merge(parent::getSourceIt(),$result);
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

    function getShortAttributes() {
        return array_merge(
            parent::getShortAttributes(),
            array('TaskType', 'Priority', 'Planned', 'Release', 'Assignee', 'Owner', 'Tags')
        );
    }

    function getFieldDescription( $attr )
    {
        switch( $attr ) {
            case 'Release':
                if ( $this->getEditMode() ) {
                    $report_it = getFactory()->getObject('PMReport')->getExact('projectplan');
                    return str_replace('%1', $report_it->getUrl(),
                        str_replace('%2', $report_it->getDisplayName(),
                            text(2263)));
                }
            default:
                return parent::getFieldDescription($attr);
        }
    }

    protected function getNeighbourAttributes() {
        return array('Assignee', 'Release', 'Priority', 'ChangeRequest');
    }

    function getTemplateObject() {
        return getFactory()->getObject('TaskTemplate');
    }
}