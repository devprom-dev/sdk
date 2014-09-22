<?php

include_once SERVER_ROOT_PATH."core/methods/ExportWebMethod.php";

class MakeSnapshotWebMethod extends ExportWebMethod
{
 	function getCaption()
 	{
 		return text(1557);
 	}
 	
 	function getLink( $anchor_it, $object_it )
 	{
 		$list_id = join(':', array(get_class($anchor_it->object), $anchor_it->getId()));
 		
 		return getFactory()->getObject('Snapshot')->getMakePage($anchor_it, $object_it, $list_id);
 	}
 	
 	function getJSCall()
 	{
 		return parent::getJSCall( array( 
 				'class' => IteratorExportSnapshot,
 				'redirect' => $_SERVER['REQUEST_URI'] 
 		));
 	}
}
