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
					array( new FilterHasNoAttributePredicate('IsClosed', 'Y') )
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
		$auth_factory = new AuthenticationFactory();
		$auth_factory->setUser( getFactory()->getObject('cms_User')->getEmptyIterator() );

		$project_it = getFactory()->getObject('pm_Project')->getInArray('pm_ProjectId', $chunk );
		while ( !$project_it->end() )
		{
			$session = new PMSession($project_it->copy(), $auth_factory);

			getFactory()->setAccessPolicy(new AccessPolicy(getFactory()->getCacheService()));
			getFactory()->resetCache();

			$project = getFactory()->getObject('pm_Project');
			foreach( $session->getBuilders('ProjectMetricsModelBuilder') as $builder ) {
				$builder->build($project);
			}

			$service = new StoreMetricsService();
			$service->execute($project->getExact($project_it->getId()));
			
			$project_it->moveNext();
		}
	}
}
