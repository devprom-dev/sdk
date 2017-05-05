<?php
include "ApplicationSlackBuilder.php";
include "ApplicationJiraBuilder.php";
include "ApplicationReviewBoardBuilder.php";
include "ApplicationRedmineBuilder.php";
include "ApplicationYouTrackBuilder.php";

class IntegrationApplicationRegistry extends ObjectRegistrySQL
{
	public function getAll()
	{
	    $language = strtolower(getSession()->getLanguageUid());

		return $this->createIterator(
			array (
                array (
                    'entityId' => 'slack',
                    'Caption' => 'Slack',
                    'ReferenceName' => '/plugins/integration/resources/json/slack-'.$language.'.json',
                    'ModelBuilder' => 'ApplicationSlackBuilder',
                    'Type' => 'chat'
                ),
				array (
					'entityId' => 'jirarest',
					'Caption' => 'Jira',
					'ReferenceName' => '/plugins/integration/resources/json/jira-rest-api.json',
                    'ModelBuilder' => 'ApplicationJiraBuilder',
                    'Type' => 'tracker'
				),
                array (
                    'entityId' => 'youtrack',
                    'Caption' => 'YouTrack',
                    'ReferenceName' => '/plugins/integration/resources/json/youtrack-rest-api.json',
                    'ModelBuilder' => 'ApplicationYouTrackBuilder',
                    'Type' => 'tracker'
                ),
                array (
                    'entityId' => 'redmine',
                    'Caption' => 'Redmine',
                    'ReferenceName' => '/plugins/integration/resources/json/redmine-rest-api.json',
                    'ModelBuilder' => 'ApplicationRedmineBuilder',
                    'Type' => 'tracker'
                ),
				array (
					'entityId' => 'reviewboard',
					'Caption' => 'Review Board',
					'ReferenceName' => '/plugins/integration/resources/json/reviewboard.json',
                    'ModelBuilder' => 'ApplicationReviewBoardBuilder',
                    'Type' => 'code'
				)
			)
		);
	}
}