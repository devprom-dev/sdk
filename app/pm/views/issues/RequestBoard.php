<?php

include_once SERVER_ROOT_PATH."core/views/c_issue_type_view.php";
include_once SERVER_ROOT_PATH."pm/methods/CommentWebMethod.php";

class RequestBoard extends PMPageBoard
{
	private $terminal_states = array();
 	private $task_terminal_states = array();
 	private $non_terminal_states = array();
 	private $tasks_array = array();
 	private $priority_actions = array();
    private $owner_actions = array();
 	private $task_transition_it = array();
 	private $task_target_states_array = array();
 	private $method_comment = null;
 	private $visible_column = array();
 	private $spent_time_title = '';
 	private $task_uid_service = null;
 	private $estimation_actions = array();
	private $estimation_scale = array();
 	private $estimation_title = '';
 	private $method_spend_time = null;
	private $uidVisible = true;
	private $task = null;
	private $taskBoardStates = array();
	private $taskBoardModuleIt = null;

 	function __construct( $object )
 	{
 		parent::__construct( $object );
 		
 		$this->task_uid_service = new ObjectUid('', getFactory()->getObject('Task'));
 	}

 	function buildRelatedDataCache()
 	{
        $this->getTable()->buildRelatedDataCache();
		$rowset = $this->getIteratorRef()->getRowset();

		$ids = array_map(
			function($val) {
				return $val['pm_ChangeRequestId'];
			},
			$rowset
		);
		if ( count($ids) < 1 ) {
			$task_it = getFactory()->getObject('Task')->getEmptyIterator();
		}
		else {
			$task_it = getFactory()->getObject('Task')->getRegistry()->Query(
				array_merge(
					array (
						new FilterAttributePredicate('ChangeRequest', $ids),
						new SortAttributeClause('ChangeRequest')
					),
					$this->visible_column['OpenTasks']
						? array( new StatePredicate('notresolved') )
						: array()
				)
			);
		}

		foreach( $task_it->getRowset() as $row ) {
			$this->tasks_array[$row['ChangeRequest']][] = $row;
		}
		$this->task = getFactory()->getObject('Task');

		$ids = array_filter(
			array_map(function($val) {
				return $val['Type'];
			}, $rowset),
			function($value) { return $value > 0; }
		);
		if ( count($ids) < 1 ) $ids = array(0);

 		$object = $this->getObject();
		$object_it = $object->getEmptyIterator();
 		
 		$this->terminal_states = $object->getTerminalStates();
 		$this->non_terminal_states = $object->getNonTerminalStates();

 	 	$task = getFactory()->getObject('Task');
 		$this->task_terminal_states = $task->getTerminalStates();

		$this->task_transition_it = WorkflowScheme::Instance()->getTransitionIt($task);
		while( !$this->task_transition_it->end() )
		{
			$this->task_target_states_array[$this->task_transition_it->getId()] = $this->task_transition_it->get('TargetStateReferenceName');
			$this->task_transition_it->moveNext();
		}

 		$method = new CommentWebMethod( $object->getEmptyIterator() );
 		if ( $method->hasAccess() ) {
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
			$priority_it->moveNext();
		}

        // cache assignees
        $user_it = getFactory()->getObject('ProjectUser')->getAll();
		$taskIt = getFactory()->getObject('Task')->getEmptyIterator();
        while( !$user_it->end() )
        {
            $method = new ModifyAttributeWebMethod($object_it, 'Owner', $user_it->getId());
            if ( $method->hasAccess() )
            {
                $method->setCallback( "donothing" );
                $this->owner_actions[$user_it->getId()] = array(
                    'name' => $user_it->getDisplayName(),
                    'method' => $method
                );
            }
            $method = new ModifyAttributeWebMethod($taskIt, 'Assignee', $user_it->getId());
            if ( $method->hasAccess() )
            {
                $method->setCallback( "donothing" );
                $this->assignee_actions[$user_it->getId()] = array(
                    'name' => $user_it->getDisplayName(),
                    'method' => $method
                );
            }
            $user_it->moveNext();
        }

		$scale = getSession()->getProjectIt()->getMethodologyIt()->getEstimationStrategy()->getScale();
		$this->estimation_scale = array_flip($scale);
		foreach( $scale as $label => $item )
		{
			$method = new ModifyAttributeWebMethod($object_it, 'Estimation', $item);
			if ( $method->hasAccess() )
			{
				$method->setCallback( "donothing" );
				$this->estimation_actions[] = array( 
					    'name' => $label,
						'method' => $method 
				);
			}
		}
		
 		$method = new SpendTimeWebMethod($object_it);
 		if ( $method->hasAccess() ) {
 			$this->method_spend_time = $method;
 		}
		
		$this->spent_time_title = $this->getObject()->getAttributeUserName('Fact');
 		$this->estimation_title = $this->getObject()->getAttributeUserName('Estimation');

		$info = $this->getTable()->getPage()->getPageWidgetNearestUrl();
		if ( is_object($info['widget']) ) {
            $this->tags_url = $info['widget']->getUrl('tag=%');
        }

		$states = WorkflowScheme::Instance()->getStates($this->getObject());
		foreach( $states as $stateFullKey => $state ) {
			$this->attribute_it = WorkflowScheme::Instance()->getStateAttributeIt($this->getObject(), $state);
			while( !$this->attribute_it->end() ) {
				if ( $this->attribute_it->get('ReferenceName') == 'Tasks' ) {
					$this->taskBoardStates[] = $state;
				}
				$this->attribute_it->moveNext();
			}
		}

		$state_it = getFactory()->getObject($this->getObject()->getStateClassName())->getAll();
		while( !$state_it->end() ) {
			if ( $state_it->get('TaskTypes') != '' ) {
				$this->taskBoardStates[] = $state_it->get('ReferenceName');
			}
			$state_it->moveNext();
		}

		$report = getFactory()->getObject('PMReport');
		$module_it = $report->getExact('tasksboardforissues');
		if ( getFactory()->getAccessPolicy()->can_read($module_it) ) {
			$this->taskBoardModuleIt = $module_it;
		}

        $module_it = $report->getExact('iterationplanningboard');
        if ( getFactory()->getAccessPolicy()->can_read($module_it) ) {
            $this->groomingModuleIt = $module_it;
        }
 	}
 	
