<?php

class ReportSpentTimeList extends PMStaticPageList
{
 	var $days_map, $activities_map, $comments_map;
 	var $user_it, $request_it, $task_it;
 	private $group = '';
 	
 	function getIterator() 
 	{
		$object = $this->getObject();

		$predicates = array();
		
		$plugins = getSession()->getPluginsManager();
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
	    global $model_factory;
	    
		if ( count($this->days_map) > 12 )
		{
		    for ( $i = 0; $i < count($this->days_map); $i++ )
    		{
    			$this->object->addAttribute('Day'.$this->days_map[$i], '', $this->days_map[$i], true);
    		}
		}
		elseif ( count($this->days_map) == 12 ) 
		{
    		$date = $model_factory->getObject('DateMonth');
		    
			$date_it = $date->getAll();
			
			while( !$date_it->end() )
			{
    			$this->object->addAttribute(
                    'Day'.$this->days_map[$date_it->getId()-1], '', $date_it->getDisplayName(), true
    			);
    			
			    $date_it->moveNext();
			}
		}
		else
		{
		    for ( $i = 0; $i < count($this->days_map); $i++ )
    		{
    			$this->object->addAttribute('Day'.$this->days_map[$i], '', $this->days_map[$i], true);
    		}
		}

		$this->object->setAttributeCaption('Caption', $this->getRowsObject()->getDisplayName());
		$this->object->addAttribute('Total', '', translate('Итого'), true);

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
                if ( $attribute == 'Type' ) continue;
				if ( $attribute == 'Attachment' ) continue;
				if ( $attribute == 'Watchers' ) continue;
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
                if ( $attribute == 'TaskType' ) continue;
                if ( $attribute == 'ChangeRequest' ) continue;
                if ( $attribute == 'Attachment' ) continue;
                if ( $attribute == 'Watchers' ) continue;
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
			return array('SystemUser', 'Project');
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
			return 200;
		}
		elseif( $attr == 'Total' ) 
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
	
	function drawMonth( $column )
	{
	    parent::drawHeader( $column );
	}
	
	function drawHeader( $column )
	{
		if ( strpos($column, 'Day') !== false )
		{
		    if ( count($this->days_map) > 12 )
		    {
		        $this->drawDay( $column );
		    }
		    else
		    {
		        $this->drawMonth( $column );
		    }
		}
		else
		{
			parent::drawHeader( $column );
		}
	}

	function drawGroupRow( $group, $object_it, $columns )
	{
        if ( $object_it->get('Group') == '' ) return;

		foreach( $this->getObject()->getAttributes() as $attribute => $data )
		{
			if ( !in_array($attribute, array('Caption','Total')) && strpos($attribute, 'Day') === false ) continue;
			echo '<td id="'.strtolower($attribute).'" style="background-color:white;font-weight:bold;">';
				echo $this->drawCell($object_it, $attribute);
			echo '</td>';
		}
	}
	
	function drawCell( $object_it, $attr ) 
	{
		if( $attr == 'Caption' )
		{
			if ( $object_it->get('Group') != '' ) {
					$this->report_group_it->moveToId($object_it->get('ItemId'));
					echo '<div style="padding-left:'.($this->getOffsetLevel($object_it->get('Item')) * 12).'px;">'; 
						echo $this->report_group_it->getDisplayName();
    				echo '</div>';
			}
			else {
					$this->row_it->moveToId( $object_it->get('ItemId') );
					if ( $this->row_it->getId() != '' )
					{
    					$uid = new ObjectUID;
    					echo '<div style="padding-left:'.($this->getOffsetLevel($object_it->get('Item')) * 12).'px;">';
    						$uid->drawUidInCaption($this->row_it);
    					echo '</div>';
					}
			}
		}
		elseif ( $attr == 'Total' )	{
			if ( $object_it->get('Total') > 0 ) {
				echo $object_it->get('Total');
			}
			else {
				echo '<span style="color:silver;">0</span>';
			}
		}
		else {
			$hours = $object_it->get($attr);
			if ( $hours > 0 ) {
				$comment_attr = preg_replace('/Day(\d+)/', 'Comment\\1', $attr);
				$actions = array();
				if ( is_array($object_it->get($comment_attr)) ) {
					foreach ($object_it->get($comment_attr) as $task) {
						if ($task['Text'] == '') continue;
						$actions[] = array(
							'url' => $this->getUidService()->getObjectUrl('T-' . $task['Task']),
							'name' => 'T-' . $task['Task'] . ' ' . substr($task['Text'], 0, 120)
						);
					}
				}
				if ( count($actions) > 0 ) {
					echo $this->getTable()->getView()->render('core/SpentTimeMenu.php', array (
						'title' => $hours,
						'items' => $actions
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

	function IsNeedToDisplayNumber()
	{
		return false;
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