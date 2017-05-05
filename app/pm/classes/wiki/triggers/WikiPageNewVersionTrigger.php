<?php

class WikiPageNewVersionTrigger extends SystemTriggersBase
{
    function add( $object_it )
    { 
        if ( !is_a($object_it->object, 'WikiPage') ) return;

        $record_data = $this->getRecordData();
        
        if ( $record_data['DocumentVersion'] == '' ) return;
        
        $this->createNewVersion( $object_it, $record_data['DocumentVersion'] );
    }
    
	function process( $object_it, $kind, $content = array(), $visibility = 1) 
	{
	}
	
	function createNewVersion( $object_it, $caption )
	{
	    global $model_factory;

		$model_factory->getObject('Snapshot')->add_parms( 
				array (
					'Caption' => $caption,
					'ListName' => 'branch',
					'ObjectId' => $object_it->getId(),
					'ObjectClass' => get_class($object_it->object),
					'SystemUser' => getSession()->getUserIt()->getId(),
					'Type' => 'branch'
				)
		);
	}
}