	function getGroupDefault()
	{
		if ( $this->getProjectIt()->count() > 1 ) return 'Project';

		$methodology_it = getSession()->getProjectIt()->getMethodologyIt();
		if ( $methodology_it->HasReleases() ) return 'PlannedRelease';
		if ( $methodology_it->HasPlanning() ) return 'Iteration';
		if ( $methodology_it->HasFeatures() ) return 'Function';
		
		return '';
	}
	
	function getGroupFields() {
	    return $this->getTable()->getGroupFields(parent::getGroupFields());
	}
	
	function getGroup() 
	{
		$group = parent::getGroup();
		if ( $group == 'OwnerUser' ) return 'Owner';
		if ( $group == 'Type' ) return 'TypeBase';
		return $group;
	}

	function getGroupNullable( $field_name, $state )
	{
		switch( $field_name ) {
			case 'DueWeeks':
			case 'TypeBase':
				return false;
			default:
				return parent::getGroupNullable( $field_name, $state );
		}
	}

	function getGroupFilterValue()
	{
		$values = array_filter($this->getFilterValues(), function($value) {
			return !in_array($value, PageTable::FILTER_OPTIONS);
		});

		$group = $this->getGroup();
		if ( !$this->getObject()->IsReference($group) ) return '';

		switch($this->getObject()->getAttributeObject($group)->getEntityRefName())
		{
			case 'pm_Version':
				return $values['release'];
			case 'pm_Release':
				return $values['iteration'];
			case 'pm_Function':
				return $values['function'];
			case 'cms_User':
				return $this->getTable()->getFilterUsers($values['owner'], $values);
			case 'Priority':
				return $values['priority'];
		}
		return '';
	}

