<?php

class WikiPageTraceUnsyncReasonRegistry extends ObjectRegistrySQL
{
	public function getAll()
	{
		return $this->createIterator(
				array (
						array (
								'entityId' => 'text-changed',
								'Caption' => text(770)
						),
						array (
								'entityId' => 'structure-append',
								'Caption' => text(1736)
						)
				)
		);
	}
}