<?php

class KanbanEstimationSection extends InfoSection
{
	var $object_it;
	
 	function __construct( $object_it )
 	{
 		$this->object_it = $object_it;
 		
 		parent::InfoSection();
 	}

 	function getIcon()
 	{
 	    return 'icon-time';
 	}
 	
 	function getCaption()
 	{
 		return text('kanban15');
 	}

 	function drawBody()
 	{
 		global $model_factory, $project_it;
 		
 		if ( !is_a($this->object_it, 'IteratorBase') ) return;
 		
 		$request = $model_factory->getObject('pm_ChangeRequest');
		
		$request->addFilter( new StatePredicate('terminal') );
		
		$avg_cycle_time = $request->getLifecycleDuration() / 24;
		
		$request = $model_factory->getObject('pm_ChangeRequest');
		
		$request->addFilter( new FilterInPredicate($this->object_it->idsToArray()) );
		$request->addFilter( new StatePredicate('notresolved') );
			
		$left_duration = round($request->getRecordCount() * $avg_cycle_time, 1);

		echo '<div class="tagscloud">';
			echo '<div class="line">';
				echo str_replace('%1', '<b>'.round($avg_cycle_time,1).'</b>', text('kanban6'));
			echo '</div>';
			echo '<div class="line">';
				echo str_replace('%1', '<b>'.$left_duration.'</b>', text('kanban7'));
			echo '</div>';
		echo '</div>';
	}
}  