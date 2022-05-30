<?php

include_once SERVER_ROOT_PATH.'pm/classes/sessions/PMSession.php';

class ProcessRevisionLog extends TaskCommand 
{
 	function execute()
	{
		if ( !class_exists('Subversion', false) ) return;

		$this->logStart();

		$ids = $this->getChunk(); 

		// shift the next chunk
		if ( count($ids) < 1 )
		{
			$job_it = $this->getJob();
			if ( $job_it->getId() > 0 )
			{
				$ids = array_filter(preg_split('/,/', $job_it->get('Parameters')), function($value) {
							return $value > 0;
				});
				$job_it->object->delete($job_it->getId());
			}
		}

		// pull new chunks
		if ( count($ids) < 1 )
		{
			$repo = getFactory()->getObject('pm_Subversion');
			$ids = $repo->getRegistry()->QueryKeys(
						array( new RepositoryActivePredicate() )
				)->idsToArray();

			$parameters = $this->getData()->getParameters();
			$step = $parameters['limit'] > 0 ? $parameters['limit'] : 30;
			
			$chunks = array_chunk($ids, $step);
			$ids = array_shift( $chunks );
			
			foreach ( $chunks as $chunk ) {
				$this->addJob(join(',', $chunk));
			}
		}
		
		if ( count($ids) > 0 )
		{
			$this->processChunk( $ids );
		}
		
		$this->logFinish();
	}

	function processChunk( $chunk )
	{
		global $session;

		$scm_it = getFactory()->getObject('pm_Subversion')->getRegistry()->Query(
            array (
                new FilterInPredicate($chunk)
            )
		);
		
		while ( !$scm_it->end() )
		{
			$this->logInfo( "Check for revisions on: ".$scm_it->getDisplayName().' (id: '.$scm_it->getId().')' );

			$project_it = $scm_it->getRef('Project');

			$auth_factory = new AuthenticationFactory();
			$auth_factory->setUser( getFactory()->getObject('cms_User')->getSuperUserIt() );
			
			$session = new PMSession($project_it->copy(), $auth_factory);
			getFactory()->setAccessPolicy(new AccessPolicy(getFactory()->getCacheService()));
			
			$object = getFactory()->getObject('pm_Subversion');
			
			if ( !$object instanceof Subversion ) {
				$scm_it->moveNext(); continue;
			}

			$method = new CodeRefreshRepoWebMethod($object->getExact($scm_it->getId()));
            $method->execute_request();

			$scm_it->moveNext();
		}
	}

  	function logInfo( $message )
 	{
 		$log = $this->getLogger();
 		if( is_object($log) ) $log->info( $message );
 	}
}
