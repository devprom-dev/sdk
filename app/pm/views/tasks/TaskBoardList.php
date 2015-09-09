<?php

include_once "TaskProgressFrame.php";
include_once "TaskBalanceFrame.php";
include_once SERVER_ROOT_PATH.'core/views/c_issue_type_view.php';
include_once SERVER_ROOT_PATH.'core/views/c_priority_view.php';
include_once SERVER_ROOT_PATH."pm/methods/CommentWebMethod.php";

class TaskBoardList extends PMPageBoard
{
 	var $is_finished;
 	
 	private $priorities_array = array();
 	private $visible_column = array();
 	private $method_comment = null;
 	private $priority_actions = array();
 	private $terminal_states = array();
 	private $method_spend_time = null;
    private $estimation_strategy = null;
 	
 	function __construct( $object ) 
	{
		$this->priority_frame = new PriorityFrame();
        $this->estimation_strategy = getSession()->getProjectIt()->getMethodologyIt()->getEstimationStrategy();

		parent::__construct( $object );
		
		$this->getObject()->addAttribute( 'Basement', '', '', false, false, '', 99999 );
	}

	function buildRelatedDataCache()
	{
		$object_it = $this->getObject()->getEmptyIterator();
		$this->terminal_states = $this->getObject()->getTerminalStates();
		
		$priority_it = getFactory()->getObject('Priority')->getAll();
		while( !$priority_it->end() )
		{
			$method = new ModifyAttributeWebMethod($object_it, 'Priority', $priority_it->getId());
			if ( $method->hasAccess() )
			{
				$method->setCallback( "donothing" );
				$this->priority_actions[$priority_it->getId()] = array( 
				    'name' => $priority_it->getDisplayName(),
					'method' => $method 
				);
			}
			$this->priorities_array[] = $priority_it->copy();
			$priority_it->moveNext();
		}
		
	 	$method = new CommentWebMethod($object_it);
 		if ( $method->hasAccess() )
 		{
 			$method->setRedirectUrl('donothing');
 			$this->method_comment = $method;
 		}

	 	$method = new SpendTimeWebMethod($object_it);
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
	
 	function buildBoardAttributeIterator()
 	{
		if ( $this->getTable()->getReportBase() == 'tasksboardcrossproject' )
		{
			if ( $this->hasCommonStates() )
			{
		 		return getFactory()->getObject($this->getBoardAttributeClassName())->getRegistry()->Query(
		 				array (
		 						new FilterVpdPredicate(array_shift($this->getObject()->getVpds())),
		 						new SortAttributeClause('OrderNum')
		 				)
		 		);
			}
			else
			{
				$metastate = getFactory()->getObject('StateMeta');
	 			
	 			$metastate->setAggregatedStateObject(getFactory()->getObject($this->getBoardAttributeClassName()));
	 			
	 			return $metastate->getRegistry()->getAll();
			}
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
		
		return parent::getColumns();
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
		
		return 'Assignee';
	}
 		
	function getGroupFields() 
	{
		$fields = parent::getGroupFields();

		foreach( array('Spent', 'Watchers', 'Attachment', 'TraceTask') as $field )
		{
			if ( in_array($field, $fields) ) unset($fields[array_search($field, $fields)]);
		}
		
		$fields[] = 'DueDays';
		
		return $fields;
	}
	
	function getGroup() 
	{
		$group = parent::getGroup();
		if ( $group == 'AssigneeUser' ) return 'Assignee'; 
		return $group;
	}
	
	function getGroupIt()
	{
		$values = $this->getFilterValues();
		if ( $this->getTable()->hasCrossProjectFilter() ) {
			$vpd_filter = new FilterVpdPredicate();
		} else {
			$vpd_filter = new FilterBaseVpdPredicate();
		}
		switch($this->getGroup())
		{
			case 'Release':
				$object = getFactory()->getObject('Iteration');
				$ids = array_merge(
						$object->getRegistry()->Query(
								array (
										new IterationTimelinePredicate(IterationTimelinePredicate::NOTPASSED),
										new SortAttributeClause('StartDate'),
                                        $vpd_filter,
										$values['iteration'] != '' 
												? new FilterInPredicate(preg_split('/,/', $values['iteration'])) : null
								)
							)->idsToArray(),
						parent::getGroupIt()->idsToArray()
				);
				return $object->getRegistry()->Query(array(new FilterInPredicate($ids)));
			case 'Assignee':
				return getFactory()->getObject('User')->getRegistry()->Query(
						array (
								new UserWorkerPredicate(),
								new SortAttributeClause('Caption'),
								$values['taskassignee'] != '' 
										? new FilterInPredicate(preg_split('/,/', $values['taskassignee'])) : null
						)
					);
			case 'Priority':
				return getFactory()->getObject('Priority')->getRegistry()->Query(
						array (
							$values['taskpriority'] != '' 
										? new FilterInPredicate(preg_split('/,/', $values['taskpriority'])) : null
						)
				);
			default:
				return parent::getGroupIt();
		}
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
			case 'Assignee':
				return true;
				
			default: 
				return false;
		}
	}
 	
 	function drawRefCell( $ref_it, $object_it, $attr )
 	{
 		switch ( $attr )
 		{
 		    case 'Assignee':
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
			case 'Progress':
				break;
			
			case 'UID':
				
				echo '<div class="title-on-card">';
					echo '<div class="left-on-card">';
						$this->drawCheckbox($object_it);
						parent::drawCell( $object_it, $attr );
					echo '</div>';
	
					echo '<div class="right-on-card">';
						foreach(preg_split('/,/', $object_it->get('TraceTaskInfo')) as $link)
						{
							if ( $link == '' ) continue;
							list($task_id, $task_state) = preg_split('/:/',$link);
							if ( in_array($task_state, $this->terminal_states) ) continue;
							 
							$uid_info = $this->getUidService()->getUIDInfo($object_it->object->getExact($task_id));
							echo '<a class="with-tooltip block-sign" tabindex="-1" data-placement="right" data-original-title="" data-content="" info="'.$uid_info['tooltip-url'].'" href="'.$uid_info['url'].'" title="'.text(390).'"></a>';
							break;
						}
					
						if ( $this->visible_column['OrderNum'] && $object_it->get('OrderNum') != '' ) {
							echo '<span class="order" title="'.translate('Номер').'">';
								echo $object_it->get('OrderNum');
							echo '</span>';
						}
					echo '</div>';
				echo '</div>';
				
				break;

			case 'Basement':
   				
				echo '<div class="item-footer">';
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
					
					if ( $this->visible_column['Progress'] )
					{
						if ( $object_it->IsFinished() )
						{
							$frame = new TaskBalanceFrame( $object_it->get('Planned'), $object_it->get('Fact') );
							
							echo '<div style="display:table-cell;text-align:right;">';
								$frame->draw();
							echo '</div>';
						}
						elseif ( $object_it->get('Planned') > 0 )
						{
							$frame = new TaskProgressFrame( $object_it->getProgress() );
							
							echo '<div style="display:table-cell;text-align:right;">';
								$frame->draw();
							echo '</div>';
						}
					}
						
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
								echo $this->getTable()->getView()->render('core/CommentsIconMini.php', array (
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
			case 'Assignee':
				$workload = $this->getTable()->getAssigneeUserWorkloadData();
				echo $this->getTable()->getView()->render('pm/UserWorkload.php', array ( 
							'user' => $object_it->getRef('Assignee')->getDisplayName(),
							'data' => $workload[$object_it->get($group_field)]
					));
				break;

			case 'Release':
            case 'PlannedRelease':
				parent::drawGroup($group_field, $object_it);
				echo ' &nbsp; &nbsp; &nbsp; &nbsp; ';

				$release_it = $this->getGroupIt();
				$release_it->moveToId($object_it->get($group_field));

				if ( $release_it->getId() > 0 ) {
					$estimation = $release_it->getTotalWorkload();
					list( $capacity, $maximum, $actual_velocity ) = $release_it->getEstimatedBurndownMetrics();
					echo sprintf(
						text(2053),
						$release_it->getDateFormatShort('StartDate'),
                        $release_it->get('FinishDate') == '' ? '?' : $release_it->getDateFormatShort('FinishDate'),
						$this->estimation_strategy->getDimensionText(round($maximum, 1)),
                        $estimation > $maximum ? 'label label-important' : ($estimation < $maximum ? 'label label-success': ''),
						$this->estimation_strategy->getDimensionText(round($estimation, 1))
					);
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
		    	return $object_it->get('StateColor');
		    	
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
		
		$priority_actions = $this->priority_actions;
		
		foreach( $priority_actions as $key => $action )
		{
			if ( $object_it->get('Priority') == $key )
			{
				unset($priority_actions[$key]);
				continue;
			}
			
			$method = $priority_actions[$key]['method'];
			$priority_actions[$key]['url'] = $method->getJSCall(array(), $object_it);  
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
	
	function drawScripts()
	{
		parent::drawScripts();
			
		?>
		<script type="text/javascript">
			$(document).ready( function()
			{
				boardItemOptions.itemFormUrl = '/tasks/board';
				if ( typeof draggableOptions != 'undefined' ) {
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
		$this->visible_column['Progress'] = $this->getColumnVisibility('Progress');
		
		return $parms; 
	}
}