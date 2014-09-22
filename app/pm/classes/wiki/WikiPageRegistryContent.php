<?php

class WikiPageRegistryContent extends ObjectRegistrySQL
{
	function getQueryClause()
	{
	    return " (SELECT WikiPageId, Content PageContent, UserField3 PageStyle FROM WikiPage) ";
	}
}