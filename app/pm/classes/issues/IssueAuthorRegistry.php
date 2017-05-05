<?php

include "IssueAuthorRegistryUsersBuilder.php";

class IssueAuthorRegistry extends ObjectRegistrySQL
{
	private $data = array();
	
	function Merge( array $data, array $alternativeKey = array() )
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

	function Query( $parms = array() )
	{
		$rowset = $this->createSQLIterator('')->getRowset();

		foreach( $parms as $parm )
		{
			if ( $parm instanceof FilterInPredicate )
			{
				$id_key = $this->getObject()->getIdAttribute();
				$id_value = array_filter(
					!is_array($parm->getValue()) ? preg_split('/,/', $parm->getValue()) : $parm->getValue(),
					function($value) {
						return $value != '';
					}
				);
				if ( count($id_value) > 0 ) {
					$rowset = array_filter( $rowset, function(&$row) use($id_key, $id_value) {
						return in_array($row[$id_key], $id_value);
					});
				}
				else {
					$rowset = array();
				}
			}
			if ( $parm instanceof FilterAttributePredicate )
			{
				$id_key = $parm->getAttribute();
				$id_value = preg_split('/,/', $parm->getValue());

				if ( $id_value != '' ) {
					$rowset = array_filter($rowset, function (&$row) use ($id_key, $id_value) {
						return in_array($row[$id_key], $id_value);
					});
				}
				else {
					$rowset = array();
				}
			}
			if ( $parm instanceof FilterSearchAttributesPredicate )
			{
				$id_key = 'Caption';
				$id_value = $parm->getValue();

				if ( $id_value != '' ) {
					$rowset = array_filter($rowset, function (&$row) use ($id_key, $id_value) {
						return mb_stripos($row[$id_key], $id_value) !== false;
					});
				}
				else {
					$rowset = array();
				}
			}
		}
		return $this->createIterator(array_values($rowset));
	}
}