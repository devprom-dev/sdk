<?php

include_once "PMSession.php";

include SERVER_ROOT_PATH."pm/classes/model/permissions/AccessPolicyPortfolio.php";
include SERVER_ROOT_PATH."pm/classes/model/ModelPortfolioOriginationService.php";
include SERVER_ROOT_PATH."pm/views/common/PageSettingPortfolioBuilder.php";

class SessionPortfolio extends PMSession
{
 	public function buildAccessPolicy( $cache_service )
 	{
 		return new AccessPolicyPortfolio( $cache_service, $this );
 	}

    protected function findProject( $info )
    {
    	if ( $info instanceof ProjectIterator ) return $info->copy();

	 	if ( $info instanceof OrderedIterator )
	 	{
			return getFactory()->getObject('Portfolio')->getExact( $info->getId() );
	 	}
        
        $project_it = getFactory()->getObject('Portfolio')->getAll();
        
        while( !$project_it->end() )
        {
            if ( $project_it->get('CodeName') == $info )
            {
                return $project_it->copy();
            }
            
            $project_it->moveNext();
        }
        
        return getFactory()->getObject('Project')->getEmptyIterator();
    }
    
 	function createBuilders()
 	{
 	    $builders = parent::createBuilders();
 	    
 	    $builders[] = new PageSettingPortfolioBuilder();
 	    
 	    return $builders;
 	}
    
    protected function buildProjectData( & $project_it )
    {
        $result = array();

        $result['project'] = $project_it;
        $result['linked'] = $project_it->getRef('LinkedProject');
        
        return $result;
    }
    
    protected function buildParticipantData()
 	{
        $result = array();
        $user_it = $this->getUserIt();

        $participant = new Participant();
        $result['participant'] = $participant->createCachedIterator( array( array(
            'pm_ParticipantId' => $user_it->getId(),
            'Project' => $this->getProjectIt()->getId(),
            'SystemUser' => $user_it->getId(),
            'Caption' => $user_it->getDisplayName(),
            'Email' => $user_it->get('Email')
        ))); 
        
        $result['roles'] = array (
            'lead' => true
        );
        $result['participant_roles'] = array (
            'lead' => true
        );
        return $result;
 	}

 	public function buildOriginationService( $cache_service ) {
 		return new ModelPortfolioOriginationService($this, $cache_service);
 	}
 	
 	function getLanguageUid() {
 	    return $this->getUserIt()->get('Language') == 2 ? 'EN' : 'RU';
 	}

    function getProjectObject() {
        return new Portfolio();
    }
}