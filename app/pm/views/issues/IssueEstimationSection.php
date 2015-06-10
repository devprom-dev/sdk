<?php

class IssueEstimationSection extends InfoSection
{
    var $object_it;
     
    function __construct( $object_it = null )
    {
        parent::__construct();
        
        $this->object_it = $object_it;
    }
     
 	function getCaption()
 	{
        return text(821);
 	}

 	function getIcon()
 	{
 	    return 'icon-tasks';
 	}
 	
	function IsActive()
	{
		return getSession()->getProjectIt()->getMethodologyIt()->RequestEstimationUsed()
		    && getSession()->getProjectIt()->getMethodologyIt()->HasVelocity(); 
	}
 	
 	function drawBody()
 	{
 	    if ( !is_object($this->object_it) ) return;

 	    if ( !is_a($this->object_it, 'IteratorBase') ) return;
 	    
 	    $this->drawEstimations( $this->object_it );
	}
	
	function drawEstimations( $request_it )
	{
 		global $project_it;
 		
 		if ( $request_it->count() < 1 )
 		{
 			return;
 		}
 		
 		$methodology_it = getSession()->getProjectIt()->getMethodologyIt();
 		
 		$strategy = $methodology_it->getEstimationStrategy();
 		
		list($total_open, $percent) = $strategy->getEstimationByIt( $request_it );  
 		
		echo '<div class="line">';
			echo str_replace('%1', $total_open, $strategy->getEstimationText());
		echo '</div>';

		$velocity = $project_it->getTeamVelocity();
		
		$duration = min(0, $methodology_it->getReleaseDuration() * $project_it->getDaysInWeek());
		
		if ( $methodology_it->HasFixedRelease() && $duration > 0 )
		{
			$velocity /= $duration;
		}

		echo '<div class="line">';
			if ( $velocity == 0 )
			{
				$duration = '?';
			}
			else
			{
				$duration = round($total_open / $velocity, 1);
			}
			
			echo translate('Оценка срока выполнения').': <b>'.$duration.' '.translate('дн.').'</b>';
		echo '</div>';

		if ( $strategy->hasEstimationValue() )
		{
			echo '<div class="line">';
				echo translate('Оценено пожеланий').': <b>'.$percent.'%</b>';
			echo '</div>';
		}
	}
}  