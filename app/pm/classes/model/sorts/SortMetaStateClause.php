<?php

class SortMetaStateClause extends SortAttributeClause
{
	function __construct() {
		parent::__construct('VPD');
	}

	function clause() {
		return  " (SELECT IF(s.IsTerminal = 'N', 0, 1) FROM pm_State s " .
				"   WHERE s.ObjectClass = '".$this->getObject()->getStatableClassName()."' " .
				"	  AND s.VPD = ".$this->getAlias().".VPD".
				"     AND s.ReferenceName = ".$this->getAlias().".State LIMIT 1) ASC ";
 	}
}
