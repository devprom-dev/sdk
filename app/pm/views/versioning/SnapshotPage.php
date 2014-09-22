<?php

include "SnapshotTable.php";
include "SnapshotForm.php";

class SnapshotPage extends PMPage
{
 	function getTable() 
 	{
		return new SnapshotTable( $this->getObject() );
 	}
 	
 	function getObject()
 	{
 		global $model_factory;
 		return $model_factory->getObject('cms_Snapshot');
 	}
 	
 	function getForm() 
 	{
 		$object = $this->getObject();
 		
 		if ( $_REQUEST['cms_SnapshotId'] != '' )
 		{
 			$object_it = $object->getExact($_REQUEST['cms_SnapshotId']); 
 		}
 		
		return new SnapshotForm( is_object($object_it) && $object_it->getId() > 0 ? $object_it : $this->getObject() );
 	}
}