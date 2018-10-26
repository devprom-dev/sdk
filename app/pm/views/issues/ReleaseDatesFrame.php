<?php

class ReleaseDatesFrame
{
 	var $release_it, $infosection;
 	
 	function ReleaseDatesFrame( $release_it )
 	{
 		$this->release_it = $release_it;
 	}
 	
 	function setInfosection( $section )
 	{
 		$this->infosection = $section;
 	}
 	
 	function draw()
 	{
 		global $model_factory;
 		
 		$methodology_it = getSession()->getProjectIt()->getMethodologyIt();
 		
		$start_date = $this->release_it->getStartDate();
		$finish_date = $this->release_it->getFinishDate();

		$estimated_start = $this->release_it->getDateFormat('EstimatedStartDate');
		$estimated_finish = $this->release_it->getDateFormat('EstimatedFinishDate');
		
		$need_delimiter = false;

		echo '<div style="float:left;">';
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
				echo '<br/>';
			}
			
			echo translate('Фактические').':<br/>';
			echo $estimated_start.'&nbsp;-&nbsp;'.$estimated_finish;

			$offset = $this->release_it->getFinishOffsetDays();
			if ( $offset > 0 )
			{
				echo '<br/><span style="color:red;">'.translate('Отклонение от графика').': '.$offset.' '.translate('дн.').'</span>';
			}	
			
			$need_delimiter = true;
		}
		echo '</div>';
		echo '<div style="float:right;">';
			if ( is_object($this->infosection) )
			{
				$this->infosection->drawMenu();
			}
		echo '</div>';
		echo '<div style="clear:both;"></div>';
		
		$strategy = $methodology_it->getEstimationStrategy();
		
		if ( false && $methodology_it->HasPlanning() )
		{
			$iteration = $model_factory->getObject('Iteration');
			
			$iteration->addFilter( new IterationTimelinePredicate(IterationTimelinePredicate::NOTPASSED) );
			
			$iteration_it = $iteration->getAll();
			
			while ( !$iteration_it->end() )
			{
				if ( $iteration_it->get('Version') == $this->release_it->getId() )
				{
					if ( $need_delimiter )
					{
							echo '<br/>';
					}
			
					echo translate('Итерация').': '.$iteration_it->getDisplayName();
					
					echo '<div style="padding:15px;">';

					    $flot = new FlotChartBurndownWidget();
                    $flot->setLegend(false);

                    $report_it = $model_factory->getObject('PMReport')->getExact('iterationburndown');
					
					    $url = $report_it->getUrl().'&release='.$iteration_it->getId();

					    $chart_id = 'chart'.md5($url);
					    
					    echo '<div id="'.$chart_id.'" class="plot" url="'.$url.'" style="width:220px;height:120px;"></div>';
					    
					    $flot->setUrl( getSession()->getApplicationUrl().
							'chartburndown.php?release_id='.$iteration_it->getId().'&json=1' );
					    
					    $flot->draw( $chart_id );
					    
					echo '</div>';

					$maximum = $this->release_it->getVelocity();
					if ( !$methodology_it->HasFixedRelease() ) 
					{
						$maximum *= $iteration_it->getLeftDuration();
					}

					$estimation = $iteration_it->getEstimation();
					if ( $estimation == '' ) 
					{
						$estimation = 0; 
					}
					
					echo '<div>';
						echo text(1020).': '.$strategy->getDimensionText(round($maximum));
					echo '</div>';
					echo '<div style="'.($maximum > 0 && $estimation > $maximum ? 'color:red;' : '').'">';
						echo text(1021).': '.$strategy->getDimensionText(round($estimation));
					echo '</div>';
				}
				
				$iteration_it->moveNext();
			}
		}
		else
		{
			echo '<div style="padding:10px 0 25px 0;">';
			
				$flot = new FlotChartBurndownWidget();
				
				$report_it = $model_factory->getObject('PMReport')->getExact('releaseburndown');
			
				$url = $report_it->getUrl().'&release='.$this->release_it->getId();
				
			    $chart_id = 'chart'.md5($url);
			    
			    echo '<div id="'.$chart_id.'" class="plot" url="'.$url.'" style="width:220px;height:120px;"></div>';
				
				$flot->setUrl( getSession()->getApplicationUrl().
					'chartburndownversion.php?version='.$this->release_it->getId().'&json=1' );
			    
				$flot->draw($chart_id);
				
			echo '</div>';
			
			$maximum = $this->release_it->getVelocity();
			if ( !$methodology_it->HasFixedRelease() ) 
			{
				$maximum *= $this->release_it->getLeftDuration();
			}

			$estimation = $this->release_it->getTotalWorkload();
			if ( $estimation == '' ) 
			{
				$estimation = 0; 
			}
			
			echo '<div style="clear:both">';
				echo text(1020).': '.$strategy->getDimensionText(round($maximum));
			echo '</div>';
			echo '<div style="'.($maximum > 0 && $estimation > $maximum ? 'color:red;' : '').'">';
				echo text(1021).': '.$strategy->getDimensionText(round($estimation));
			echo '</div>';
		}
 	}
}
