<?php

 ////////////////////////////////////////////////////////////////////////////////////////////////////////
 class CoScheduledJobIterator extends OrderedIterator
 {
 	function getParameters()
 	{
 		if ( function_exists('json_decode') )
 		{
 			$json = json_decode( $this->getHtmlDecoded('Parameters'), true );
 			return !is_null($json) ? $json : array();
 		}
 		else
 		{
 			return array();
 		}
 	}
 	
 	function getType()
 	{
 		$parms = $this->getParameters();
 		return $parms['type']; 
 	}
 }

 ////////////////////////////////////////////////////////////////////////////////////////////////////////
 class CoScheduledJob extends Metaobject
 {
 	function CoScheduledJob() 
 	{
 		parent::Metaobject('co_ScheduledJob');
 		$this->defaultsort = ' OrderNum ';
 		
 		$this->addPersister( new DurationPersister() );
 	}
 	
 	function createIterator() 
 	{
 		return new CoScheduledJobIterator( $this );
 	}
 }

 ///////////////////////////////////////////////////////////////////////////////////////////////
 class DurationPersister extends ObjectSQLPersister
 {
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		$alias = $alias != '' ? $alias."." : "";
 		
		$object = $this->getObject();
  		$objectPK = $alias.$object->getClassName().'Id';
 		
 		array_push( $columns, 
 			" (SELECT UNIX_TIMESTAMP(ru.RecordModified) - UNIX_TIMESTAMP(ru.RecordCreated)" .
			"    FROM co_JobRun ru " .
			"   WHERE ru.ScheduledJob = ".$objectPK.
			"   ORDER BY ru.RecordModified DESC LIMIT 1) LastDuration " );

 		array_push( $columns, 
 			" (SELECT AVG(UNIX_TIMESTAMP(ru.RecordModified) - UNIX_TIMESTAMP(ru.RecordCreated)) " .
			"    FROM co_JobRun ru " .
			"   WHERE ru.ScheduledJob = ".$objectPK.") AverageDuration " );
 		
 		return $columns;
 	}
 } 
 
?>