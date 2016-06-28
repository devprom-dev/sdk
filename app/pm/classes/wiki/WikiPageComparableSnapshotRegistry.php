<?php

class WikiPageComparableSnapshotRegistry extends ObjectRegistrySQL
{
	public function getAll()
	{
		$uid = new ObjectUID;
		$projectId = getSession()->getProjectIt()->getId();

		$document_it = $this->getObject()->getDocumentIt();
		$registry = new ObjectRegistrySQL($document_it->object);
		$snapshot_registry = getFactory()->getObject('Snapshot')->getRegistry();

		$documentIds = array(
			$document_it->getId()
		);
		$data = array();

		$trace_registry = getFactory()->getObject('WikiPageTrace')->getRegistry();

		$source = $documentIds;
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
			$documentIds = array_merge($documentIds, $source);
			
			$branch_it = $snapshot_registry->Query( 
					array (
						new FilterAttributePredicate('ObjectClass', get_class($document_it->object)),
						new FilterAttributePredicate('ObjectId', $source),
						new FilterAttributePredicate('Type', 'branch')
					)
				);
			
			if ( $branch_it->count() > 0 ) {
				$data = $this->buildBranch($data, $branch_it, $projectId);
			}
		}

		$source = $documentIds;
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
			$documentIds = array_merge($documentIds, $source);
			
			$branch_it = $snapshot_registry->Query( 
					array (
						new FilterAttributePredicate('ObjectClass', get_class($document_it->object)),
						new FilterAttributePredicate('ObjectId', $source),
						new FilterAttributePredicate('Type', 'branch')
					)
				);
			
			if ( $branch_it->count() > 0 ) {
				$data = $this->buildBranch($data, $branch_it, $projectId);
			}
		}

		foreach( $documentIds as $document_id ) {
			$id = 'document:'.$document_id;
			if ( is_array($data[$id]) ) continue;
			$data = $this->buildBaseline($data, $document_id, $uid, $registry);
		}

		$snapshot_it = $snapshot_registry->Query(
			array (
				new FilterAttributePredicate('ObjectClass', get_class($document_it->object)),
				new FilterAttributePredicate('ObjectId', $document_it->getId()),
				new FilterAttributePredicate('Type', 'none')
			)
		);
		foreach( $snapshot_it->getRowset() as $row ) {
			$row['Caption'] = $projectId != $row['Project'] ? '{'.$row['ProjectCodeName'].'} '.$row['Caption'] : $row['Caption'];
			$data[$row['cms_SnapshotId']] = $row;
		};

		return $this->createIterator(array_values($data));
	}

	protected function buildBaseline( $data, $document_id, $uid, $registry ) {
		$title = '';
		$object_it = $registry->Query(
			array(
				new FilterInPredicate($document_id),
				new DocumentVersionPersister()
			)
		);
		$info = $uid->getUIDInfo($object_it, true);
		if ( $info['alien'] ) $title .= '{'.$info['project'].'} ';
		$title .= $object_it->get('DocumentVersion') != '' ? $object_it->get('DocumentVersion') : $info['caption'];

		$id = 'document:'.$document_id;
		$data[$id] = array (
			'cms_SnapshotId' => $id,
			'ObjectId' => $document_id,
			'Type' => 'document',
			'Caption' => $title
		);
		return $data;
	}

	protected function buildBranch( $data, $branch_it, $projectId ) {
		$items = array_map(function($row) use($projectId) {
			return array (
				'cms_SnapshotId' => 'document:'.$row['ObjectId'],
				'ObjectId' => $row['ObjectId'],
				'Type' => 'document',
				'Caption' => $projectId != $row['Project'] ? '{'.$row['ProjectCodeName'].'} '.$row['Caption'] : $row['Caption']
			);
		}, $branch_it->getRowset());
		foreach( $items as $key => $row ) {
			$data[$row['cms_SnapshotId']] = $row;
		}
		return $data;
	}
}