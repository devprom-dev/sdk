<?php

class ReportSpentTimeList extends PMStaticPageList
{
 	var $days_map, $activities_map, $comments_map;
 	var $user_it, $request_it, $task_it;
 	private $group = '';
    private $userReportUrl = '';

    function buildMethods()
    {
        parent::buildMethods();

        $values = $this->getFilterValues();
        $report_it = getFactory()->getObject('PMReport')->getExact('tasksplanbyfact');
        if ( $report_it->getId() != '' ) {
            $this->userReportUrl = $report_it->getUrl('taskassignee=%1&modifiedafter='.$values['start']);
        }
    }

    function getIterator()
 	{
		$object = $this->getObject();

		$predicates = array();

		$plugins = getFactory()->getPluginsManager();
 		$plugins_interceptors = is_object($plugins) ? $plugins->getPluginsForSection($this->getTable()->getSection()) : array();
		foreach( $plugins_interceptors as $plugin ) {
		    $plugin->interceptMethodListGetPredicates( $this, $predicates, $this->getFilterValues() );
		}
		
		foreach ( array_merge($predicates, $this->getTable()->getFilterPredicates()) as $predicate ) {
			$object->addFilter( $predicate );
		}
		
		$this->group = $this->getGroup();
		if ( !in_array($this->group, array('', 'none')) ) {
		    $object->setGroup($this->group);
		}

        $rows_object = $this->getRowsObject();
        $attribute = $this->getGroup();

        if ( !$object->IsReference($attribute) && $rows_object->IsReference($attribute) ) {
            $object->addAttribute(
                $attribute,
                $rows_object->getAttributeDbType($attribute),
                $rows_object->getAttributeUserName($attribute),
                false
            );
        }

        $it = $object->getAll();
        $this->days_map = $it->getDaysMap();

		$this->setupColumns();
	
		$items = array_filter($it->fieldToArray('ItemId'), function( $value ) {
		    return $value > 0;
		});
		
		$this->row_it = count($items) > 0
			? $rows_object->getRegistry()->Query( array(new FilterInPredicate($items)) )
			: $rows_object->getEmptyIterator();

        if ( $object->IsReference($this->getGroup()) ) {
            $this->report_group_it = $object->getAttributeObject($this->getGroup())->getAll();
        }
        else {
            $this->report_group_it = $rows_object->getEmptyIterator();
        }

		$it->moveFirst();
		return $it;
	}
	
	function getRowsObject()
	{
		if ( is_object($this->rows_object) ) return $this->rows_object;
		
		switch( $this->getObject()->getView() )
		{
			case 'issues':
				return getFactory()->getObject('Request');
			case 'participants':
				return getFactory()->getObject('User');
			case 'projects':
				return getFactory()->getObject('Project');
			default:
				return getFactory()->getObject('Task');
		}
	}
	
	function setupColumns()
	{
		if ( !is_array($this->days_map) ) return;
		parent::setupColumns();
	}
	
	function getColumns()
	{
        foreach( $this->days_map as $dayId => $dayName ) {
            $this->object->addAttribute('Day'.$dayId, '', $dayName, true);
        }

		$this->object->setAttributeCaption('Caption', $this->getRowsObject()->getDisplayName());
		$this->object->addAttribute('Total', '', translate('Итого'), true);

		$rowsObject = $this->getRowsObject();
		if ( $rowsObject instanceof Task ) {
            $this->object->addAttribute('Planned', 'INTEGER', $rowsObject->getAttributeUserName('Planned'), true);
        }

		return parent::getColumns();
	}

	function getGroupDefault()
	{
	}
	
