<?php

class SortProjectSelfFirstClause extends SortAttributeClause
{
	function __construct() {
		parent::__construct('VPD');
	}

	function clause() {
 		return "IF(".$this->getAlias().".VPD='".getSession()->getProjectIt()->get('VPD')."',0,1) ASC";
 	}
}
