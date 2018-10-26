<?php

 class PrepareJobRating 
 {
 	function execute()
	{
		global $model_factory;

		$user = $model_factory->getObject('cms_User');
		$user_it = $user->getAll();

		$this->makeJob( 'UserRating', $user_it );

		$team = $model_factory->getObject('co_Team');
		$team_it = $team->getAll();

		$this->makeJob( 'TeamRating', $team_it );

		$project = $model_factory->getObject('pm_Project');
		$project_it = $project->getAllPublicIt();
		
		$this->makeJob( 'ProjectRating', $project_it );
	}
	
	function makeJob( $job_name, $it )
	{
		$step = 20;
		$current = 0;
		$parameters = array();
		
		$job = new Metaobject('cms_BatchJob');
		$left = $job->getByRefArrayCount(
			array ( 'Caption' => $job_name )
			);

		if ( $left > 0 )
		{
			echo 'job wasn\'t processed yet';
			return;
		}
		
		while ( !$it->end() )
		{
			array_push( $parameters, $it->getId() );
			$current++;

			if ( $current > $step || $current >= $it->count() )
			{
				$job->add_parms(
					array ( 'Caption' => $job_name,
						    'Parameters' => join(',', $parameters) ) 
				);
					
				$parameters = array();
				$current = 0;
			}
			
			$it->moveNext();
		}		
	}
 }
 
?>
