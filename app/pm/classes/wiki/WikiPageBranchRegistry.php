<?php

class WikiPageBranchRegistry extends ObjectRegistrySQL
{
	function getFilters()
	{
		return array_merge( parent::getFilters(), 
				array (
					new FilterVpdPredicate(),
					new FilterAttributePredicate('ObjectClass', $this->getObject()->getObjectClass()),
					new FilterAttributePredicate('Type', 'branch')
				)
		);
	}
	
	function getAll()
	{
		$snapshot_it = getFactory()->getObject('cms_Snapshot')->getRegistry()->Query( $this->getFilters() );
		
		$values = array();
		
		while( !$snapshot_it->end() )
		{
			$values[$snapshot_it->getDisplayName()] = array( 
					'cms_SnapshotId' => $snapshot_it->getId(),
					'Caption' => $snapshot_it->getDisplayName(),
					'ObjectId' => $snapshot_it->get('ObjectId')
			);
			
			$snapshot_it->moveNext();
		}
		
		return $this->createIterator( array_values($values) );
	}
}