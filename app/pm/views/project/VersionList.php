<?php

if ( !class_exists('IssuesProgressFrame', false) ) include(SERVER_ROOT_PATH.'/pm/views/c_request_frame.php');

class VersionList extends PMPageList
{
 	var $release_it, $iteration_it;
	private $issues_widget = '';
	private $tasks_widget = '';

	function __construct( $object )
	{
		parent::__construct($object);

		$report = getFactory()->getObject('PMReport');

		$report_it = $report->getExact('issues-trace');
		if ( getFactory()->getAccessPolicy()->can_read($report_it) ) {
			$menu = $report_it->buildMenuItem();
			$this->issues_widget = $menu['url'];
		}
		$report_it = $report->getExact('tasks-trace');
		if ( getFactory()->getAccessPolicy()->can_read($report_it) ) {
			$menu = $report_it->buildMenuItem();
			$this->tasks_widget = $menu['url'];
		}
	}

	function getIt( $object_it )
	{
		if ( $object_it->get('Release') > 0 )
		{
			if ( !is_object($this->iteration_it) ) {
				$this->iteration_it = getFactory()->getObject('pm_Release')->getAll();
			}
			$this->iteration_it->moveToId($object_it->get('Release'));
			return $this->iteration_it->getCurrentIt();
		}

		if ( $object_it->get('Version') > 0 )
		{
			if ( !is_object($this->release_it) ) {
				$this->release_it = getFactory()->getObject('pm_Version')->getAll();
			}
			$this->release_it->moveToId($object_it->get('Version'));
			return $this->release_it->getCurrentIt();
		}
		
		return $object_it;
	}
	
	function IsNeedToDisplayNumber( ) { return false; }
	function IsNeedToDelete( ) { return false; }

