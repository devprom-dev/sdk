<?php

include_once SERVER_ROOT_PATH."pm/classes/common/SharedObjectsBuilder.php";

class SharedObjectsPlanBuilder extends SharedObjectsBuilder
{
    public function getGroup()
    {
        return 'Releases';
    }
    
    public function checkSharedInProject( $project_it )
    {
        return true;
    }
    
    public function build( SharedObjectRegistry & $set )
    {
		$entities = array (
    			'Milestone',
		        'Version',
     	        'Release',
     	        'Iteration',
				'Stage'
		);
		
 		foreach( $entities as $key )
		{
			$set->add( $key, $this->getGroup() );
		} 	            
    }
}