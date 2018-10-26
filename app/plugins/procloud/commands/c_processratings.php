<?php
/*
 * DEVPROM (http://www.devprom.net)
 * c_processratings.php
 *
 * Copyright (c) 2005, 2006 Evgeny Savitsky <admin@devprom.net>
 * You can modify this code freely for your own needs,
 * but you can't distribute it manually.
 * 
 */

 ////////////////////////////////////////////////////////////////////////////
 class ProcessRatings
 {
 	function execute()
	{
		global $model_factory, $project_it;

		$model_factory->enableVpd(false);
		$model_factory->object_factory->access_policy = new AccessPolicy;

		$job = new Metaobject('cms_BatchJob');
		$job_it = $job->getByRefArray(
			array ('Caption' => 'UserRating'), 1
			);
			
		if ( $job_it->count() > 0 )
		{
			$user = $model_factory->getObject('cms_User');
			$user_it = $user->getInArray('cms_UserId',
				preg_split('/[,\s]/', $job_it->get('Parameters')) );
			
			while ( !$user_it->end() )
			{
				$rating = new UserRating ( $user_it );
				$value = $rating->getValue();
				
				$user_it->modify(
					array ( 'Rating' => $value )
					);
					
				$user_it->moveNext();
			}
			
			$job->delete($job_it->getId());
		}

		$job = new Metaobject('cms_BatchJob');
		$job_it = $job->getByRefArray(
			array ('Caption' => 'TeamRating'), 1
			);
			
		if ( $job_it->count() > 0 )
		{
			$team = $model_factory->getObject('co_Team');
			$team_it = $team->getInArray('co_TeamId',
				preg_split('/[,\s]/', $job_it->get('Parameters')) );
			
			while ( !$team_it->end() )
			{
				$rating = new TeamRating ( $team_it );
				$value = $rating->getValue();
				
				$team_it->modify(
					array ( 'Rating' => $value )
					);
					
				$team_it->moveNext();
			}

			$job->delete($job_it->getId());
		}

		$job = new Metaobject('cms_BatchJob');
		$job_it = $job->getByRefArray(
			array ('Caption' => 'ProjectRating'), 1
			);
			
		if ( $job_it->count() > 0 )
		{
			$project = $model_factory->getObject('pm_Project');
			$project_it = $project->getInArray('pm_ProjectId',
				preg_split('/[,\s]/', $job_it->get('Parameters')) );
			
			while ( !$project_it->end() )
			{
				$rating = new ProjectRating ( $project_it );
				$value = $rating->getValue();
				
				$project_it->modify(
					array ( 'Rating' => $value )
					);
					
				$project_it->moveNext();
			}

			$job->delete($job_it->getId());
		}
	}
 }
 
?>