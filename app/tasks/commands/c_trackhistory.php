<?php

include_once SERVER_ROOT_PATH.'pm/classes/sessions/PMSession.php';
include_once SERVER_ROOT_PATH.'pm/classes/workflow/WorkflowModelBuilder.php';

class TrackHistory extends TaskCommand
{
 	function execute()
	{
		global $model_factory;

		$this->logStart();
		
		$job_data_it = $this->getData();
		
		$parameters = $job_data_it->getParameters();
		
		$step = $parameters['limit'] > 0 ? $parameters['limit'] : 30;
				
		$job_it = $this->getJob();
		
		if ( $job_it->count() > 0 )
		{
			$job_it->delete();
			
			$this->processChunk( preg_split('/,/', $job_it->get('Parameters')) );
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
	
	function processChunk( $projects )
	{
		global $session;
		
		$auth_factory = new AuthenticationFactory();
		$auth_factory->setUser( getFactory()->getObject('cms_User')->getEmptyIterator() );
				
		$history = getFactory()->getObject('PMEntityCluster');
		$project_it = getFactory()->getObject('pm_Project')->getExact( $projects );

		while ( !$project_it->end() )
		{
			$this->logDebug("Track history for: ".$project_it->getDisplayName()." [".$project_it->get('CodeName')."]");
			
			$session = new PMSession($project_it->copy(), $auth_factory);
			$session->addBuilder( new WorkflowModelBuilder() );

			getFactory()->setAccessPolicy(new AccessPolicy(getFactory()->getCacheService()));
			getFactory()->resetCache();

			$history->deleteLatest();
			
			$cluster_parms = array();
			
    		$object_it = getFactory()->getObject('HistoricalObjects')->getAll();
    		while ( !$object_it->end() )
    		{
    			$cluster_parms[$object_it->getId()] = $object_it->get('attributes');
    			$object_it->moveNext();
    		}

			foreach ( $cluster_parms as $entity => $attributes )
			{
				$this->logDebug("Process entity: ".$entity);
				
				$class_name = getFactory()->getClass($entity);
				if ( !class_exists($class_name) ) continue;
				
				$object = getFactory()->getObject($class_name);
				$persisters = $object->getPersisters();

				foreach ( $attributes as $attribute )
				{
					$this->logDebug("Process attribute: ".$attribute);
					$object->resetAggregates();

					$group = new AggregateBase( $attribute, $object->getClassName().'Id', 'GROUP_CONCAT');
					$object->addAggregate( $group );
					
					$it = $object->getAggregated( 't', array(), $persisters );
					while ( !$it->end() )
					{
						$items = $it->get($group->getAggregateAlias());

						$this->logDebug("Found records: ".count(preg_split('/,/', $items)));
						
						$history->add_parms(
							array (
								'ObjectClass' => get_class($object),
								'ObjectAttribute' => $attribute,
								'AttributeValue' => $it->get($attribute),
								'ObjectIds' => ','.$items.',',
								'TotalCount' => count(preg_split('/,/', $items)),
								'RecordModified' => SystemDateTime::date()
							)
						);

						$it->moveNext();
					}
				}
			}
	
			$project_it->moveNext();
		}
	}

  	function logInfo( $message )
 	{
 		$log = $this->getLogger();
 		if( is_object($log) ) $log->info( $message );
 	}

  	function logDebug( $message )
 	{
 		$log = $this->getLogger();
 		if( is_object($log) ) $log->debug( $message );
 	}
}
