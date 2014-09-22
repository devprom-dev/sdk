<?php

include_once SERVER_ROOT_PATH."pm/classes/common/persisters/EntityProjectPersister.php";

class WorkloadSection extends InfoSection
{
 	function getCaption()
 	{
 		return text(716);
 	}

 	function getIcon()
 	{
 	    return 'icon-user';
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
		    
		    $columns = $iteration_it->count() > 3 ? 4 : 3; 
		    
            while( !$iteration_it->end() && $columns-- > 0 )
            {
                $self_it = $iteration_it->getRef('Project');
                
                echo '<td>';
                
                $info = $uid->getUIDInfo( $iteration_it );
                
			    echo '<table class="table"><thead><tr><th>';
        	        echo ($self_it->getId() != $project_it->getId() ? '{'.$self_it->get('CodeName').'} ' : '').
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
	    
		$release_left_capacity = $iteration_it->getLeftCapacity();
		
		$part = $model_factory->getObject('pm_Participant');
		
		$part->addFilter( new ParticipantIterationInvolvedPredicate($iteration_it) );
		
		$part_it = $part->getAll();

		for ( $i = 0; $i < $part_it->count(); $i++ )
		{
			$left_work = $iteration_it->getLeftWorkParticipant( $part_it );

			$part_capacity = $release_left_capacity * $part_it->get('Capacity');
				
			if ( true || $this->show_tasks )
			{
				echo '<div><a href="javascript: filterLocation.setup(\'taskassignee='.$part_it->get('SystemUser').'\');">'.$part_it->get('Caption').'</a></div>';
			}
			else
			{
				echo '<div>'.$part_it->get('Caption').'</div>';
			}
			
			$measure = translate('ч.');
			$full_volume = $part_capacity;
			$used_volume = $left_work;
			
			$left_volume = $full_volume - $used_volume;
        	
        	if ( $full_volume > 0.0 )
        	{
        		$filled_volume = round(($used_volume / $full_volume) * 100, 0);
        	}
        	
        	$overload = false;
        	
        	if($left_volume < 0) 
        	{
        		$overload = true;
        		if ( $filled_volume > 0.0 )
        		{
        			$filled_volume = round((100 / $filled_volume) * 100, 0);
        		}
        		else
        		{
        			$filled_volume = 0;
        		}
        	}
			
			if ( $left_volume < 0 )
			{
				$left_name = translate('перегрузка');
			}
			else
			{
				$left_name = translate('свободно');
			}
			
			echo '<div>'.round($used_volume, 1).'&nbsp;'.translate('из').'&nbsp;'.round($full_volume, 1).
				',&nbsp;'.$left_name.'&nbsp;'.abs(round($left_volume,2)).
				'&nbsp;'.$measure.'</div>';
			
			if ( $overload )
			{
			?>
            <div class="progress">
              <div class="bar bar-danger" style="width: 100%;"></div>
            </div>
            <?php
			}
            else
			{
			?>
            <div class="progress">
              <div class="bar bar-success" style="width: <?=$filled_volume?>%;"></div>
            </div>
            <?php
			} 
			
			echo '<div class="clearfix"></div>';
			
			$part_it->moveNext();
		}	    
	}
	
	function IsActive()
	{
		return getSession()->getProjectIt()->getMethodologyIt()->IsTimeTracking();
	}
}
