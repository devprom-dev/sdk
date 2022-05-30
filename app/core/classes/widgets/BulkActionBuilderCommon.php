<?php
include_once SERVER_ROOT_PATH."core/classes/widgets/BulkActionBuilder.php";

class BulkActionBuilderCommon extends BulkActionBuilder
{
 	function build( BulkActionRegistry $registry )
 	{
 		$object = $registry->getObject()->getObject();

 	 	if ( getFactory()->getAccessPolicy()->can_delete($object) ) {
			$registry->addDeleteAction(
					translate('Удалить'),
					'Method:BulkDeleteWebMethod:class='.strtolower(get_class($object))
			);
		}
 	 	if ( !getFactory()->getAccessPolicy()->can_modify($object) ) return;
		
    	foreach ( $object->getBulkAttributes() as $attribute ) {
			$registry->addModifyAction( translate($object->getAttributeUserName($attribute)), $attribute );
		}
 	}
}