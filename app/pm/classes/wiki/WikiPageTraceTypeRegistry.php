<?php

class WikiPageTraceTypeRegistry extends ObjectRegistrySQL
{
	public function getAll()
	{
		return $this->createIterator(
				array (
						array (
								'entityId' => 'coverage',
								'Caption' => translate('��������')
						),
						array (
								'entityId' => 'branch',
								'Caption' => translate('�����')
						)
				)
		);
	}
}