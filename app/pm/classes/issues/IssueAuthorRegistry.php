<?php

include "IssueAuthorRegistryUsersBuilder.php";

class IssueAuthorRegistry extends ObjectRegistrySQL
{
	private $data = array();
	
	function merge( $data )
	{
		$this->data = array_merge($this->data, $data);
	}
	
	function createSQLIterator( $sql )
	{
		$this->data = array();
		
		$builder = new IssueAuthorRegistryUsersBuilder();
		$builder->build($this);
		foreach( getSession()->getBuilders('IssueAuthorRegistryBuilder') as $builder ) $builder->build($this);

		return $this->createIterator( $this->data );
	}

	function Query( $parms )
	{
		$rowset = $this->createSQLIterator('')->getRowset();
		
		foreach( $parms as $parm )
		{
			if ( $parm instanceof FilterInPredicate )
			{
				$id_key = $this->getObject()->getIdAttribute();
				$id_value = preg_split('/,/', $parm->getValue());
				
				$rowset = array_filter( $rowset, function(&$row) use($id_key, $id_value) {
						return in_array($row[$id_key], $id_value);
				});
			}

			if ( $parm instanceof FilterAttributePredicate )
			{
				$id_key = $parm->getAttribute();
				$id_value = preg_split('/,/', $parm->getValue());
				
				$rowset = array_filter( $rowset, function(&$row) use($id_key, $id_value) {
						return in_array($row[$id_key], $id_value);
				});
			}
		}
		return $this->createIterator(array_values($rowset));
	}}