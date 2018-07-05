<?php

class CacheSessionProjectTrigger extends SystemTriggersBase
{
	private $invalidate = false;

	function process( $object_it, $kind, $content = array(), $visibility = 1) 
	{
        // clean session data
		switch( $object_it->object->getEntityRefName() )
		{
			case 'pm_AccessRight':
			case 'pm_ObjectAccess':
			case 'pm_ParticipantRole':
			case 'pm_ProjectRole':
			case 'pm_IssueType':
			case 'pm_TaskType':
			case 'WikiPageType':
            case 'cms_Resource':
            case 'pm_WorkspaceMenu':
            case 'pm_WorkspaceMenuItem':
            case 'pm_CustomReport':
            case 'pm_State':
            case 'pm_Transition':
            case 'pm_TransitionPredicate':
            case 'pm_TransitionRole':
            case 'pm_TransitionAttribute':
            case 'pm_StateAttribute':
                $this->invalidateProjectCache();
			    break;

            case 'pm_Project':
			case 'pm_ProjectLink':
            case 'pm_Participant':
            case 'pm_Methodology':
            case 'pm_CustomAttribute':
				$this->invalidateProjectsCache();
				break;

            default:
                if ( $object_it->object instanceof RequestTemplate ) {
                    $this->invalidateProjectCache();
                }
		}
	}

    public function invalidateProjectCache()
    {
        getFactory()->getAccessPolicy()->invalidateCache();
        getFactory()->getEntityOriginationService()->invalidateCache();
        getFactory()->getCacheService()->setReadonly();
        getFactory()->getCacheService()->invalidate('sessions');
        getSession()->truncate();
    }

	public function invalidateProjectsCache()
	{
        getFactory()->getAccessPolicy()->invalidateCache();
        getFactory()->getEntityOriginationService()->invalidateCache();
        getFactory()->getCacheService()->setReadonly();
        getFactory()->getCacheService()->invalidate('sessions');
        getFactory()->getCacheService()->invalidate('projects');
		$this->invalidate = true;
	}

	function __destruct()
	{
		if ( !$this->invalidate ) return;
		if ( function_exists('opcache_reset') ) opcache_reset();
	}
}
 