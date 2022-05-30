<?php

class WikiPageTraceTypeRegistry extends ObjectRegistrySQL
{
	public function Query($parms = array())
	{
		return $this->createIterator(
				array (
						array (
								'entityId' => 'coverage',
								'Caption' => translate('Трассировка')
						),
						array (
								'entityId' => 'branch',
								'Caption' => translate('Ветка')
						)
				)
		);
	}
}