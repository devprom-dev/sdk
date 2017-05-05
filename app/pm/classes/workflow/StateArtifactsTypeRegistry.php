<?php

class StateArtifactsTypeRegistry extends ObjectRegistrySQL
{
	public function getAll()
	{
		return $this->createIterator(
            array (
                array (
                    'entityId' => 'Requirement',
                    'Caption' => translate('Требования')
                ),
                array (
                    'entityId' => 'TestScenario',
                    'Caption' => translate('Тестовая документация')
                ),
                array (
                    'entityId' => 'HelpPage',
                    'Caption' => translate('Справочная документация')
                )
            )
		);
	}
}