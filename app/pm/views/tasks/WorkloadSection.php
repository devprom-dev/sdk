<?php

include_once SERVER_ROOT_PATH."pm/classes/common/persisters/EntityProjectPersister.php";

class WorkloadSection extends InfoSection
{
	private $data = array();
	
	public function __construct()
	{
		parent::__construct();
		$this->data = $this->buildData();
	}
	
	protected function buildData()
	{
		$data = array();
		
		$iteration_it = getFactory()->getObject('Iteration')->getRegistry()->Query(
				array (
						new IterationTimelinePredicate(IterationTimelinePredicate::NOTPASSED),
						new FilterVpdPredicate(),
						new EntityProjectPersister(),
						new SortAttributeClause('Project')
				)
		);
		
		while ( !$iteration_it->end() )
		{
				$part_it = getFactory()->getObject('pm_Participant')->getRegistry()->Query(
						array (
								new ParticipantIterationInvolvedPredicate($iteration_it),
								new FilterAttributePredicate('Project', $iteration_it->get('Project'))
						)
				);
				
				if ( $part_it->count() < 1 )
				{
					$iteration_it->moveNext();
					continue;
				}

				$data[] = array (
						'iteration' => $iteration_it->copy(),
						'participant' => $part_it->copyAll()
				);
				
				$iteration_it->moveNext();
		}
		
		return $data;
	}
	
	function getData()
	{
		return $this->data;
	}
	
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
 		$project_it = getSession()->getProjectIt();
		$uid = new ObjectUID;
		
		echo '<table>';
		for( $i = 0; $i < count($this->data); )
		{
		    echo '<tr>';
			$columns = count($this->data) > 3 ? 3 : 2; 
		    
            while( $i < count($this->data) && $columns-- > 0 )
            {
            	$iteration_it = $this->data[$i]['iteration'];
				$part_it = $this->data[$i]['participant'];
            	$self_it = $iteration_it->getRef('Project');
                
                echo '<td style="min-width:300px;">';
				    echo '<table class="table"><thead><tr><th style="white-space:normal;">';
	        	        echo ($self_it->getId() != $project_it->getId() ? '{'.$self_it->get('CodeName').'} ' : '').
	        	            translate('Итерация').': '.$iteration_it->getDisplayName();
	    		    echo '</th></tr></thead>';
	    		    
	    		    echo '<tbody><tr><td>';
	        		    $this->drawIteration($iteration_it, $part_it);
	    		    echo '</td></tr></tbody></table>';
    		    echo '</td>';
    		    $i++;
    		}
    		echo '</tr>';
		}
		echo '</table>';
	}
	
	function drawIteration( $iteration_it, $part_it )
	{
		$release_left_capacity = $iteration_it->getLeftCapacity();
		
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
			
			$text = preg_replace(
						array('/%1/', '/%2/', '/%3/'),
						array(round($used_volume, 1), round($full_volume, 1), abs(round($left_volume,2))),
						$left_volume < 0 ? text(1900) : text(1899)
				);
			echo '<div>'.$text.'</div>';
			
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
