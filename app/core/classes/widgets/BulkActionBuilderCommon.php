<?php

include_once SERVER_ROOT_PATH."core/classes/widgets/BulkActionBuilder.php";

class BulkActionBuilderCommon extends BulkActionBuilder
{
 	function build( BulkActionRegistry $registry )
 	{
 		$object = $registry->getObject()->getObject();
 		
 	 	if ( getFactory()->getAccessPolicy()->can_delete($object) )
 		{
			$registry->addDeleteAction(
					translate('Удалить'),
					'Method:BulkDeleteWebMethod:class='.strtolower(get_class($object))
			);
		}

 	 	if ( !getFactory()->getAccessPolicy()->can_modify($object) ) return;
		
 		// modifiable attributes
		$bulk_attributes = array_merge(
				$object->getAttributesByGroup('bulk')
		);
 		$system_attributes = array_merge(
            $object->getAttributesByGroup('system'),
            $object->getAttributesByGroup('nonbulk'),
            $object->getAttributesByType('wysiwyg'),
            $object->getAttributesByType('text'),
            $object->getAttributesByType('varchar'),
            array (
                'OrderNum'
            )
 		);
 		$system_types = array('date', 'datetime', 'file', 'image', 'varchar', 'text', 'char', 'password');

		foreach ( $object->getAttributes() as $key => $attribute )
		{
			if ( in_array($key, $system_attributes) ) continue;
            if ( !getFactory()->getAccessPolicy()->can_modify_attribute($object,$key) ) continue;
			if ( !in_array($key, $bulk_attributes) ) {
				if ( in_array($object->getAttributeType($key), $system_types) ) continue;
				if ( !$object->IsAttributeStored($key) ) continue;
			}
			$registry->addModifyAction( translate($object->getAttributeUserName($key)), $key );
		}
 	}
}