	function drawCell( $source_it, $attr )
	{
		global $model_factory;

		$methodology_it = getSession()->getProjectIt()->getMethodologyIt();
		
		$object_it = $this->getIt( $source_it );
		
		if ( $object_it->getId() == '' ) return;
		
		$report = $model_factory->getObject('PMReport');

		switch ( $attr )
		{
			case 'Burndown':
				$this->drawBurndown( $object_it );
				break;

            case 'Artefacts':
                $objects = preg_split('/,/', $source_it->get($attr));
                $uids = array();

                foreach( $objects as $object_info )
                {
                    list($class, $id) = preg_split('/:/',$object_info);
                    $class = getFactory()->getClass($class);
                    if ( $class == '' ) continue;
                    $ref_it = getFactory()->getObject($class)->getExact($id);
                    $uids[] = $this->getUidService()->getUidIcon($ref_it);
                }
                echo join(', ',$uids);
                return;
		}
		
		if ( $attr == 'Stage' || $attr == 'VersionNumber' )
		{
			switch ( $object_it->object->getClassName() )
			{
				case 'pm_Version':
					$offset = '0px';
					echo '<div style="padding-left:'.$offset.';">';
						$caption = $object_it->getDisplayName();
						if ( is_numeric($caption) ) $caption = translate('Релиз').' '.$caption;
						echo $caption;
					echo '</div>';		
					break;

				case 'pm_Release':
					$offset = '0px';
					echo '<div style="padding-left:'.$offset.';">';
						$caption = $object_it->getDisplayName();
						if ( is_numeric($caption) ) $caption = translate('Итерация').' '.$caption;
						echo $caption;
					echo '</div>';		
					break;
			}
		}
		elseif ( $attr == 'Description' )
		{
			if ( $object_it->get('ProjectStage') > 0 )
			{
				$stage_it = $object_it->getRef('ProjectStage');
			}

			if ( $object_it->get('Description') != '' )
			{
				drawMore( $object_it, 'Description', 30 );
			}
			elseif ( is_object($stage_it) && $stage_it->get('Description') != '' )
			{
				drawMore( $stage_it, 'Description', 10 );
			}
		}
		elseif ( $attr == 'VersionNumber' )
		{
			switch ( $object_it->object->getClassName() )
			{
				case 'pm_Version':
					echo $source_it->get('Caption');
					break;

				case 'pm_Release':
					echo $source_it->get('Caption');
					break;
			}
		}
		else
		{
			switch ( $object_it->object->getClassName() )
			{
				case 'pm_Version':
					if ( $attr == 'Deadlines' )
					{
						$start_date = $object_it->getStartDate();
						$finish_date = $object_it->getFinishDate();
						
						if ( $methodology_it->HasStatistics() )
						{
							$estimated_start = $object_it->getDateFormat('EstimatedStartDate');
							$estimated_finish = $object_it->getDateFormat('EstimatedFinishDate');
							
							if ( $start_date != '' || $finish_date != '' )
							{
								echo translate('По плану').':<br/>';
								echo $start_date.'&nbsp;-&nbsp;'.$finish_date.'<br/><br/>';
							}
							
							if ( $start_date != $estimated_start || $finish_date != $estimated_finish )
							{
								echo translate('Фактические').':<br/>';
								echo $estimated_start.'&nbsp;-&nbsp;'.$estimated_finish;
		
								$offset = $object_it->getFinishOffsetDays();
								if ( $offset > 0 )
								{
									echo '<br/><span style="color:red;">'.translate('Отклонение от графика').': '.$offset.' '.translate('дн.').'</span>';
								}	
							}
						}
						else if ( $start_date != '' || $finish_date != '' )
						{
							echo $start_date.'&nbsp;-&nbsp;'.$finish_date.'<br/><br/>';
						}
					}
					else if ( $attr == 'Indexes' ) 
					{
						$this->drawIndex( $object_it );
					}
					elseif( $attr == 'EstimatedStartDate' || $attr == 'EstimatedFinishDate' )
					{ 
						parent::drawCell( $object_it, $attr );
					}
					else
					{
						parent::drawCell( $source_it, $attr );
					}
	
					break;
	
				case 'pm_Release':
					if ( $attr == 'Deadlines' )
					{
						$offset = $object_it->getFinishOffsetDays();

						if ( $offset > 0 )
						{
							echo translate('По плану').':<br/>';
							echo $object_it->getDateFormat('StartDate');
							echo '&nbsp;-&nbsp;';
							echo $object_it->getDateFormat('FinishDate');
	
							echo '<br/><br/>'.translate('Фактические').':<br/>';
							echo $object_it->getDateFormat('EstimatedStartDate');
							echo '&nbsp;-&nbsp;';
							echo $object_it->getDateFormat('EstimatedFinishDate');
	
							echo '<br/><span style="color:red;">'.translate('Отклонение от графика').': '.$offset.' '.translate('дн.').'</span>';
						}
						else
						{
							echo $object_it->getDateFormat('StartDate');
							echo '&nbsp;-&nbsp;';
							echo $object_it->getDateFormat('FinishDate');
						}
					}
					elseif ( $attr == 'Indexes' ) 
					{
						$this->drawIndex( $object_it );
					}
					elseif( $attr == 'EstimatedStartDate' || $attr == 'EstimatedFinishDate' )
					{ 
						parent::drawCell( $object_it, $attr );
					}
					else
					{
						parent::drawCell( $source_it, $attr );
					}
					break;
			}
		}
	}
	
