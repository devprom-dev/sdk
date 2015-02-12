<?php

class WatcherUserRegistry extends ObjectRegistrySQL
{
	function getFilters()
	{
		$object = new Metaobject('pm_Watcher');
		$object_it = $object->getRegistry()->Query(
				array (
						new FilterVpdPredicate()
				)
		);
		
		$ids = $object_it->fieldToArray('SystemUser');
		if ( count($ids) < 1 ) $ids = array(0);
		
		return array_merge(
				parent::getFilters(),
				array (
						new FilterInPredicate($ids)
				)
		);
	}
}