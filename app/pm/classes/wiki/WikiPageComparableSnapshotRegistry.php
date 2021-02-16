<?php

class WikiPageComparableSnapshotRegistry extends ObjectRegistrySQL
{
	public function getAll()
	{
		$uid = new ObjectUID;
		$projectId = getSession()->getProjectIt()->getId();

		$document_it = $this->getObject()->getDocumentIt();
		$snapshot_registry = getFactory()->getObject('Snapshot')->getRegistry();

		$documentUID = $document_it->get('UID');
		if ( $documentUID == '' ) {
            $documentUID = $uid->getObjectUid($document_it);
        }

        $registry = new ObjectRegistrySQL($document_it->object);
		$documentIds = $registry->Query(
                array(
                    new FilterTextExactPredicate('UID', $documentUID)
                )
            )->idsToArray();

        $data = array();

        $branch_it = $snapshot_registry->Query(
            array (
                new FilterAttributePredicate('ObjectClass', get_class($document_it->object)),
                new FilterAttributePredicate('ObjectId', $documentIds),
                new FilterAttributePredicate('Type', 'branch'),
                new SortRecentClause()
            )
        );
        $data = $this->buildBranch($data, $branch_it, $projectId);

		foreach( $documentIds as $document_id ) {
			$id = 'document:'.$document_id;
			if ( is_array($data[$id]) ) continue;

            $registry = new ObjectRegistrySQL($document_it->object);
			$data = $this->buildBaseline($data, $document_id, $uid, $registry);
		}

		$snapshot_it = $snapshot_registry->Query(
			array (
				new FilterAttributePredicate('ObjectClass', get_class($document_it->object)),
				new FilterAttributePredicate('ObjectId', $documentIds),
				new FilterAttributePredicate('Type', 'none'),
                new SortRecentClause()
			)
		);
		foreach( $snapshot_it->getRowset() as $row ) {
			$row['Caption'] = $projectId != $row['Project'] ? '{'.$row['ProjectCodeName'].'} '.$row['Caption'] : $row['Caption'];
			$data[$row['cms_SnapshotId']] = $row;
		};

		usort($data, function($left, $right) {
		    return $left['Caption'] > $right['Caption'];
        });

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