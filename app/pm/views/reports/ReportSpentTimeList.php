<?php

class ReportSpentTimeList extends PMStaticPageList
{
 	var $days_map, $activities_map, $comments_map;
 	var $user_it, $request_it, $task_it;
 	
 	function getIterator() 
 	{
 		global $model_factory, $_REQUEST;

		$object = $this->getObject();

		$predicates = array();
		
		$plugins = getSession()->getPluginsManager();
		
 		$plugins_interceptors = is_object($plugins) ? $plugins->getPluginsForSection($this->getTable()->getSection()) : array();
		
		foreach( $plugins_interceptors as $plugin )
		{
		    $plugin->interceptMethodListGetPredicates( $this, $predicates, $this->getFilterValues() );
		}
		
		foreach ( array_merge($predicates, $this->getTable()->getFilterPredicates()) as $predicate )
		{
			$object->addFilter( $predicate );
		}
		
		if ( !in_array($_REQUEST['group'], array('', 'none')) )
		{
		    $object->setGroup($_REQUEST['group']);
		}
		
		$it = $object->getAll();

		$this->days_map = $it->getDaysMap();
		$this->activities_map = $it->getActivitiesMap();
		$this->comments_map = $it->getCommentsMap();

		$this->setupColumns();
		
		$user = $model_factory->getObject('cms_User');

		$items = array_filter($it->fieldToArray('SystemUser'), function( $value ) {
		    return $value != '';
		});
		
		$this->user_it = count($items) > 0 ? $user->getExact($items) : $user->getEmptyIterator();

		$items = array_filter($it->fieldToArray('ItemId'), function( $value ) {
		    return $value > 0;
		});
		
		$request = getFactory()->getObject('pm_ChangeRequest');
		
		$this->request_it = count($items) > 0 
			? $request->getRegistry()->Query( array(new FilterInPredicate($items)) )
			: $request->getEmptyIterator();

		$task = getFactory()->getObject('pm_Task');

		$this->task_it = count($items) > 0 
			? $task->getRegistry()->Query( array(new FilterInPredicate($items)) ) 
			: $task->getEmptyIterator();
		
		$it->moveFirst();
		
		return $it;
	}
	
	function setupColumns()
	{
		if ( !is_array($this->days_map) )
		{
			return;
		}
		
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

		$method = new ViewSpentTimeWebMethod();
		
		$method->setFilter( $this->getFiltersName() );
		
		$values = $method->getValues();
		
		$this->object->setAttributeCaption('Caption', $values[$method->getValue()]);
		
		$this->object->addAttribute('Total', '', translate('Итого'), true);

		return parent::getColumns();
	}

	function getGroup()
	{
	    return '';
	}
	
	function getGroupFields()
	{
		return array('SystemUser', 'Project');
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
		switch ( $kind )
		{
			case 'Participant':
				return 0;
		    
		    case 'Project':
				return 0;

			case 'Task':
			case 'ChangeRequest':
				return in_array($_REQUEST['group'], array('', 'none')) ? 0 : 2;
				
			default:
				return parent::getOffsetLevel( $kind );
		}
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
		global $model_factory;
		
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

	function drawCell( $object_it, $attr ) 
	{
		global $model_factory;
		
		if( $attr == 'Caption' ) 
		{
			switch ( $object_it->get('Item') )
			{
				case 'Project':
					$project = $model_factory->getObject('pm_Project');
					
					$project_it = $project->getExact($object_it->get('ItemId')); 
					echo $project_it->getDisplayName();
					
					break;
			    
			    case 'Participant':
				    $this->user_it->moveToId( $object_it->get('ItemId') );
					
					if ( $this->hasDetails() )
					{
						echo '<b>'.$this->user_it->getDisplayName().'</b>';
					}
					else
					{
						echo $this->user_it->getDisplayName();
					}
					
					break;

				case 'Task':
				    $this->task_it->moveToId( $object_it->get('ItemId') );
				    
					if ( $this->task_it->getId() != '' )
					{
    					$uid = new ObjectUID;
    					
    					echo '<div style="padding-left:'.($this->getOffsetLevel($object_it->get('Item')) * 12).'px;">';
    						$uid->drawUidInCaption($this->task_it);
    					echo '</div>';
					}
					
					break;

				case 'ChangeRequest':
					if ( $object_it->get('ItemId') == '' )
					{
						echo '<div style="padding-left:'.($this->getOffsetLevel($object_it->get('Item')) * 12).'px;">';
							echo text(756);
						echo '</div>';
					}
					else
					{
				        $this->request_it->moveToId($object_it->get('ItemId')); 
	
						$uid = new ObjectUID;
						echo '<div style="padding-left:'.($this->getOffsetLevel($object_it->get('Item')) * 12).'px;">';
							$uid->drawUidInCaption($this->request_it);
						echo '</div>';
					}
					
					break;
			}
		}
		elseif ( $attr == 'Total' )
		{
			foreach( preg_split('/,/', $object_it->get('SystemUser')) as $user_id )
			{
    			$items = $this->activities_map[$object_it->get('Item').$object_it->get('ItemId')][$user_id];
				
			    $total += is_array($items) ? array_sum($items) : 0;
			}
			
			if ( $total > 0 )
			{
				if ( in_array($object_it->get('Item'), array('Participant', 'Project')) && ($this->hasDetails()) )
				{
					echo '<b>'.$total.'</b>';
				}
				else
				{
					echo $total;
				}
			}
			else
			{
				echo '<span style="color:silver;">0</span>';
			}
		}
		else
		{
			$attr = preg_replace('/Day/', '', $attr);
			
			foreach( preg_split('/,/', $object_it->get('SystemUser')) as $user_id )
			{
    			$hours += $this->activities_map[$object_it->get('Item').$object_it->get('ItemId')][$user_id][$attr];
			}
			
			if ( $hours > 0 )
			{
				if ( in_array($object_it->get('Item'), array('Participant', 'Project')) && ($this->hasDetails()) )
				{
					echo '<b>'.$hours.'</b>';
				}
				else
				{
					echo $hours;
				}
				
			}
			else
			{
				echo '<span style="color:#dfdfdf;">0</span>';
			}
			
			echo '&nbsp;';
		}
	}

	function getCellComment ( $object_it, $attr )
	{
		$attr = preg_replace('/Day/', '', $attr);

		if ( is_numeric($attr) )
		{
			$comments = $this->comments_map[$object_it->get('Item').
				$object_it->get('ItemId')][$object_it->get('SystemUser')][$attr];
				
			return $comments;
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

	function getRowBackgroundColor( $object_it )
	{
		return $object_it->get('Item') == $_REQUEST['group'] ? '#F6F3FE' : 'white'; 
	}	
}