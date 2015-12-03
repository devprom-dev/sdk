<?php

class ClearObjectAccessEvent extends ObjectFactoryNotificator
{
 	function add( $object_it ) {
	}

 	function modify( $prev_object_it, $object_it ) {
	}

 	function delete( $object_it ) 
	{
		if ( !$this->checkEntity($object_it) ) return;

		$access = new Metaobject('pm_ObjectAccess');
		$access_it = $access->getRegistry()->Query(
			array (
				new FilterAttributePredicate('ObjectId', $object_it->getId()),
				new FilterAttributePredicate('ObjectClass', strtolower(get_class($object_it->object)))
			)
		);
		while( !$access_it->end() )
		{
			$access->delete($access_it->getId());
			$access_it->moveNext();
		}
	}

	protected function checkEntity( $object_it ) {
		return $object_it->object instanceof ProjectPage;
	}
}