<?php
 
include_once SERVER_ROOT_PATH.'core/classes/schedule/DateMonth.php';
 
class ResourceList extends PMStaticPageList
{
 	var $usage_it, $workload_it, $scale, $year, $month, $format, $cache_it, $group_it;
 	
 	function __construct( $object, $scale = 'month' )
 	{
 		$this->scale = $scale;
 		
 		parent::__construct( $object );
 	}
 	
 	function getBaseObject()
 	{
		$filter_values = $this->getFilterValues();
		
		switch ( $filter_values['viewpoint'] )
		{
			case 'roles':
				$cache = getFactory()->getObject('ProjectRoleBase');
				break;
				
			case 'projects':
				$cache = getFactory()->getObject('EEProject');
				break;

			case 'users':
			default:
				$cache = getFactory()->getObject('EEUser');
		}
		
		return $cache;
 	}
 	
 	function getIterator() 
 	{
 		global $model_factory, $_REQUEST;

 		$cache = $this->getBaseObject();
		$filter_values = $this->getFilterValues();
 		
		switch ( $filter_values['viewpoint'] )
		{
			case 'roles':
				$group = getFactory()->getObject('ProjectRoleBase');
				break;
				
			case 'projects':
				$group = getFactory()->getObject('EEProject');
				$group->addFilter( new FilterInPredicate($this->getProjects()) );
				
				break;

			case 'users':
				$group = getFactory()->getObject('EEUser');
				break;
				
			default:
		}
		
		if ( is_object($group) && $group->getClassName() != $cache->getClassName() )
		{
			$this->group_it = $group->getAll();
		}
		 
		
		$this->year = $filter_values['year']; 
		if ( $this->year == '' )
		{
			$this->year = date('Y');
		}
		
		$this->month = $filter_values['month']; 

		$this->format = $filter_values['format']; 
 		$resource = $model_factory->getObject('HumanResource');

		$predicates = $this->getPredicates( $this->getFilterValues() );

		foreach ( $predicates as $predicate )
		{
			$resource->addFilter( $predicate );
		}

 		$this->usage_it = $resource->getUsageByInterval( 
 			$this->scale, $this->year, 
 				$this->month, strtolower(get_class($cache)),
 					strtolower(get_class($this->group_it->object))
 		);

		$this->usage_data = array();
		$this->group_data = array();
		
		$row_ids = array();
 		$divider_ids = array();

		while ( !$this->usage_it->end() )
		{
			if ( is_object($this->group_it) )
			{
				if ( !is_array( $this->group_data[$this->usage_it->get('GroupId')][$interval] ) )
				{
					$this->group_data[$this->usage_it->get('GroupId')][$interval]['duration'] = 0;
					$this->group_data[$this->usage_it->get('GroupId')][$interval]['workloadduration'] = 0;
					$this->group_data[$this->usage_it->get('GroupId')][$interval]['workloadactual'] = 0;
					$this->group_data[$this->usage_it->get('GroupId')][$interval]['tasks'] = '';
				}
				
				$row = $this->usage_it->get('GroupId').','.$this->usage_it->get('RowId');
			}
			else
			{
				$row = $this->usage_it->get('RowId');
			}

			$interval = $this->usage_it->get('IntervalCaption');
			
			if ( $this->usage_it->get('UsageDuration') > 0 )
			{
				$this->usage_data[$row][$interval]['maxduration'] = 
					$this->usage_it->get('MaxUsageDuration'); 
				
				$this->usage_data[$row][$interval]['duration'] = 
					$this->usage_it->get('UsageDuration'); 

				$usage = round(
					$this->usage_it->get('UsageDuration')  / 
						$this->usage_it->get('MaxUsageDuration') * 100, 0);

				$this->usage_data[$row][$interval]['usage'] = $usage; 
			}

			if ( $this->usage_it->get('WorkloadDuration') > 0 || $this->usage_it->get('WorkloadActual') > 0 )
			{
				$this->usage_data[$row][$interval]['workloadduration'] = 
					$this->usage_it->get('WorkloadDuration'); 

				$this->usage_data[$row][$interval]['workloadactual'] = 
					$this->usage_it->get('WorkloadActual'); 

				$this->usage_data[$row][$interval]['tasks'] = 
					$this->usage_it->get('Tasks'); 
					
				$workload = round(
					$this->usage_it->get('WorkloadDuration')  / 
						$this->usage_it->get('MaxUsageDuration') * 100, 0);

				$this->usage_data[$row][$interval]['workload'] = $workload; 
			}

			$has_data = $this->usage_data[$row][$interval]['duration'] > 0 
				|| $this->usage_data[$row][$interval]['workloadduration'] > 0 
				|| $this->usage_data[$row][$interval]['workloadactual'] > 0;
				
			if ( $has_data )
			{
				array_push( $row_ids, $this->usage_it->get('RowId') );
			}

			if ( is_object($this->group_it) && $has_data )
			{
				array_push( $divider_ids, $this->usage_it->get('GroupId') );
				
				$this->group_data[$this->usage_it->get('GroupId')][$interval]['duration'] +=
					$this->usage_it->get('UsageDuration');

				$this->group_data[$this->usage_it->get('GroupId')][$interval]['workloadduration'] +=
					$this->usage_it->get('WorkloadDuration');

				$this->group_data[$this->usage_it->get('GroupId')][$interval]['workloadactual'] +=
					$this->usage_it->get('WorkloadActual');

				$this->group_data[$this->usage_it->get('GroupId')][$interval]['tasks'] =
					join(',', array( $this->usage_it->get('Tasks'), 
						$this->group_data[$this->usage_it->get('GroupId')][$interval]['tasks']));
			}
			
			$this->usage_it->moveNext();
		}

 		$cache->addFilter(  new FilterInPredicate( join(array_unique($row_ids),',') ) );
 		
		$this->cache_it = $cache->getAll();

		return $this->object->getAll(
			strtolower(get_class($cache)), 
			$this->cache_it->idsToArray(), 
			strtolower(get_class($this->group_it->object)),
			array_unique($divider_ids)
		);
	}
	
