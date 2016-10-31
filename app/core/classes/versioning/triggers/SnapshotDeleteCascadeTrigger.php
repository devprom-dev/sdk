<?php



class SnapshotDeleteCascadeTrigger extends SystemTriggersBase
{
	function process( $object_it, $kind, $content = array(), $visibility = 1) 
	{
	    if ( $kind != TRIGGER_ACTION_DELETE ) return;
	    
	    if ( is_a($object_it->object, 'Snapshot') ) return;

	    $this->deleteSnapshots($object_it);
	}
	
	function deleteSnapshots( $object_it )
	{
		global $model_factory;
		
		$snapshot_it = $model_factory->getObject('Snapshot')->getRegistry()->Query( array(
				new FilterAttributePredicate('ObjectId', $object_it->getId()),
				new FilterAttributePredicate('ObjectClass', get_class($object_it->object))
		));
		
		while( !$snapshot_it->end() )
		{
			$snapshot_it->delete();
			$snapshot_it->moveNext();
		}
	}
}
 