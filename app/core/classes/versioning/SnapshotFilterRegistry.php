<?php

class SnapshotFilterRegistry extends ObjectRegistrySQL
{
	public function Query($parms = array())
	{
		$data = parent::Query($parms)->getRowset();

		array_unshift($data, array (
				'cms_SnapshotId' => '',
				'Caption' => translate('Текущая')
		));
		
		return $this->createIterator($data);
	}
}