	function getProjects()
	{
		$ids = array(getSession()->getProjectIt()->getId());
		
		if ( getSession()->getProjectIt()->get('LinkedProject') != '' )
		{
			$ids = array_merge( $ids, preg_split('/,/', getSession()->getProjectIt()->get('LinkedProject'))); 
		}
		
		return $ids;
	}
	
 	function getPredicates( $values )
 	{
 		return array (
 			new ResourceUsageProjectPredicate( $this->getProjects() ),
 			new ResourceUsageUserPredicate( $values['usergroup'] )
		);
 	}
 	
	function getColumns()
	{
		$columns = array();
		$titles = array();
		
		switch ( $this->scale )
		{
			case 'week':
				$week = date('W');

				for ( $i = 0; $i < 52; $i++ )
				{
					if ( $this->month != '' && round($i / 4, 0) != $this->month )
					{
						continue;
					}

					array_push($columns, $i + 1);

					if ( $week == $i + 1)
					{
						array_push($titles, '<b>'.str_pad( $i + 1, 2, '0', STR_PAD_LEFT).'</b>');
					}
					else
					{
						array_push($titles, str_pad( $i + 1, 2, '0', STR_PAD_LEFT));
					}
				}
				break;
			
			case 'month':
				$month = date('m');

				$date = new DateMonth;
				$date_it = $date->getAll();
				
				for ( $i = 0; $i < 12; $i++ )
				{
					array_push($columns, $i + 1);
					
					$date_it->moveToPos( $i );
					if ( $i + 1 == $month )
					{
						array_push($titles, '<b>'.$date_it->getDisplayName().'</b>');
					}
					else
					{
						array_push($titles, $date_it->getDisplayName());
					}
				}
				break;

			case 'quarter':
			default:
				$month = date('m');
				array_push($columns, '1', '2', '3', '4');

				foreach ( $columns as $column )
				{
					if ( $column == ceil($month / 3) )
					{
						array_push($titles, '<b>Q'.$column.'</b>');
					}
					else
					{
						array_push($titles, 'Q'.$column);
					}
				}
		}
		
		foreach ( $columns as $key => $column )
		{
			$this->object->addAttribute('Period'.$column, '', $titles[$key], true);
		}
		
		return parent::getColumns();
	}

	function getColumnAlignment( $attr )
	{
		if ( strpos($attr, 'Period') !== false )
		{
			return 'center';
		}
		else
		{
			return parent::getColumnAlignment( $attr );
		}
	}
	
	function IsNeedToDisplay( $attr ) 
	{
		switch( $attr ) 
		{
			default:
				if ( strpos($attr, 'Period') !== false )
				{
					return true;
				}
		}
		
		return false;
	}

	function drawGroup($group_field, $object_it)
	{
	    global $model_factory;
	    
		$module = $model_factory->getObject('Module');
	    
		if ( $object_it->get('ResourceClass') == 'Group' )
		{
			$this->group_it->moveToId( $object_it->get('ResourceId') );
			
			echo '<b>'.$this->group_it->getDisplayName().'</b>';
		}
		else
		{
			if ( is_object($this->group_it) )
			{
				echo '<div style="padding-left:12px;">';
			}
			else
			{
				echo '<div>';
			}
			
			$this->cache_it->moveToId( $object_it->get('ResourceId') );

			$module = $module->getExact('resman/resourceload');
			
			switch ( $this->cache_it->object->getEntityRefName() )
			{
				case 'pm_Project':
				    
					echo '<a href="/pm/'.$this->cache_it->get('CodeName').'/module/resman/resourceload/resourceusage?viewpoint=users">'.$this->cache_it->getDisplayName().'</a>';
					
					break;

				case 'pm_ProjectRole':
					
				    $menu = $module->buildMenuItem('?role='.$this->cache_it->getId());
				
				    echo '<a href="'.$menu['url'].'">'.$this->cache_it->getDisplayName().'</a>';
				    
					break;
				    
				case 'cms_User':

				    $menu = $module->buildMenuItem('?user='.$this->cache_it->getId());
				    
				    echo '<a href="'.$menu['url'].'">'.$this->cache_it->getDisplayName().'</a>';
				    
					break;
				    
			    default:
			        
					echo $this->cache_it->getDisplayName();
			}
			
			echo '</div>';
		}
	}
	
