<?php

include_once SERVER_ROOT_PATH."core/views/c_issue_type_view.php";
include_once SERVER_ROOT_PATH."core/views/c_priority_view.php";
include_once SERVER_ROOT_PATH."pm/methods/CommentWebMethod.php";

class RequestBoard extends PMPageBoard
{
	private $terminal_states = array();
 	private $task_terminal_states = array();
 	private $non_terminal_states = array();
 	private $tasks_array = array();
 	private $priorities_array = array();
 	private $priority_actions = array();
 	private $task_transitions_array = array();
 	private $task_target_states_array = array();
 	private $method_create_task = null;
 	private $method_comment = null;
 	private $visible_column = array();
 	private $spent_time_title = '';
 	private $types_array = array();
 	private $task_uid_service = null;
 	private $estimation_actions = array();
 	private $estimation_title = '';
    private $estimation_strategy = null;
 	private $method_spend_time = null;
 	
 	function __construct( $object )
 	{
 		$this->priority_frame = new PriorityFrame();
 		
 		parent::__construct( $object );
 		
 		$this->task_uid_service = new ObjectUid('', getFactory()->getObject('Task'));
        $this->estimation_strategy = getSession()->getProjectIt()->getMethodologyIt()->getEstimationStrategy();
 		
		$this->getObject()->addAttribute( 'Basement', '', '', false, false, '', 99999 );
 	}
 	
 	function buildRelatedDataCache()
 	{
 		$object = $this->getObject();
		$object_it = $object->getEmptyIterator();
 		
 		$this->terminal_states = $object->getTerminalStates();
 		$this->non_terminal_states = $object->getNonTerminalStates();
 		
 	 	$task = getFactory()->getObject('Task');
 		$this->task_terminal_states = $task->getTerminalStates();
 		
 		$state_it = $task->cacheStates();
 		while( !$state_it->end() )
 		{
 			$transition_it = $state_it->getTransitionIt();
 			$this->task_transitions_array[$state_it->get('ReferenceName')] = $transition_it;
 		 	while( !$transition_it->end() )
 			{
 				$this->task_target_states_array[$transition_it->getId()] = $transition_it->getRef('TargetState');
 				$transition_it->moveNext();
 			}
 			$state_it->moveNext();
 		}
 		
 		$method = new RequestCreateTaskWebMethod( $object->getEmptyIterator() );
 		if ( $method->hasAccess() )
 		{
 			$method->setRedirectUrl('donothing');
 			$this->method_create_task = $method;
 		}

 		$method = new CommentWebMethod( $object->getEmptyIterator() );
 		if ( $method->hasAccess() )
 		{
 			$method->setRedirectUrl('donothing');
 			$this->method_comment = $method;
 		}

		// cache priorities
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
			$this->priorities_array[$priority_it->getId()] = $priority_it->copy();
			$priority_it->moveNext();
		}
		
		$strategy = getSession()->getProjectIt()->getMethodologyIt()->getEstimationStrategy();
		foreach( $strategy->getScale() as $item )
		{
			$method = new ModifyAttributeWebMethod($object_it, 'Estimation', $item);
			if ( $method->hasAccess() )
			{
				$method->setCallback( "donothing" );
				$this->estimation_actions[] = array( 
					    'name' => ' '.$item,
						'method' => $method 
				);
			}
		}
		
 		$method = new SpendTimeWebMethod($object_it);
 		if ( $method->hasAccess() )
 		{
 			$method->setRedirectUrl('donothing');
 			$this->method_spend_time = $method;
 		}
		
