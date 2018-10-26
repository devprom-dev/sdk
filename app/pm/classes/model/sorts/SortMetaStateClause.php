<?php

class SortMetaStateClause extends SortAttributeClause
{
	function __construct() {
		parent::__construct('VPD');
	}

	function clause() {
	    $vpd = getSession()->getProjectIt()->get('VPD');
		return  " IFNULL((SELECT IF(s.IsTerminal = 'Y', 'Z', IF(s.VPD='".$vpd."',CONCAT('!', s.VPD), CONCAT('$',s.VPD))) FROM pm_State s " .
				"   WHERE s.ObjectClass = '".$this->getObject()->getStatableClassName()."' " .
				"	  AND s.VPD = ".$this->getAlias().".VPD".
				"     AND s.ReferenceName = ".$this->getAlias().".State LIMIT 1), '$') ASC ";
 	}
}
