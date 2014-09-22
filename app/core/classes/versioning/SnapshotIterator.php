<?php

class SnapshotIterator extends OrderedIterator
{
 	function freezeTotal( $values )
 	{
 		global $model_factory;
 		
		$snapshotitem = $model_factory->getObject('cms_SnapshotItem');
		$itemvalue = $model_factory->getObject('cms_SnapshotItemValue');

		$item_id = $snapshotitem->add_parms(
			array ( 'Snapshot' => $this->getId(),
					'ObjectId' => 1,
					'ObjectClass' => '' )
		);

		// freeze values of each item
		$keys = array_keys($values);
		
		foreach ( $keys as $key )
		{
			$itemvalue->add_parms(
				array ( 'SnapshotItem' => $item_id,
					    'Caption' => $key,
					    'ReferenceName' => $key,
					    'Value' => $values[$key] )
			);
		}
 	}
}
