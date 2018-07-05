<?php
include_once SERVER_ROOT_PATH . "pm/classes/comments/persisters/CommentRecentPersister.php";
include_once SERVER_ROOT_PATH . "pm/classes/plan/sorts/SortStageClause.php";
include "PlanChart.php";

class VersionList extends PMPageList
{
 	var $release_it, $iteration_it;

    function getIt( $object_it )
	{
		if ( $object_it->get('Release') > 0 )
		{
			if ( !is_object($this->iteration_it) ) {
				$this->iteration_it = getFactory()->getObject('pm_Release')->getRegistry()->Query(
				    array(
				        new FilterVpdPredicate(),
                        new CommentRecentPersister()
                    )
                );

			}
			$this->iteration_it->moveToId($object_it->get('Release'));
			return $this->iteration_it->getCurrentIt();
		}

		if ( $object_it->get('Version') > 0 )
		{
			if ( !is_object($this->release_it) ) {
				$this->release_it = getFactory()->getObject('pm_Version')->getRegistry()->Query(
                    array(
                        new FilterVpdPredicate(),
                        new CommentRecentPersister()
                    )
                );
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
					$text = $this->getUidService()->getUidIconGlobal($ref_it, false);
                    $text .= '<span class="ref-name">'.$ref_it->getDisplayNameExt().'<br/></span>';
                    $uids[] = $text;
					$this->getUidService()->setBaseline('');
                }
                echo join(' ',$uids);
                return;

            case 'RecentComment':
                parent::drawCell($object_it, $attr);
                return;
		}
		
		if ( $attr == 'Stage' || $attr == 'VersionNumber' )
		{
            $offset = '0px';
            echo '<div style="padding-left:'.$offset.';">';
                $caption = $source_it->getHtmlDecoded('Caption');
                if ( is_numeric($caption) ) $caption =  $source_it->getHtmlDecoded('CaptionType');
                echo trim($source_it->getHtmlDecoded('CaptionPrefix').' '.$caption);
            echo '</div>';
		}
		elseif ( $attr == 'Description' )
		{
			if ( $object_it->get('Description') != '' )
			{
				drawMore( $object_it, 'Description', 30 );
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

	function getItemActions( $column_name, $object_it )
	{
		$it = $this->getIt( $object_it );
        $methodology_it = $it->getRef('Project')->getMethodologyIt();

		$actions = parent::getItemActions( $column_name, $it );

		switch ( $it->object->getClassName() )
		{
			case 'pm_Version':
				
				$new_actions = $this->getNewRelatedActions($it, $methodology_it);
                if ( count($new_actions) > 0 ) {
                    $actions[] = array();
                    $actions[] = array (
                        'name' => translate('Создать'),
                        'items' => $new_actions
                    );
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
				
	            $module_it = getFactory()->getObject('Module')->getExact('issues-backlog');
	            
	            if ( getFactory()->getAccessPolicy()->can_read($module_it) )
	            {
					if ( $actions[array_pop(array_keys($actions))]['name'] != '' ) $actions[] = array();
	                
				    $states = getFactory()->getObject('Request')->getNonTerminalStates();
				    $info = $module_it->buildMenuItem('?release='.$it->getId().'&group=State&state='.join(',',$states));
				    
	                $actions[] = array( 
	                    'url' => $info['url'],
	                    'name' => translate('Бэклог релиза')
	                );
	            }

				break;

			case 'pm_Release':

                $new_actions = $this->getNewRelatedActions($it, $methodology_it);
                if ( count($new_actions) > 0 ) {
                    $actions[] = array();
                    $actions[] = array (
                        'name' => translate('Создать'),
                        'items' => $new_actions
                    );
                }

				$method = new ResetBurndownWebMethod();
				
				if ( getFactory()->getAccessPolicy()->can_modify($it) && $method->hasAccess() )
				{
					if ( $actions[array_pop(array_keys($actions))]['name'] != '' ) $actions[] = array();
					array_push( $actions, array(
					    'url' => $method->url( $it ),
					    'name' => $method->getCaption() 
					));
				}

                if ( $methodology_it->HasTasks() ) {
                    $task_list_it = getFactory()->getObject('Module')->getExact('tasks-list');
                    $states = getFactory()->getObject('Task')->getNonTerminalStates();
                }
                else {
                    $task_list_it = getFactory()->getObject('Module')->getExact('issues-backlog');
                    $states = getFactory()->getObject('Request')->getNonTerminalStates();
                }

	            if ( getFactory()->getAccessPolicy()->can_read($task_list_it) ) {
					if ( $actions[array_pop(array_keys($actions))]['name'] != '' ) $actions[] = array();
	                
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

	function getNewRelatedActions( $object_it, $methodology_it )
    {
        $actions = array();
        if ( $object_it->object instanceof Release ) {
            $method = new ObjectCreateNewWebMethod(getFactory()->getObject('Iteration'));
            if ( getSession()->getProjectIt()->getMethodologyIt()->HasPlanning() && $method->hasAccess() )
            {
                $method->setRedirectUrl('donothing');
                $actions[] = array(
                    'url' => $method->getJSCall( array('Version' => $object_it->getId()) ),
                    'name' => $method->getCaption()
                );
            }
            $method = new ObjectCreateNewWebMethod(getFactory()->getObject('Request'));
            if ( $method->hasAccess() ) {
                $actions[] = array (
                    'name' => $method->getCaption(),
                    'url' => $method->getJSCall(array('PlannedRelease' => $object_it->getId()))
                );
            }
        }
        if ( $object_it->object instanceof Iteration ) {
            $method = new ObjectCreateNewWebMethod(getFactory()->getObject('Request'));
            if ( $method->hasAccess() ) {
                $actions[] = array (
                    'name' => $method->getCaption(),
                    'url' => $method->getJSCall(array('Iteration' => $object_it->getId()))
                );
            }
            if ( $methodology_it->HasTasks() ) {
                $method = new ObjectCreateNewWebMethod(getFactory()->getObject('Task'));
                if ( $method->hasAccess() ) {
                    $actions[] = array (
                        'name' => $method->getCaption(),
                        'url' => $method->getJSCall(array('Release' => $object_it->getId()))
                    );
                }
            }
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
	    return array_diff(
	        parent::getColumnFields(),
            array (
                'StartDate', 'FinishDate', 'IsActual',
                'RecordCreated', 'RecordModified',
                'InitialEstimationError', 'InitialBugsInWorkload'
		));
	}

	function getGroupDefault()
    {
        return '';
    }

    function getSorts()
    {
        return array_merge(
            parent::getSorts(),
            array(
                new SortStageClause()
            )
        );
    }

    function render($view, $parms)
    {
        echo '<div class="hie-chart">';
            $planChart = new PlanChart();
            $planChart->setTable($this->getTable());
            $planChart->retrieve();
            $planChart->render($view, $parms);
        echo '</div>';

        $methodologyIt = getSession()->getProjectIt()->getMethodologyIt();
        if ( $methodologyIt->HasPlanning() || $methodologyIt->HasReleases() ) {
            parent::render($view, $parms);
        }
    }
}
