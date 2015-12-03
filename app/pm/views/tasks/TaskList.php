<?php

include_once "TaskProgressFrame.php";
include_once "TaskBalanceFrame.php";
include_once SERVER_ROOT_PATH.'core/views/c_issue_type_view.php';
include_once SERVER_ROOT_PATH.'core/views/c_priority_view.php';
include_once SERVER_ROOT_PATH."pm/methods/c_priority_methods.php";

class TaskList extends PMPageList
{
 	var $has_grouping, $free_states;
 	
 	function TaskList( $object ) 
	{
		$this->priority_frame = new PriorityFrame();
		
		parent::__construct( $object );
	}

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

		// cache priority method
		$has_access = getFactory()->getAccessPolicy()->can_modify($this->getObject())
				&& getFactory()->getAccessPolicy()->can_modify_attribute($this->getObject(), 'Priority');
		
		if ( $has_access )
		{
			$this->priority_method = new ChangePriorityWebMethod(getFactory()->getObject('Priority')->getAll());
		}
		
		$this->getTable()->buildRelatedDataCache();
	}
	
 	function getColumns()
	{
		$attrs = $this->object->getAttributes();
		
		if ( array_key_exists( 'Planned', $attrs ) )
		{
			$this->object->addAttribute( 'Progress', '', translate('Прогресс'), false );
		}
		
		return parent::getColumns();
	}
	
	function getColumnFields()
	{
		$cols = parent::getColumnFields();

		$cols[] = 'OrderNum';

		return $cols;
	}
	
	function getGroupFields() 
	{
		$fields = parent::getGroupFields();

		$fields[] = 'DueDays';
		
		return $fields;
	}

	function getGroup() 
	{
		$group = parent::getGroup();
		if ( $group == 'AssigneeUser' ) return 'Assignee'; 
		return $group;
	}
	
	function getSorts()
	{
		$sorts = parent::getSorts();
		
		foreach( $sorts as $key => $sort )
		{
			if ( $sort instanceof SortAttributeClause && $sort->getAttributeName() == 'ChangeRequest' )
			{
				if ( getSession()->getProjectIt()->getMethodologyIt()->get('IsRequestOrderUsed') == 'Y' )
				{
					array_unshift($sorts, new TaskRequestOrderSortClause());
				}
				else
				{
					array_unshift($sorts, new TaskRequestPrioritySortClause());
				}
			}
		}
		
		return $sorts;
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
	    	
			case 'Priority':
				if ( is_object($this->priority_method) )
				{
					$this->priority_method->drawMethod( $object_it, 'Priority' );
				}
				else
				{
					parent::drawRefCell( $entity_it, $object_it, $attr );
				}
				break;
	        
			case 'ChangeRequest':

				$states = array();
				
				if ( $entity_it->getId() != '' )
				{
				    $states = $entity_it->getRef('Tasks')->getStatesArray();
				}
				
			    $title = $entity_it->getDisplayName();
			     
				if ( count($states) > 0 )
				{
					echo $this->getTable()->getView()->render('pm/TasksIcons.php', array (
							'states' => $states
					));
				}
				
				parent::drawRefCell( $entity_it, $object_it, $attr );
				
				echo ' '.$title;
				
				break;
				
			case 'Spent':
				
			    $field = new FieldSpentTimeTask( $object_it );
				
				$field->setEditMode( false );
				
				$field->setReadonly( !getFactory()->getAccessPolicy()->can_modify_attribute($object_it->object, 'Fact') );
				
				$field->render( $this->getTable()->getView() );

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
				
			case 'IssueTraces':
				$objects = preg_split('/,/', $object_it->get($attr));
				$uids = array();
				
				foreach( $objects as $object_info )
				{
					list($class, $id) = preg_split('/:/',$object_info);
					if ( $class == '' ) continue;
					$ref_it = getFactory()->getObject($class)->getExact($id);
					$uids[] = $this->getUidService()->getUidIcon($ref_it); 
				}
				echo join(', ',$uids);
				break;
				
			default:
				parent::drawCell( $object_it, $attr );
		}
	}
 	
	function drawGroup($group_field, $object_it)
	{
		global $model_factory, $row_num;
		
		switch ( $group_field )
		{
			case 'ChangeRequest':
				$row_num = 0;
				
				if( $object_it->get('ChangeRequest') != '' )
				{
					$this->request_it->moveToId( $object_it->get('ChangeRequest') );
					$resolved = $this->request_it->IsFinished();
				}
				else
				{
					$resolved = false;
				}
				
				echo '<div style="float:left">';
				
				if ( $object_it->get('ChangeRequest') != '' )
				{
					$object_uid = new ObjectUID;
		
					$priority_it = $this->request_it->getRef('Priority');
					$title = $priority_it->getDisplayName();

					$type_it = $this->request_it->getRef('Type');
					
						echo '<img title="'.translate('Приоритет').': '.$title.'" src="/images/'.
							$this->priority_frame->getIcon($this->request_it->get('Priority')).'" style="padding-right:4px;">';
						
						echo '<img src="/images/'.IssueTypeFrame::getIcon($type_it).'" style="margin-top:0;">&nbsp;';
		
						$object_uid->drawUidInCaption( $this->request_it, 0 );
				}
				else 
				{
					echo text(756);
				}
				
				echo ' &nbsp; </div>';
				
				echo '<div style="float:left;">';
					
					if ( $object_it->get('ChangeRequest') != '' )
					{
					    $states = $this->request_it->getRef('Tasks')->getStatesArray();
					
						echo $this->getTable()->getView()->render('pm/TasksIcons.php', array (
								'states' => $states
						));
					}
					
				echo '</div>';
				
				break;
				
			case 'Assignee':
				$workload = $this->getTable()->getAssigneeUserWorkloadData();
				if ( count($workload) > 0 )
				{
						echo $this->getTable()->getView()->render('pm/UserWorkload.php', array (
								'user' => $object_it->getRef('Assignee')->getDisplayName(),
								'data' => $workload[$object_it->get($group_field)]
						));
				}				
				break;
				
			default:
				parent::drawGroup($group_field, $object_it);
		}

		$this->getTable()->drawGroup($group_field, $object_it);
	}
 	
 	function getColumnWidth( $attr ) 
	{
		if ( $attr == 'Priority' )
			return 80;

		if ( $attr == 'State' )
			return 80;
		
		if ( $attr == 'Spent' )
			return 220;
		
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
}