<?php

class StateBaseRegistry extends ObjectRegistrySQL
{
 	function getQueryClause()
 	{
 	    if ( $this->getObject()->getObjectClass() == '' ) return parent::getQueryClause();

 	    return "(SELECT t.* FROM pm_State t WHERE t.ObjectClass = '".strtolower($this->getObject()->getObjectClass())."')";
 	}
}