	function getGroupFields()
	{
		$rows_object = $this->getRowsObject();

        $attributes = array();
        $skip_attributes = array_merge(
            $rows_object->getAttributesByGroup('system'),
            $rows_object->getAttributesByGroup('trace'),
            $rows_object->getAttributesByGroup('workflow')
        );

		if ( $rows_object instanceof Request )
		{
			foreach($rows_object->getAttributes() as $attribute => $info) {
                if ( in_array($attribute,array('Type','Attachment','Watchers','Tasks','OpenTasks','Deadlines')) ) continue;
				if ( !$rows_object->IsReference($attribute) ) continue;
				if ( in_array($attribute, $skip_attributes) ) continue;
				$attributes[$rows_object->getAttributeUserName($attribute)] = $attribute;
			}
            foreach( array('TypeBase') as $attribute ) {
                $attributes[$rows_object->getAttributeUserName($attribute)] = $attribute;
            }
			return $attributes;
		}
        elseif ( $rows_object instanceof Task ) {
            foreach($rows_object->getAttributes() as $attribute => $info) {
				if ( in_array($attribute,array('TaskType','ChangeRequest','Attachment','Watchers')) ) continue;
                if ( !$rows_object->IsReference($attribute) ) continue;
                if ( in_array($attribute, $skip_attributes) ) continue;
                $attributes[$rows_object->getAttributeUserName($attribute)] = $attribute;
            }
            foreach( array('TypeBase') as $attribute ) {
                $attributes[$rows_object->getAttributeUserName($attribute)] = $attribute;
            }
            return $attributes;
        }
		else
		{
			$fields = array('SystemUser', 'Project');
			switch( $this->getObject()->getView() ) {
				case 'participants':
					$fields = array_diff($fields, array('SystemUser'));
					break;
				case 'projects':
					$fields = array_diff($fields, array('Project'));
					break;
			}
			return $fields;
		}
	}
	
	function hasDetails()
	{
		$object = $this->getObject();
		return $object->getView() != '';
	}
	
	function IsNeedToDisplay( $attr ) 
	{
		switch ( $attr )
		{
			case 'Caption':
			case 'Total':
            case 'Planned':
				return true;
				
			default:
				return strpos($attr, 'Day') !== false;
		}
	}
	
	function getColumnFields()
	{
		return array();
	}

	function getColumnWidth( $attr ) 
	{
		if( $attr == 'Caption' )
		{
			return '';
		}
		elseif( $attr == 'Total' || $attr == 'Planned' )
		{
			return 40;
		}
		else 
		{
			return 30;
		}
	}

	function getOffsetLevel( $kind ) 
	{
		if ( $this->group == $kind ) return 0;
    	return in_array($this->group, array('', 'none')) ? 0 : 2;
	}
	
	function drawDay( $column )
	{
	    global $model_factory;
	    
	    $column = preg_replace('/Day/', '', $column);
	    
		$weekday = $model_factory->getObject('DateWeekday');
		
		$weekday_it = $weekday->getAll();
		
		$year = $this->object->getReportYear();
		
		$month = $this->object->getReportMonth();
			
		$week_day = date('w', mktime(0, 0, 0, $month, (int) $column, $year));
			
		$weekday_it->moveToPos( $week_day );
		 
		if( is_numeric($column) ) 
		{
			if ( $column == date('d') && $month == date('m') && $year == date('Y') )
			{
				echo '<span title="'.$weekday_it->getDisplayName().'"><b>'.$column.'</b></span>';
			}
			else
			{
				switch ( $week_day )
				{
					case 0:
					case 6:
						echo '<span style="color:silver;" title="'.$weekday_it->getDisplayName().'">'.$column.'</span>';
						break;
						
					default:
						echo '<span title="'.$weekday_it->getDisplayName().'">'.$column.'</span>';
				}
			}
		}	    
	}
	
	function drawHeader( $column, $title )
	{
		if ( strpos($column, 'Day') !== false )
		{
	        $this->drawDay( $column );
		}
		else
		{
			parent::drawHeader( $column, $title );
		}
	}

	function drawGroupRow( $group, $object_it, $columns )
	{
        if ( $object_it->get('Group') == '' ) return;

        echo '<td style="background-color:white;"></td>';
		foreach( $this->getObject()->getAttributes() as $attribute => $data )
		{
			if ( !in_array($attribute, array('Caption','Total')) && strpos($attribute, 'Day') === false ) continue;
			echo '<td id="'.strtolower($attribute).'" style="background-color:white;font-weight:bold;">';
				echo $this->drawCell($object_it, $attribute);
			echo '</td>';
		}
		echo '<td id="operations" style="background-color:white;font-weight:bold;"></td>';
	}
	
