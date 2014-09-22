<?php

include_once "TaskProgressFrame.php";
include_once "TaskBalanceFrame.php";
include_once SERVER_ROOT_PATH.'core/views/c_issue_type_view.php';
include_once SERVER_ROOT_PATH.'core/views/c_priority_view.php';

class TaskBoardList extends PMPageBoard
{
 	var $is_finished;
 	
 	private $priorities_array = array();

 	function TaskBoardList( $object ) 
	{
		global $model_factory;
		
		$this->priority_frame = new PriorityFrame();
		
		parent::__construct( $object );
	}

	function buildRelatedDataCache()
	{
		$priority_it = getFactory()->getObject('Priority')->getAll();
		
		while( !$priority_it->end() )
		{
			$this->priorities_array[] = $priority_it->copy();
			 
			$priority_it->moveNext();
		}
		
		$this->getTable()->buildRelatedDataCache();
	}
	
	function retrieve()
	{
		global $model_factory;
		
		parent::retrieve();
		
		$it = $this->getIteratorRef();
		
		$ids = array_values( array_unique($it->fieldToArray('ChangeRequest')) );

		for( $i = 0; $i < count($ids); $i++ )
		{
			if ( $ids[$i] < 1 ) unset($ids[$i]);
		}

		$ids = array_values(array_unique($ids));

		$request = $model_factory->getObject('pm_ChangeRequest');
		
		$this->request_it = count($ids) > 0 ? $request->getExact($ids) : $request->getEmptyIterator();
	}
	
 	function buildBoardAttributeIterator()
 	{
		if ( $this->getTable()->getReportBase() == 'tasksboardcrossproject' )
		{
 			$metastate = getFactory()->getObject('StateMeta');
 			
 			$metastate->setAggregatedStateObject(getFactory()->getObject($this->getBoardAttributeClassName()));
 			
 			return $metastate->getRegistry()->getAll();
		}
		else
		{
			return parent::buildBoardAttributeIterator();
		}
 	}
	
 	function getStateFilterName()
 	{
 		return 'taskstate';
 	}
	
 	function getColumns()
	{
		$attrs = $this->object->getAttributes();
		
		if ( array_key_exists( 'Planned', $attrs ) )
		{
			$this->object->addAttribute( 'Progress', '', translate('Прогресс'), false );
		}
		
		$columns = parent::getColumns();
		
		// use AssigneeUser (User) instead of Assignee (Participant)
		$key = array_search('Assignee', $columns);
		if ( $key !== false ) unset($columns[$key]);
		
		return $columns;
	}

	function getColumnFields()
	{
		$cols = parent::getColumnFields();
		
		foreach ( $cols as $key => $col )
		{
			if ( $col == 'Progress' ) continue;
			
			if ( $this->object->getAttributeDbType($col) == '' )
			{
				unset( $cols[$key] );
			}
		}
		
		if ( getSession()->getProjectIt()->getMethodologyIt()->get('IsRequestOrderUsed') == 'Y' )
		{
			array_push( $cols, 'OrderNum');
		}
		return $cols;
	}

	function getGroupDefault()
	{
		if ( $this->getTable()->hasCrossProjectFilter() ) return 'Project';
		
		return 'AssigneeUser';
	}
	
	function getGroupFields() 
	{
		$fields = parent::getGroupFields();

		foreach( array('Spent', 'Watchers', 'Attachment', 'TraceTask') as $field )
		{
			if ( in_array($field, $fields) ) unset($fields[array_search($field, $fields)]);
		}
		
		return $fields;
	}
	
 	function getBoardAttribute()
 	{
 		return 'State';
 	}
 	
 	function getBoardAttributeClassName()
 	{
 		return 'TaskState';
 	}
 	
	function IsNeedToDisplay( $attr ) 
	{
		switch( $attr ) 
		{
			case 'UID':
			case 'Caption':
			case 'Progress':
			case 'AssigneeUser':
				return true;
				
			default: 
				return false;
		}
	}
 	
