<?php
// PHPLOCKITOPT NOENCODE
// PHPLOCKITOPT NOOBFUSCATE

class FilterPredicate
{
 	private $filter = '';
 	private $object = null;
 	private $alias = 't';
 	
 	function FilterPredicate ( $filter )
 	{
		$this->setValue($filter);
 	}

 	function setValue( $filter )
 	{
 		$this->filter = $this->check($filter);
 	}
 	
  	function getValue()
 	{
 		return $this->filter;
 	}
 	
 	public function setAlias( $alias )
 	{
 		$this->alias = $alias; 
 	}
 	
 	public function getAlias()
 	{
 		return $this->alias;
 	}
 	
 	function getPredicate( $filter = '' )
 	{
 		if ( !$this->defined($filter) )
 		{
 			if ( !$this->defined($this->filter) )
 			{
 				return "";
 			}

 			return $this->_predicate( $this->filter );
 		}
 		else
 		{
 			return $this->_predicate( $this->check($filter) );
 		}
 	}
 	
 	function check( $filter )
 	{
 		if ( is_array($filter) )
 		{
 			if ( count($filter) < 1 ) return $filter; 

 			array_walk( $filter, function (&$value, $key) 
 			{
 				return $value = htmlspecialchars(DAL::Instance()->Escape(addslashes($value)), ENT_QUOTES | ENT_HTML401, APP_ENCODING);
 			});
 			
 			return join($filter, ',');
 		}
 		else if ( is_object($filter) )
 		{
 		}
 		else if ( strpos($filter, ',') !== false )
 		{
 			return $this->check( preg_split('/,/', $filter) ); 
 		}
 		else
 		{
 			$filter = htmlspecialchars(DAL::Instance()->Escape(addslashes($filter)), ENT_QUOTES | ENT_HTML401, APP_ENCODING);
 		}
 		
 		return $filter;
 	}
 	
 	function defined( $filter )
 	{
 		if ( is_array($filter) )
 		{
 			$filter = array_filter($filter, function( $value ) {
		    	return !in_array($value, array('','all','hide'));
			});
 			return count($filter) > 0;
 		}
 		else if ( is_object($filter) )
 		{
 			return true;
 		}
 		else 
 		{
 			return $this->defined(preg_split('/,/', $filter));
 		}
 	}
 	
 	function _predicate( $filter )
 	{
 		return "";
 	}
 	
 	function setObject( $object )
 	{
 		$this->object = $object;
 	}
 	
 	function getObject()
 	{
 		return $this->object;
 	}
 	
 	function getPK( $alias )
 	{
 	    $alias = $alias != '' ? $alias."." : "";
 	    	
 	    $object = $this->getObject();
 	
 	    return $alias.$object->getClassName().'Id';
 	}

	public function __sleep()
	{
		unset($this->object);
		$this->object = null;
		return array('filter', 'alias');
	}
	
	public function __destruct()
	{
		unset($this->object);
		$this->object = null;
	}
	
	public function __wakeup()
	{
		$this->object = null;
	}
}