<?php

class SortClauseBase
{
 	var $object, $alias;
 	
 	function SortClauseBase()
 	{
 	}
 	
 	function setObject( $object )
 	{
 		$this->object = $object;
 	}
 	
 	function getObject()
 	{
 		return $this->object;
 	}
 	
 	function setAlias( $alias )
 	{
 		$this->alias = $alias;
 	}

 	function getAlias()
 	{
 		return $this->alias;
 	}
 	
 	function clause()
 	{
 	}
 	
 	function _clause()
 	{
 		return $this->clause();
 	}
 	
 	function setColumnAlias( $column )
 	{
 	    return $this->getAlias() != '' ? $this->getAlias().'.'.$column : $column;
 	}
}