		$this->spent_time_title = $this->getObject()->getAttributeUserName('Fact');
 		$this->estimation_title = $this->getObject()->getAttributeUserName('Estimation');
 	}
 	
	function retrieve()
	{
		global $model_factory;
		
		parent::retrieve();

		$iterator = $this->getIteratorRef()->copyAll();
		
		$task_it = getFactory()->getObject('Task')->getRegistry()->Query(
				array (
						new FilterAttributePredicate('ChangeRequest', $iterator->idsToArray()),
						new SortAttributeClause('ChangeRequest')
				)
		);
		
		while( !$task_it->end() )
		{
			$this->tasks_array[$task_it->get('ChangeRequest')][] = $task_it->copy(); 
			
			$task_it->moveNext();
		}

		$ids = array_filter($this->getIteratorRef()->fieldToArray('Type'), function($value) {
				return $value > 0;
		});
		
		if ( count($ids) < 1 ) $ids = array(0);
		
		$type_it = getFactory()->getObject('RequestType')->getRegistry()->Query(
				array (
						new FilterInPredicate($ids)
				)
		);
		
		while( !$type_it->end() )
		{
			$this->types_array[$type_it->getId()] = IssueTypeFrame::getIcon($type_it);
			
			$type_it->moveNext();
		}
		
		$this->types_array[''] = IssueTypeFrame::getIcon(getFactory()->getObject('RequestType')->getEmptyIterator());
	}
	
 	function buildBoardAttributeIterator()
 	{
		if ( $this->getTable()->getReportBase() == 'issuesboardcrossproject' )
		{
			$class_name = $this->getBoardAttributeClassName();
			if ( $this->hasCommonStates() )
			{
		 		return getFactory()->getObject($class_name)->getRegistry()->Query(
		 				array (
		 						new FilterVpdPredicate(array_shift($this->getObject()->getVpds())),
		 						new SortAttributeClause('OrderNum')
		 				)
		 		);
			}
			else
			{
	 			$metastate = getFactory()->getObject('StateMeta');
	 			$metastate->setAggregatedStateObject(getFactory()->getObject($class_name));
	 			return $metastate->getRegistry()->getAll();
			}
		}
		else
		{
			return parent::buildBoardAttributeIterator();
		}
 	}

	function getGroupDefault()
	{
		if ( $this->getTable()->hasCrossProjectFilter() ) return 'Project';
		if ( getSession()->getProjectIt()->getMethodologyIt()->HasReleases() ) return 'PlannedRelease'; 
		if ( getSession()->getProjectIt()->getMethodologyIt()->HasFeatures() ) return 'Function'; 
		
		return '';
	}
	
	function getGroupFields() 
	{
		$fields = array_merge( parent::getGroupFields(), array( 'Tags', 'Deadlines' ) );

		foreach( array('Fact', 'Spent', 'Watchers', 'Attachment') as $field )
		{
			if ( in_array($field, $fields) ) unset($fields[array_search($field, $fields)]);
		}
		
		if ( $this->getObject()->getAttributeType('Estimation') != '' )
		{
			$fields[] = 'Estimation';
		}
		
		return array_merge( $fields, array( 'ClosedInVersion', 'SubmittedVersion', 'DueDays' ) );
	}
	
	function getGroup() 
	{
		$group = parent::getGroup();
		if ( $group == 'OwnerUser' ) return 'Owner'; 
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
			case 'PlannedRelease':
				$object = getFactory()->getObject('Release');
				$ids = array_merge(
						$object->getRegistry()->Query(
								array (
										new ReleaseTimelinePredicate('not-passed'),
										new SortAttributeClause('StartDate'),
										$vpd_filter,
										$values['release'] != '' 
												? new FilterInPredicate(preg_split('/,/', $values['release'])) : null
								)
							)->idsToArray(),
						parent::getGroupIt()->idsToArray()
				);
				return $object->getRegistry()->Query(array(new FilterInPredicate($ids)));
			case 'Iterations':
				return getFactory()->getObject('Iteration')->getRegistry()->Query(
						array (
								new IterationTimelinePredicate(IterationTimelinePredicate::NOTPASSED),
								new SortAttributeClause('StartDate'),
								$vpd_filter,
								$values['iterations'] != '' 
										? new FilterInPredicate(preg_split('/,/', $values['iterations'])) : null
						)
					);
			case 'Function':
				return getFactory()->getObject('Feature')->getRegistry()->Query(
						array (
								new SortAttributeClause('Importance'),
								$vpd_filter,
								$values['function'] != '' 
										? new FilterInPredicate(preg_split('/,/', $values['function'])) : null
						)
					);
			case 'Owner':
				return getFactory()->getObject('User')->getRegistry()->Query(
						array (
								new UserWorkerPredicate(),
								new SortAttributeClause('Caption'),
								$values['owner'] != '' 
										? new FilterInPredicate(preg_split('/,/', $values['owner'])) : null
						)
					);
			case 'Priority':
				return getFactory()->getObject('Priority')->getRegistry()->Query(
						array (
								$values['priority'] != '' 
										? new FilterInPredicate(preg_split('/,/', $values['priority'])) : null
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
 		return 'IssueState';
 	}
 	
 	function getColumnVisibility( $attribute )
 	{
 		if ( $attribute == 'Basement' ) return array_sum($this->visible_column) > 0;
 		
 		return parent::getColumnVisibility( $attribute );
 	}
 	
	function getColumnFields()
	{
		$methodology_it = getSession()->getProjectIt()->getMethodologyIt();
		
		$cols = parent::getColumnFields();
		
		foreach ( $cols as $key => $col )
		{
			if ( $this->object->getAttributeDbType($col) == '' )
			{
				unset( $cols[$key] );
			}

			if ( $col == 'Function' && !$methodology_it->HasFeatures() )
			{
				unset( $cols[$key] );
			}
		}
		
		if ( $methodology_it->get('IsRequestOrderUsed') == 'Y' )
		{
			array_push( $cols, 'OrderNum');
		}
		
		return $cols;
	}

	function getGroupBackground( $object_it, $attr_it ) 
	{
 		switch ( $this->getGroup() )
 		{
 			default:
 				return parent::getGroupBackground($object_it, $attr_it);
 		}
	}
	
 	function drawGroup($group_field, $object_it)
	{
		switch ( $group_field )
		{
			case 'Function':
				echo '<div class="feature-group" modifiable="1" object="'.$object_it->get($group_field).'">';
					$title = $this->getTable()->getFeatureTitle($this->getGroupIt(), $object_it, $this->getUIDService());
					if ( $title == '' ) {
						parent::drawGroup($group_field, $object_it);
					}
					else {
						echo $title;
					}
				echo '</div>';
				break;

			case 'Estimation':
				echo $object_it->object->getAttributeUserName($group_field).': '.$object_it->get($group_field);
				break;

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
                        $estimation > $maximum ? 'label label-important' : ($maximum > 0 && $estimation < $maximum ? 'label label-success': ''),
                        $this->estimation_strategy->getDimensionText(round($estimation, 1))
                    );
                }
				break;

			case 'Project':
				parent::drawGroup($group_field, $object_it);
				$project_it = $this->getGroupIt();
				$project_it->moveToId($object_it->get($group_field));
				echo $this->getTable()->getView()->render('pm/RowGroupActions.php', array (
						'actions' => $this->getTable()->getNewCardActions($project_it)
					));
				break;
				
			default:
				parent::drawGroup($group_field, $object_it);
				break;
		}
	}

	function drawRefCell( $ref_it, $object_it, $attr )
	{
		switch ( $attr )
		{
			case 'Owner':
				if ( $object_it->get($attr) < 1 || $object_it->get('OwnerPhotoId') > 0 ) return;
				
				echo '<div style="padding:0 0 0 0;overflow-y:hidden;height:16px;">';
					echo $ref_it->getDisplayName();
				echo '</div>';
				
				break;
				
			case 'Attachment':
			case 'Tasks':
				break;
				
			case 'Author':
				if ( $object_it->get('Author') == '' )
				{
					$value = $object_it->get('ExternalAuthor');
				}
				else
				{
					$value = $ref_it->getDisplayName();
				}
					
				echo '<div style="overflow:hidden;height:1.7em;word-break:break-all" title="'.$value.'">';
					echo $value;
				echo '</div>';
				
				break;
				
			default:
				parent::drawRefCell( $ref_it, $object_it, $attr );
		}
	}

	function drawCell( $object_it, $attr )
	{
		switch( $attr )
		{
			case 'UID':
				
				echo '<div class="title-on-card">';
					echo '<div class="left-on-card">';
						$this->drawCheckbox($object_it);
						$type_image = $this->types_array[$object_it->get('Type')];
						if ( $type_image != '' ) echo '<img src="/images/'.$type_image.'"> ';
						parent::drawCell( $object_it, $attr );
					echo '</div>';
					echo '<div class="right-on-card">';
						foreach(preg_split('/,/', $object_it->get('LinksWithTypes')) as $link_info)
						{
							list($type_name, $link_id, $type_ref, $link_state, $direction) = preg_split('/:/',$link_info);
							if ( $type_ref == 'blocked' && $direction == 2 && !in_array($link_state,$this->terminal_states)) {
								$uid_info = $this->getUidService()->getUIDInfo($object_it->object->getExact($link_id));
								echo '<a class="with-tooltip block-sign" tabindex="-1" data-placement="right" data-original-title="" data-content="" info="'.$uid_info['tooltip-url'].'" href="'.$uid_info['url'].'" title="'.text(961).'"></a>';
							}
							if ( $type_ref == 'implemented' && $direction == 2 && !in_array($link_state,$this->terminal_states)) {
								$uid_info = $this->getUidService()->getUIDInfo($object_it->object->getExact($link_id));
								echo '<a class="with-tooltip impl-sign" tabindex="-1" data-placement="right" data-original-title="" data-content="" info="'.$uid_info['tooltip-url'].'" href="'.$uid_info['url'].'" title="'.text(2035).'"></a>';
							}
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
						if ( $this->visible_column['Tasks'] && is_array($this->tasks_array[$object_it->getId()]) )
						{
							$states = array();
	
						 	foreach( $this->tasks_array[$object_it->getId()] as $task_it ) 
					 		{
					 			if ( in_array($task_it->get('State'), $this->task_terminal_states) ) continue;
	
					 			$info = $this->task_uid_service->getUidInfo($task_it);
					 			
					 			$states[] = array (
					 					'id' => $task_it->getId(),
					 					'name' => $task_it->get('TaskTypeShortName'),
					 					'progress' => in_array($task_it->get('State'), $this->task_terminal_states) ? '100%' : '0%',
					 					'photo_id' => $task_it->get('TaskAssigneePhotoId'),
					 					'actions' => $this->getTaskActions($task_it),
					 					'url' => $info['tooltip-url']
					 			);
					 		}
					 		
							echo $this->getTable()->getView()->render('pm/TasksIcons.php', array (
									'states' => $states
							));
						}
						else if ( $object_it->get('OwnerPhotoId') != '' )
						{
							echo '<div class="btn-group">';
								echo $this->getTable()->getView()->render('core/UserPicture.php', array ( 
										'id' => $object_it->get('OwnerPhotoId'), 
										'class' => 'user-mini', 
										'image' => 'userpics-mini',
										'title' => $object_it->get('OwnerPhotoTitle')
								));
							echo '</div>';
						}
						if ( $this->visible_column['Attachment'] )
						{
							echo '<div class="btn-group" style="display:inline-block;">';
								parent::drawRefCell($this->getFilteredReferenceIt('Attachment', $object_it->get('Attachment')), $object_it, 'Attachment' );
							echo '</div>';
						}
					echo '</div>';
						
					echo '<div style="display:table-cell;text-align:right;">';
						if ( $this->visible_column['Estimation'] )
						{
							$actions = $this->estimation_actions;
							
							foreach( $actions as $key => $action )
							{
								$method = $action['method'];
								
								$actions[$key]['url'] = $method->getJSCall(array(), $object_it);
							}
							
							echo '<div style="display: inline-block;">';
								echo $this->getTable()->getView()->render('pm/EstimationIcon.php', array (
										'title' => $this->estimation_title,
										'data' => $object_it->get('Estimation') != '' ? $object_it->get('Estimation') : '0',
										'items' => $actions
								));
							echo '</div>';
						}
					
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

			case 'OrderNum':
			case 'Estimation':
			case 'Fact':
			case 'RecentComment':
				break;

			default:
				parent::drawCell( $object_it, $attr );
		}
	}

	function getRenderParms()
	{
 		$this->buildRelatedDataCache();
		
		$parms = parent::getRenderParms();
		
		foreach( array('Tasks', 'Attachment', 'RecentComment', 'OrderNum', 'Estimation', 'RecentComment', 'Fact') as $column )
		{
			if ( $this->getObject()->getAttributeType($column) == '' ) continue;
			
			$this->visible_column[$column] = $this->getColumnVisibility($column);
		}
		
		return $parms; 
	}
	
	function buildFilterActions( & $base_actions )
	{
	    parent::buildFilterActions( $base_actions );

	    $this->buildFilterColumnsGroup( $base_actions, 'workflow' );
	    
	    $this->buildFilterColumnsGroup( $base_actions, 'trace' );
	    
	    $this->buildFilterColumnsGroup( $base_actions, 'time' ); 
	    
	    $this->buildFilterColumnsGroup( $base_actions, 'dates' ); 
	}
	
	function getActions( $object_it ) 
	{
		$actions = parent::getActions( $object_it );

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
			$pos = array_search(array('uid'=>'middle'), $actions);
			
			$actions = array_merge(
					array_slice($actions, 0, $pos),
					array(
							array(),
							array(
								'name' => translate('Приоритет'),
								'items' => $priority_actions
							)
					),
					array_slice($actions, $pos)
			); 
		}
		
		if ( is_object($this->method_create_task) )
		{
			$this->method_create_task->setRequestIt($object_it);
			
			$actions[] = array();
			$actions[] = array ( 
				'name' => $this->method_create_task->getCaption(), 
				'url' => $this->method_create_task->getJSCall() 
			);
		}
				
		if ( is_object($this->method_comment) )
		{
			$this->method_comment->setAnchorIt($object_it);
			
			$actions[] = array();
			$actions[] = array ( 
				'name' => $this->method_comment->getCaption(), 
				'url' => $this->method_comment->getJSCall() 
			);
		}
		
		return $actions;
	}
	
	function getTaskActions( $object_it )
	{
		$actions = array();

		$method = new ObjectModifyWebMethod($object_it);
		
		$method->setRedirectUrl('donothing');
		
		$actions[] = array (
				'name' => translate('Изменить'),
				'url' => $method->getJSCall()
		);

		$transition_it = $this->task_transitions_array[$object_it->get('State')];
		
		if ( !is_object($transition_it) ) return $actions;
		
		$transition_it->moveFirst();

		$need_separator = true;
		
		while ( !$transition_it->end() )
		{
			$method = new TransitionStateMethod( $transition_it, $object_it );

			$method->setTargetStateRefName($this->task_target_states_array[$transition_it->getId()]->get('ReferenceName'));
			
			if ( $need_separator )
			{
				$actions[] = array();
				
				$need_separator = false;
			}

			$method->setRedirectUrl('donothing');
			
			$actions[] = array( 
				'url' => $method->getJSCall(), 
				'name' => $method->getCaption()
			);
			
			$transition_it->moveNext();
		}
			
		return $actions;
	}
	
	function getItemStyle( $object_it )
	{
	    // priority driven coloring
	    $style = ';background:'.$this->priority_frame->getColor($object_it->get('Priority')).';';
	    
	    // deadlines driven coloring
	    $deadline_alert = 
	    		in_array($object_it->get('State'), $this->non_terminal_states) 
	    		&& $object_it->get('DueDays') < 1 && $object_it->get('DeadlinesDate') != '';
	    
	    if ($deadline_alert) {
	    	$style .= 'border: 2px solid red;';
	    }
	    					
		return parent::getItemStyle( $object_it ).$style;
	}
 	
	function getCardColor( $object_it )
	{ 	
		$values = $this->getFilterValues();
		
		switch ( $values['color'] )
		{
		    case 'state':
		    	return $object_it->get('StateColor');
		    	
		    case 'priority':
		    	return is_object($this->priorities_array[$object_it->get('Priority')]) 
		    			? $this->priorities_array[$object_it->get('Priority')]->get('RelatedColor')
		    			: '';
		    	
		    case 'type':
		    	return $object_it->getRef('Type')->get('RelatedColor');
		}
	}
	
	function drawScripts()
	{
		global $model_factory;
		
		parent::drawScripts();
		
		$feature = $model_factory->getObject('Feature');
	?>
	<script type="text/javascript">
		var featureOptions = null;
		
		$(document).ready( function()
		{
			boardItemOptions.itemFormUrl = '/issues/board';
			
			board( boardItemOptions );

			featureOptions = jQuery.extend({}, boardItemOptions);
			
			featureOptions.className = '<?=$feature->getClassName()?>';
			featureOptions.classUserName = '<?=$feature->getDisplayName()?>';
			featureOptions.itemFormUrl = '<?=$feature->getPage()?>';

			$('.feature-group').each(function() {
				$(this).dblclick(function() {
					modifyBoardItem( $(this), featureOptions, function() {window.location.reload();} );
				});
			});
		});
	</script>
	<?
	}
}
