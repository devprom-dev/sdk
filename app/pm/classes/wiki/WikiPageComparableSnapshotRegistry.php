<?php

class WikiPageComparableSnapshotRegistry extends ObjectRegistrySQL
{
	public function getAll()
	{
		$document_it = $this->getObject()->getDocumentIt();
		
		$snapshot_registry = getFactory()->getObject('Snapshot')->getRegistry();
		
		$snapshot_it = $snapshot_registry->Query( 
				array (
					new FilterAttributePredicate('ObjectClass', get_class($document_it->object)),
					new FilterAttributePredicate('ObjectId', $document_it->getId()),
					new FilterAttributePredicate('Type', 'none')
				)
		);

		$data = $snapshot_it->getRowset();
		
		$trace_registry = getFactory()->getObject('WikiPageTrace')->getRegistry();

		$source = array($document_it->getId());
		
		while( count($source) > 0 )
		{
			$trace_it = $trace_registry->Query(
					array (
							new FilterAttributePredicate('TargetPage', $source),
							new FilterAttributePredicate('Type', 'branch')
					)
				);

			if ( $trace_it->getId() < 1 ) break;

			$source = $trace_it->fieldToArray('SourcePage');
			
			$branch_it = $snapshot_registry->Query( 
					array (
						new FilterAttributePredicate('ObjectClass', get_class($document_it->object)),
						new FilterAttributePredicate('ObjectId', $source),
						new FilterAttributePredicate('Type', 'branch')
					)
				);
			
			if ( $branch_it->count() > 0 )
			{
				$data = array_merge($data, $branch_it->getRowset()); 
			}
			else
			{
				foreach( $source as $document_id )
				{
					$data[] = array (
							'cms_SnapshotId' => 'document:'.$document_id,
							'ObjectId' => $document_id,
							'Type' => 'document',
							'Caption' => $document_it->object->getExact($document_id)->getDisplayName()
					);
				} 
			}
		}

		$source = array($document_it->getId());
		
		while( count($source) > 0 )
		{
			$trace_it = $trace_registry->Query(
					array (
							new FilterAttributePredicate('SourcePage', $source),
							new FilterAttributePredicate('Type', 'branch')
					)
				);

			if ( $trace_it->getId() < 1 ) break;

			$source = $trace_it->fieldToArray('TargetPage');
			
			$branch_it = $snapshot_registry->Query( 
					array (
						new FilterAttributePredicate('ObjectClass', get_class($document_it->object)),
						new FilterAttributePredicate('ObjectId', $source),
						new FilterAttributePredicate('Type', 'branch')
					)
				);
			
			if ( $branch_it->count() > 0 )
			{
				$data = array_merge($data, $branch_it->getRowset()); 
			}
			else
			{
				foreach( $source as $document_id )
				{
					$data[] = array (
							'cms_SnapshotId' => 'document:'.$document_id,
							'ObjectId' => $document_id,
							'Type' => 'document',
							'Caption' => $document_it->object->getExact($document_id)->getDisplayName()
					);
				} 
			}
		}
		
		return $this->createIterator($data);
	}
}