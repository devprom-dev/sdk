<?php

class WikiPageRegistryContent extends ObjectRegistrySQL
{
	function getQueryClause()
	{
	    return " (SELECT WikiPageId, Caption, Content PageContent, UserField3 PageStyle FROM WikiPage) ";
	}
}