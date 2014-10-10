<?php

include_once SERVER_ROOT_PATH.'core/classes/model/events/SystemTriggersBase.php';
include_once SERVER_ROOT_PATH.'core/classes/system/LockFileSystem.php';

class ChangesWaitLockReleaseTrigger extends SystemTriggersBase
{
	private $affected = null;
	
	private $classes_list = array();
	
	public function __construct()
	{
		$this->affected = getFactory()->getObject('AffectedObjects');
	}
	
	function process( $object_it, $kind, $content = array(), $visibility = 1) 
	{
		if ( $object_it->object instanceof AffectedObjects ) return; // avoid infinite recursion
		
		// skip unnesessary events
		if ( $object_it->object instanceof CoScheduledJob ) return;
		
		$skipped_entities = array (
				'co_JobRun', 
				'pm_ProjectUse', 
				'ObjectChangeLog', 
				'ObjectChangeLogAttribute', 
				'EmailQueue', 
				'EmailQueueAddress',
				'cms_EntityCluster'
		);
		
		if ( in_array($object_it->object->getEntityRefName(), $skipped_entities) ) return;
		
		$this->classes_list = array();
		
		// put itself in the queue
		$class_name = get_class($object_it->object);
		
		if ( strtolower($class_name) != 'metaobject' )
		{
			$this->storeAffectedRows($class_name, $object_it);
			
			foreach( $this->getDescendants($class_name) as $class )
			{
				$this->storeAffectedRows($class, $object_it);
			}
		}
	    
	    // put references in the queue
    	foreach( $object_it->object->getAttributes() as $attribute => $data )
    	{
    		if ( !$object_it->object->IsReference($attribute) ) continue;

    		if ( in_array($attribute, array("DocumentId","ParentPage")) ) continue;
    		
    		if ( $object_it->get($attribute) == '' ) continue;
    		
    		$class_name = get_class($object_it->object->getAttributeObject($attribute));
    		
    		$ref_it = $object_it->getRef($attribute);
    		
   			$this->storeAffectedRows($class_name, $ref_it);
   			
			foreach( $this->getDescendants($class_name) as $class )
			{
				$this->storeAffectedRows($class, $ref_it);
			}
    	}

		// put specific references not covered by metadata
	    foreach( $this->getCustomReferences($kind, $object_it) as $class_name => $ref_it )
	    {
			$this->storeAffectedRows($class_name, $ref_it);
	    }

	    // drop old records (purge the queue)
	    $mapper = new ModelDataTypeMappingDate();
	    
		DAL::Instance()->Query( 
		 		"DELETE FROM co_AffectedObjects WHERE RecordModified <= '".
		 				$mapper->map(
		 						strftime('%Y-%m-%d %H:%M:%S', strtotime('-10 minutes', strtotime(SystemDateTime::date())))
         				)."' "
        );
	    
		// notify listeners data has been refreshed
	    $this->classes_list[] = 'ChangeLogAggregated';
	    
		foreach( array_unique($this->classes_list) as $class_name )
		{
	        $lock = new LockFileSystem( $class_name );
	        
	        $lock->Release();
		}	
	}
	
	function storeAffectedRows( $class_name, $object_it )
	{
		while( !$object_it->end() )
		{
			if ( !is_numeric($object_it->getId()) )
			{
				$object_it->moveNext();
				continue;
			}				

			$this->affected->setNotificationEnabled(false);
		
			$this->affected->setVpdContext($object_it);
			
			$this->affected->add_parms(
					array (
							'ObjectClass' => $class_name,
							'ObjectId' => $object_it->getId()
					)
			);
			
			$object_it->moveNext();
		}
		
		$object_it->moveFirst();

		$this->classes_list[] = $class_name;
	}
	
	function getDescendants( $class_name )
	{
		if ( $class_name == 'Metaobject' ) return array();
		
 		return array_filter( get_declared_classes(), function($value) use($class_name) 
 		{
 			return is_subclass_of($value, $class_name);
 		});
	}
	
	function getCustomReferences( $kind, $object_it )
	{
		switch ( $object_it->object->getEntityRefName() )
		{
		    case 'Comment':
		    	$data = $this->getRecordData();

		    	if ( $kind == TRIGGER_ACTION_DELETE || $data['DoNotAffectObjects'] ) break;
			
			case 'pm_Watcher':
		    	$ref_it = $object_it->getAnchorIt();
		    	
		    	if ( is_object($ref_it) )
		    	{
			    	return array( 
			    		get_class($object_it->getAnchorIt()->object) => $object_it->getAnchorIt() 
			    	);
		    	}
		    	
		    	break;
		    	
		    case 'pm_Activity':
		    	$ref_it = $object_it->getRef('Task');
		    	
		    	if ( $ref_it instanceof TaskIterator )
		    	{
			    	return array( 
			    		'Request' => $ref_it->getRef('ChangeRequest')
			    	);
		    	}
		    	else
		    	{
			    	return array( 
			    		'Request' => $ref_it
			    	);
		    	}
		    
		    case 'pm_Release':
		    case 'pm_Version': 
		    	return array( 
		    		'Stage' => $object_it->copy()
		    	);

		    case 'pm_ParticipantRole':
		    	return array( 
		    		'User' => $object_it->getRef('Participant')->getRef('SystemUser') 
		    	);
		    	
		    case 'WikiPageTrace':
		    	return array( 
		    		'Requirement' => $object_it->getRef('SourcePage'), 
		    		'Requirement' => $object_it->getRef('TargetPage'), 
		    		'TestScenario' => $object_it->getRef('SourcePage'),
		    		'TestScenario' => $object_it->getRef('TargetPage')
		    	);

		    case 'pm_AttributeValue':
		    	
		    	$class = getFactory()->getClass($object_it->getRef('CustomAttribute')->get('EntityReferenceName'));
		    	
		    	if ( !class_exists($class) ) return array();
		    	
		    	$ref_object = getFactory()->getObject($class);
		    	
		    	return array (
		    			get_class($ref_object) => $ref_object->getExact($object_it->get('ObjectId'))
		    	);
		}
		
		return array();		
	}
}
 