<?php

include_once "ChangeLog.php";
include "ChangeLogAggregatedRegistry.php";
include "persisters/ChangeLogAggregatePersister.php";

class ChangeLogAggregated extends ChangeLog
{
    function __construct()
    {
        parent::__construct( new ChangeLogAggregatedRegistry($this) );
        
 		$this->addPersister( new ChangeLogAggregatePersister() );
 		
   		$system_attributes = array (
		        'ObjectId',
		        'ObjectUrl',
		        'VisibilityLevel',
		        'EntityRefName',
		        'ClassName'
		);
		
		foreach( $system_attributes as $attribute )
		{
 			$this->addAttributeGroup($attribute, 'system');
		}
    }
}