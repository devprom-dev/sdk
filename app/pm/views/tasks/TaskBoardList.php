<?php
include_once "TaskProgressFrame.php";
include_once "TaskBalanceFrame.php";
include_once SERVER_ROOT_PATH.'core/views/c_issue_type_view.php';
include_once SERVER_ROOT_PATH."pm/methods/CommentWebMethod.php";

class TaskBoardList extends PMPageBoard
{
 	private $priorities_array = array();
 	private $visible_column = array();
 	private $method_comment = null;
 	private $priority_actions = array();
 	private $terminal_states = array();
 	private $method_spend_time = null;
	private $uidVisible = true;
	private $planned_actions = array();
	private $planned_title = '';
 	
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
 		if ( $method->hasAccess() ) {
 			$this->method_comment = $method;
 		}

	 	$method = new SpendTimeWebMethod($object_it);
 		if ( $method->hasAccess() ) {
 			$this->method_spend_time = $method;
 		}

		if ( getSession()->getProjectIt()->getMethodologyIt()->TaskEstimationUsed() )
		{
			$strategy = new EstimationHoursStrategy();
			foreach( $strategy->getScale() as $item ) {
				$method = new ModifyAttributeWebMethod($object_it, 'Planned', $item);
				if ( $method->hasAccess() ) {
					$method->setCallback( "donothing" );
					$this->planned_actions[] = array(
						'name' => ' '.$item,
						'method' => $method
					);
				}
			}
			$this->planned_title = $this->getObject()->getAttributeUserName('Planned');
		}

