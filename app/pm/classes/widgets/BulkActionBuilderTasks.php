<?php

include_once SERVER_ROOT_PATH."core/classes/widgets/BulkActionBuilder.php";

class BulkActionBuilderTasks extends BulkActionBuilder
{
 	function build( BulkActionRegistry $registry )
 	{
 		$object = $registry->getObject()->getObject();
 	 	if ( !getFactory()->getAccessPolicy()->can_modify($object) ) return;
 		
 		foreach( array('Project') as $attribute ) {
			$registry->addModifyAction(
					translate($object->getAttributeUserName($attribute)),
					$attribute
			);
 		}
 	}
}