	function drawCell( $object_it, $attr )
	{
		$attr = preg_replace('/Period/', '', $attr );

		$row = is_object($this->group_it)
			? $object_it->get('GroupId').','.$object_it->get('ResourceId') : $object_it->get('ResourceId');
		
		switch ( $this->format )
		{
			case 'hours':
				if ( $object_it->get('ResourceClass') == 'Group' )
				{
					$this->drawHours( $object_it->get('GroupId'), $this->group_data, $attr );
				}
				else
				{
					$this->drawHours( $row, $this->usage_data, $attr );
				}
				break;
				
			default:
				if ( $object_it->get('ResourceClass') == 'Group' )
				{
				}
				else
				{
					$this->drawGraphical( $row, $this->usage_data, $attr );
				}
		}
	}

	function drawGraphical( $id, &$data, $attr )
	{
		$usage = $data[$id][$attr]['usage'];
		$workload = $data[$id][$attr]['workload'];

		if ( $usage > 0 || $workload > 0 )
		{
			$filled = max($usage, $workload);
			$text = $filled > 100 && $this->scale != 'week' ? $filled.'%' : '';

			$normalized_usage = min($usage, 100);
			$normalized_workload = min($workload, 100);
			
			$workload_text = '';
			$usage_text = '';

			if ( $workload < $usage )
			{
				$workload_text = $text;
			}
			else
			{
				$usage_text = $text;
			}

			$title = str_replace( '%2', $normalized_workload.'%', 
				str_replace('%1', $filled.'%', text('ee57')) );
			
			echo '<div class="progress_bar_frame" title="'.$title.'">';
				if ( $workload - $usage > 0 )
				{
					if ( $normalized_workload - $normalized_usage > 50 )
					{
						$usage_text = $text;
						$workload_text = '';
					}

					if ( $usage > 0 )
					{
						echo '<div class="progress_bar" style="background:#EBE614;width:'.$normalized_usage.'%;">'.$workload_text.'</div>';
					}

					if ( $normalized_workload - $normalized_usage > 0 )
					{
						echo '<div class="progress_bar" style="background:red;width:'.($normalized_workload - $normalized_usage).'%;">'.$usage_text.'</div>';
					}
				}
				else
				{
					if ( $workload > 0 )
					{
						echo '<div class="progress_bar" style="background:#EBE614;width:'.$normalized_workload.'%;">'.$workload_text.'</div>';
					}

					if ( $normalized_usage - $normalized_workload > 0 )
					{
						echo '<div class="progress_bar" style="width:'.($normalized_usage - $normalized_workload).'%;">'.$usage_text.'</div>';
					}
				}
				
				if ( $workload + $usage < 1 )
				{
					echo '&nbsp;';
				}
			echo '</div>';
		}
		else
		{
			echo '<div class="progress_bar_frame" title="0%">';
				echo '&nbsp;';
			echo '</div>';
		}		
	}
	
	function drawHours( $id, &$data, $attr )
	{
		$row_class = $data[$id][$attr]['workloadactual'] > $data[$id][$attr]['workloadduration']
			? "resource_hours_cell overloaded" : "resource_hours_cell";
			
		echo '<div class="'.$row_class.'">';
		
		if ( $data[$id][$attr]['tasks'] != '' )
		{
			$url = getSession()->getApplicationUrl().'tooltip/objectlist/task/'.$data[$id][$attr]['tasks'];
			
			echo '<a style="float:left;" class="with-tooltip" data-placement="right" data-original-title="" data-content="" info="'.$url.'">';
		}
		
		echo '<div class="item">';
			$this->drawHourValue($data[$id][$attr]['duration']);
		echo '</div>';

		echo '<div class="item">';
			$this->drawHourValue($data[$id][$attr]['workloadduration']);
		echo '</div>';

		echo '<div class="right_item">';
			$this->drawHourValue($data[$id][$attr]['workloadactual']);
		echo '</div>';

		if ( $data[$id][$attr]['tasks'] != '' )
		{
		    echo '</a>';
		}
		
		echo '</div>';
	}
	
	function drawHourValue( $value )
	{
		if ( $value == '' || $value == 0 ) {
			echo '<span class="nullvalue">0</span>';
		}
		else {
			echo $value;
		}
	}
	
	function getRowBackgroundColor( $object_it ) 
	{
		return $object_it->get('ResourceClass') == 'Group'
			? '#efefef' : 'white';
	}
	
	function getColumnWidth( $attr ) 
	{
		switch ( $this->scale )
		{
			case 'quarter':
				return '20%';
				
			case 'month':
				return '7%';
		}

		return parent::getColumnWidth( $attr );
	}

	function getGroupFields()
	{
		return array('ResourceTitle');
	}
	
	function getColumnFields()
	{
		return array();
	}
}
