<?php

include_once SERVER_ROOT_PATH."pm/classes/common/persisters/EntityProjectPersister.php";

class IterationBurndownSection extends InfoSection
{
 	function getCaption()
 	{
 	    return translate('Burndown');
 	}
 	
 	function getIcon()
 	{
 	    return 'icon-fire';
 	}
 	
 	function drawBody()
 	{
 		global $model_factory;
 		
 		$project_it = getSession()->getProjectIt();
 		
		$iteration = $model_factory->getObject('Iteration');
		
		$iteration->addFilter( new IterationTimelinePredicate(IterationTimelinePredicate::NOTPASSED) );
		
		$iteration->addPersister( new EntityProjectPersister() );
		
		$iteration_it = $iteration->getAll();

		$uid = new ObjectUID;
		
		echo '<table>';
		
		while ( !$iteration_it->end() )
		{
		    echo '<tr>';
		    
		    $columns = $iteration_it->count() > 3 ? 3 : 2; 
		    
            while( !$iteration_it->end() && $columns-- > 0 )
            {
                $self_project_it = $iteration_it->getRef('Project');
                
                echo '<td>';
        		    echo '<table class="table"><thead><tr><th>';
            	        echo ($self_project_it->getId() != $project_it->getId() ? '{'.$self_project_it->get('CodeName').'} ' : '').
            	            translate('Итерация').': '.$iteration_it->getDisplayName();
        		    echo '</th></tr></thead>';
        		    
        		    echo '<tbody><tr><td>';
            		    $this->drawIteration($iteration_it);
        		    echo '</td></tr></tbody></table>';
                echo '</td>';
                
                $iteration_it->moveNext();
            }
            
		    echo '</tr>';
		}
		
		echo '</table>';
 	}
 	
 	function drawIteration( $iteration_it )
 	{
 		global $model_factory;

 		$methodology_it = getSession()->getProjectIt()->getMethodologyIt();
 		
		$start_date = $iteration_it->getStartDate();
		$finish_date = $iteration_it->getFinishDate();

		$estimated_start = $iteration_it->getDateFormat('EstimatedStartDate');
		$estimated_finish = $iteration_it->getDateFormat('EstimatedFinishDate');
		
		$need_delimiter = false;
		
		echo '<div>';
			if ( $start_date != '' || $finish_date != '' )
			{
				echo translate('По плану').':<br/>';
				echo $start_date.'&nbsp;-&nbsp;'.$finish_date;
				
				$need_delimiter = true;
			}
			
			if ( $start_date != $estimated_start || $finish_date != $estimated_finish )
			{
				if ( $need_delimiter )
				{
					echo '<br/><br/>';
				}
				
				echo translate('Фактические').':<br/>';
				echo $estimated_start.'&nbsp;-&nbsp;'.$estimated_finish;
	
				$offset = $iteration_it->getFinishOffsetDays();
				if ( $offset > 0 )
				{
					echo '<br/><span style="color:red;">'.translate('Отклонение от графика').': '.$offset.' '.translate('дн.').'</span>';
				}	
				
				echo '<br/><br/>';
			}
		echo '</div>';
		
		$strategy = $methodology_it->getEstimationStrategy();
		
		$flot = new FlotChartBurndownWidget();
		
		$report_it = $model_factory->getObject('PMReport')->getExact('iterationburndown');
	
		$url = $report_it->getUrl().'&release='.$iteration_it->getId();
	
		$chart_id = 'chart'.md5($url);
		
		echo '<div id="'.$chart_id.'" class="plot" url="'.$url.'" style="width:220px;height:140px;"></div>';
		
		$flot->setUrl( getSession()->getApplicationUrl().
			'chartburndown.php?release_id='.$iteration_it->getId().'&json=1');
		
		$flot->draw($chart_id);

		echo '<div style="clear:both;">&nbsp;</div>';
		
		$release_it = $iteration_it->getRef('Version');
		$maximum = $release_it->getVelocity();
		
		if ( !$methodology_it->HasFixedRelease() ) 
		{
			$maximum *= $iteration_it->getLeftCapacity();
		}

		$estimation = $iteration_it->getEstimation();
		if ( $estimation == '' ) 
		{
			$estimation = 0; 
		}
		
		echo '<div class="line">';
			echo text(1020).': '.$strategy->getDimensionText(round($maximum,0));
		echo '</div>';
		
		echo '<div class="line" style="'.($estimation > $maximum ? 'color:red;' : '').'">';
			echo text(1021).': '.$strategy->getDimensionText(round($estimation,0));
		echo '</div>';
 	}
 	
	function getActions()
	{
		$actions = parent::getActions();
		
		if ( getFactory()->getAccessPolicy()->can_modify($this->release_it) )
		{
			array_push( $actions, array (
				'url' => $this->release_it->getEditUrl(),
				'name' => translate('Изменить')
			));
		}
		
		$method = new ResetBurndownWebMethod();
		
		if ( $method->hasAccess() )
		{
			if ( $actions[count($actions)-1]['name'] != '' ) array_push($actions, array() );
			array_push( $actions, array( 
				'url' => $method->getJSCall( $this->release_it ),
				'name' => $method->getCaption() 
			));
		}
		
		return $actions;
	}
}