 	function drawRefCell( $object_it, $attr )
 	{
 		switch ( $attr )
 		{
 		    case 'Fact':
 		        echo '<div style="padding:3px 0 3px 0;">';
 		            echo ($object_it->get('Fact') > 0 ? $object_it->get('Fact').' '.translate('ч.') : '');
 		        echo '</div>';
 		        
 		        break;
 		         
 		    case 'AssigneeUser':
 		    	if ( $object_it->get($attr) != '' )
 		    	{
	 		        echo '<div style="padding:3px 0 3px 0;">';
	 		            echo $object_it->getRef($attr)->getDisplayName();
	 		        echo '</div>';
 		    	}
 		        
 		        break;
 		        
 		    default:
				echo '<div style="padding:3px 0 3px 0;">';
 					parent::drawRefCell( $object_it, $attr );
 				echo '</div>';
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
					
					$frame->draw();
				}
				elseif ( $object_it->get('Planned') > 0 )
				{
					$frame = new TaskProgressFrame( $object_it->getProgress() );
					
					$frame->draw();
				}

				break;
			
			case 'UID':
				
				echo '<div>';
					$this->drawCheckbox($object_it);

					parent::drawCell( $object_it, $attr );
				echo '</div>';

				break;

			case 'OrderNum':
				if ( !$object_it->IsFinished() && getFactory()->getAccessPolicy()->can_modify($object_it) )
				{
					echo '<div style="float:left;padding:6px 0 3px 0;width:40%;">';
						$method = new AutoSaveFieldWebMethod( $object_it, 'OrderNum' );
						$method->setInput();
						$method->draw();
					echo '</div>';
					break;
				}				
			
			default:
				parent::drawCell( $object_it, $attr );
		}
	}

	function drawGroup($group_field, $object_it)
	{
		switch ( $group_field )
		{
			case 'AssigneeUser':
				
				parent::drawGroup($group_field, $object_it);
				
				$workload = $this->getTable()->getAssigneeUserWorkloadData();
				
				if ( count($workload) > 0 )
				{
					echo ' '.str_replace('%1', $workload[$object_it->get($group_field)]['Planned'],
								str_replace('%2', $workload[$object_it->get($group_field)]['LeftWork'],
										str_replace('%3', $workload[$object_it->get($group_field)]['Fact'],text(1857))
							));
				}				
					
				break;
				
			default:
				parent::drawGroup($group_field, $object_it);
		}
	}
		
	function getGroupBackground2( $object_it, $attr_it ) 
	{
 		switch ( $this->getGroup() )
 		{
 			case 'ChangeRequest':
 				return 'white';
 				
 			default:
 				return parent::getGroupBackground($object_it, $attr_it);
 		}
	}
	
	function getGroupStyle2()
 	{
 		switch ( $this->getGroup() )
 		{
 			case 'ChangeRequest':
 				return GROUP_STYLE_COLUMN;
 				
 			default:
 				return parent::getGroupStyle();
 		}
 	}
	
	function getItemStyle( $object_it )
	{
		return parent::getItemStyle($object_it).
			';background:'.$this->priority_frame->getColor($object_it->get('Priority')).';';
	}
	
	function getCardColor( $object_it )
	{ 	
		$values = $this->getFilterValues();
		
		switch ( $values['color'] )
		{
		    case 'state':
		    	return $object_it->getStateIt()->get('RelatedColor');
		    	
		    case 'priority':
		    	return $object_it->getRef('Priority')->get('RelatedColor');
		    	
		    case 'type':
		    	return $object_it->getRef('TaskType')->get('RelatedColor');
		}
	}

	function getActions( $object_it ) 
	{
		global $model_factory;
		
		$actions = parent::getActions( $object_it );
		
		if ( $object_it->IsFinished() )
		{
			return $actions;	
		}	
		
		$project_roles = getSession()->getRoles();
		
		if( $project_roles['lead'] && !$this->getTable()->hasCrossProjectFilter() ) 
		{
			if ( !isset($this->futher_it) )
			{
				$release = $model_factory->getObject('Iteration');
				
				$release->addFilter( new IterationTimelinePredicate(IterationTimelinePredicate::NOTPASSED) );
				
				$this->futher_it = $release->getAll();
			}
			else
			{
				$this->futher_it->moveFirst();
			}
			
			$need_separator = true;
			while( !$this->futher_it->end() )
			{
				if ( $this->futher_it->getId() != $object_it->get('Release') )
				{
					if ( $need_separator )
					{
						array_push($actions, array());
						$need_separator = false;
					}
					
					$it = $this->futher_it->_clone();
					$method = new MoveTaskWebMethod($it);
		
					array_push($actions,
							   array( 'name' => $method->getCaption(), 
							   		  'url' => $method->getJSCall( array( 'Task' => $object_it->getId(),
							   			'Release' => $this->futher_it->getId())) ) );
				}
						   			
				$this->futher_it->moveNext();
			}
		}
		
		$priority_actions = array();
		
		foreach( $this->priorities_array as $priority_it )
		{
			if ( $object_it->get('Priority') == $priority_it->getId() ) continue;
			
			$method = new ModifyAttributeWebMethod($object_it, 'Priority', $priority_it->getId());
				
			$refreshScript = "donothing";
			
			$method->setCallback( $refreshScript );
				
			$priority_actions[] = array( 
			    'name' => $priority_it->getDisplayName(), 
			    'url' => $method->getJSCall()
			);
		}
		
		if ( count($priority_actions) > 0 )
		{
			$actions[] = array();
			$actions[] = array(
					'name' => translate('Приоритет'),
					'items' => $priority_actions
			);
		}

		return $actions;
	}			
	
	function getWorkflowSettingsModule()
	{
		return getFactory()->getObject('Module')->getExact('workflow-taskstate');
	}
	
	function drawScripts()
	{
		parent::drawScripts();
			
		?>
		<script type="text/javascript">
			$(document).ready( function()
			{
				boardItemOptions.itemFormUrl = '<?=$this->getObject()->getPage()?>';
				boardItemOptions.resetParms = boardItemOptions.resetParms + "&taskstate=all"; 
				
				if ( typeof draggableOptions != 'undefined' )
				{
					boardItemOptions.initializeBoardItemCustom = refreshHelpSections;	
				}

				board( boardItemOptions );
			});

			function refreshHelpSections( items, options )
			{
				$('.sectionbody').each(function() { $(this).trigger("dblclick"); });
			}
		</script>
		<?
	}
	
	function getRenderParms()
	{
		$this->buildRelatedDataCache();
		
		return parent::getRenderParms();
	}
}