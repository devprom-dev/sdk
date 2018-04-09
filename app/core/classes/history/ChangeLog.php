<?php

include "ChangeLogIterator.php";
include "ChangeLogRegistry.php";
include "ChangeLogGranularityRegistry.php";
include "ChangeLogRegistryProjectTemplate.php";
include "predicates/ChangeLogActionFilter.php";
include "predicates/ChangeLogExceptParticipantFilter.php";
include "predicates/ChangeLogExceptUserFilter.php";
include "predicates/ChangeLogFinishFilter.php";
include "predicates/ChangeLogItemFilter.php";
include "predicates/ChangeLogItemDateFilter.php";
include "predicates/ChangeLogObjectFilter.php";
include "predicates/ChangeLogParticipantFilter.php";
include "predicates/ChangeLogStartFilter.php";
include "predicates/ChangeLogVisibilityFilter.php";
include "predicates/ChangeLogAccessFilter.php";
include "predicates/ChangeLogStartServerFilter.php";
include "sorts/SortChangeLogRecentClause.php";

class ChangeLog extends Metaobject
{
 	function __construct( ObjectRegistrySQL $registry = null ) 
 	{
 		parent::__construct('ObjectChangeLog', is_object($registry) ? $registry : new ChangeLogRegistry($this));
 		
 		$this->setAttributeType( 'Author', 'REF_pm_ParticipantId' );
 		$this->setAttributeType( 'Content', 'WYSIWYG' );
 		
 		$this->setSortDefault( array(
 		    new SortAttributeClause('RecordCreated.D')
 		));
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

	function beforeDelete($deleted_it) {
	}

	function IsDeletedCascade($object) {
		return false;
	}

	function IsUpdatedCascade($object) {
		return false;
	}

	function add_parms($parms)
	{
		$parms['Transaction'] = self::getTransaction();
		return parent::add_parms($parms);
	}

	public static function getTransaction()
	{
		if ( static::$transactionId != '' ) return static::$transactionId;
		return static::$transactionId = md5(microtime().uniqid('', true));
	}

	public static $transactionId = '';
}