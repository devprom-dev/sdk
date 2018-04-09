<?php
// PHPLOCKITOPT NOENCODE
// PHPLOCKITOPT NOOBFUSCATE

class ChangesWaitLockReleaseTrigger extends SystemTriggersBase
{
	private $affected_queue = array();
	private $skipped_entities = array();
	private $skipClasses = array(
	    'Metaobject',
        'Object',
        'StoredObjectDB',
        'MetaobjectCacheable',
        'MetaobjectStatable',
        'PMCustomAttribute'
    );
    static $declaredClasses = array();
    static $parentClasses = array();
    static $childrenClasses = array();
	
	public function __construct()
	{
        if ( count(self::$declaredClasses) < 1 ) {
            self::$declaredClasses = get_declared_classes();
        }
		$this->skipped_entities = array (
			'co_JobRun',
			'pm_ProjectUse',
			'ObjectChangeLog',
			'ObjectChangeLogAttribute',
			'EmailQueue',
			'EmailQueueAddress',
			'cms_EntityCluster',
			'pm_StateObject',
            'cms_SnapshotItem',
            'cms_SnapshotItemValue',
            'Priority',
            'TaskType'
		);
        $this->finalize();
	}

	public function __sleep() {
        return array('skipped_entities', 'skipClasses');
    }

    public function __wakeup() {
        $this->finalize();
    }

    protected function finalize() {
        register_shutdown_function(array($this, 'terminate'));
    }

    protected function getClassParents( $className ) {
        if ( !is_array(self::$parentClasses[$className]) ) {
            self::$parentClasses[$className] = class_parents($className);
        }
        return self::$parentClasses[$className];
    }

    public function terminate()
	{
        if ( !\DeploymentState::IsInstalled() ) return;

        // drop old records (purge the queue)
        DAL::Instance()->Query(
            "DELETE FROM co_AffectedObjects WHERE UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(RecordModified) > 40 "
        );
		if ( count($this->affected_queue) < 1 ) return;

		foreach( $this->affected_queue as $item ) {
			DAL::Instance()->Query(
				"INSERT INTO co_AffectedObjects (RecordCreated, RecordModified, ObjectClass, ObjectId, VPD, DocumentId) 
					VALUES (NOW(), NOW(), '".$item['ObjectClass']."', ".$item['ObjectId'].", '".$item['VPD']."', ".($item['DocumentId'] == '' ? 'NULL' : $item['DocumentId']).") "
			);
		}

		foreach( $this->affected_queue as $item ) {
			$lock = new LockFileSystem($item['ObjectClass']);
			$lock->Release();
		}
		$lock = new LockFileSystem('ChangeLogAggregated');
		$lock->Release();
	}
	
	function process( $object_it, $kind, $content = array(), $visibility = 1) 
	{
		if ( $object_it->object instanceof AffectedObjects ) return; // avoid infinite recursion
		if ( $object_it->object instanceof CoScheduledJob ) return;
		if ( in_array($object_it->object->getEntityRefName(), $this->skipped_entities) ) return; // skip unnesessary events
		
		// put itself in the queue
		$class_name = get_class($object_it->object);
		
		if ( !in_array($class_name, $this->skipClasses) ) {
			$this->storeAffectedRows($class_name, $object_it);
			
			foreach( $this->getDescendants($class_name) as $class ) {
				$this->storeAffectedRows($class, $object_it);
			}
			foreach( $this->getClassParents($class_name) as $class ) {
				if ( in_array($class, $this->skipClasses) ) break;
				$this->storeAffectedRows($class, $object_it);
			}
		}
	    
	    // put references in the queue
    	foreach( $object_it->object->getAttributes() as $attribute => $data )
    	{
    		if ( $object_it->get($attribute) == '' ) continue;
    		if ( !$object_it->object->IsReference($attribute) ) continue;
    		if ( in_array($attribute, array("Project","DocumentId","ParentPage")) ) continue;
    		
    		$ref = $object_it->object->getAttributeObject($attribute);
    		if ( in_array($ref->getEntityRefName(), array('cms_User', 'pm_Participant')) ) continue;
    		
    		$class_name = get_class($ref);
    		
    		$ref_it = $object_it->getRef($attribute);
   			$this->storeAffectedRows($class_name, $ref_it);
   			
			foreach( $this->getDescendants($class_name) as $class ) {
				$this->storeAffectedRows($class, $ref_it);
			}
    		foreach( $this->getClassParents($class_name) as $class ) {
				if ( in_array($class, $this->skipClasses) ) break;
				$this->storeAffectedRows($class, $ref_it);
			}
    	}

		// put specific references not covered by metadata
	    foreach( $this->getCustomReferences($kind, $object_it) as $class_name => $ref_it ) {
			$this->storeAffectedRows($class_name, $ref_it);
	    }
	}
	
