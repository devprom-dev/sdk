<?php

class WikiPageTraceTypeRegistry extends ObjectRegistrySQL
{
	public function getAll()
	{
		return $this->createIterator(
				array (
						array (
								'entityId' => 'coverage',
								'Caption' => translate('Трассировка')
						),
						array (
								'entityId' => 'branch',
								'Caption' => translate('Копия')
						)
				)
		);
	}
}