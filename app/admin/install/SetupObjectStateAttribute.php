<?php

class SetupObjectStateAttribute extends Installable
{
	// checks all required prerequisites
	function check()
	{
		return true;
	}

	// skip install actions
	function skip()
	{
		return false;
	}

	// cleans after the installation script has been executed
	function cleanup()
	{
	}

	// makes install actions
	function install()
	{
	    $map = array (
	        'request' => 'pm_ChangeRequest',
	        'question' => 'pm_Question',
	        'requirement' => 'WikiPage',
	        'task' => 'pm_Task',
	        'testscenario' => 'WikiPage',
	        'storymappingperson' => 'sm_Person',
	        'storymappingaim' => 'sm_Aim',
            'storymappingactivity' => 'sm_Activity',
	        'storymappingaction' => 'sm_Action' 
	    );
	    
	    foreach ( $map as $class => $entity )
	    {
	        $this->info( 'Process class: '.$class );
	        
	        if ( $this->checkAttribute( $entity ) ) continue;
	        
	        // append attribute
			$sql = " alter table ".$entity." add column StateObject INTEGER ";
			
			// copy data
			$sql = " update ".$entity." set StateObject = (select t.pm_StateObjectId from pm_StateObject t where t.ObjectId = ".$entity."Id and t.ObjectClass = '".$class."' order by t.pm_StateObjectId DESC limit 1) where StateObject is null ";
	    }
	    
		return true;
	}
	
	function checkAttribute( $table )
	{
	    global $model_factory;
	    
	    $entity = $model_factory->getObject('entity');
	    
	    $entity_it = $entity->createSQLIterator('describe '.$table);
	    
	    $found = false;
	    
	    while ( !$entity_it->end() )
	    {
	        if ( $entity_it->get('Field') == 'StateObject' )
	        {
	            $found = true; break;
	        }
	        
	        $entity_it->moveNext(); 
	    }
	    
	    return $found;
	}
}