	function getGroupSort() {
		$groupOrder = parent::getGroupSort();
		if ( $groupOrder == '' && in_array($this->getGroup(), array('PlannedRelease','Iteration','Function')) ) {
			return "D";
		}
		return $groupOrder;
	}

	function buildGroupIt()
	{
		if ( !$this->getObject()->IsReference($this->getGroup()) ) return parent::buildGroupIt();

		$groupOrder = $this->getGroupSort();
		$groupFilter = $this->getGroupFilterValue();
		$filterValues = $this->getFilterValues();

		foreach( $this->getTable()->getFilterPredicates($filterValues) as $filter ) {
			if ( $filter instanceof ProjectVpdPredicate && $filter->defined($filter->getValue())) {
				$vpd_filter = $filter;
			}
		}
		if ( !is_object($vpd_filter) ) $vpd_filter = new FilterVpdPredicate();

		switch($this->getObject()->getAttributeObject($this->getGroup())->getEntityRefName())
		{
			case 'pm_Version':
				$object = getFactory()->getObject('Release');
				$ids = array_merge(
						$object->getRegistry()->Query(
								array (
                                    $vpd_filter,
                                    !in_array($groupFilter, array('','any'))
                                            ? new FilterInPredicate(preg_split('/,/', $groupFilter))
                                            : new ReleaseTimelinePredicate('not-passed')
								)
							)->idsToArray(),
						parent::buildGroupIt()->idsToArray()
				);
				return $object->getRegistry()->Query(
					array(
						new FilterInPredicate($ids),
                        $filterValues['sortgroup'] != ''
                            ? new SortAttributeClause($filterValues['sortgroup'])
						    : new SortAttributeClause('StartDate.A')
					)
				);
			case 'pm_Release':
				$object = getFactory()->getObject('Iteration');
				$ids = array_merge(
						$object->getRegistry()->Query(
							array (
                                $vpd_filter,
                                !in_array($groupFilter, array('','any'))
                                    ? new FilterInPredicate(preg_split('/,/', $groupFilter))
                                    : new IterationTimelinePredicate(IterationTimelinePredicate::NOTPASSED),
                                new FilterAttributePredicate('Version', $filterValues['release'])
							)
						)->idsToArray(),
						parent::buildGroupIt()->idsToArray()
				);
				return $object->getRegistry()->Query(
					array(
						new FilterInPredicate($ids),
                        $filterValues['sortgroup'] != ''
                            ? new SortAttributeClause($filterValues['sortgroup'])
						    : new SortAttributeClause('StartDate.A')
					)
				);
			case 'pm_Function':
				$object = getFactory()->getObject('Feature');
				$ids = array_merge(
					$object->getRegistry()->Query(
						array (
							new FeatureStateFilter('open'),
                            new FeatureIssuesAllowedFilter(),
							$vpd_filter,
                            !in_array($groupFilter, array('','any'))
								? new FilterInPredicate(preg_split('/,/', $groupFilter)) : null
						)
					)->idsToArray(),
					parent::buildGroupIt()->idsToArray()
				);

				$sortClause = array();
                if ( $filterValues['sortgroup'] != '' ) {
                    $sortClause[] = new SortAttributeClause($filterValues['sortgroup']);
                }
                else {
                    $clause = new SortAttributeClause('Importance.A');
                    $clause->setNullOnTop(false);
                    $sortClause[] = $clause;
                    $sortClause[] = new SortProjectImportanceClause();
                }

				return $object->getRegistry()->Query(
                        array_merge(
                            array(
                                new FilterInPredicate($ids),
                            ),
                            $sortClause
                        )
                    );
			case 'cms_User':
                if ( $this->getGroup() == 'Author' ) {
                    return parent::buildGroupIt();
                }
				else {
                    $registry = new WorkerUserRegistry(getFactory()->getObject('ProjectUser'));
                    return $registry->Query(
                        array (
                            new UserTitleSortClause(),
                            !in_array($groupFilter, array('','any'))
                                ? new FilterInPredicate(preg_split('/,/', $groupFilter)) : null
                        )
                    );
                }
			case 'Priority':
				return getFactory()->getObject('Priority')->getRegistry()->Query(
					array (
                        !in_array($groupFilter, array('','any'))
							? new FilterInPredicate(preg_split('/,/', $groupFilter)) : null,
                        $filterValues['sortgroup'] != ''
                            ? new SortAttributeClause($filterValues['sortgroup'])
                            : new SortAttributeClause('OrderNum.'.$groupOrder),
					)
				);
            case 'pm_Severity':
                return getFactory()->getObject('pm_Severity')->getRegistry()->Query(
                    array (
                        !in_array($groupFilter, array('','any'))
                            ? new FilterInPredicate(preg_split('/,/', $groupFilter)) : null,
                        $filterValues['sortgroup'] != ''
                            ? new SortAttributeClause($filterValues['sortgroup'])
                            : new SortAttributeClause('OrderNum.'.$groupOrder),
                    )
                );
			default:
				return parent::buildGroupIt();
		}
	}
	
