<?php

class ObjectTemplateRegistry extends ObjectRegistrySQL
{
	function getQueryClause()
	{
		return " (SELECT t.* FROM cms_Snapshot t WHERE t.ListName = '".$this->getObject()->getListName()."' ) ";
	}
}