	function storeAffectedRows( $class_name, $object_it )
	{
		while( !$object_it->end() )
		{
			if ( !is_numeric($object_it->getId()) ) {
				$object_it->moveNext();
				continue;
			}
			$this->affected_queue[] = array (
				'ObjectClass' => $class_name,
				'ObjectId' => $object_it->getId(),
				'VPD' => $object_it->get('VPD'),
                'DocumentId' => $object_it->object->getEntityRefName() == 'WikiPage' ? $object_it->get('DocumentId') : ''
			);
			$object_it->moveNext();
		}
		$object_it->moveFirst();
	}
	
	function getDescendants( $class_name )
	{
		if ( $class_name == 'Metaobject' ) return array();
		if ( $class_name == 'Object' ) return array();

        if ( is_array(self::$childrenClasses[$class_name]) ) {
            return self::$childrenClasses[$class_name];
        }
 		return self::$childrenClasses[$class_name] = array_filter( self::$declaredClasses, function($value) use($class_name) {
 			return is_subclass_of($value, $class_name);
 		});
	}
	
	function getCustomReferences( $kind, $object_it )
	{
		switch ( $object_it->object->getEntityRefName() )
		{
		    case 'pm_Activity':
				if ( $object_it->get('Task') == '' ) return array();
		    	$ref_it = $object_it->getRef('Task');
		    	
		    	if ( $ref_it instanceof TaskIterator && $ref_it->object->getAttributeType('ChangeRequest') != '' )
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
		    
		    case 'pm_ChangeRequest':
				$classes = array('WorkItem' => $object_it->copy());
		    	if ( $object_it->get('Links') != '' ) {
					$classes = array_merge(
						$classes,
						array('Request' => $object_it->getRef('Links'))
						);
		    	}
		    	return $classes;

			case 'pm_Task':
				$classes = array('WorkItem' => $object_it->copy());
				return $classes;

		    case 'pm_Release':
		    	return array( 
		    		'Stage' => $object_it->copy()
		    	);
		    case 'pm_Version': 
		    	return array( 
		    		'Stage' => $object_it->copy()
		    	);

		    case 'pm_ParticipantRole':
		    	return array( 
		    		'User' => $object_it->getRef('Participant')->getRef('SystemUser') 
		    	);

		    case 'pm_Participant':
		    	return array( 
		    		'User' => $object_it->getRef('SystemUser') 
		    	);
		    	
		    case 'WikiPageTrace':
				if ( $object_it->get('SourcePage') == '' ) return array();
				if ( $object_it->get('TargetPage') == '' ) return array();
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
		
		if ( $object_it->object instanceof Stage )
		{
			if ( $object_it->get('Release') != '' ) { 
		    	return array( 
		    		'Iteration' => getFactory()->getObject('Iteration')->getExact($object_it->get('Release')),
		    	);
			}
			else {
		    	return array( 
		    		'Release' => getFactory()->getObject('Release')->getExact($object_it->get('Version'))
		    	);
			}
		}
		
		if ( $object_it->object instanceof Watcher || $object_it->object instanceof Comment )
		{
	    	$ref_it = $object_it->getAnchorIt();
	    	if ( $ref_it->getId() != '' ) {
		    	return array_merge(
		    	    array(
		    		    get_class($ref_it->object) => $ref_it
                    ),
                    $this->getCustomReferences($kind, $ref_it)
		    	);
	    	}
		}
		
		return array();		
	}
}
 