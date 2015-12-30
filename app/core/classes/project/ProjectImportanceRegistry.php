<?php

class ProjectImportanceRegistry extends ObjectRegistrySQL
{
	public function getAll()
	{
		return $this->createIterator(
				array (
						array (
								'entityId' => 1,
								'Caption' => text(2002)
						),
						array (
								'entityId' => 2,
								'Caption' => text(2005)
						),
						array (
								'entityId' => 3,
								'Caption' => text(2003)
						),
						array (
								'entityId' => 4,
								'Caption' => text(2004)
						)
				)
		);
	}
}