<?php

include "ChangeLogIterator.php";
include "ChangeLogRegistry.php";
include "ChangeLogGranularityRegistry.php";
include "ChangeLogRegistryProjectTemplate.php";
include "persisters/ChangeLogDetailsPersister.php";
include "predicates/ChangeLogActionFilter.php";
include "predicates/ChangeLogExceptParticipantFilter.php";
include "predicates/ChangeLogExceptUserFilter.php";
include "predicates/ChangeLogFinishFilter.php";
include "predicates/ChangeLogItemFilter.php";
include "predicates/ChangeLogObjectFilter.php";
include "predicates/ChangeLogParticipantFilter.php";
include "predicates/ChangeLogStartFilter.php";
include "predicates/ChangeLogVisibilityFilter.php";
include "predicates/ChangeLogAccessFilter.php";

class ChangeLog extends Metaobject
{
 	function __construct( ObjectRegistrySQL $registry = null ) 
 	{
 		global $model_factory;
 		
 		parent::__construct('ObjectChangeLog', is_object($registry) ? $registry : new ChangeLogRegistry($this));
 		
 		$this->setAttributeType( 'Author', 'REF_pm_ParticipantId' );
 		
 		$this->addAttribute( 'ChangeDate', 'DATE', translate('Дата изменения'), false, false );

 		$this->addPersister( new ChangeLogDetailsPersister() );
 	}
 	
 	function createIterator() 
 	{
 		return new ChangeLogIterator( $this );
 	}
 	
 	function getNotificationEnabled()
 	{
 	    return false;
 	}
 	
 	function resetFilters()
 	{
 	    parent::resetFilters();
 	    
 	    $this->addFilter( new ChangeLogAccessFilter() );
 	}
 	
 	function getDefaultAttributeValue( $name )
 	{
 	}
}