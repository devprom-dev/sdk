<?php

class SortProjectImportanceClause extends SortAttributeClause
{
	function __construct()	{
		parent::__construct('VPD');
	}

	function clause() {
 		return "(SELECT IFNULL(p.Importance,99) FROM pm_Project p WHERE p.VPD = ".$this->getAlias().".VPD) ASC, (SELECT p.Caption FROM pm_Project p WHERE p.VPD = ".$this->getAlias().".VPD) ASC";
 	}
}
