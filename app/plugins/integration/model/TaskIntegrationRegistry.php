<?php

class TaskIntegrationRegistry extends ObjectRegistrySQL
{
    function getQueryClause()
    {
        return " ( SELECT t.*, (SELECT MAX(l.URL) FROM pm_IntegrationLink l
	 		                     WHERE l.ObjectClass = 'Task'
	 		                       AND l.ObjectId = t.pm_TaskId ) ExternalLink
	 		         FROM ".parent::getQueryClause()." t ) ";
    }
}