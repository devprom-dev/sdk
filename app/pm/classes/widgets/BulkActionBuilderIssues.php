<?php

include_once SERVER_ROOT_PATH."core/classes/widgets/BulkActionBuilder.php";

class BulkActionBuilderIssues extends BulkActionBuilder
{
 	function build( BulkActionRegistry $registry )
 	{
 		$object = $registry->getObject()->getObject();
 	 	if ( !getFactory()->getAccessPolicy()->can_modify($object) ) return;
 		
 		$registry->addAction(text(861), 'Method:ModifyRequestWebMethod:Tag');
 		$registry->addAction(text(862), 'Method:ModifyRequestWebMethod:RemoveTag');
 		
		$method = new DuplicateIssuesWebMethod();
		if ( $method->hasAccess() ) $registry->addAction(text(867), $method->getMethodName());
 	}
}