 	function getColumnVisibility( $attribute )
 	{
		if ( $attribute == 'BlockReason' ) return true;
 		return parent::getColumnVisibility( $attribute );
 	}
 	
	function getGroupBackground( $object_it, $attr_it )
	{
 		switch ( $this->getGroup() )
 		{
 			default:
 				return parent::getGroupBackground($object_it, $attr_it);
 		}
	}

	function drawHeader( $board_value, $board_title )
	{
		parent::drawHeader($board_value, $board_title);

		if ( is_object($this->taskBoardModuleIt) && count(array_intersect(preg_split('/,/',$board_value), $this->taskBoardStates)) > 0 ) {
			echo '<div class="module-link">';
				echo '<i class="icon-th"></i> ';
				echo '<a href="'.$this->taskBoardModuleIt->getUrl('issueState='.$board_value).'">'.mb_strtolower(translate('Доска задач')).'</a>';
			echo '</div>';
		}
		if ( $board_value === 'grooming' && is_object($this->groomingModuleIt) ) {
            echo '<div class="module-link">';
                echo '<i class="icon-th"></i> ';
                echo '<a href="'.$this->groomingModuleIt->getUrl().'">'.mb_strtolower($this->groomingModuleIt->getDisplayName()).'</a>';
            echo '</div>';
        }
	}

 	function drawGroup($group_field, $object_it)
	{
		switch ( $group_field )
		{
			case 'Estimation':
				echo $object_it->object->getAttributeUserName($group_field).': '.$object_it->get($group_field);
				break;
			default:
				parent::drawGroup($group_field, $object_it);
				break;
		}

		$this->getTable()->drawGroup($group_field, $object_it);
	}

