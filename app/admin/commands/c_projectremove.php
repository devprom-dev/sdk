<?php

include_once SERVER_ROOT_PATH.'pm/classes/sessions/PMSession.php';

class ProjectRemove extends Command
{
	function execute()
	{
		$this->logStart();
		
		if( !getSession()->getUserIt()->IsAdministrator() ) return;

		$ids = preg_split('/-/', $_REQUEST['project']);
		
		foreach( $ids as $project_id )
		{
		    $this->deleteProject( $project_id );
		}
			
		$this->logFinish();

		exit(header('Location: /admin/projects.php'));
	}
	
	function deleteProject( $project_id )
	{
	    $prj_cls = getFactory()->getObject('pm_Project');
	    
	    $object_it = $prj_cls->getExact($project_id);
	    
	    if ( $object_it->get('VPD') == '' ) return;

        $session = new PMSession($object_it);

        // use empty events manager
    	getFactory()->setEventsManager( new ModelEventsManager() );
        
    	$entities = array (
    			'ChangeLog',
    			'WikiPage',
    			'Request',
    			'Participant'
    	);
    	
    	foreach( $entities as $entity )
    	{
    		$object = getFactory()->getObject($entity);
    		
    		$object->setVpdContext($object_it->get('VPD'));
    		
    		$object->deleteAll();
    	}

    	$object_it->delete();

        getFactory()->getCacheService()->invalidate('sessions');
        getFactory()->getCacheService()->invalidate('projects');
	}
}
