<?php

include_once SERVER_ROOT_PATH.'core/classes/model/events/SystemTriggersBase.php';

class CacheSessionProjectTrigger extends SystemTriggersBase
{
	private $invalidate = false;

	function process( $object_it, $kind, $content = array(), $visibility = 1) 
	{
		global $session;

        // clean session data		
		switch( $object_it->object->getEntityRefName() )
		{
			case 'pm_CustomAttribute':
			case 'pm_AccessRight':
			case 'pm_ObjectAccess':
			case 'pm_ParticipantRole':
			case 'pm_ProjectRole':
			case 'pm_Methodology':
			case 'pm_IssueType':
			case 'pm_TaskType':
			case 'WikiPageType':
				$this->invalidateCache();
			    break;

			case 'pm_State':
				$object = getFactory()->getObject($object_it->get('ObjectClass'));
				$action = new BulkAction($object);
				$action->resetCache();
				WorkflowScheme::Instance()->invalidate();
				break;

			case 'pm_Transition':
				$object = getFactory()->getObject($object_it->getRef('SourceState')->get('ObjectClass'));
				$action = new BulkAction($object);
				$action->resetCache();
				WorkflowScheme::Instance()->invalidate();
				break;

			case 'pm_TransitionPredicate':
			case 'pm_TransitionRole':
			case 'pm_TransitionAttribute':
			case 'pm_StateAttribute':
				WorkflowScheme::Instance()->invalidate();
				break;

			case 'cms_Resource':
				$this->invalidateCache();
 			    break;
			    
			case 'pm_CustomReport':
				getFactory()->getObject('PMReport')->resetCache();
				$this->invalidateCache();
				break;
			    
			case 'pm_ProjectLink':
			    // reset cached values for linked project
			    $project_it = getSession()->getProjectIt()->getRef('LinkedProject');
			    while( !$project_it->end() ) {
			        $session->truncateForProject($project_it);
			        $project_it->moveNext();
			    }
				$this->invalidateCache();
				break;
				
			case 'pm_Participant':
			    $session->truncateForProject( $object_it->getRef('Project') );

			    $portfolio_it = getFactory()->getObject('Portfolio')->getAll();
			    while( !$portfolio_it->end() ) {
			        $portfolio_it->setUser($object_it->getRef('SystemUser'));
			        $session->truncateForProject( $portfolio_it );
			        $portfolio_it->moveNext();
			    }
			    $this->invalidateCache();
				break;
				
			case 'pm_Project':
				$portfolio_it = getFactory()->getObject('Portfolio')->getAll();
				while( !$portfolio_it->end() ) {
					$session->truncateForProject( $portfolio_it );
					$portfolio_it->moveNext();
				}
				getFactory()->getObject('ProjectCache')->resetCache();
				$session->truncateForProject( $object_it );
				$this->invalidateCache();
         		break;
		}
	}
	
	public function invalidateCache()
	{
		getSession()->truncate();
		getFactory()->getAccessPolicy()->invalidateCache();
		getFactory()->getEntityOriginationService()->invalidateCache();
		// skip any cache modifications after it was truncated during the current script execution
		getFactory()->getCacheService()->setReadonly();
		$this->invalidate = true;
	}

	function __destruct()
	{
		if ( !$this->invalidate ) return;
		if ( function_exists('opcache_reset') ) opcache_reset();
	}
}
 