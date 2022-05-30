<?php

class IntegrationTypeRegistry extends ObjectRegistrySQL
{
	public function Query($parms = array())
	{
		return $this->createIterator(
			array (
				array (
					'entityId' => 'read',
					'Caption' => translate('integration2')
				),
				array (
					'entityId' => 'write',
					'Caption' => translate('integration3')
				),
				array (
					'entityId' => 'readwrite',
					'Caption' => translate('integration4')
				),
			)
		);
	}
}