	function drawRefCell( $ref_it, $object_it, $attr )
	{
		switch ( $attr )
		{
			case 'Owner':
				if ( $object_it->get($attr) < 1 || $object_it->get('OwnerPhotoId') > 0 ) return;
				echo '<div style="padding:0;overflow-y:hidden;height:16px;">';
					echo $ref_it->getDisplayName();
				echo '</div>';
				break;
				
			case 'Attachment':
			case 'Tasks':
			case 'OpenTasks':
			case 'Tags':
			case 'BlockReason':
				break;
				
			case 'Author':
				echo '<div style="overflow:hidden;height:1.7em;word-break:break-all">';
					PageList::drawRefCell( $ref_it, $object_it, $attr );
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
						$type_image = IssueTypeFrame::getIconByRefName($object_it->get('TypeReferenceName'));
						if ( $type_image != '' ) echo '<img src="/images/'.$type_image.'"> ';
						parent::drawCell( $object_it, $attr );
					echo '</div>';
					echo '<div class="right-on-card">';
						$this->drawImportantItems( $object_it );
					echo '</div>';
				echo '</div>';
				break;

			case 'Basement':
				parent::drawCell($object_it, $attr);

				$deadline_alert =
					in_array($object_it->get('State'), $this->non_terminal_states)
					&& $object_it->get('DueWeeks') < 4 && $object_it->get('Deadlines') != '';

                echo '<div class="item-footer">';
					echo '<div style="display:table-cell;text-align:left;">';
						if ( $object_it->get('OwnerPhotoId') != '' )
						{
							echo '<div class="btn-group">';
							echo $this->getRenderView()->render('core/UserPicture.php', array (
								'id' => $object_it->get('OwnerPhotoId'),
								'class' => 'user-mini',
								'image' => 'userpics-mini',
								'title' => $object_it->get('OwnerPhotoTitle')
							));
							echo '</div>';
						}
						if ( ($this->visible_column['Tasks'] || $this->visible_column['OpenTasks']) && $object_it->get('Tasks') != '' )
						{
							$states = array();

							$task_it = $this->task->createCachedIterator($this->tasks_array[$object_it->getId()]);
						 	while( !$task_it->end() )
					 		{
					 			$info = $this->task_uid_service->getUidInfo($task_it);
					 			$states[] = array (
					 					'id' => $task_it->getId(),
					 					'name' => $task_it->get('TaskTypeShortName'),
					 					'progress' => in_array($task_it->get('State'), $this->task_terminal_states) ? '100%' : '0%',
					 					'photo_id' => $task_it->get('TaskAssigneePhotoId'),
					 					'actions' => $this->getTaskActions($task_it),
					 					'url' => $info['tooltip-url']
					 			);
								$task_it->moveNext();
					 		}
					 		
							echo $this->getRenderView()->render('pm/TasksIcons.php', array (
								'states' => $states,
								'random' => $object_it->getId()
							));
						}
                        if ( $this->visible_column['TaskTypeProgress'] && $object_it->get('TaskTypeProgress') != '' )
                        {
                            $items = array();
                            foreach( \TextUtils::parseItems($object_it->get('TaskTypeProgress')) as $type ) {
                                list($title, $color) = preg_split('/:/', $type);
                                $items[] = '<span class="label" style="background-color:'.$color.'">'.$title.'</span>';
                            }
                            echo '<div style="display:inline-block;">'.join(' ', $items).'</div>';
                        }
						if ( $this->visible_column['Attachment'] && $object_it->get('Attachment') != '' )
						{
							echo '<div style="display:inline-block;">';
								parent::drawRefCell($this->getFilteredReferenceIt('Attachment', $object_it->get('Attachment')), $object_it, 'Attachment' );
							echo '</div>';
						}
						if ( $this->visible_column['Tags'] && $object_it->get('TagNames') != '' )
						{
                            $html = array();
							$tagIds = preg_split('/,/', $object_it->get('Tags'));
                            foreach( preg_split('/,/', $object_it->get('TagNames')) as $key => $name ) {
                                if ( $this->tags_url != '' ) {
                                    $name = '<a href="'.preg_replace('/%/', $tagIds[$key], $this->tags_url).'">'.$name.'</a>';
                                }
                                $html[] = '<div class="btn-group label-tag" style="display:inline-block;"><span class="label label-info">'.$name.'</span></div>';
                            }
                           	echo join(' ',$html);
				        }
						if ( !$this->visible_column['DeliveryDate'] && $deadline_alert )
						{
							echo '<div class="btn-group">';
								echo '<span class="label '.($object_it->get('DueWeeks') < 3 ? 'label-important' : 'label-warning').'">';
									echo $object_it->getDateFormattedShort('DeliveryDate');
								echo '</span>';
							echo '</div>';
						}
					echo '</div>';

					echo '<div style="display:table-cell;text-align:right;">';
						if ( !$this->uidVisible ) {
							$this->drawImportantItems( $object_it );
						}
						if ( $this->visible_column['Estimation'] )
						{
							$actions = $this->estimation_actions;
							
							foreach( $actions as $key => $action )
							{
								$method = $action['method'];
								$method->setObjectIt($object_it);
								$actions[$key]['url'] = $method->getJSCall();
							}

							$estimationValue = $this->estimation_scale[$object_it->get('Estimation')];
							if ( $estimationValue == '' ) $estimationValue = $object_it->get('Estimation');
							if ( is_numeric($estimationValue) ) $estimationValue = round($estimationValue, 1);

                            echo $this->getRenderView()->render('pm/EstimationIcon.php', array (
                                'title' => $this->estimation_title,
                                'data' => $estimationValue != '' ? $estimationValue : '0',
                                'items' => $actions,
                                'random' => $object_it->getId()
                            ));
						}

						if ( $this->visible_column['TasksPlanned'] && $object_it->get('TasksPlanned') > 0 ) {
                            echo '<div class="btn-group">';
                                echo '<span class="label label-success">'.round($object_it->get('TasksPlanned'), 1).'</span>';
                            echo '</div>';
                        }
					
						if ( $this->visible_column['Fact'] && $object_it->get('Fact') > 0 )
						{
							echo '<div class="board-item-fact" title="'.$this->spent_time_title.'">';
								if ( is_object($this->method_spend_time) ) {
									$this->method_spend_time->setAnchorIt($object_it);
									echo '<a href="'.$this->method_spend_time->getJSCall().'">'.number_format($object_it->get('Fact'), 1).'</a>';
								}
								else {
									echo number_format($object_it->get('Fact'), 1);
								}
							echo '</div>';
						}
	
						if ( $this->visible_column['RecentComment'] && $object_it->get('CommentsCount') > 0 )
						{
							echo '<div style="margin-left:4px;display: inline-block;">';
								echo $this->getRenderView()->render('pm/CommentsIconMini.php', array (
										'object_it' => $object_it
								));
							echo '</div>';
						}
					echo '</div>';
				echo '</div>';

				break;						 

			case 'OrderNum':
			case 'Estimation':
            case 'TasksPlanned':
			case 'Fact':
            case 'TaskTypeProgress':
			case 'RecentComment':
				break;

			default:
				parent::drawCell( $object_it, $attr );
		}
	}

