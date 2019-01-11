<?php

class RequestIntegrationRegistry extends ObjectRegistrySQL
{
    function getQueryClause()
    {
        return " ( SELECT t.*, (SELECT MAX(l.URL) FROM pm_IntegrationLink l
	 		                     WHERE l.ObjectClass = 'Request'
	 		                       AND l.ObjectId = t.pm_ChangeRequestId ) ExternalLink
	 		         FROM ".parent::getQueryClause()." t ) ";
    }
}