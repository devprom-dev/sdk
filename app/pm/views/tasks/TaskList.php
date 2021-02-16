<?php

include_once "TaskProgressFrame.php";
include_once "TaskBalanceFrame.php";
include_once "FieldTaskPlanned.php";
include_once SERVER_ROOT_PATH.'core/views/c_issue_type_view.php';
include "TaskChart.php";
include "TaskPlanFactChart.php";
include "TaskChartBurndown.php";

class TaskList extends PMPageList
{
 	var $has_grouping, $free_states;
	private $planned_field = null;
    private $assigneeField = null;

	function buildRelatedDataCache()
	{
		$it = $this->getIteratorRef();
		
		$ids = array_values( array_unique($it->fieldToArray('ChangeRequest')) );

		for( $i = 0; $i < count($ids); $i++ )
		{
			if ( $ids[$i] < 1 ) unset($ids[$i]);
		}

		$ids = array_values(array_unique($ids));

		$request = getFactory()->getObject('pm_ChangeRequest');

		$this->request_it = count($ids) > 0 ? $request->getExact($ids) : $request->getEmptyIterator();

		$this->has_grouping = $this->getGroup() != '';

		$this->planned_field = new FieldTaskPlanned();
		$this->getTable()->buildRelatedDataCache();

        if ( getFactory()->getAccessPolicy()->can_modify_attribute($this->getObject(), 'Assignee') ) {
            $this->assigneeField = new FieldReferenceAttribute(
                $this->getObject()->getEmptyIterator(),
                'Assignee',
                getFactory()->getObject('ProjectUser')
            );
        }
	}

	function extendModel()
    {
        $attrs = $this->getObject()->getAttributes();
        if ( array_key_exists( 'Planned', $attrs ) ) {
            $this->getObject()->addAttribute( 'Progress', '', translate('Прогресс'), true );
            $this->getObject()->addAttributeGroup('Progress', 'workload');
        }

        parent::extendModel();
    }

    function getGroupObject() {
        $object = parent::getGroupObject();
        $object->addAttributeGroup('Estimation', 'display-name');
        return $object;
    }

	function getGroup()
	{
		$group = parent::getGroup();
		if ( $group == 'AssigneeUser' ) return 'Assignee';
        if ( $group == 'TaskType' ) return 'TaskTypeBase';
		return $group;
	}

    function getGroupFields()
    {
        $fields = parent::getGroupFields();

        if ( $this->getObject()->hasAttribute('Requirement') ) {
            $fields[] = 'Requirement';
        }
        return $fields;
    }

    function IsNeedToSelect()
	{
		return true;
	}
	
	function IsNeedToSelectRow( $object_it )
	{
		return true;
	}
	
	function drawRefCell( $entity_it, $object_it, $attr )
	{
	    switch( $attr )
	    {
			case 'TaskType':
				echo $object_it->get('TaskTypeDisplayName');
				break;

            case 'Assignee':
                if ( is_object($this->assigneeField) ) {
                    $this->assigneeField->setObjectIt($object_it);
                    $this->assigneeField->draw($this->getRenderView());
                }
                else {
                    parent::drawRefCell( $entity_it, $object_it, $attr );
                }
                break;

			case 'ChangeRequest':
				$states = array();
				
				if ( $entity_it->getId() != '' && $entity_it->object->getAttributeType('Tasks') != '' )
				{
				    $states = $entity_it->getRef('Tasks')->getStatesArray();
				}
				
			    $title = $entity_it->getDisplayName();
			     
				if ( count($states) > 0 )
				{
					echo $this->getRenderView()->render('pm/TasksIcons.php', array (
						'states' => $states,
						'random' => $entity_it->getId()
					));
				}
				
				parent::drawRefCell( $entity_it, $object_it, $attr );
				
				echo ' '.$title;
				
				break;
				
			case 'Spent':
			    $field = new FieldSpentTimeTask( $object_it );
				$field->setEditMode( false );
                $field->setShortMode();
				$field->setReadonly( !getFactory()->getAccessPolicy()->can_modify_attribute($object_it->object, 'Fact') );
				$field->render( $this->getRenderView() );
				break;
				
	        default:
	            
	            parent::drawRefCell( $entity_it, $object_it, $attr );
	    }
	}
	
	function drawCell( $object_it, $attr )
	{
		switch($attr)	
		{
			case 'Progress':
				if ( $object_it->IsFinished() )
				{
					$frame = new TaskBalanceFrame( $object_it->get('Planned'), $object_it->get('Fact') );
				}
				else
				{
					$frame = new TaskProgressFrame( $object_it->getProgress() );
				}
				$frame->draw();
				break;

			case 'Planned':
				echo '<div style="margin-left:36px;">';
					$this->planned_field->setObjectIt($object_it);
					$this->planned_field->draw($this->getRenderView());
				echo '</div>';
				break;

			case 'IssueTraces':
				$this->getTable()->drawCell( $object_it, $attr );
				break;

			default:
				parent::drawCell( $object_it, $attr );
		}
	}
 	
	function drawGroup($group_field, $object_it)
	{
		switch ( $group_field )
		{
			default:
				parent::drawGroup($group_field, $object_it);
		}

		$this->getTable()->drawGroup($group_field, $object_it);
	}
 	
 	function getColumnWidth( $attr ) 
	{
		if ( $attr == 'Priority' )
			return 80;
		if ( $attr == 'Planned' )
			return 80;

		if ( $attr == 'State' )
			return 80;
		
		if ( $attr == 'Spent' )
			return 190;
		
		if ( $attr == 'OrderNum' )
			return '50';

		if ( $attr == 'Progress' )
			return '80';
		
		return parent::getColumnWidth( $attr );
	}

	function getRenderParms()
	{
		$this->buildRelatedDataCache();
		
		return parent::getRenderParms();
	}


    function render($view, $parms)
    {
        switch( $this->getTable()->getReportBase() ) {
            case 'tasksplanbyfact':
                $chart = new TaskPlanFactChart( $this->getObject() );
                break;
            case 'iterationburndown':
                $chart = new TaskChartBurndown( $this->getObject() );
                break;
            default:
        }
        if ( is_object($chart) ) {
            $chart->setTable($this->getTable());
            $chart->retrieve();
            $chart->render($view, $parms);
        }

        parent::render($view, $parms);
    }
}