	function drawImportantItems( $object_it )
	{
		if ( $object_it->get('BlockReason') != '' &&  $object_it->object->IsReference('BlockReason') ) {
			echo '<a class="block-sign" tabindex="-1" data-toggle="popover" data-placement="right" data-original-title="" data-content="'.$object_it->getRef('BlockReason')->getDisplayName().'"></a>';
		}

		if ( $this->visible_column['OrderNum'] && $object_it->get('OrderNum') != '' ) {
			echo '<span class="order" title="'.translate('Номер').'">';
			echo $object_it->get('OrderNum');
			echo '</span>';
		}
	}

	function getRenderParms()
	{
		$attributes = array(
			'Tasks',
			'OpenTasks',
			'Attachment',
			'RecentComment',
			'OrderNum',
			'Estimation',
			'TasksPlanned',
			'RecentComment',
			'Fact',
			'DeliveryDate',
			'Tags',
            'TaskTypeProgress'
		);
		foreach( $attributes as $column )
		{
			if ( $this->getObject()->getAttributeType($column) == '' ) continue;
			$this->visible_column[$column] = $this->getColumnVisibility($column);
		}
		$this->uidVisible = $this->getColumnVisibility('UID');

		$this->buildRelatedDataCache();

		return parent::getRenderParms();
	}
	
