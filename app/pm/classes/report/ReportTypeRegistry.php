<?php

class ReportTypeRegistry extends ObjectRegistrySQL
{
	public function Query($parms = array())
	{
		return $this->createIterator(
				array (
						array (
								'entityId' => 'table',
								'Caption' => text(2230)
						),
						array (
								'entityId' => 'chart',
								'Caption' => text(2229)
						)
				)
		);
	}
}