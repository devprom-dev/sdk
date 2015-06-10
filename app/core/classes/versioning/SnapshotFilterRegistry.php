<?php

class SnapshotFilterRegistry extends ObjectRegistrySQL
{
	public function getAll()
	{
		$data = parent::getAll()->getRowset();

		array_unshift($data, array (
				'cms_SnapshotId' => '',
				'Caption' => translate('Текущая')
		));
		
		return $this->createIterator($data);
	}
}