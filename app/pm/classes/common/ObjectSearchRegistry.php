<?php

class ObjectSearchRegistry extends ObjectRegistrySQL
{
	function getQueryClause()
	{
		$columns = array('t.*');
		
		foreach( $this->getPersisters() as $persister )
		{
			if ( !$persister instanceof CustomAttributesPersister ) continue;
			$columns = array_merge($columns, $persister->getSelectColumns('t')); 
		}
		
		if ( count($columns) < 2 ) return parent::getQueryClause();
		
		return "(SELECT ".join(',',$columns)." FROM ".parent::getQueryClause()." t)";
	}
}