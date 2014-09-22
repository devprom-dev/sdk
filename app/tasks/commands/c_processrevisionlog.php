<?php

include_once SERVER_ROOT_PATH.'pm/classes/sessions/PMSession.php';

class ProcessRevisionLog extends TaskCommand 
{
 	function execute()
	{
		$this->logStart();
		
		$step = 5;
		
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
			$subversion_it = getFactory()->getObject('pm_Subversion')->getRegistry()->getAll();
			
			$ids = $subversion_it->idsToArray();
			
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

		$user = getFactory()->getObject('cms_User');
		
		$scm_it = getFactory()->getObject('pm_Subversion')->getRegistry()->Query( 
				array (
						new FilterInPredicate($chunk)		
				)
		);
		
		while ( !$scm_it->end() )
		{
			$this->logInfo( "Check for revisions on: ".$scm_it->getDisplayName() );

			$project_it = $scm_it->getRef('Project');

			$auth_factory = new AuthenticationFactory();
			
			$auth_factory->setUser( $user->getEmptyIterator() );
			
			$session = new PMSession($project_it, $auth_factory);
			
			ob_start();

			$object = getFactory()->getObject('pm_Subversion');
			
			if ( !$object instanceof Subversion )
			{
				$scm_it->moveNext(); continue;
			}
			
			$it = $object->getExact($scm_it->getId());
			 
			$connector = $it->getConnector();
			
			$it->refresh();
			
			$this->logInfo( ob_get_contents());
			
			ob_end_clean();
			
			$scm_it->moveNext();
		}
	}

  	function logInfo( $message )
 	{
 		$log = $this->getLogger();
 		if( is_object($log) ) $log->info( $message );
 	}
}
