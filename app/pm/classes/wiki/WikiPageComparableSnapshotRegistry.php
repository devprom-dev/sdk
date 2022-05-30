<?php

class WikiPageComparableSnapshotRegistry extends ObjectRegistrySQL
{
	public function Query($parms = array())
	{
		$uid = new ObjectUID;
		$projectId = getSession()->getProjectIt()->getId();
		$document_it = $this->getObject()->getDocumentIt();

        $data = array();
        $queryParms = array (
            new FilterAttributePredicate('ObjectClass', get_class($document_it->object)),
            new FilterVpdPredicate(),
            new SortRecentClause()
        );

        if ( $document_it->count() > 0 ) {
            $documentUID = $document_it->get('UID');
            if ( $documentUID == '' ) {
                $documentUID = $uid->getObjectUid($document_it);
            }
            $registry = new ObjectRegistrySQL($document_it->object);
            $documentIds = $registry->QueryKeys(
                    array(
                        new FilterTextExactPredicate('UID', $documentUID)
                    )
                )->idsToArray();
            $queryParms[] = new FilterAttributePredicate('ObjectId', $documentIds);
        }

        $snapshot_registry = getFactory()->getObject('Snapshot')->getRegistry();
        $branch_it = $snapshot_registry->Query(
            array_merge(
                $queryParms,
                array(
                    new FilterAttributePredicate('Type', 'branch')
                )
            ));

        $data = $this->buildBranch($data, $branch_it, $projectId);
        $documentIds = $branch_it->fieldToArray('ObjectId');

        $registry = new ObjectRegistrySQL($document_it->object);
		foreach( $documentIds as $document_id ) {
			$id = 'document:'.$document_id;
			if ( is_array($data[$id]) ) continue;
			$data = $this->buildBaseline($data, $document_id, $uid, $registry);
		}

		$snapshot_it = $snapshot_registry->Query(
            array_merge(
                $queryParms,
                array(
                    new FilterAttributePredicate('Type', 'none'),
                    new FilterAttributePredicate('ObjectId', $documentIds)
                )
            ));
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