	function drawCell( $object_it, $attr ) 
	{
		if( $attr == 'Caption' )
		{
			if ( $object_it->get('Group') != '' ) {
					$this->report_group_it->moveToId($object_it->get('ItemId'));
					echo '<div style="padding-left:'.($this->getOffsetLevel($object_it->get('Item')) * 12).'px;">'; 
						echo $this->report_group_it->getDisplayName();

                        if ( $this->userReportUrl != '' && $this->report_group_it->object instanceof User ) {
                            echo ' &nbsp; <a target="_blank" href="'.str_replace('%1', $this->report_group_it->getId(), $this->userReportUrl).'" style="font-weight:normal;">';
                                echo text(2274);
                            echo '</a>';
                        }
    				echo '</div>';
			}
			else {
					$this->row_it->moveToId( $object_it->get('ItemId') );
					if ( $this->row_it->getId() != '' )
					{
    					$uid = new ObjectUID;
    					echo '<div class="hover-holder" style="padding-left:'.($this->getOffsetLevel($object_it->get('Item')) * 12).'px;">';
    						$uid->drawUidInCaption($this->row_it);

                            if ( $this->userReportUrl != '' && $this->row_it->object instanceof User ) {
                                echo ' &nbsp; <a class="dashed dashed-hidden" target="_blank" href="'.str_replace('%1', $this->row_it->getId(), $this->userReportUrl).'">';
                                    echo text(2274);
                                echo '</a>';
                            }
    					echo '</div>';
					}
			}
		}
		elseif ( $attr == 'Total' )	{
			if ( $object_it->get('Total') > 0 ) {
				echo round($object_it->get('Total'), 0);
			}
			else {
				echo '<span style="color:silver;">0</span>';
			}
		}
        elseif ( $attr == 'Planned' )	{
            echo round($this->row_it->get('Planned'), 0);
        }
		else {
			$hours = round($object_it->get($attr), 1);
			if ( $hours > 0 ) {
				$comment_attr = preg_replace('/Day(\d+)/', 'Comment\\1', $attr);
				$actions = array();
				if ( is_array($object_it->get($comment_attr)) ) {
					foreach ($object_it->get($comment_attr) as $task) {
						if ($task['Text'] == '') continue;
						if ( $task['Task'] > 0 ) {
                            $actions[$task['Task']] = array(
                                'url' => $this->getUidService()->getObjectUrl('T-' . $task['Task']),
                                'name' => 'T-' . $task['Task'] . ' ' . substr($task['Text'], 0, 120)
                            );
                        }
                        else {
                            $actions[$task['Issue']] = array(
                                'url' => $this->getUidService()->getObjectUrl('I-' . $task['Issue']),
                                'name' => 'I-' . $task['Issue'] . ' ' . substr($task['Text'], 0, 120)
                            );
                        }
					}
				}
				if ( count($actions) > 0 ) {
					echo $this->getRenderView()->render('core/SpentTimeMenu.php', array (
						'title' => $hours,
						'items' => $actions,
						'id' => $object_it->getId().$attr
					));
				}
				else {
					echo $hours;
				}
			}
			else {
				echo '<span style="color:#dfdfdf;">0</span>';
			}
			echo '&nbsp;';
		}
	}

	function getColumnAlignment ( $attr )
	{
		if ( is_numeric($attr) )
		{
			return 'right';
		}
		
		return parent::getColumnAlignment ( $attr );
	}

	function IsNeedToDisplayRow($object_it)
	{
		return $object_it->get('Group') == '' || in_array($this->group, array('','none'));
	}

	function getRowBackgroundColor( $object_it )
	{
		return $object_it->get('Group') != '' ? '#F6F3FE' : 'white';
	}	
}