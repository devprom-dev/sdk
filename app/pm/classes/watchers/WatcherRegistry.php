<?php

class WatcherRegistry extends ObjectRegistrySQL
{
	function getFilters()
	{
		$object_it = $this->getObject()->getObjectIt();
		
		if ( !is_object($object_it) ) return parent::getFilters();
		
		return array_merge(
				parent::getFilters(),
				array (
						new FilterAttributePredicate('ObjectId', $object_it->idsToArray()),
						new FilterAttributePredicate('ObjectClass', 
								array (
										strtolower($object_it->object->getClassName()),
										strtolower(get_class($object_it->object))
								)
						)
				)
		);
	}
	
	function getSorts()
	{
		return array_merge( parent::getSorts(),
				array (
						new SortAttributeClause('ObjectId'),
						new SortAttributeClause('RecordCreated')
				)
		);
	}
}