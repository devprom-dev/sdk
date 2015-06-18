<?php

use Devprom\ProjectBundle\Service\Project\StoreMetricsService;

include_once SERVER_ROOT_PATH.'pm/classes/sessions/PMSession.php';

class ProcessStatistics extends TaskCommand
{
 	function execute()
	{
		$this->logStart();
		
		getFactory()->getObject('Calendar')->getAll();
		
		$job_data_it = $this->getData();
		
		$parameters = $job_data_it->getParameters();
		
		$step = $parameters['limit'] > 0 ? $parameters['limit'] : 30;
		
		$job_it = $this->getJob();
		
		if ( $job_it->count() > 0 )
		{
			while ( !$job_it->end() )
			{
				$this->processChunk( preg_split('/,/', $job_it->get('Parameters')) );
				
				$job_it->delete();

				$job_it->moveNext();
			}
		}
		else
		{
			$ids = getFactory()->getObject('pm_Project')->getRegistry()->Query(
					array( new FilterAttributePredicate('IsClosed', 'N') )
				)->idsToArray();
			
			$chunks = array_chunk($ids, $step);
	
			$immediate_chunk = array_shift( $chunks );
			
			$this->processChunk( $immediate_chunk );
			
			foreach ( $chunks as $chunk )
			{
				$this->addJob(join(',', $chunk));
			}
		}

		$this->logFinish();
	}
	
	function processChunk( $chunk )
	{
		global $model_factory, $session;
		
		$auth_factory = new AuthenticationFactory();
			
		$auth_factory->setUser( $model_factory->getObject('cms_User')->getEmptyIterator() );
		
		$project = $model_factory->getObject('pm_Project');
		
		$project_it = $project->getInArray('pm_ProjectId', $chunk );

		while ( !$project_it->end() )
		{
			$session = new PMSession($project_it->get('CodeName'), $auth_factory);

			$service = new StoreMetricsService();
			$service->execute($project_it);
			
			$project_it->moveNext();
		}
	}
}
