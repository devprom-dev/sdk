<?php

class IntegrationApplicationRegistry extends ObjectRegistrySQL
{
	public function getAll()
	{
		return $this->createIterator(
			array (
				array (
					'entityId' => 'jirarest',
					'Caption' => 'JIRA REST API',
					'ReferenceName' => '/plugins/integration/resources/json/jira-rest-api.json'
				),
				array (
					'entityId' => 'reviewboard',
					'Caption' => 'Review Board',
					'ReferenceName' => '/plugins/integration/resources/json/reviewboard.json'
				)
			)
		);
	}
}