	function drawBurndown( $object_it )
	{
		global $model_factory;
		
		switch ( $object_it->object->getClassName() )
		{
			case 'pm_Version':
				if ( $object_it->IsFuture() ) return;
				
				echo '<div style="padding-right:8px;">';
					
    				$flot = new FlotChartBurndownWidget();
					
					$report_it = $model_factory->getObject('PMReport')->getExact('releaseburndown');
				
					$url = $report_it->getUrl().'&release='.$object_it->getId();
				
					$chart_id = 'chart'.md5($url);
					
					echo '<div id="'.$chart_id.'" class="plot" url="'.$url.'" style="height:90px;width:180px;"></div>';

					$flot->setUrl( getSession()->getApplicationUrl().
						'chartburndownversion.php?version='.$object_it->getId().'&json=1' );
					
					$flot->draw($chart_id);
					
				echo '</div>';
				
				break;
				
			case 'pm_Release':
				if ( $object_it->IsFuture() ) return;
				 
				echo '<div style="padding-right:8px;">';
				
    				$flot = new FlotChartBurndownWidget();
					
					$report_it = $model_factory->getObject('PMReport')->getExact('iterationburndown');
				
					$url = $report_it->getUrl().'&release='.$object_it->getId();
				
					$chart_id = 'chart'.md5($url);
					
					echo '<div id="'.$chart_id.'" class="plot" url="'.$url.'" style="height:90px;width:180px;"></div>';

					$flot->setUrl( getSession()->getApplicationUrl().
						'chartburndown.php?release_id='.$object_it->getId().'&json=1' );
					
					$flot->draw($chart_id);
				
				echo '</div>';
				
				break;
		}		
	}

	function drawIndex( $object_it )
	{
		global $model_factory;
		
		$methodology_it = getSession()->getProjectIt()->getMethodologyIt();
		
		switch ( $object_it->object->getClassName() )
		{
			case 'pm_Version':
				if ( !$methodology_it->HasVelocity() ) break;
				
				$velocity = round($object_it->getVelocity(), 1);
				$estimation = $object_it->getTotalWorkload();
				$strategy = $methodology_it->getEstimationStrategy();

				echo '<div class="line">';
					echo str_replace('%1', $velocity, $strategy->getVelocityText($object_it->object));
				echo '</div>';

				list( $capacity, $maximum, $actual_velocity ) = $object_it->getEstimatedBurndownMetrics();

				$show_limit = SystemDateTime::date() <= $object_it->get('EstimatedFinishDate') || $object_it->get('UncompletedIssues') > 0;
				
				echo '<div class="line">';
					echo text(1020).': '.$strategy->getDimensionText(round($maximum, 1));
				echo '</div>';
				echo '<div class="line" style="'.($show_limit && $maximum > 0 && $estimation > $maximum ? 'color:red;' : '').'">';
					echo text(1021).': '.$strategy->getDimensionText(round($estimation));
				echo '</div>';
				
				break;
				
			case 'pm_Release':
				$strategy = $methodology_it->getEstimationStrategy();
				$velocity = round($object_it->getVelocity(), 0);
				
				echo '<div class="line">';
					echo str_replace('%1', $velocity, $strategy->getVelocityText($object_it->object));
				echo '</div>';
				
				list( $capacity, $maximum, $actual_velocity ) = $object_it->getEstimatedBurndownMetrics();
				
				$estimation = $object_it->getEstimation();
				
				if ( $estimation == '' ) 
				{
					$estimation = 0; 
				}

				$show_limit = SystemDateTime::date() <= $object_it->get('EstimatedFinishDate') || $object_it->get('UncompletedTasks') > 0;
				
				echo '<div class="line">';
					echo text(1020).': '.$strategy->getDimensionText(round($maximum, 1));
				echo '</div>';
				echo '<div class="line" style="'.($show_limit && $estimation > $maximum ? 'color:red;' : '').'">';
					echo text(1021).': '.$strategy->getDimensionText(round($estimation,0));
				echo '</div>';
				
				break;
		}
	}
	
	function getReferencesListWidget( $object )
	{
		if ( $object instanceof Task ) {
			return $this->tasks_widget;
		}
		if ( $object instanceof Request ) {
			return $this->issues_widget;
		}
		return parent::getReferencesListWidget( $object );
	}

	function IsNeedToDisplayOperations()
	{
	    return true;    
	}
	
