<?php
include "IssueAuthorRegistryUsersBuilder.php";

class IssueAuthorRegistry extends ObjectRegistrySQL
{
	private $data = array();
	
	function Merge( $data, array $alternativeKey = array() ) {
		$this->data[] = $data;
	}

	function getQueryClause()
    {
        $this->data = array();

        $builder = new IssueAuthorRegistryUsersBuilder();
        $builder->build($this);
        foreach( getSession()->getBuilders('IssueAuthorRegistryBuilder') as $builder ) $builder->build($this);

        return "( ".join(" UNION ", $this->data).") ";
    }
}