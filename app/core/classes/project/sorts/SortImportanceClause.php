<?php

class SortImportanceClause extends SortAttributeClause
{
	function __construct() {
		parent::__construct('VPD');
	}

	function clause() {
 		return "(SELECT IFNULL(p.Importance,99) FROM pm_Project p WHERE p.VPD = ".$this->getAlias().".VPD) ASC";
 	}
}
