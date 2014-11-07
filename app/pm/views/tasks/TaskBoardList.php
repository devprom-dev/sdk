<?php

include_once "TaskProgressFrame.php";
include_once "TaskBalanceFrame.php";
include_once SERVER_ROOT_PATH.'core/views/c_issue_type_view.php';
include_once SERVER_ROOT_PATH.'core/views/c_priority_view.php';
include_once SERVER_ROOT_PATH."pm/methods/CommentWebMethod.php";
include_once SERVER_ROOT_PATH."pm/methods/SpendTimeWebMethod.php";

class TaskBoardList extends PMPageBoard
{
 	var $is_finished;
 	
 	private $priorities_array = array();

 	private $visible_column = array();

 	private $method_comment = null;
 	
 	private $method_spend_time = null;
 	
 	function __construct( $object ) 
	{
		$this->priority_frame = new PriorityFrame();
		
		parent::__construct( $object );
		
		$this->getObject()->addAttribute( 'Basement', '', '', false, false, '', 99999 );
	}

	function buildRelatedDataCache()
	{
		$priority_it = getFactory()->getObject('Priority')->getAll();
		
		while( !$priority_it->end() )
		{
			$this->priorities_array[] = $priority_it->copy();
			 
			$priority_it->moveNext();
		}
		
	 	$method = new CommentWebMethod( $this->getObject()->getEmptyIterator() );
 		
 		if ( $method->hasAccess() )
 		{
 			$method->setRedirectUrl('donothing');
 			
 			$this->method_comment = $method;
 		}

 		$method = new SpendTimeWebMethod( $this->getObject()->getEmptyIterator() );
 		
 		if ( $method->hasAccess() )
 		{
 			$method->setRedirectUrl('donothing');
 			
 			$this->method_spend_time = $method;
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

 	function getColumnVisibility( $attribute )
 	{
 		if ( $attribute == 'Basement' ) return array_sum($this->visible_column) > 0;
 		
 		return parent::getColumnVisibility( $attribute );
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
 	
 	function drawRefCell( $ref_it, $object_it, $attr )
 	{
 		switch ( $attr )
 		{
 		    case 'AssigneeUser':
 		    case 'Attachment':
 		        break;
 		        
 		    default:
				echo '<div style="padding:3px 0 3px 0;">';
 					parent::drawRefCell( $ref_it, $object_it, $attr );
 				echo '</div>';
 		}
 	}
 	
	function drawCell( $object_it, $attr )
	{
		switch($attr)	
		{
 		    case 'Fact':
			case 'OrderNum':
			case 'RecentComment':
				break;
			
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
				
				echo '<div class="title-on-card">';
					echo '<div class="left-on-card">';
						$this->drawCheckbox($object_it);
						parent::drawCell( $object_it, $attr );
					echo '</div>';
	
					if ( $this->visible_column['OrderNum'] )
					{
						echo '<div class="right-on-card">';
							echo '<span class="order" title="'.translate('Номер').'">';
								echo $object_it->get('OrderNum');
							echo '</span>';
						echo '</div>';
					}
				echo '</div>';
				
				break;

			case 'Basement':
   				
				echo '<div style="display:table;width:100%;margin-bottom:3px;height:23px;">';
					echo '<div style="display:table-cell;text-align:left;">';
						if ( $object_it->get('OwnerPhotoId') != '' )
						{
							echo $this->getTable()->getView()->render('core/UserPicture.php', array ( 
									'id' => $object_it->get('OwnerPhotoId'), 
									'class' => 'user-mini', 
									'image' => 'userpics-mini',
									'title' => $object_it->get('OwnerPhotoTitle')
							));
						}
						if ( $this->visible_column['Attachment'] )
						{
							echo '<div style="display: inline-block;vertical-align:bottom;">';
								parent::drawRefCell($this->getFilteredReferenceIt('Attachment', $object_it->get('Attachment')), $object_it, 'Attachment' );
							echo '</div>';
						}
					echo '</div>';
						
					echo '<div style="display:table-cell;text-align:right;">';
						if ( $this->visible_column['Fact'] && $object_it->get('Fact') > 0 && is_object($this->method_spend_time) )
						{
							$this->method_spend_time->setAnchorIt($object_it);
							
							echo '<div class="board-item-fact" title="'.$this->spent_time_title.'">';
								echo '<a href="'.$this->method_spend_time->getJSCall().'">'.$object_it->get('Fact').'</a>';
		    				echo '</div>';
						}
	
						if ( $this->visible_column['RecentComment'] && $object_it->get('CommentsCount') > 0 )
						{
							echo '<div style="margin-left:4px;display: inline-block;">';
								echo $this->getTable()->getView()->render('core/CommentsIcon.php', array (
										'object_it' => $object_it,
										'redirect' => 'donothing'
								));
							echo '</div>';
						}
					echo '</div>';
				echo '</div>';

				break;					
								
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
		$actions = parent::getActions( $object_it );
		
		if ( is_object($this->method_comment) )
		{
			$this->method_comment->setAnchorIt($object_it);
			
			$actions[] = array();
			$actions[] = array ( 
				'name' => $this->method_comment->getCaption(), 
				'url' => $this->method_comment->getJSCall() 
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
				boardItemOptions.itemFormUrl = '/tasks/board';
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
		
		$parms = parent::getRenderParms();
		
		foreach( array( 'Attachment', 'RecentComment', 'Fact', 'OrderNum') as $column )
		{
			if ( $this->getObject()->getAttributeType($column) == '' ) continue;
			
			$this->visible_column[$column] = $this->getColumnVisibility($column);
		}
		
		return $parms; 
	}
}