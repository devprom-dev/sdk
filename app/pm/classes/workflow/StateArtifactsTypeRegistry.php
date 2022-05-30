<?php

class StateArtifactsTypeRegistry extends ObjectRegistrySQL
{
	public function Query($parms = array())
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
                    'entityId' => 'TestExecution',
                    'Caption' => text('testing78')
                ),
                array (
                    'entityId' => 'HelpPage',
                    'Caption' => translate('Справочная документация')
                )
            )
		);
	}
}