	function getItemActions( $column_name, $object_it ) 
	{
		global $model_factory;
		
		$it = $this->getIt( $object_it );

		$actions = parent::getItemActions( $column_name, $it );

		$version_number = '';
		
		$iteration = $model_factory->getObject('pm_Release');
		
		switch ( $it->object->getClassName() )
		{
			case 'pm_Version':
				
				$method = new ObjectCreateNewWebMethod($iteration);
				
				$method->setRedirectUrl('donothing');
				
				if ( getSession()->getProjectIt()->getMethodologyIt()->HasPlanning() && $method->hasAccess() )
				{
				    if ( $actions[count($actions)-1]['name'] != '' ) $actions[] = array();
				    	
					$actions[] = array(
					    'url' => $method->getJSCall( array('Version' => $it->getId()) ), 
						'name' => translate('Создать итерацию')
					);

					$need_separator = true;
				}

				$method = new ResetBurndownWebMethod();
				
				if ( $method->hasAccess() )
				{
				    if ( $actions[count($actions)-1]['name'] != '' ) $actions[] = array();

				    array_push( $actions, array( 
				        'url' => $method->getJSCall( $it ), 
				        'name' => $method->getCaption() 
				    ));
				}
				
	            $module_it = $model_factory->getObject('Module')->getExact('issues-backlog');
	            
	            if ( getFactory()->getAccessPolicy()->can_read($module_it) )
	            {
				    if ( $actions[count($actions)-1]['name'] != '' ) $actions[] = array();
	                
				    $states = $model_factory->getObject('Request')->getNonTerminalStates();
				    
				    $info = $module_it->buildMenuItem('?release='.$it->getId().'&group=State&state='.join(',',$states));
				    
	                $actions[] = array( 
	                    'url' => $info['url'],
	                    'name' => translate('Бэклог релиза')
	                );
	            }

				break;

			case 'pm_Release':

				$method = new ResetBurndownWebMethod();
				
				if ( getFactory()->getAccessPolicy()->can_modify($it) && $method->hasAccess() )
				{
				    if ( $actions[count($actions)-1]['name'] != '' ) $actions[] = array();
				    
					array_push( $actions, array( 
					    'url' => $method->getJSCall( $it ), 
					    'name' => $method->getCaption() 
					));
				}
				
			    $task_list_it = $model_factory->getObject('Module')->getExact('tasks-list');
			    
	            if ( getFactory()->getAccessPolicy()->can_read($task_list_it) )
	            {
				    if ( $actions[count($actions)-1]['name'] != '' ) $actions[] = array();
	                
				    $states = $model_factory->getObject('Task')->getNonTerminalStates();
				    
				    $info = $task_list_it->buildMenuItem('?iteration='.$it->getId().'&group=State&state='.join(',',$states));
				    
	                $actions[] = array(
	                    'url' => $info['url'],
	                    'name' => translate('Бэклог итерации')
	                );
	            }

				break;
		}

		return $actions;
	}
		
	function getActions( $object_it )
	{
		$actions = $this->getItemActions('', $object_it);
		$it = $this->getIt( $object_it );
		
		switch ( $it->object->getClassName() )
		{
			case 'pm_Version':
				$form = new ReleaseForm();
				break;
			case 'pm_Release':
				$form = new IterationForm();
				break;
		}
		
	    $form->show($it);
		
	    $delete = $form->getDeleteActions();
        if ( count($delete) > 0 ) {
		    $actions = array_merge($actions, array(array()), $delete); 
        }
		
        return $actions;
	}
	
 	function getColumnWidth( $attr )
 	{
 		switch ( $attr )
 		{
 			case 'Progress':
 				return 160;
 				
 			case 'Indexes':
 				return '15%';

 			case 'Deadlines':
 				return 210;

 			default:
 				return parent::getColumnWidth( $attr );
 		}
 	}

 	function getRowBackgroundColor( $object_it ) 
	{
		return '#ffffff';
	}
	
	function getColumnFields()
	{
		$methodology_it = getSession()->getProjectIt()->getMethodologyIt();
		
		$fields = parent::getColumnFields();
		
	    $fields = array_diff( $fields, array (
			'StartDate', 'FinishDate', 'IsActual', 
			'RecordCreated', 'RecordModified', 
			'InitialEstimationError', 'InitialBugsInWorkload'
		));
		
		if ( $methodology_it->HasPlanning() )
		{
			array_push( $fields, 'Burnup' );
		}
		
		return $fields;
	}
	
	function _getGroupFields()
	{
		return array();
	}
}
