<?php

include_once SERVER_ROOT_PATH.'admin/classes/CheckpointFactory.php';

class SystemCheckRegistry extends ObjectRegistrySQL
{
	function createSQLIterator( $sql )
	{
	    $checkpoint_factory = getCheckpointFactory();
	    
	    $checkpoint = $checkpoint_factory->getCheckpoint( 'CheckpointSystem' );
	    	
	    $entries = $checkpoint->getEntries();
	    	
	    usort( $entries, "iterator_check_enabled_sort" );

	    $items = array();
	    
	    foreach ( $entries as $key => $entry )
	    {
	        $data = array();
	        
	        $data['cms_CheckpointId'] = $entry->getUid();
	        $data['Caption'] = $entry->getTitle();
	        $data['Description'] = $entry->getDescription();
	        $data['Value'] = $entry->getValue();
	        $data['CheckResult'] = $data['Value'] != '' ? ($entry->check() ? 'Y' : 'N') : '';
	        $data['IsEnabled'] = $entry->enabled() ? 'Y' : 'N';
	        
	        $items[] = $data;
	    }
	     
		return $this->createIterator( $items );
	}
}

class SystemCheck extends MetaobjectCacheable
{
	function __construct()
	{
		parent::__construct('cms_Checkpoint', new SystemCheckRegistry($this));
		
		$this->setAttributeType('Description', 'varchar');
	}
}

function iterator_check_enabled_sort( $left, $right )
{
	return $left->check() && $left->check() != $right->check() ? 1 : -1;	
}
