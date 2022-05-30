<?php

class ObjectTemplateRegistry extends ObjectRegistrySQL
{
	function getQueryClause(array $parms)
	{
		return " (SELECT t.* FROM cms_Snapshot t WHERE t.ListName = '".$this->getObject()->getListName()."' ) ";
	}
}