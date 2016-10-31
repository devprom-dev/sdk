<?php
include "PlanChart.php";

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
		$methodology_it = getSession()->getProjectIt()->getMethodologyIt();
		
		$object_it = $this->getIt( $source_it );
		if ( $object_it->getId() == '' ) return;
		
		switch ( $attr )
		{
            case 'Artefacts':
                $objects = preg_split('/,/', $source_it->get($attr));
                $uids = array();

				$branches = array();
				foreach( $objects as $object_info ) {
					list($class, $id, $type, $baseline) = preg_split('/:/', $object_info);
					if ($type == 'branch') $branches[] = $id;
				}
                foreach( $objects as $object_info )
                {
                    list($class, $id, $type, $baseline) = preg_split('/:/',$object_info);
					if ( $type != 'branch' && in_array($id, $branches) ) continue;
                    $class = getFactory()->getClass($class);
                    if ( $class == '' ) continue;
                    $ref_it = getFactory()->getObject($class)->getExact($id);
					if ( $type != 'branch' ) $this->getUidService()->setBaseline($baseline);
                    $uids[] = $this->getUidService()->getUidIconGlobal($ref_it, false);
					$this->getUidService()->setBaseline('');
                }
                echo join(' ',$uids);
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
						$start_date = $object_it->get('StartDate');
						$finish_date = $object_it->get('FinishDate');
						
						if ( $methodology_it->HasStatistics() )
						{
							$estimated_start = $object_it->get('EstimatedStartDate');
							$estimated_finish = $object_it->get('EstimatedFinishDate');
							
							if ( $start_date != $estimated_start || $finish_date != $estimated_finish )
							{
                                echo translate('По плану').':<br/>';
                                $this->drawDates($start_date,$finish_date);

								echo '<br/>'.translate('Фактические').':<br/>';
                                $this->drawDates($estimated_start,$estimated_finish);

								$offset = $object_it->getFinishOffsetDays();
								if ( $offset > 0 )
								{
									echo '<br/><span style="color:red;">'.translate('Отклонение от графика').': '.$offset.' '.translate('дн.').'</span>';
								}	
							}
							else {
                                $this->drawDates($start_date,$finish_date);
                            }
						}
						else if ( $start_date != '' || $finish_date != '' )
						{
                            $this->drawDates($start_date,$finish_date);
						}
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
                            $this->drawDates($object_it->get('StartDate'),$object_it->get('FinishDate')).'<br/>';

							echo '<br/><br/>'.translate('Фактические').':<br/>';
                            $this->drawDates($object_it->get('EstimatedStartDate'),$object_it->get('EstimatedFinishDate'));

							echo '<br/><span style="color:red;">'.translate('Отклонение от графика').': '.$offset.' '.translate('дн.').'</span>';
						}
						else
						{
                            $this->drawDates($object_it->get('StartDate'),$object_it->get('FinishDate'));
						}
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

	protected function drawDates( $start, $finish )
    {
        echo getSession()->getLanguage()->getDateFormattedShort($start);
        echo '&nbsp;:&nbsp;';
        echo getSession()->getLanguage()->getDateFormattedShort($finish);
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

	function getItemActions( $column_name, $object_it )
	{
		global $model_factory;
		
		$it = $this->getIt( $object_it );

		$actions = parent::getItemActions( $column_name, $it );

		$iteration = $model_factory->getObject('pm_Release');
		switch ( $it->object->getClassName() )
		{
			case 'pm_Version':
				
				$method = new ObjectCreateNewWebMethod($iteration);
				
				$method->setRedirectUrl('donothing');
				
				if ( getSession()->getProjectIt()->getMethodologyIt()->HasPlanning() && $method->hasAccess() )
				{
					if ( $actions[array_pop(array_keys($actions))]['name'] != '' ) $actions[] = array();
					$actions[] = array(
					    'url' => $method->getJSCall( array('Version' => $it->getId()) ), 
						'name' => translate('Создать итерацию')
					);

					$need_separator = true;
				}

				$method = new ResetBurndownWebMethod();
				
				if ( $method->hasAccess() )
				{
					if ( $actions[array_pop(array_keys($actions))]['name'] != '' ) $actions[] = array();
				    array_push( $actions, array(
				        'url' => $method->url( $it ),
				        'name' => $method->getCaption() 
				    ));
				}
				
	            $module_it = $model_factory->getObject('Module')->getExact('issues-backlog');
	            
	            if ( getFactory()->getAccessPolicy()->can_read($module_it) )
	            {
					if ( $actions[array_pop(array_keys($actions))]['name'] != '' ) $actions[] = array();
	                
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
					if ( $actions[array_pop(array_keys($actions))]['name'] != '' ) $actions[] = array();
					array_push( $actions, array(
					    'url' => $method->url( $it ),
					    'name' => $method->getCaption() 
					));
				}
				
			    $task_list_it = $model_factory->getObject('Module')->getExact('tasks-list');
			    
	            if ( getFactory()->getAccessPolicy()->can_read($task_list_it) )
	            {
					if ( $actions[array_pop(array_keys($actions))]['name'] != '' ) $actions[] = array();
	                
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

	function getGroupDefault()
    {
        return '';
    }

    function render($view, $parms)
    {
        echo '<div class="hie-chart">';
            $planChart = new PlanChart();
            $planChart->setTable($this->getTable());
            $planChart->retrieve();
            $planChart->render($view, $parms);
        echo '</div>';

        parent::render($view, $parms);
    }
}
