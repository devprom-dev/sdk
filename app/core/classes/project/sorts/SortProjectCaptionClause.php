<?php

class SortProjectCaptionClause extends SortAttributeClause
{
	function __construct() {
		parent::__construct('Caption');
	}

	function clause() {
 		return "(SELECT p.Caption FROM pm_Project p WHERE p.pm_ProjectId = ".$this->getAlias().".Project) ASC";
 	}
}