	function getActions( $object_it )
	{
		$actions = parent::getActions( $object_it );

		array_unshift($actions,
			array (
				'name' => translate('Открыть'),
				'url' => getSession()->getApplicationUrl($object_it) . $object_it->get('UID')
			)
		);

		$priority_actions = $this->priority_actions;
		foreach( $priority_actions as $key => $action )
		{
			if ( $object_it->get('Priority') == $key )
			{
				unset($priority_actions[$key]);
				continue;
			}
			
			$method = $priority_actions[$key]['method'];
			$method->setObjectIt($object_it);
			$priority_actions[$key]['url'] = $method->getJSCall(array());
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

        $owner_actions = $this->owner_actions;
        foreach( $owner_actions as $key => $action ) {
            if ( $object_it->get('Owner') == $key ) {
                unset($owner_actions[$key]);
                continue;
            }
            $method = $owner_actions[$key]['method'];
            $method->setObjectIt($object_it);
            $owner_actions[$key]['url'] = $method->getJSCall(array());
        }
        if ( count($owner_actions) > 1 ) {
            $pos = array_search(array('uid'=>'middle'), $actions);
            $actions = array_merge(
                array_slice($actions, 0, $pos),
                array(
                    array(),
                    array(
                        'name' => $object_it->object->getAttributeUserName('Owner'),
                        'items' => $owner_actions
                    )
                ),
                array_slice($actions, $pos)
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

		$actions[] = array (
			'name' => translate('Открыть'),
			'url' => getSession()->getApplicationUrl($object_it).'T-'.$object_it->getId()
		);

		$method = new ObjectModifyWebMethod($object_it);
		$actions[] = array (
				'name' => $method->getCaption(),
				'url' => $method->getJSCall()
		);

		if ( $this->task_transition_it->count() > 0 && $object_it->get('State') != '' )
		{
			$this->task_transition_it->moveTo('SourceStateReferenceName', $object_it->get('State'));
			$need_separator = true;

			while ( $this->task_transition_it->get('SourceStateReferenceName') == $object_it->get('State') )
			{
				$method = new TransitionStateMethod( $this->task_transition_it, $object_it );
				$method->setTargetStateRefName($this->task_target_states_array[$this->task_transition_it->getId()]);

				if ( $need_separator ) {
					$actions[] = array();
					$need_separator = false;
				}

				$actions[] = array(
					'url' => $method->getJSCall(),
					'name' => $method->getCaption()
				);
				$this->task_transition_it->moveNext();
			}
		}

        $owner_actions = $this->assignee_actions;
        foreach( $owner_actions as $key => $action ) {
            if ( $object_it->get('Assignee') == $key ) {
                unset($owner_actions[$key]);
                continue;
            }
            $method = $owner_actions[$key]['method'];
            $method->setObjectIt($object_it);
            $owner_actions[$key]['url'] = $method->getJSCall(array());
        }
        if ( count($owner_actions) > 1 ) {
            $actions = array_merge(
                $actions,
                array(
                    array(),
                    array(
                        'name' => $object_it->object->getAttributeUserName('Assignee'),
                        'items' => $owner_actions
                    )
                )
            );
        }

		return $actions;
	}
	
	function getCardColor( $object_it )
	{ 	
		$values = $this->getFilterValues();
		switch ( $values['color'] )
		{
		    case 'state':
		    	return $object_it->getStateIt()->get('RelatedColor');
		    case 'priority':
				return $object_it->get('PriorityColor');
		    case 'type':
		    	return $object_it->get('TypeColor');
		}
	}

    function getItemFormUrl() {
        return '/issues/board';
    }

    function dontGroupFirstColumn( $group ) {
        return in_array($group, array('PlannedRelease', 'Iteration', 'Owner'));
    }

    function getAppendCardTitle($boardValue, $groupValue) {
 	    switch( $this->getGroup() ) {
            case 'Type':
                return $this->getObject()->getAttributeObject('Type')->getExact($groupValue)->getDisplayName();
            case 'TypeBase':
                return $this->getObject()->getAttributeObject('Type')->getByRef('ReferenceName', $groupValue)->getDisplayName();
        }
        return '';
    }

    function getAppendCardParms($boardValue, $groupValue, $parms) {
        switch( $this->getGroup() ) {
            case 'TypeBase':
                unset($parms['TypeBase']);
                $parms['Type'] = $this->getObject()->getAttributeObject('Type')->getByRef('ReferenceName', $groupValue)->getId();
        }
        return $parms;
    }
}