        $info = $this->getTable()->getPage()->getPageWidgetNearestUrl();
        if ( is_object($info['widget']) ) {
            $this->tags_url = $info['widget']->getUrl('tag=%');
        }
	}

    function getGroupObject() {
        $object = parent::getGroupObject();
        $object->addAttributeGroup('Estimation', 'display-name');
        return $object;
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
	
    function extendModel()
    {
        $attrs = $this->getObject()->getAttributes();
        if ( array_key_exists( 'Planned', $attrs ) ) {
            $this->getObject()->addAttribute( 'Progress', '', translate('Прогресс'), true );
            $this->getObject()->addAttributeGroup('Progress', 'workload');
        }

        parent::extendModel();
    }

	function getGroupDefault()
	{
        if ( $this->getProjectIt()->count() > 1 ) return 'Project';
		return 'Assignee';
	}

	function getGroupNullable( $field_name, $state )
	{
		switch( $field_name ) {
			case 'DueWeeks':
				return false;
			default:
				return parent::getGroupNullable( $field_name, $state );
		}
	}

	function getGroupFields() 
	{
		$fields = array_diff(
		    parent::getGroupFields(),
            array('Spent', 'Watchers', 'Attachment', 'TraceTask', 'IssueAttachment')
        );

		$fields[] = 'Planned';

		if ( $this->getObject()->hasAttribute('Requirement') ) {
            $fields[] = 'Requirement';
        }
		return $fields;
	}
	
	function getGroup() 
	{
		$group = parent::getGroup();
		if ( $group == 'AssigneeUser' ) return 'Assignee';
        if ( $group == 'TaskType' ) return 'TaskTypeBase';
		return $group;
	}

	function getGroupFilterValue()
	{
		$values = array_filter($this->getTable()->getPredicateFilterValues(), function($value) {
			return !in_array($value, PageTable::FILTER_OPTIONS);
		});

        $group = $this->getGroup();
		if ( !$this->getObject()->IsReference($group) ) return '';

		switch($this->getObject()->getAttributeObject($group)->getEntityRefName())
		{
			case 'pm_Release':
				return $values['iteration'];
			case 'cms_User':
				return $this->getTable()->getFilterUsers($values['taskassignee'], $values);
			case 'Priority':
				return $values['taskpriority'];
		}
		return '';
	}

	function buildGroupIt()
	{
		if ( !$this->getObject()->IsReference($this->getGroup()) ) return parent::buildGroupIt();

		$groupOrder = $this->getGroupSort();
		$groupFilter = $this->getGroupFilterValue();
        $values = $this->getFilterValues();

        if ( $groupFilter == 'any' ) return parent::buildGroupIt();

        foreach( $this->getTable()->getFilterPredicates($values) as $filter ) {
            if ( $filter instanceof ProjectVpdPredicate && $filter->defined($filter->getValue()) ) {
                $vpd_filter = $filter;
            }
        }
        if ( !is_object($vpd_filter) ) $vpd_filter = new FilterVpdPredicate();

		switch($this->getObject()->getAttributeObject($this->getGroup())->getEntityRefName())
		{
			case 'pm_Version':
				$object = getFactory()->getObject('Release');
				$ids = array_merge(
					$object->getRegistry()->QueryKeys(
						array (
							$vpd_filter,
							!in_array($groupFilter, array(''))
								? new FilterInPredicate(preg_split('/,/', $groupFilter))
                                : new ReleaseTimelinePredicate('not-passed')
						)
					)->idsToArray(),
					parent::buildGroupIt()->idsToArray()
				);
				return $object->getRegistry()->Query(
					array(
						new FilterInPredicate($ids),
                        $values['sortgroup'] != ''
                            ? new SortAttributeClause($values['sortgroup'])
                            : new SortAttributeClause('StartDate.'.$groupOrder)
					)
				);
			case 'pm_Release':
				$object = getFactory()->getObject('Iteration');
				$ids = array_merge(
						$object->getRegistry()->QueryKeys(
								array (
									$vpd_filter,
                                    !in_array($groupFilter, array(''))
										? new FilterInPredicate(preg_split('/,/', $groupFilter))
                                        : new IterationTimelinePredicate(IterationTimelinePredicate::NOTPASSED)
								)
							)->idsToArray(),
						parent::buildGroupIt()->idsToArray()
				);
				return $object->getRegistry()->Query(
					array(
						new FilterInPredicate($ids),
                        $values['sortgroup'] != ''
                            ? new SortAttributeClause($values['sortgroup'])
						    : new SortAttributeClause('StartDate.'.$groupOrder)
					)
				);
			case 'cms_User':
                $registry = new WorkerUserRegistry(getFactory()->getObject('ProjectUser'));
				return $registry->Query(
						array (
                            new UserTitleSortClause(),
                            !in_array($groupFilter, array(''))
                                    ? new FilterInPredicate(preg_split('/,/', $groupFilter)) : null
						)
					);
			case 'Priority':
				return getFactory()->getObject('Priority')->getRegistry()->Query(
						array (
                            !in_array($groupFilter, array(''))
								? new FilterInPredicate(preg_split('/,/', $groupFilter)) : null
						)
				    );
            case 'pm_Project':
                return PageList::buildGroupIt();
            default:
				return parent::buildGroupIt();
		}
	}
	
 	function drawRefCell( $ref_it, $object_it, $attr )
 	{
 		switch ( $attr )
 		{
 		    case 'Assignee':
 		    case 'Attachment':
            case 'Tags':
 		        break;
 		        
 		    default:
				echo '<div class="brd-rv">';
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
			case 'Planned':
				break;

			case 'CaptionNative':
				if ( $object_it->get('TaskTypeDisplayName') != '' ) {
					echo $object_it->get('TaskTypeDisplayName').': ';
				}
				parent::drawCell($object_it, $attr);
				break;

			case 'IssueTraces':
				$this->getTable()->drawCell( $object_it, $attr );
				break;

            case 'IssueState':
                echo $this->getRenderView()->render('pm/StateColumn.php', array (
                    'stateIt' => $object_it->getRef('ChangeRequest')->getStateIt(),
                    'terminal' => false
                ));
                break;

			case 'UID':
				echo '<div class="title-on-card">';
					echo '<div class="left-on-card">';
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
                parent::drawCell($object_it, $attr);

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
						if ( $object_it->get('PlannedFinishDate') != '' && $object_it->get('DueWeeks') < 4 )
						{
							echo ' <div class="btn-group">';
								echo '<span class="label '.($object_it->get('DueWeeks') < 3 ? 'label-important' : 'label-warning').'">';
									echo $object_it->getDateFormattedShort('PlannedFinishDate');
								echo '</span>';
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
						if ( $this->visible_column['Attachment'] )
						{
							echo '<div class="btn-group" style="display:inline-block;">';
								parent::drawRefCell($this->getFilteredReferenceIt('Attachment', $object_it->get('Attachment')), $object_it, 'Attachment' );
							echo '</div>';
						}
					echo '</div>';
					
					echo '<div style="display:table-cell;text-align:right;">';
						if ( ($this->visible_column['Progress'] || $this->visible_column['Planned']) and count($this->planned_actions) > 0 )
						{
							$actions = $this->planned_actions;
							foreach( $actions as $key => $action ) {
								$method = $action['method'];
								$method->setObjectIt($object_it);
								$actions[$key]['url'] = $method->getJSCall();
							}

							echo $this->getRenderView()->render('pm/EstimationIcon.php', array (
								'title' => $this->planned_title,
								'data' => $object_it->get('Planned') != '' ? round($object_it->get('Planned'),1) : '0',
								'items' => $actions,
								'random' => $object_it->getId()
							));
						}
						if ( $this->visible_column['Fact'] && $object_it->get('Fact') > 0 )
						{
							echo '<div class="board-item-fact" title="'.$this->spent_time_title.'">';
								if ( is_object($this->method_spend_time) ) {
									$this->method_spend_time->setAnchorIt($object_it);
									echo '<a href="'.$this->method_spend_time->getJSCall().'">'.round($object_it->get('Fact'),1).'</a>';
								}
								else {
									echo $object_it->get('Fact');
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
								
			default:
				parent::drawCell( $object_it, $attr );
		}
	}

	function drawGroup($group_field, $object_it)
	{
		switch ( $group_field )
		{
			case 'Planned':
				echo $object_it->object->getAttributeUserName($group_field).': '.$object_it->get($group_field);
				break;
			default:
				parent::drawGroup($group_field, $object_it);
		}

		$this->getTable()->drawGroup($group_field, $object_it);
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
	
	function getCardColor( $object_it )
	{ 	
		$values = $this->getFilterValues();
		switch ( $values['color'] ) {
			case 'state':
				return $object_it->getStateIt()->get('RelatedColor');
			case 'priority':
				return $object_it->get('PriorityColor');
			case 'type':
				return $object_it->get('TypeColor');
		}
	}

	function getActions( $object_it )
	{
		$actions = parent::getActions( $object_it );

		if ( !$this->uidVisible ) {
			array_unshift($actions,
				array (
					'name' => translate('Открыть'),
					'url' => getSession()->getApplicationUrl().'T-'.$object_it->getId()
				)
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
		
		$priority_actions = $this->priority_actions;
		foreach( $priority_actions as $key => $action ) {
			if ( $object_it->get('Priority') == $key )
			{
				unset($priority_actions[$key]);
				continue;
			}
			$method = $priority_actions[$key]['method'];
			$method->setObjectIt($object_it);
			$priority_actions[$key]['url'] = $method->getJSCall();
		}
		if ( count($priority_actions) > 0 ) {
			$actions[] = array();
			$actions[] = array(
					'name' => translate('Приоритет'),
					'items' => $priority_actions
			);
		}

		return $actions;
	}

    function getItemFormUrl() {
        return '/tasks/board';
    }

	function getRenderParms()
	{
 		$this->buildRelatedDataCache();
		
		$parms = parent::getRenderParms();
		
		foreach( array( 'Attachment', 'RecentComment', 'Fact', 'OrderNum', 'Planned', 'Tags') as $column )
		{
			if ( $this->getObject()->getAttributeType($column) == '' ) continue;
			$this->visible_column[$column] = $this->getColumnVisibility($column);
		}
		$this->visible_column['Progress'] = $this->getColumnVisibility('Progress');
		$this->uidVisible = $this->getColumnVisibility('UID');
		
		return $parms; 
	}

    function dontGroupFirstColumn( $group ) {
        return in_array($group, array('Assignee'));
    }

    function getAppendCardTitle($boardValue, $groupValue) {
        switch( $this->getGroup() ) {
            case 'TaskTypeBase':
                return $this->getObject()->getAttributeObject($this->getGroup())->getExact($groupValue)->getDisplayName();
        }
        return '';
    }

    function getAppendCardParms($boardValue, $groupValue, $parms) {
        switch( $this->getGroup() ) {
            case 'TaskTypeBase':
                unset($parms['TaskTypeBase']);
                $parms['TaskType'] = $this->getObject()->getAttributeObject('TaskType')->getByRef('ReferenceName', $groupValue)->getId();
        }
        return $parms;
    }
}